<?php

/**
 * Nextengine APIクライアントクラス
 *
 * Class Client_Neapi
 */
class Client_Neapi extends neApiClient
{
    const PATH_RECEIVEORDER_BASE_SEARCH             = '/api_v1_receiveorder_base/search';
    const PATH_RECEIVEORDER_BASE_BULKUPDATE         = '/api_v1_receiveorder_base/bulkupdate';
    const PATH_NOTICE_EXECUTION_ADD                 = '/api_v1_notice_execution/add';
    const PATH_SHOP_SEARCH                          = '/api_v1_master_shop/search';
    const PATH_CONTRACTED_COMPANIES_GET             = '/api_app/company';
    const PATH_LOGIN_COMPANY_INFO                   = '/api_v1_login_company/info';
    const PATH_LOGIN_USER_INFO                      = '/api_v1_login_user/info';
    const PATH_RECEIVEORDER_CONFIRM_SEARCH          = '/api_v1_receiveorder_confirm/search';
    const PATH_SYSTEM_CANCELTYPE_INFO               = '/api_v1_system_canceltype/info';
    const PATH_SYSTEM_DELIVERY_INFO                 = '/api_v1_system_delivery/info';
    const PATH_SYSTEM_PAYMENTMETHOD_INFO            = '/api_v1_system_paymentmethod/info';
    const PATH_SYSTEM_DEPOSITTYPE_INFO              = '/api_v1_system_deposittype/info';
    const PATH_RECEIVEORDER_FORWARDINGAGENT_SEARCH  = '/api_v1_receiveorder_forwardingagent/search';
    const PATH_SYSTEM_CREDITTYPE_INFO               = '/api_v1_system_credittype/info';
    const PATH_SYSTEM_CREDITAPPROVALTYPE_INFO       = '/api_v1_system_creditapprovaltype/info';
    const PATH_SYSTEM_CUSTOMRTYPE_INFO              = '/api_v1_system_customertype/info';
    const PATH_RECEIVEORDER_GROUPINGTAG_SEARCH      = '/api_v1_receiveorder_groupingtag/search';


    // 受注伝票一括更新時のエラーコード（エラーが1件以上ある場合にこのエラーコードになる）
    const ERROR_CODE_BULKUPDATE = '020500';
    // 最終更新日が更新されている時のエラーコード
    const ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE = '020006';
    // 「受注取り込み中」のエラーコード
    const ERROR_CODE_RECEIVE_ORDER_IMPORTING = '020007';
    // メール取り込み済みの伝票の受注キャンセルと作業用欄以外の項目を更新しようとした際のエラーコード
    const ERROR_CODE_STATUS_MAIL_IMPORTED = '020009';
    // メイン機能がメンテナンス中だった時のエラーコード
    const ERROR_CODE_PLATFORM_MAINTENANCE = '002007';
    const ERROR_CODE_MAIN_FUNCTION_HOST_MAINTENANCE = '003004';
    const ERROR_CODE_MAIN_FUNCTION_MAINTENANCE = '003005';
    // メイン機能が高負荷だった時のエラーコード
    const ERROR_CODE_HEAVY_LOADING1 = '003001';
    const ERROR_CODE_HEAVY_LOADING2 = '003002';
    // 復帰不能のエラーコード
    const ERROR_CODE_EXCEPTION = '999999';
    // 古いアクセストークンでリクエストした場合のエラーコード
    const ERROR_CODE_OLD_ACCESS_TOKEN = '002002';
    // 存在しないパスだった場合のエラーコード
    const ERROR_CODE_NON_EXISTING_PATH = '000001';

    // 一定時間後にリトライすると復帰するエラーコード一覧
    const CAN_RETRY_LONG_WAIT_ERROR_CODES = [
        self::ERROR_CODE_PLATFORM_MAINTENANCE,
        self::ERROR_CODE_MAIN_FUNCTION_HOST_MAINTENANCE,
        self::ERROR_CODE_MAIN_FUNCTION_MAINTENANCE,
    ];

    // 少し待ってからリトライすると復帰するエラーコード一覧
    const CAN_RETRY_LITTLE_WAIT_ERROR_CODES = [
        self::ERROR_CODE_HEAVY_LOADING1,
        self::ERROR_CODE_HEAVY_LOADING2,
    ];

