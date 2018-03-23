<?php
namespace Fuel\Tasks;

/**
 * SQSからデキューして伝票更新を実行する処理
 * 実行方法: php oil r updateexecution
 * 主なロジックフロー
 * this -> Domain_Model_Updatesetting::execution(更新ロジック), Domain_Model_Updatesetting::add_notice_for_bulkupdate(お知らせ配信ロジック)
 */
class Updateexecution{
    const SLEEP_SECONDS = 1;
    const TARGET_SIGNALS = [SIGHUP, SIGINT, SIGTERM, SIGUSR1, SIGUSR2];
    // プロセスを無限ループさせているとメモリが増え続けて死んでしまうことがあるため一定回数で停止するようにしている
    // NOTE: 暫定値なので都度調整して良い
    const LOOP_COUNT = 100;

    // メモリ使用の上限値を設定しておく（単位: MiB）
    // NOTE: 暫定値なので都度調整して良い
    const MEMORY_LIMIT = 64;

    public static function run() {
        try{
            // シグナルのディスパッチ
            // これを有効にしておくとシグナルが常に監視されているイメージ
            pcntl_async_signals(true);
            // シグナル発火を検知するためのフラグ
            $terminate = false;
            foreach(self::TARGET_SIGNALS as $signal){
                pcntl_signal($signal, function($signo, $siginfo) use(&$terminate) {
                    \Log::info_ex('シグナルを受信しました。終了フラグを立てて処理を続行します', ['signo' => $signo]);
                    $terminate = true;
                });
            }

            $sqs = new \Domain_Model_Sqs();
            // キューの監視
            $count = 0;
            while(1){
                if($terminate){
                    \Log::info_ex('終了フラグが立てられたため監視を終了します');
                    break;
                }

                if((memory_get_usage() / (1024 * 1024)) > self::MEMORY_LIMIT){
                    \Log::info_ex('メモリ使用量の上限に達したためキュー監視を終了します');
                    break;
                }

                if($count > self::LOOP_COUNT){
                    \Log::info_ex('ループ回数の上限に達したためキュー監視を終了します');
                    break;
                }
                $count++;

                // SQSの設定でロングポーリングで20秒のスリープが入るが念のためタスク側でもスリープを入れておく
                sleep(self::SLEEP_SECONDS);
                $que = $sqs->deque();
                // キューがない場合は監視継続（これ以降の処理をしない）
                if(count($que->get('Messages')) <= 0){
                    continue;
                }

                /***** キューが存在した場合は更新処理開始 *****/
                // キューの中身が意図しない形の場合は処理中止
                if(!isset($que->get('Messages')[0]['Body']) || !isset($que->get('Messages')[0]['ReceiptHandle'])){
                    \Log::error_ex('キューのmessage内が意図した形になっていないため一括更新処理中止');
                    // この状態のキューは再実行しても失敗するのでキューを削除
                    if(isset($que->get('Messages')[0]['ReceiptHandle'])){
                        $receipt_handle = $que->get('Messages')[0]['ReceiptHandle'];
                        $sqs->delete_message($receipt_handle);
                        \Log::info_ex('キューの状態が不正だったためキューを削除しました');
                    }
                    continue;
                }
                // MessageBody内にはexecution_bulk_update_settingsのid値が入っている
                $execution_bulk_update_setting_id = $que->get('Messages')[0]['Body'];
                $receipt_handle = $que->get('Messages')[0]['ReceiptHandle'];
                \Log::info_ex('更新処理を開始します。', ['execution_bulk_update_setting_id' => $execution_bulk_update_setting_id, 'receipt_handle' => $receipt_handle]);

                // 既に実行済みならば対象外とする
                $execution_bulk_update_setting = \Model_Executionbulkupdatesetting::query()
                    ->where('id', $execution_bulk_update_setting_id)
                    ->where('executed', false)
                    ->related('company')
                    ->get_one();
                if(empty($execution_bulk_update_setting)){
                    \Log::error_ex("execution_bulk_update_setting_id: {$execution_bulk_update_setting_id}のレコードが見つからない、または実行済みだったので一括更新処理中止");
                    // この状態のキューは再実行しても失敗するのでキューを削除
                    $sqs->delete_message($receipt_handle);
                    \Log::info_ex("execution_bulk_update_setting_id: {$execution_bulk_update_setting_id}のレコードが見つからない、または実行済みだったので該当のキューを削除");
                    continue;
                }

                // 既に解約済みの企業であればキューを削除しタスクを処理済みにし処理を終了する
                $stoped_at = $execution_bulk_update_setting->company->stoped_at;
                if($stoped_at !== null && $stoped_at < date('Y-m-d H:i:s')){
                    $sqs->delete_message($receipt_handle);
                    $execution_bulk_update_setting->executed = true;
                    $execution_bulk_update_setting->save();
                    \Log::info_ex("解約済み企業のキューだったのでキューを削除しタスクを処理済みにし処理を中止します", ['company_id' => $execution_bulk_update_setting->company_id, 'execution_bulk_update_setting_id' => $execution_bulk_update_setting_id]);
                    continue;
                }

                // 更新対象伝票に更新内容を記載し一括更新APIで更新
                $domain_model_updatesetting = new \Domain_Model_Updatesetting();
                $execution_result = $domain_model_updatesetting->execution($execution_bulk_update_setting);
                $bulkupdate_response = $execution_result->get_response();
                // レスポンス結果から4通りに分岐する
                // 1. メンテナンスなど一定時間後にリトライすると復帰するもの→可視性タイムアウトを延ばし処理終了
                // 2. リトライしても復帰不可能なもの→お知らせを送ってキュー削除をして処理終了
                // 3. リトライで復帰するもの かつ メンテナンスほど長時間待たずに済むもの→デフォの可視性タイムアウトに任せる→何もせずに処理終了
                // 4. 上記以外は正常終了なのでエラー処理せず次に進む
                // codeがある場合 かつ 一括更新のエラーコードでない場合は何かしらのエラー
                if(isset($bulkupdate_response['code']) && $bulkupdate_response['code'] !== \Client_Neapi::ERROR_CODE_BULKUPDATE){
                    $code = $bulkupdate_response['code'];
                    // 一定時間後にリトライすると復帰するエラー
                    if(in_array($code, \Client_Neapi::CAN_RETRY_LONG_WAIT_ERROR_CODES, true)){
                        // キューの可視性タイムアウトを延ばす
                        $sqs->change_message_visibility($receipt_handle);
                        \Log::info_ex('リトライ可能な一括更新エラーを検知したため可視性タイムアウトを延ばして処理を中断します', ['execution_bulk_update_setting_id' => $execution_bulk_update_setting_id, 'code' => $code]);
                        continue;

                    // 少し待ってからリトライすると復帰するエラー
                    } elseif(in_array($code, \Client_Neapi::CAN_RETRY_LITTLE_WAIT_ERROR_CODES, true)){
                        \Log::info_ex('少し待てばリトライ可能なため何もせずに処理を中断します', ['execution_bulk_update_setting_id' => $execution_bulk_update_setting_id, 'code' => $code]);
                        continue;

                    // リトライ不可能なもの
                    } else {
                        \Log::error_ex('リトライ不可能な一括更新エラーを検知したため処理中止', ['execution_bulk_update_setting_id' => $execution_bulk_update_setting_id, 'code' => $code]);
                        // この状態のキューは再実行しても失敗するのでキューを削除
                        $sqs->delete_message($receipt_handle);
                        \Log::info_ex("一括更新に失敗したためキューを削除しました", ['execution_bulk_update_setting_id' => $execution_bulk_update_setting_id]);
                        // 実行済みにする
                        $execution_bulk_update_setting->executed = true;
                        $execution_bulk_update_setting->save();

                        // 失敗した旨のお知らせを通知
                        $client_neapi = new \Client_Neapi($execution_bulk_update_setting->user_id);
                        $execution_notice_content = "エラーが発生したため一括更新処理を中止しました。\nお手数ですがサポートまでお問い合わせください。";
                        \Log::info_ex('NEAPIエラーレスポンスメッセージ', ['message' => $bulkupdate_response['message']]);
                        $notice_add_params = [
                            'execution_notice_success' => \Client_Neapi::EXECUTION_NOTICE_SUCCESS_FALSE,
                            'execution_notice_title'   => '受注一括更新が失敗しました',
                            'execution_notice_content' => "実行タスクID：{$execution_bulk_update_setting->request_key}\n一括更新設定名称：{$execution_bulk_update_setting->name}\n\n{$execution_notice_content}",
                        ];
                        $notice_add_result = $client_neapi->apiExecute(\Client_Neapi::PATH_NOTICE_EXECUTION_ADD, $notice_add_params);
                        if($notice_add_result['result'] !== \Client_Neapi::RESULT_SUCCESS){
                            \Log::error_ex('お知らせ配信に失敗しました', ['execution_bulk_update_setting_id' => $execution_bulk_update_setting_id]);
                        }
                        continue;
                    }
                }

                // 更新が完了したらキューを削除
                $sqs->delete_message($receipt_handle);
                \Log::info_ex('更新処理を完了しキューを削除しました', ['execution_bulk_update_setting_id' => $execution_bulk_update_setting_id]);
                // お知らせを配信し全ての処理が完了してから実行済みフラグをtrueにする
                $execution_bulk_update_setting->executed = true;
                $execution_bulk_update_setting->save();

                // 結果通知APIに結果を通知
                $add_notice_result = $domain_model_updatesetting->add_notice_for_bulkupdate($execution_bulk_update_setting, $execution_result);
                if($add_notice_result){
                    \Log::info_ex('お知らせを配信しました。一連の処理を終了し引き続き監視します', ['execution_bulk_update_setting_id' => $execution_bulk_update_setting_id]);
                } else {
                    \Log::error_ex('お知らせ配信に失敗しました', ['execution_bulk_update_setting_id' => $execution_bulk_update_setting_id]);
                }
            }

        // タスク内で例外が発生した場合、最終的にここでキャッチされる
        } catch(\Exception $e) {
            \Log::info_ex('キュー監視・一括更新実行処理において例外が発生したため処理を停止しました');
            \Log::exception($e);
        }
    }

}