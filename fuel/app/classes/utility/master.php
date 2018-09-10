<?php
/**
 *
 * マスタデータに関するクラス
 *
 * キャッシュしたマスタデータは
 * (company_id)_master_(マスタ名).cache
 * のファイル名でメイン機能単位に保存される
 * サーバが複数ある場合はそれぞれのサーバ上にキャッシュファイルを生成し、サーバ間でのキャッシュの共有はしない
 * NOTE: 別途キャッシュサーバを用意すると複数サーバ間でキャッシュの共有が可能となる
 *
 * マスタデータはDomain_Value_Masterオブジェクトの配列で返る
 *
 * マスタを取得する際はgetメソッドを使うか、各マスタを取得するメソッドを使う
 * 発送方法別項目タイプマスタを取得する際だけ特殊で、get_forwarding_agentメソッドを使う
 *
 * Class Utility_Master
 */
class Utility_Master
{
    /**
     * キャッシュ時間（1日）
     */
    const CACHE_EXPIRATION = 86400;

    /**
     * キャッシュファイルのキーワード
     */
    const CACHE_KEYWORD = 'master';

    /**
     * キャッシュファイル名の区切り文字
     */
    const CACHE_FILE_NAME_SEPARATOR = '_';

    /**
     * キャッシュする際のマスタ名
     */
    const MASTER_NAME_SHOP                          = 'shop'; // 店舗マスタ
    const MASTER_NAME_CONFIRM                       = 'confirm'; // 受注確認内容
    const MASTER_NAME_CANCEL                        = 'cancel'; // 受注キャンセル区分
    const MASTER_NAME_DELIVERY                      = 'delivery'; // 発送方法区分
    const MASTER_NAME_PAYMENT                       = 'payment'; // 支払区分
    const MASTER_NAME_DEPOSIT                       = 'deposit'; // 入金区分
    const MASTER_NAME_FORWARDINGAGENT               = 'forwarding_agent'; // 発送方法別項目タイプ
    const MASTER_NAME_CREDIT                        = 'credit'; // クレジット種類区分
    const MASTER_NAME_CREDITAPPROVAL                = 'credit_approval'; // クレジット承認区分
    const MASTER_NAME_CUSTOMER                      = 'customer'; // 顧客区分
    const MASTER_NAME_GIFT                          = 'gift'; // ギフト区分
    const MASTER_NAME_TAG                           = 'tag'; // 受注分類タグ

    /**
     * APIクライアント
     *
     * @var Client_Neapi
     */
    protected $client_neapi = null;

    /**
     * companiesテーブルのid値
     * キャッシュ生成時のファイル名として使用
     *
     * @var string
     */
    protected $company_id = null;

    /**
     * メモリキャッシュ
     *
     * @var array
     */
    protected static $cache = null;

    /**
     * 引数としてユーザーIDを渡した場合、キャッシュにデータがない場合APIで取得する
     * ユーザーIDを渡さなかった場合、キャッシュからのみ取得する
     * company_idはどの企業のキャッシュデータなのかを判断するために必須
     *
     * Master constructor.
     * @param string $company_id
     * @param string $user_id
     */
    public function __construct(string $company_id, string $user_id = null) {
        $this->company_id = $company_id;
        if (!is_null($user_id)) {
            $this->client_neapi = new Client_Neapi($user_id);
        }
    }

    /**
     * マスタ名を指定してマスタを取得する
     * 発送方法別項目タイプのマスタの場合はすべての発送方法のマスタが取得されます
     * 発送方法別のマスタを取得したい場合はget_forwarding_agentメソッドを使うか、受け取り側で絞り込んでください
     *
     * @param string $name
     * @param bool $enabled 有効なデータのみ取得するかどうか
     * @return array Domain_Value_Masterオブジェクトの配列
     * @throws BadMethodCallException
     */
    public function get(string $name, bool $enabled = true) : array {
        $function_name = 'get_' . $name;
        if (!method_exists($this, $function_name)) {
            throw new BadMethodCallException('存在しないマスタデータです');
        }

        // メモリキャッシュがあれば取得する(各メソッド内での並び替えをが件数によっては遅くなるため、ここでキャッシュを返す)
        $cache_name = $function_name . '_' . ($enabled ? '1' : '0');
        if (isset(self::$cache[$cache_name])) {
            return self::$cache[$cache_name];
        }
        $master_data = $this->$function_name($enabled);
        self::$cache[$cache_name] = $master_data;
        return $master_data;
    }