    // 受注メール取込済のステータスコード
    const STATUS_CODE_RECEIVE_ORDER_MAIL_CAPTURED = '1';
    // 起票済(CSV/手入力)のステータスコード
    const STATUS_CODE_RECEIVE_ORDER_ISSUED = '2';
    // 納品書印刷待ちのステータスコード
    const STATUS_CODE_RECEIVE_ORDER_WAIT_FOR_PRINT = '20';
    // 出荷確定済みのステータスコード
    const STATUS_CODE_RECEIVE_ORDER_SHIPPED = '50';

    // 受注確認内容「AG：備考欄を見て下さい。 」のID
    const RECEIVE_ORDER_CONFIRM_ID_LOOK_NOTE = 'AG';

    // 入金状況「未入金」のID
    const RECEIVE_ORDER_DEPOSIT_TYPE_ID_NOT = '0';

    // 承認状況「未承認」のID
    const RECEIVE_ORDER_CREDIT_APPROVAL_TYPE_ID_NOT = '10';

    // お知らせのsuccessの定数
    const EXECUTION_NOTICE_SUCCESS_TRUE  = '1';
    const EXECUTION_NOTICE_SUCCESS_FALSE = '0';

    // 出荷確定済みでも更新するかどうかのフラグの定数
    // NOTE: APIの仕様では1がtrueで1以外がfalseだが本アプリでは0をfalseとして扱う
    const RECEIVE_ORDER_SHIPPED_UPDATE_FLAG_TRUE  = '1';
    const RECEIVE_ORDER_SHIPPED_UPDATE_FLAG_FALSE = '0';

    // Yahoo!ショッピングのモールコード
    const MALL_CODE_YAHOO = '2';

    // 通貨単位区分
    const JAPANESE_YEN = '1';

    // NEAPIから取得できる端数区分ID(切り捨て)
    const ROUND_DOWN = '0';
    // NEAPIから取得できる端数区分ID(四捨五入)
    const ROUND      = '1';
    // NEAPIから取得できる端数区分ID(切り上げ)
    const ROUND_UP   = '2';

    // 受注伝票の数値型の項目の最大値と最小値
    const RECEIVE_ORDER_NUMBER_COLUMN_MAX = 999999999.99;
    const RECEIVE_ORDER_NUMBER_COLUMN_MIN = -999999999.99;

    /**
     * 最大何回リトライするか
     */
    const MAX_RETRY_COUNT = 3;

    /**
     * リトライする際のデフォルトのスリープ時間（秒）
     */
    const DEFAULT_RETRY_SLEEP_SECONDS = 10;

    /**
     * ユーザーオブジェクト
     *
     * @var Model_User
     */
    protected $user = null;

    /**
     * リトライする際のスリープ時間（秒）
     *
     * @var int
     */
    protected $retry_sleep_seconds;

    /**
     * リトライが可能だった場合、リトライを行うかどうか
     *
     * @var bool
     */
    protected $is_retry = false;

    /**
     * リトライするエラーコード
     *
     * @var array
     */
    protected static $retry_error_codes = [
        self::ERROR_CODE_OLD_ACCESS_TOKEN,
        self::ERROR_CODE_NON_EXISTING_PATH // デプロイ中や高負荷だった場合にこのエラーコードになることがあるのでリトライする
    ];

    /**
     * APIサーバーのホスト名
     *
     * @var string
     */
    protected static $api_server = null;

    /**
     * ネクストエンジンサーバーのホスト名
     *
     * @var string
     */
    protected static $ne_server = null;

    /**
     * APIクライアントオブジェクトの初期化を行う
     * 引数にuser_idを渡した場合、DBのアクセストークンとリフレッシュトークンを使う
     * 引数が無い場合は、API実行時にログイン処理を実行し、アクセストークンとリフレッシュトークンを取得する
     *
     * Client_Neapi constructor.
     * @param string $user_id ユーザーID
     * @param bool $is_retry リトライが可能だった場合、リトライを行うかどうか
     * @param int $retry_sleep_seconds リトライ時のスリープ時間
     * @throws Exception
     * @throws FuelException
     */
    public function __construct(string $user_id = null, bool $is_retry = false, int $retry_sleep_seconds = self::DEFAULT_RETRY_SLEEP_SECONDS) {

        parent::__construct(
            \Config::get('nextengine.client_id'),
            \Config::get('nextengine.client_secret')
        );

        static::$api_server =  \Config::get('host.api_server');
        static::$ne_server =  \Config::get('host.ne_server');
        $this->is_retry = $is_retry;
        $this->retry_sleep_seconds = $retry_sleep_seconds;

        // アクセストークンを設定する
        $this->set_access_token($user_id);
    }

