<?php
/**
 * SQSに関するロジッククラス
 * SQSのクライアントはこのクラス経由で操作すること
 * Controller or Task -> Domain(本クラス) -> Aws\Sqs\Client(公式SDK)
 * NOTE: Clinet_Sqsという公式SDKをoverrideした自前クライアントクラスを作ろうとしたが以下理由で断念した
 * ・fuel/vendor/aws/aws-sdk-php/src/AwsClient.phpのparseClass内においてクラス名依存による処理があるため
 * ・この処理以外にもクラス名依存処理がある可能性があるため
 * そのためこのドメイン層にクライアント操作的なものを多少含むことを許容した
 *
 * SQSクライアント公式ドキュメント
 * @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/class-Aws.Sqs.SqsClient.html
 */
class Domain_Model_Sqs{
    const SQS_VERSION = '2012-11-05';
    const MAX_NUMBER_OF_MESSAGES = 1;
    const WAIT_TIME_SECONDS = 20;

    // 可視性タイムアウトを延ばす時の値
    // NOTE: とりあえず1時間に設定
    const LONG_VISIBILITY_TIMEOUT = 3600;

    /**
     * SQSクライアントオブジェクト
     * @var Aws\Sqs\SqsClient
     */
    private $_sqs_client = null;

    /**
     * キューのURL（キャッシュ用）
     * @var string
     */
    private $_queue_url = null;

    /**
     * 仕様:
     * 引数でconfigを渡せばそれが使われる
     * 引数にconfigを渡さなかった場合、環境毎のsqs.phpが参照される
     *   開発環境: config/development/sqs.php
     *   その他環境: config/sqs.php
     *   該当のconfigに指定の属性が定義されていない場合、その属性は設定されない
     *   設定しないことによりSDK内部でEC2の設定を読み取ってくれる
     *   そのためステージングや本番はむしろ設定しない方が良い
     *
     * @param array $config AwsClientのconfigと一緒の形
     */
    public function __construct(array $config = []){
        if(!isset($config['credentials']) && Config::get('sqs.credentials')){
            $config['credentials'] = [];
            $config['credentials']['key'] = Config::get('sqs.credentials.key');
            $config['credentials']['secret'] = Config::get('sqs.credentials.secret');
        }

        if(!isset($config['region']) && Config::get('sqs.region')){
            $config['region'] = Config::get('sqs.region');
        }

        if(!isset($config['endpoint']) && Config::get('sqs.endpoint')){
            $config['endpoint'] = Config::get('sqs.endpoint');
        }

        // versionは最新安定版を固定値で指定する
        if(!isset($config['version'])){
            $config['version'] = self::SQS_VERSION;
        }

        $this->_sqs_client = new Aws\Sqs\SqsClient($config);
    }

    /**
     * SQSにキューを登録する処理
     * @param string $message_body
     * @param string $message_group_id
     * @return Aws\Result
     * @throws Aws\Exception\AwsException
     */
    public function enque(string $message_body, string $message_group_id) : Aws\Result {
        try{
            $queue_url = $this->_get_queue_url();
            $params = [
                'QueueUrl'       => $queue_url,
                'MessageBody'    => $message_body,
                'MessageGroupId' => $message_group_id,
            ];
            \Log::notice_ex('SQS enque start', $params);
            // メッセージ登録
            $result = $this->_sqs_client->sendMessage($params);
            \Log::notice_ex('SQS enque end', (array)$result);
            return $result;

        } catch(Aws\Exception\AwsException $e) {
            Log::error_ex('SQSへのキュー登録処理で例外発生', [
                'RequestId' => $e->getAwsRequestId(),
                'ErrorType' => $e->getAwsErrorType(),
                'ErrorCode' => $e->getAwsErrorCode(),
            ]);
            throw $e;
        }
    }