    /**
     * マスタ名一覧を取得する
     *
     * @return array
     */
    public static function get_master_names() : array {
        return [
            self::MASTER_NAME_SHOP,
            self::MASTER_NAME_CONFIRM,
            self::MASTER_NAME_CANCEL,
            self::MASTER_NAME_DELIVERY,
            self::MASTER_NAME_PAYMENT,
            self::MASTER_NAME_DEPOSIT,
            self::MASTER_NAME_FORWARDINGAGENT,
            self::MASTER_NAME_CREDIT,
            self::MASTER_NAME_CREDITAPPROVAL,
            self::MASTER_NAME_CUSTOMER,
            self::MASTER_NAME_GIFT,
            self::MASTER_NAME_TAG,
        ];
    }

    /**
     * 発送方法別項目タイプマスタ名かどうか
     *
     * @param string $name
     * @return bool
     */
    public static function is_forwarding_agent(string $name) : bool {
        return strpos($name, self::MASTER_NAME_FORWARDINGAGENT) !== false;
    }

    /**
     * マスタデータ名から発送方法別項目タイプ名を取得する
     *
     * @param string $name マスタ名
     * @return string 発送方法別項目タイプ名
     */
    public static function get_forwarding_agent_type(string $name) : string {
        if (self::is_forwarding_agent($name)) {
            return str_replace(self::MASTER_NAME_FORWARDINGAGENT . '_', '', $name) . '_kbn';
        }
        return '';
    }

    /**
     * 発送方法別項目タイプ名からマスタデータ名を取得する
     *
     * @param string $name 発送方法別項目タイプ名
     * @return string マスタ名
     */
    public static function get_forwarding_agent_master_name(string $name) : string {
        return self::MASTER_NAME_FORWARDINGAGENT . str_replace('_kbn', '', $name);
    }

    /**
     * 店舗マスタを取得する
     *
     * @param bool $enabled 有効なデータのみ取得するかどうか
     * @return array
     */
    public function get_shop(bool $enabled = true) : array {
        $shop_list = $this->get_master(
            self::MASTER_NAME_SHOP,
            Client_Neapi::PATH_SHOP_SEARCH,
            'shop_id,shop_name,shop_deleted_flag');

        if (empty($shop_list)) {
            return [];
        }

        // 整形して返す
        return $this->get_data_formating(
            $shop_list,
            'shop_id',
            'shop_name',
            $enabled,
            ['shop_deleted_flag' => '1']);
    }

    /**
     * 受注確認内容を取得する
     *
     * @param bool $enabled 有効なデータのみ取得するかどうか
     * @return array
     */
    public function get_confirm(bool $enabled = true) : array {
        $confirm_list = $this->get_master(
            self::MASTER_NAME_CONFIRM,
            Client_Neapi::PATH_RECEIVEORDER_CONFIRM_SEARCH,
            'confirm_id,confirm_name,confirm_display_order,confirm_valid_flag,confirm_deleted_flag');

        if (empty($confirm_list)) {
            return [];
        }

        // 並び替え
        usort($confirm_list, function ($a, $b){
            return $a['confirm_display_order'] > $b['confirm_display_order'];
        });

        // 整形して返す
        return $this->get_data_formating(
            $confirm_list,
            'confirm_id',
            'confirm_name',
            $enabled,
            ['confirm_deleted_flag' => '1']);
    }

    /**
     * 受注キャンセル区分を取得する
     *
     * @param bool $enabled 有効なデータのみ取得するかどうか(このマスタでは使用しない)
     * @return array
     */
    public function get_cancel(bool $enabled = true) : array {
        $cancel_list = $this->get_master(
            self::MASTER_NAME_CANCEL,
            Client_Neapi::PATH_SYSTEM_CANCELTYPE_INFO);

        if (empty($cancel_list)) {
            return [];
        }

        // 整形して返す
        return $this->get_data_formating(
            $cancel_list,
            'cancel_type_id',
            'cancel_type_name'
        );
    }