    /**
     * 環境ごとのAPIサーバーのホスト名を返すためオーバーライド
     *
     * @return string
     */
    protected static function getApiServerHost() : string {
        return static::$api_server ;
    }

    /**
     * 環境ごとのNEサーバーのホスト名を返すためオーバーライド
     *
     * @return string
     */
    protected static function getNeServerHost() : string {
        return static::$ne_server;
    }

    /**
     * ネクストエンジンAPIを実行し、結果を返します。
     *
     * #### 親クラスからの拡張点
     * - userプロパティがセットされており、アクセストークンが更新されたら自動でDBの値を更新する
     *
     * @param string $path         親クラスのメソッドを参照
     * @param array  $api_params   親クラスのメソッドを参照
     * @param string $redirect_uri 親クラスのメソッドを参照
     * @return array APIのレスポンス。詳しくは[https://developer.next-engine.com/api]を参照。
     */
    public function apiExecute($path, $api_params = [], $redirect_uri = null) : array {

        for ($count = 0; $count <= self::MAX_RETRY_COUNT; $count++) {

            $start_log_message = 'api request start';
            $end_log_message = 'api request end';
            $log_params = ['path' => static::getApiServerHost() . $path, 'api_params' => $api_params];
            // もしバイナリデータが入っている場合はログに表示できないのでlog_paramsから除去
            // NOTE: 本当はバイナリデータの判定をしたいが簡単に判定できる方法がなさそうだったので
            // データを圧縮して送っているものはログに出さない、という風にした
            if(isset($api_params['data_type']) && $api_params['data_type'] === 'gz'){
                unset($log_params['api_params']['data']);
            }

            if ($count > 0) {
                // スリープを入れる
                sleep($this->retry_sleep_seconds);
                // アクセストークンを更新する
                $this->set_access_token();

                $start_log_message = 'retry ' . $start_log_message;
                $end_log_message = 'retry ' . $end_log_message;
                $log_params['retry_count'] = $count;
                Log::info_ex('retry start.', ['retry_count' => $count]);
            }

            Log::notice_ex($start_log_message, $log_params);
            $response = parent::apiExecute($path, $api_params, $redirect_uri);
            $log_params['response'] = $response;
            Log::notice_ex($end_log_message, $log_params);

            // access_token_end_dateが新しくなっていた場合、DBに保存しているaccess_tokenとrefresh_tokenを更新する
            $this->update_user_access_token($response);

            // リトライを実行するかどうか（リトライをしない場合はそのままループを抜ける）
            if (!$this->is_execute_retry($response)) {
                break;
            }

        }

        // リトライ上限までいっても成功しなかったものはエラーログを吐く
        if ($count > self::MAX_RETRY_COUNT && $response['result'] === self::RESULT_ERROR) {
            Log::error_ex('NEAPIのリトライ上限に達しましたが成功しませんでした。', $log_params);
        }

        return $response;
    }

    /**
     * ネクストエンジンログインが不要なAPIを実行します。
     *
     * #### 親クラスからの拡張点
     * - リトライ機構を入れています
     *
     * @param string $path         親クラスのメソッドを参照
     * @param array  $api_params   親クラスのメソッドを参照
     * @return array APIのレスポンス。詳しくは[https://developer.next-engine.com/api]を参照。
     */
    public function apiExecuteNoRequiredLogin($path, $api_params = []) {
        for ($count = 0; $count <= self::MAX_RETRY_COUNT; $count++) {
            $start_log_message = 'api request start';
            $end_log_message = 'api request end';
            $log_params = ['path' => static::getApiServerHost() . $path, 'api_params' => $api_params];
            if ($count > 0) {
                // スリープを入れる
                sleep($this->retry_sleep_seconds);
                $start_log_message = 'retry ' . $start_log_message;
                $end_log_message = 'retry ' . $end_log_message;
                $log_params['retry_count'] = $count;
                Log::info_ex('retry start.', ['retry_count' => $count]);
            }

            Log::notice_ex($start_log_message, $log_params);
            $response = parent::apiExecuteNoRequiredLogin($path, $api_params);
            $log_params['response'] = $response;
            Log::notice_ex($end_log_message, $log_params);

            // リトライを実行するかどうか（リトライをしない場合はそのままループを抜ける）
            if (!$this->is_execute_retry($response)) {
                break;
            }
        }

        // リトライ上限までいっても成功しなかったものはエラーログを吐く
        if ($count > self::MAX_RETRY_COUNT && $response['result'] === self::RESULT_ERROR) {
            Log::error_ex('NEAPIのリトライ上限に達しましたが成功しませんでした。', $log_params);
        }

        return $response;
    }