    /**
     * SQSからキューを取得する処理
     * NOTE: 本処理内ではリクエストパラメータとレスポンスに関してログを吐いていない
     * deque処理はタスクで常駐するものとなり、ここにログを仕込むと定期実行毎にログが出て
     * ・ログを圧迫する
     * ・本来見たいログのノイズになる
     * よってログを吐いていない
     *
     * @return Aws\Result
     * @throws Aws\Exception\AwsException
     */
    public function deque() : Aws\Result {
        try{
            $queue_url = $this->_get_queue_url();
            $params = [
                'QueueUrl'              => $queue_url,
                'AttributeNames'        => ['All'],
                'MaxNumberOfMessages'   => self::MAX_NUMBER_OF_MESSAGES,
                'MessageAttributeNames' => ['All'],
            ];
            // メッセージ取得
            $result = $this->_sqs_client->receiveMessage($params);
            return $result;
        } catch(Aws\Exception\AwsException $e) {
            Log::error_ex('SQSへのキュー取得処理で例外発生', [
                'RequestId' => $e->getAwsRequestId(),
                'ErrorType' => $e->getAwsErrorType(),
                'ErrorCode' => $e->getAwsErrorCode(),
            ]);
            throw $e;
        }
    }

    /**
     * SQSからキューを削除する処理
     * @param string $receipt_handle
     * @return Aws\Result
     * @throws Aws\Exception\AwsException
     */
    public function delete_message(string $receipt_handle) : Aws\Result {
        try{
            $queue_url = $this->_get_queue_url();
            $params = [
                'QueueUrl'      => $queue_url,
                'ReceiptHandle' => $receipt_handle,
            ];
            \Log::notice_ex('SQS delete_message start', $params);
            $result = $this->_sqs_client->deleteMessage($params);
            \Log::notice_ex('SQS delete_message end', (array)$result);
            return $result;

        } catch(Aws\Exception\AwsException $e) {
            Log::error_ex('SQSへのキュー削除処理で例外発生', [
                'RequestId' => $e->getAwsRequestId(),
                'ErrorType' => $e->getAwsErrorType(),
                'ErrorCode' => $e->getAwsErrorCode(),
            ]);
            throw $e;
        }
    }

    /**
     * キューの可視性タイムアウトを変更する処理
     * @param string $receipt_handle
     * @param int $visibility_timeout
     * @return Aws\Result
     * @throws Aws\Exception\AwsException
     */
    public function change_message_visibility(string $receipt_handle, int $visibility_timeout = self::LONG_VISIBILITY_TIMEOUT) : Aws\Result {
        try{
            $queue_url = $this->_get_queue_url();
            $params = [
                'QueueUrl'          => $queue_url,
                'ReceiptHandle'     => $receipt_handle,
                'VisibilityTimeout' => $visibility_timeout,
            ];
            \Log::notice_ex('SQS change_message_visibility start', $params);
            $result = $this->_sqs_client->changeMessageVisibility($params);
            \Log::notice_ex('SQS change_message_visibility end', (array)$result);
            return $result;
        } catch(Aws\Exception\AwsException $e) {
            Log::error_ex('キューの可視性タイムアウトを変更する処理で例外発生', [
                'RequestId' => $e->getAwsRequestId(),
                'ErrorType' => $e->getAwsErrorType(),
                'ErrorCode' => $e->getAwsErrorCode(),
            ]);
            throw $e;
        }
    }

    /**
     * NOTE: キューは作成済み前提です
     * 該当のキューが存在しなかったりキューへのアクセス許可がない場合は例外となります
     * 開発環境ではElasticMQ起動時のconfigファイルにて該当のキューを作成済みなので問題なし
     * @return string 該当のキューのQueueUrlを返却する
     */
    private function _get_queue_url() : string {
        if(!is_null($this->_queue_url)) return $this->_queue_url;
        $queue_name = Config::get('sqs.que_name');
        $this->_queue_url = $this->_sqs_client->getQueueUrl(['QueueName'=>$queue_name])['QueueUrl'];
        return $this->_queue_url;
    }

}