    /**
     * 発送方法区分を取得する
     *
     * @param bool $enabled 有効なデータのみ取得するかどうか(このマスタでは使用しない)
     * @return array
     */
    public function get_delivery(bool $enabled = true) : array {
        $delivery_list = $this->get_master(
            self::MASTER_NAME_DELIVERY,
            Client_Neapi::PATH_SYSTEM_DELIVERY_INFO);

        if (empty($delivery_list)) {
            return [];
        }

        // 整形して返す
        return $this->get_data_formating(
            $delivery_list,
            'delivery_id',
            'delivery_name'
        );
    }

    /**
     * 支払区分を取得する
     *
     * @param bool $enabled 有効なデータのみ取得するかどうか(このマスタでは使用しない)
     * @return array
     */
    public function get_payment(bool $enabled = true) : array {
        $payment_list = $this->get_master(
            self::MASTER_NAME_PAYMENT,
            Client_Neapi::PATH_SYSTEM_PAYMENTMETHOD_INFO);

        if (empty($payment_list)) {
            return [];
        }

        // 整形して返す
        return $this->get_data_formating(
            $payment_list,
            'payment_method_id',
            'payment_method_name'
        );
    }

    /**
     * 入金区分を取得する
     *
     * @param bool $enabled 有効なデータのみ取得するかどうか(このマスタでは使用しない)
     * @return array
     */
    public function get_deposit(bool $enabled = true) : array {
        $deposit_list = $this->get_master(
            self::MASTER_NAME_DEPOSIT,
            Client_Neapi::PATH_SYSTEM_DEPOSITTYPE_INFO);

        if (empty($deposit_list)) {
            return [];
        }

        // 整形して返す
        return $this->get_data_formating(
            $deposit_list,
            'deposit_type_id',
            'deposit_type_name'
        );
    }

    /**
     * 発送方法別項目タイプ
     *
     * @param bool $enabled 有効なデータのみ取得するかどうか
     * @param string $delivery_id 発送方法
     * @param string $name マスタ名（forwarding_agent_◯◯という名前である前提）
     * @return array
     */
    public function get_forwarding_agent(bool $enabled = true, string $delivery_id = null, string $name = null) : array {

        // メモリキャッシュがあれば取得する(並び替えにより遅くなっているため、ここでキャッシュを返す)
        $cache_name = __FUNCTION__ . '_';
        $cache_name .= ($enabled ? '1' : '0') . '_';
        $cache_name .= ($delivery_id ? $delivery_id : 'NULL') . '_';
        $cache_name .= $name ? $name : 'NULL';
        if (isset(self::$cache[$cache_name])) {
            return self::$cache[$cache_name];
        }

        $forwarding_agent_list = $this->get_master(
            self::MASTER_NAME_FORWARDINGAGENT,
            Client_Neapi::PATH_RECEIVEORDER_FORWARDINGAGENT_SEARCH,
            'forwarding_agent_id,forwarding_agent_type,forwarding_agent_type_id,forwarding_agent_type_name,forwarding_agent_display_order,forwarding_agent_deleted_flag');

        if (empty($forwarding_agent_list)) {
            return [];
        }

        // 並び替え(第一条件:forwarding_agent_display_orderの昇順、第二条件：forwarding_agent_type_idの昇順)
        foreach($forwarding_agent_list as $key => $value ) {
            $sort_array_display_display_order[$key] = $value["forwarding_agent_display_order"];
            $sort_array_display_type_id[$key] = $value["forwarding_agent_type_id"];
        }
        array_multisort($sort_array_display_display_order, $sort_array_display_type_id, $forwarding_agent_list);

        $type = null;
        if (!is_null($name)) {
            // 発送方法別項目タイプを取得する
            $type = self::get_forwarding_agent_type($name);
        }

        // 発送方法区分と発送方法別項目タイプで絞り込む
        $result = [];
        foreach ($forwarding_agent_list as $data) {
            if ((!is_null($delivery_id) && $data['forwarding_agent_id'] !== $delivery_id)) {
                // $delivery_idが指定された場合、そのid以外のforwarding_agent_idは無視する
                continue;
            }

            if (!is_null($type) && $data['forwarding_agent_type'] !== $type) {
                // $typeが指定された場合、その$type以外のforwarding_agent_typeは無視する
                continue;
            }

            $delete_flag = false;
            if ($data['forwarding_agent_deleted_flag'] === '1') {
                if ($enabled) {
                    // 無効なデータだったため次の要素へ
                    continue;
                }
                $delete_flag = true;
            }

            if (is_null($data['forwarding_agent_type_id']) || is_null($data['forwarding_agent_type_name'])) {
                // forwarding_agent_type_idもしくはforwarding_agent_type_nameがNULLの場合は無視する
                continue;
            }

            $id = $data['forwarding_agent_type_id'];
            $name = $data['forwarding_agent_type_name'];
            // データ量を減らすため不要なデータを削除
            unset($data['forwarding_agent_type_id']);
            unset($data['forwarding_agent_type_name']);
            $unique_key = $data['forwarding_agent_id'] . '_' . $id . '_' . $data['forwarding_agent_type'];
            $result[$unique_key] = new Domain_Value_Master($id, $name, $delete_flag, $data);
        }

        // メモリキャッシュする
        self::$cache[$cache_name] = $result;

        return $result;
    }