    /**
     * APIで店舗ID一覧を取得する
     * @param array $where APIで検索する店舗の条件を示した連想配列
     * 指定しない場合は空配列となり条件未指定で全店舗取得する
     * @return array 店舗ID一覧、指定した条件の店舗が存在しない場合は空配列
     * @throws UnexpectedValueException
     */
    public function get_shop_ids(array $where = []) : array {
        $shop_search_params = ['fields' => 'shop_id'];
        $shop_search_params = array_merge($shop_search_params, $where);

        $shop_search_response = $this->apiExecute(self::PATH_SHOP_SEARCH, $shop_search_params);
        if($shop_search_response['result'] !== self::RESULT_SUCCESS){
            throw new UnexpectedValueException('店舗検索に失敗しました');
        }

        return Arr::pluck($shop_search_response['data'], 'shop_id');
    }

    /**
     * リトライを実行するかどうか
     * エラーレスポンスでかつ、is_retryがtrueでかつ、リトライ可能なエラーコードの場合はリトライする
     *
     * @param array $response APIレスポンス
     * @return bool true:リトライを実行する/false:リトライを実行しない
     */
    protected function is_execute_retry(array $response) : bool {
        if ($response['result'] === self::RESULT_ERROR &&
            $this->is_retry &&
            in_array($response['code'], static::$retry_error_codes)) {
            return true;
        }
        return false;
    }

    /**
     * userテーブルのアクセストークンとリフレッシュトークンを更新する
     * access_token_end_dateが新しくなっていた場合、DBに保存しているaccess_tokenとrefresh_tokenを更新する
     *
     * @param array $response APIレスポンス
     * @throws Exception
     * @throws FuelException
     */
    protected function update_user_access_token(array $response) {
        if(!is_null($this->user) &&
            isset($response['access_token_end_date']) &&
            strtotime($this->user->access_token_end_date) < strtotime($response['access_token_end_date'])) {
            $this->user->access_token             = $this->_access_token;
            $this->user->access_token_end_date    = $response['access_token_end_date'];
            $this->user->refresh_token            = $this->_refresh_token;
            $this->user->refresh_token_end_date   = $response['refresh_token_end_date'];
            $this->user->save();
            Log::info_ex('update access_token and refresh_token.',
                [
                    'user_id' => $this->user->id,
                    'access_token' => $this->user->access_token,
                    'access_token_end_date' => $this->user->access_token_end_date,
                    'refresh_token' => $this->user->refresh_token,
                    'refresh_token_end_date' => $this->user->refresh_token_end_date,
                ]
            );
        }
    }

    /**
     * userデータから最新のアクセストークンを取得しセットする
     * 引数を渡した場合、そのユーザーを取得し、アクセストークンをセットする
     * 引数を渡さなかった場合、プロパティのユーザオブジェクトの最新情報を取得し、アクセストークンをセットする
     * 引数を渡さず、かつユーザーオブジェクトがnullだった場合何もしない
     *
     * @param string $user_id ユーザーID
     * @throws FuelException
     */
    protected function set_access_token(string $user_id = null) {

        if (is_null($user_id)) {
            if (is_null($this->user)) {
                return;
            }
            $user_id = $this->user->id;
        }

        $this->user = \Model_User::findOne([['id', $user_id]]);
        if (!is_null($this->user)) {
            $this->_access_token = $this->user->access_token;
            $this->_refresh_token = $this->user->refresh_token;
            Log::info_ex('set access token.',
                [
                    'user_id' => $this->user->id,
                    'access_token' => $this->user->access_token,
                    'refresh_token' => $this->user->refresh_token
                ]
            );
        }
    }
}