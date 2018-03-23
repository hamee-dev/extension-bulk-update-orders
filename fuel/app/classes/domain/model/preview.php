<?php
/**
 * プレビューに関するロジッククラス
 *
 * Class Domain_Model_Preview
 */
class Domain_Model_Preview
{
    /**
     * 更新設定とシステムが更新した項目を合わせた設定オブジェクトを返す
     *
     * @param Domain_Value_Convertresult $domain_value_convert_result Domain_Model_Updatesettingのconvertメソッドの結果オブジェクト
     * @param Model_Bulkupdatesetting $setting 更新設定のオブジェクト
     * @return array bulk_update_columnの配列（更新設定の項目とシステムが自動更新する項目を合わせたもの）
     * 例: [
     *    '1' => object(Model_Bulkupdatecolumn)#64 (12) {},
     *    '2' => object(Model_Bulkupdatecolumn)#64 (12) {},
     *    '3' => object(Model_Bulkupdatecolumn)#64 (12) {},
     *  ]
     */
    public static function get_bulk_update_columns(Domain_Value_Convertresult $domain_value_convert_result, Model_Bulkupdatesetting $setting) : array {
        $evaluated_orders = $domain_value_convert_result->get_update_target_orders();
        $bulk_update_columns = $setting->bulk_update_columns;

        // 更新する予定の全項目のphysical_nameの配列を作る
        // NOTE: 全伝票で更新する項目は等しくなっているため最初の要素の項目で処理を進める
        // 今後伝票毎に更新する項目が異なる場合には全件ループで回してチェックする必要がある
        $evaluated_orders_physical_names = array_keys(current($evaluated_orders));

        // 更新対象に設定した項目のphysical_nameの配列を作る
        $setting_physical_names = [];
        foreach($bulk_update_columns as $bulk_update_column){
            $setting_physical_names[] = $bulk_update_column->receive_order_column->physical_name;
        }

        // 既に更新対象として設定されている項目を除去しシステムが自動更新した項目のみを取得する
        $system_auto_physical_names = array_diff($evaluated_orders_physical_names, $setting_physical_names);

        // 対象の項目のオブジェクトをまとめて取得する
        // NOTE: evaluated_orders_physical_namesで指定してしまうと
        // ・元の設定のupdate_methodが分からなくなってしまう
        // ・並び順がコントロールできない（更新設定項目＋システム更新項目の順にしたかった）
        // となるため差分のみを見るロジックになっている
        $receive_order_columns = Model_Receiveordercolumn::findAll([['physical_name', 'in', $system_auto_physical_names]]);
        // 更新対象一覧を作成
        $update_method = Model_Updatemethod::findOne(['id' => Model_Updatemethod::OVERWRITE]);
        foreach($receive_order_columns as $receive_order_column){
            // 受注確認内容は表示形式が特殊なので表示しない（追加しない）
            // NOTE: 今後表示しない項目が増えた場合はここを判定関数化する
            if($receive_order_column->id === Model_Receiveordercolumn::COLUMN_ID_CONFIRM_IDS) continue;
            // ここではviewで使う必要最低限の要素を作っていれておく
            $bulk_update_column = new Model_Bulkupdatecolumn();
            $bulk_update_column->receive_order_column = $receive_order_column;
            $bulk_update_column->update_method = $update_method;
            $bulk_update_columns[] = $bulk_update_column;
        }

        return $bulk_update_columns;
    }

    /**
     * プレビュー画面の表示に使うための配列を生成する
     *
     * @param Domain_Value_Convertresult $domain_value_convert_result Domain_Model_Updatesettingのconvertメソッドの結果オブジェクト
     * @param array $receive_order_list 受注伝票検索APIのdataの結果配列
     * @param array $bulk_update_columns 更新設定項目の一覧
     * @param Utility_Master $master マスタ取得用オブジェクト
     * @return array プレビュー画面で表示するために使う配列
     * 例:
     * $display_values[
     * 'id1' => [
     *              'receive_order_date'    => ['before_value' => 'before1', 'after_value' => 'after1'],
     *              'receive_order_shop_id' => ['before_value' => 'before1', 'after_value' => 'after1'],
     *              'excluded_reason'       => '',
     *          ],
     * 'id2' => [
     *              'receive_order_date'    => ['before_value' => 'before2', 'after_value' => 'after2'],
     *              'receive_order_shop_id' => ['before_value' => 'before2', 'after_value' => 'after2'],
     *              'excluded_reason'       => '出荷確定済み',
     *          ],
     * ];
     */
    public static function get_display_value(Domain_Value_Convertresult $domain_value_convert_result, array $receive_order_list, array $bulk_update_columns, Utility_Master $master) : array {
        $evaluated_orders = $domain_value_convert_result->get_update_target_orders();
        $excluded_id_and_reason = $domain_value_convert_result->get_excluded_id_and_reason();

        $display_values = [];
        foreach($receive_order_list as $receive_order){
            $receive_order_id = $receive_order['receive_order_id'];
            if(isset($excluded_id_and_reason[$receive_order_id]['excluded_reason'])){
                $display_values[$receive_order_id]['excluded_reason'] = $excluded_id_and_reason[$receive_order_id]['excluded_reason'];
            } else {
                $display_values[$receive_order_id]['excluded_reason'] = '';
            }
            foreach($bulk_update_columns as $bulk_update_column){
                $column_name = $bulk_update_column->receive_order_column->physical_name;
                $before_value = Domain_Value_Receiveordercolumn::get_display_value($bulk_update_column->receive_order_column, $master, $bulk_update_columns, $receive_order[$column_name], true, true);
                $after_value  = Domain_Value_Receiveordercolumn::get_display_value($bulk_update_column->receive_order_column, $master, $bulk_update_columns, $evaluated_orders[$receive_order_id][$column_name], false, true);
                $display_values[$receive_order_id][$column_name] = [
                    'before_value' => $before_value,
                    'after_value'  => $after_value,
                ];
            }
        }
        return $display_values;
    }
}