    /**
     * クレジット種類区分を取得する
     *
     * @param bool $enabled 有効なデータのみ取得するかどうか(このマスタでは使用しない)
     * @return array
     */
    public function get_credit(bool $enabled = true) : array {
        $credit_list = $this->get_master(
            self::MASTER_NAME_CREDIT,
            Client_Neapi::PATH_SYSTEM_CREDITTYPE_INFO);

        if (empty($credit_list)) {
            return [];
        }

        // 整形して返す
        return $this->get_data_formating(
            $credit_list,
            'credit_type_id',
            'credit_type_name'
        );
    }

    /**
     * クレジット承認区分を取得する
     *
     * @param bool $enabled 有効なデータのみ取得するかどうか(このマスタでは使用しない)
     * @return array
     */
    public function get_credit_approval(bool $enabled = true) : array {
        $approval_list = $this->get_master(
            self::MASTER_NAME_CREDITAPPROVAL,
            Client_Neapi::PATH_SYSTEM_CREDITAPPROVALTYPE_INFO);

        if (empty($approval_list)) {
            return [];
        }

        // 整形して返す
        return $this->get_data_formating(
            $approval_list,
            'credit_approval_type_id',
            'credit_approval_type_name'
        );
    }

    /**
     * 顧客区分を取得する
     *
     * @param bool $enabled 有効なデータのみ取得するかどうか(このマスタでは使用しない)
     * @return array
     */
    public function get_customer(bool $enabled = true) : array {
        $customer_list = $this->get_master(
            self::MASTER_NAME_CUSTOMER,
            Client_Neapi::PATH_SYSTEM_CUSTOMRTYPE_INFO);

        if (empty($customer_list)) {
            return [];
        }

        // 整形して返す
        return $this->get_data_formating(
            $customer_list,
            'customer_type_id',
            'customer_type_name'
        );
    }

    /**
     * ギフト区分を取得する
     * 取得するためのAPIはないため、そのまま連想配列を返す
     * データ的にはYES/NOでしかないのでBOOL型でもいいが、セレクトボックスで表示させたいのでマスタ型にしている（BOOL型の場合、チェックボックスになる）
     * もし、メイン機能側でこの値が変更になった場合は、ここも追随する必要がある
     *
     * @param bool $enabled 有効なデータのみ取得するかどうか(このマスタでは使用しない)
     * @return array
     */
    public function get_gift(bool $enabled = true) : array {
        return [
            '0' => new Domain_Value_Master('0', '無し'),
            '1' => new Domain_Value_Master('1', '有り'),
        ];
    }

    /**
     * 受注分類タグを取得する
     * 受注分類タグはAPIレスポンスをキャッシュをしない
     *
     * @param bool $enabled 有効なデータのみ取得するかどうか(このマスタでは使用しない)
     * @return array
     */
    public function get_tag(bool $enabled = true) : array {
        $tag_list = $this->get_master(
            self::MASTER_NAME_TAG,
            Client_Neapi::PATH_RECEIVEORDER_GROUPINGTAG_SEARCH,
            'grouping_tag_id,grouping_tag_name,grouping_tag_color,grouping_tag_str_color,grouping_tag_memo',
            false);

        if (empty($tag_list)) {
            return [];
        }

        // 整形して返す
        return $this->get_data_formating(
            $tag_list,
            'grouping_tag_id',
            'grouping_tag_name'
        );
    }

    /**
     * マスタを取得する
     * キャッシュにあればキャッシュを返し、なければAPIで取得する
     *
     * @param string $name マスタ名
     * @param string $path マスタを取得するためのAPIパス
     * @param string $fields APIで取得するフィールド
     * @param bool $is_cache キャッシュを使うかどうか
     *  true:キャッシュがあればキャッシュを返し、なければAPIリクエストをキャッシュする
     *  false:キャッシュがあってもキャッシュを返さないし、APIリクエストもキャッシュしない
     * @return array
     * @throws UnexpectedValueException
     */
    protected function get_master(string $name, string $path , string $fields = null, bool $is_cache = true) : array {

        $master_data = [];

        if ($is_cache) {
            try
            {
                // キャッシュがあればキャッシュを返す
                $company_id = $this->company_id;
                $cache_name = $company_id . self::CACHE_FILE_NAME_SEPARATOR . self::CACHE_KEYWORD . self::CACHE_FILE_NAME_SEPARATOR . $name;
                $master_data = Cache::get($cache_name);
            }
            catch (\CacheNotFoundException $e) {
                // 特に何もしない
            }
        }

        if (count($master_data) === 0 && !is_null($this->client_neapi)) {
            // キャッシュがない場合APIから取得する
            $api_params = [];
            if (!is_null($fields)) {
                $api_params['fields'] = $fields;
            }
            $response = $this->client_neapi->apiExecute($path, $api_params);
            if ($response['result'] === Client_Neapi::RESULT_SUCCESS) {
                if ($is_cache) {
                    // 成功した場合キャッシュする
                    Cache::set($cache_name, $response['data'], self::CACHE_EXPIRATION);
                }
                $master_data = $response['data'];
            } else {
                throw new UnexpectedValueException(__em('master_data_request_error'));
            }
        }

        return $master_data;
    }

    /**
     * データを
     * Domain_Value_Masterオブジェクトの配列の形で整形して返す
     *
     * @param array $list マスタデータ
     * @param string $id_key idとなるキー名
     * @param string $name_key nameとなるキー名
     * @param bool $enabled 有効なデータのみに絞り込むかどうか
     * @param array $disabled_flags 無効フラグの連想配列(複数渡すことでor条件となる)
     * @return array
     */
    protected function get_data_formating(array $list, string $id_key, string $name_key, bool $enabled = true, array $disabled_flags = []) : array {
        $result = [];
        foreach ($list as $data) {
            $delete_flag = false;
            if (!empty($disabled_flags)) {
                foreach ($disabled_flags as $disabled_flag_name => $disabled_flag_value) {
                    if ($data[$disabled_flag_name] === $disabled_flag_value) {
                        $delete_flag = true;
                        break;
                    }
                }
                if ($enabled && $delete_flag) {
                    // 無効なデータだったため次の要素へ
                    continue;
                }
            }

            if (is_null($data[$id_key]) || is_null($data[$name_key])) {
                // マスタデータのidもしくはnameがNULLの場合は無視する
                continue;
            }

            $id = $data[$id_key];
            $name = $data[$name_key];
            // データ量を減らすため不要なデータを削除
            unset($data[$id_key]);
            unset($data[$name_key]);
            $result[$id] = new Domain_Value_Master($id, $name, $delete_flag, $data);
        }
        return $result;
    }
}