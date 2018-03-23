<?php

class Utility_Masterdataprovider
{
    /**
     * データプロバイダー用オブジェクトのインスタンスを作成する
     *
     * @return Masterprovider
     */
    private function _create_provider_object() : Masterprovider {
        return new Masterprovider();
    }

    /**
     * [test_各マスタ取得処理が正しく動作すること]
     * 用のデータプロバイダ
     *
     * @return array
     */
    public function data_provider_master_get() {
        $data_provider = [];

        /**
         * 店舗マスタ
         */
        $api_response = [
            ['shop_id' => '1', 'shop_name' => 'name1', 'shop_deleted_flag' => '0'],
            ['shop_id' => '2', 'shop_name' => 'name2', 'shop_deleted_flag' => '1'],
            ['shop_id' => '3', 'shop_name' => 'name3', 'shop_deleted_flag' => '0'],
            ['shop_id' => '4', 'shop_name' => 'name4', 'shop_deleted_flag' => '0'],
        ];
        // 有効なデータのみ
        $data = $this->_create_provider_object();
        $data->name = Utility_Master::MASTER_NAME_SHOP;
        $data->path = Client_Neapi::PATH_SHOP_SEARCH;
        $data->fields = 'shop_id,shop_name,shop_deleted_flag';
        $data->disabled_flags = ['shop_deleted_flag' => '1'];
        $data->api_response = $api_response;
        $data->sort_response = $api_response;
        $data->id_key = 'shop_id';
        $data->name_key = 'shop_name';
        $result = [
            '1' => new Domain_Value_Master('1','name1', false),
            '3' => new Domain_Value_Master('3','name3', false),
            '4' => new Domain_Value_Master('4','name4', false),
        ];
        $data_provider[] = [$data, $result];

        // すべてのデータ
        $data = $this->_create_provider_object();
        $data->name = Utility_Master::MASTER_NAME_SHOP;
        $data->path = Client_Neapi::PATH_SHOP_SEARCH;
        $data->fields = 'shop_id,shop_name,shop_deleted_flag';
        $data->disabled_flags = ['shop_deleted_flag' => '1'];
        $data->api_response = $api_response;
        $data->sort_response = $api_response;
        $data->id_key = 'shop_id';
        $data->name_key = 'shop_name';
        $data->enabled = false;
        $result = [
            '1' => new Domain_Value_Master('1','name1', false),
            '2' => new Domain_Value_Master('2','name2', true),
            '3' => new Domain_Value_Master('3','name3', false),
            '4' => new Domain_Value_Master('4','name4', false),
        ];
        $data_provider[] = [$data, $result];

        // _get_masterの戻り値が空配列だった
        $data = $this->_create_provider_object();
        $data->name = Utility_Master::MASTER_NAME_SHOP;
        $data->path = Client_Neapi::PATH_SHOP_SEARCH;
        $data->fields = 'shop_id,shop_name,shop_deleted_flag';
        $data->disabled_flags = ['shop_deleted_flag' => '0'];
        $data_provider[] = [$data, []];

        /**
         * 受注確認内容
         */
        $api_response = [
            ['confirm_id' => '1', 'confirm_name' => 'name1', 'confirm_display_order' => '2', 'confirm_valid_flag' => '1', 'confirm_deleted_flag' => '0'],
            ['confirm_id' => '2', 'confirm_name' => 'name2', 'confirm_display_order' => '1', 'confirm_valid_flag' => '1', 'confirm_deleted_flag' => '1'],
            ['confirm_id' => '3', 'confirm_name' => 'name3', 'confirm_display_order' => '4', 'confirm_valid_flag' => '0', 'confirm_deleted_flag' => '0'],
            ['confirm_id' => '4', 'confirm_name' => 'name4', 'confirm_display_order' => '5', 'confirm_valid_flag' => '1', 'confirm_deleted_flag' => '0'],
            ['confirm_id' => '5', 'confirm_name' => 'name5', 'confirm_display_order' => '3', 'confirm_valid_flag' => '0', 'confirm_deleted_flag' => '1'],
        ];
        $sort_response = [
            ['confirm_id' => '2', 'confirm_name' => 'name2', 'confirm_display_order' => '1', 'confirm_valid_flag' => '1', 'confirm_deleted_flag' => '1'],
            ['confirm_id' => '1', 'confirm_name' => 'name1', 'confirm_display_order' => '2', 'confirm_valid_flag' => '1', 'confirm_deleted_flag' => '0'],
            ['confirm_id' => '5', 'confirm_name' => 'name5', 'confirm_display_order' => '3', 'confirm_valid_flag' => '0', 'confirm_deleted_flag' => '1'],
            ['confirm_id' => '3', 'confirm_name' => 'name3', 'confirm_display_order' => '4', 'confirm_valid_flag' => '0', 'confirm_deleted_flag' => '0'],
            ['confirm_id' => '4', 'confirm_name' => 'name4', 'confirm_display_order' => '5', 'confirm_valid_flag' => '1', 'confirm_deleted_flag' => '0'],
        ];
        // 有効なデータのみ
        $data = $this->_create_provider_object();
        $data->name = Utility_Master::MASTER_NAME_CONFIRM;
        $data->path = Client_Neapi::PATH_RECEIVEORDER_CONFIRM_SEARCH;
        $data->fields = 'confirm_id,confirm_name,confirm_display_order,confirm_valid_flag,confirm_deleted_flag';
        $data->disabled_flags = ['confirm_deleted_flag' => '1'];
        $data->api_response = $api_response;
        $data->sort_response = $sort_response;
        $data->id_key = 'confirm_id';
        $data->name_key = 'confirm_name';
        $result = [
            '1' => new Domain_Value_Master('1','name1', false),
            '4' => new Domain_Value_Master('4','name4', false),
        ];
        $data_provider[] = [$data, $result];

        // すべてのデータ
        $data = $this->_create_provider_object();
        $data->name = Utility_Master::MASTER_NAME_CONFIRM;
        $data->path = Client_Neapi::PATH_RECEIVEORDER_CONFIRM_SEARCH;
        $data->fields = 'confirm_id,confirm_name,confirm_display_order,confirm_valid_flag,confirm_deleted_flag';
        $data->disabled_flags = ['confirm_deleted_flag' => '1'];
        $data->api_response = $api_response;
        $data->sort_response = $sort_response;
        $data->id_key = 'confirm_id';
        $data->name_key = 'confirm_name';
        $data->enabled = false;
        $result = [
            '2' => new Domain_Value_Master('2','name2', true),
            '1' => new Domain_Value_Master('1','name1', false),
            '5' => new Domain_Value_Master('5','name5', true),
            '3' => new Domain_Value_Master('3','name3', true),
            '4' => new Domain_Value_Master('4','name4', false),
        ];
        $data_provider[] = [$data, $result];

        // _get_masterの戻り値が空配列だった
        $data = $this->_create_provider_object();
        $data->name = Utility_Master::MASTER_NAME_CONFIRM;
        $data->path = Client_Neapi::PATH_RECEIVEORDER_CONFIRM_SEARCH;
        $data->fields = 'confirm_id,confirm_name,confirm_display_order,confirm_valid_flag,confirm_deleted_flag';
        $data->disabled_flags = ['confirm_deleted_flag' => '1'];
        $data_provider[] = [$data, []];

        /**
         * 受注キャンセル区分
         */
        $api_response = [
            ['cancel_type_id' => '1', 'cancel_type_name' => 'name1'],
            ['cancel_type_id' => '2', 'cancel_type_name' => 'name2'],
            ['cancel_type_id' => '3', 'cancel_type_name' => 'name3'],
        ];
        // 有効なデータのみ
        $data = $this->_create_provider_object();
        $data->name = Utility_Master::MASTER_NAME_CANCEL;
        $data->path = Client_Neapi::PATH_SYSTEM_CANCELTYPE_INFO;
        $data->api_response = $api_response;
        $data->sort_response = $api_response;
        $result = [
            '1' => new Domain_Value_Master('1','name1'),
            '2' => new Domain_Value_Master('2','name2'),
            '3' => new Domain_Value_Master('3','name3'),
        ];
        $data->id_key = 'cancel_type_id';
        $data->name_key = 'cancel_type_name';
        $data_provider[] = [$data, $result];

        // _get_masterの戻り値が空配列だった
        $data = $this->_create_provider_object();
        $data->name = Utility_Master::MASTER_NAME_CANCEL;
        $data->path = Client_Neapi::PATH_SYSTEM_CANCELTYPE_INFO;
        $data_provider[] = [$data, []];

        /**
         * 発送方法区分
         */
        $api_response = [
            ['delivery_id' => '1', 'delivery_name' => 'name1'],
            ['delivery_id' => '2', 'delivery_name' => 'name2'],
            ['delivery_id' => '3', 'delivery_name' => 'name3'],
        ];
        // 有効なデータのみ
        $data = $this->_create_provider_object();
        $data->name = Utility_Master::MASTER_NAME_DELIVERY;
        $data->path = Client_Neapi::PATH_SYSTEM_DELIVERY_INFO;
        $data->api_response = $api_response;
        $data->sort_response = $api_response;
        $result = [
            '1' => new Domain_Value_Master('1','name1'),
            '2' => new Domain_Value_Master('2','name2'),
            '3' => new Domain_Value_Master('3','name3'),
        ];
        $data->id_key = 'delivery_id';
        $data->name_key = 'delivery_name';
        $data_provider[] = [$data, $result];

        // _get_masterの戻り値が空配列だった
        $data = $this->_create_provider_object();
        $data->name = Utility_Master::MASTER_NAME_DELIVERY;
        $data->path = Client_Neapi::PATH_SYSTEM_DELIVERY_INFO;
        $data_provider[] = [$data, []];

        /**
         * 発送方法区分
         */
        $api_response = [
            ['payment_method_id' => '1', 'payment_method_name' => 'name1'],
            ['payment_method_id' => '2', 'payment_method_name' => 'name2'],
            ['payment_method_id' => '3', 'payment_method_name' => 'name3'],
        ];
        // 有効なデータのみ
        $data = $this->_create_provider_object();
        $data->name = Utility_Master::MASTER_NAME_PAYMENT;
        $data->path = Client_Neapi::PATH_SYSTEM_PAYMENTMETHOD_INFO;
        $data->api_response = $api_response;
        $data->sort_response = $api_response;
        $result = [
            '1' => new Domain_Value_Master('1','name1'),
            '2' => new Domain_Value_Master('2','name2'),
            '3' => new Domain_Value_Master('3','name3'),
        ];
        $data->id_key = 'payment_method_id';
        $data->name_key = 'payment_method_name';
        $data_provider[] = [$data, $result];

        // _get_masterの戻り値が空配列だった
        $data = $this->_create_provider_object();
        $data->name = Utility_Master::MASTER_NAME_PAYMENT;
        $data->path = Client_Neapi::PATH_SYSTEM_PAYMENTMETHOD_INFO;
        $data_provider[] = [$data, []];

        /**
         * 入金区分
         */
        $api_response = [
            ['deposit_type_id' => '1', 'deposit_type_name' => 'name1'],
            ['deposit_type_id' => '2', 'deposit_type_name' => 'name2'],
            ['deposit_type_id' => '3', 'deposit_type_name' => 'name3'],
        ];
        // 有効なデータのみ
        $data = $this->_create_provider_object();
        $data->name = Utility_Master::MASTER_NAME_DEPOSIT;
        $data->path = Client_Neapi::PATH_SYSTEM_DEPOSITTYPE_INFO;
        $data->api_response = $api_response;
        $data->sort_response = $api_response;
        $result = [
            '1' => new Domain_Value_Master('1','name1'),
            '2' => new Domain_Value_Master('2','name2'),
            '3' => new Domain_Value_Master('3','name3'),
        ];
        $data->id_key = 'deposit_type_id';
        $data->name_key = 'deposit_type_name';
        $data_provider[] = [$data, $result];

        // _get_masterの戻り値が空配列だった
        $data = $this->_create_provider_object();
        $data->name = Utility_Master::MASTER_NAME_DEPOSIT;
        $data->path = Client_Neapi::PATH_SYSTEM_DEPOSITTYPE_INFO;
        $data_provider[] = [$data, []];

        /**
         * クレジット種類区分
         */
        $api_response = [
            ['credit_type_id' => '1', 'credit_type_name' => 'name1'],
            ['credit_type_id' => '2', 'credit_type_name' => 'name2'],
            ['credit_type_id' => '3', 'credit_type_name' => 'name3'],
        ];
        // 有効なデータのみ
        $data = $this->_create_provider_object();
        $data->name = Utility_Master::MASTER_NAME_CREDIT;
        $data->path = Client_Neapi::PATH_SYSTEM_CREDITTYPE_INFO;
        $data->api_response = $api_response;
        $data->sort_response = $api_response;
        $result = [
            '1' => new Domain_Value_Master('1','name1'),
            '2' => new Domain_Value_Master('2','name2'),
            '3' => new Domain_Value_Master('3','name3'),
        ];
        $data->id_key = 'credit_type_id';
        $data->name_key = 'credit_type_name';
        $data_provider[] = [$data, $result];

        // _get_masterの戻り値が空配列だった
        $data = $this->_create_provider_object();
        $data->name = Utility_Master::MASTER_NAME_CREDIT;
        $data->path = Client_Neapi::PATH_SYSTEM_CREDITTYPE_INFO;
        $data_provider[] = [$data, []];

        /**
         * クレジット承認区分
         */
        $api_response = [
            ['credit_approval_type_id' => '1', 'credit_approval_type_name' => 'name1'],
            ['credit_approval_type_id' => '2', 'credit_approval_type_name' => 'name2'],
            ['credit_approval_type_id' => '3', 'credit_approval_type_name' => 'name3'],
        ];
        // 有効なデータのみ
        $data = $this->_create_provider_object();
        $data->name = Utility_Master::MASTER_NAME_CREDITAPPROVAL;
        $data->path = Client_Neapi::PATH_SYSTEM_CREDITAPPROVALTYPE_INFO;
        $data->api_response = $api_response;
        $data->sort_response = $api_response;
        $result = [
            '1' => new Domain_Value_Master('1','name1'),
            '2' => new Domain_Value_Master('2','name2'),
            '3' => new Domain_Value_Master('3','name3'),
        ];
        $data->id_key = 'credit_approval_type_id';
        $data->name_key = 'credit_approval_type_name';
        $data_provider[] = [$data, $result];

        // _get_masterの戻り値が空配列だった
        $data = $this->_create_provider_object();
        $data->name = Utility_Master::MASTER_NAME_CREDITAPPROVAL;
        $data->path = Client_Neapi::PATH_SYSTEM_CREDITAPPROVALTYPE_INFO;
        $data_provider[] = [$data, []];

        /**
         * 顧客区分
         */
        $api_response = [
            ['customer_type_id' => '1', 'customer_type_name' => 'name1'],
            ['customer_type_id' => '2', 'customer_type_name' => 'name2'],
            ['customer_type_id' => '3', 'customer_type_name' => 'name3'],
        ];
        // 有効なデータのみ
        $data = $this->_create_provider_object();
        $data->name = Utility_Master::MASTER_NAME_CUSTOMER;
        $data->path = Client_Neapi::PATH_SYSTEM_CUSTOMRTYPE_INFO;
        $data->api_response = $api_response;
        $data->sort_response = $api_response;
        $result = [
            '1' => new Domain_Value_Master('1','name1'),
            '2' => new Domain_Value_Master('2','name2'),
            '3' => new Domain_Value_Master('3','name3'),
        ];
        $data->id_key = 'customer_type_id';
        $data->name_key = 'customer_type_name';
        $data_provider[] = [$data, $result];

        // _get_masterの戻り値が空配列だった
        $data = $this->_create_provider_object();
        $data->name = Utility_Master::MASTER_NAME_CUSTOMER;
        $data->path = Client_Neapi::PATH_SYSTEM_CUSTOMRTYPE_INFO;
        $data_provider[] = [$data, []];

        return $data_provider;
    }

    /**
     * 発送方法別項目タイプをユニークにするキーを取得する
     *
     * @param array $data APIレスポンスと同じ連想配列
     * @return string ユニークなキー
     */
    public static function get_forwarding_agent_unique_key(array $data) : string {
        return $data['forwarding_agent_id'] . '_' . $data['forwarding_agent_type_id'] . '_' . $data['forwarding_agent_type'];
    }

    /**
     * [test_get_forwarding_agent_発送方法別項目タイプマスタ取得処理が正しく動作すること]
     * 用のデータプロバイダ
     *
     * @return array
     */
    public function data_provider_get_forwarding_agent() {
        $data_provider = [];

        $api_response = [
            ["forwarding_agent_id" => "1", "forwarding_agent_type" => "binsyu_kbn", "forwarding_agent_type_id" => "1", "forwarding_agent_type_name" => "name1", "forwarding_agent_display_order" => "5", "forwarding_agent_deleted_flag" => "0"],
            ["forwarding_agent_id" => "1", "forwarding_agent_type" => "binsyu_kbn", "forwarding_agent_type_id" => "2", "forwarding_agent_type_name" => "name2", "forwarding_agent_display_order" => "1", "forwarding_agent_deleted_flag" => "0"],
            ["forwarding_agent_id" => "1", "forwarding_agent_type" => "binsyu_kbn", "forwarding_agent_type_id" => "3", "forwarding_agent_type_name" => "name3", "forwarding_agent_display_order" => "3", "forwarding_agent_deleted_flag" => "1"],
            ["forwarding_agent_id" => "1", "forwarding_agent_type" => "eigyosyo_dome_kbn", "forwarding_agent_type_id" => "4", "forwarding_agent_type_name" => "name4", "forwarding_agent_display_order" => "2", "forwarding_agent_deleted_flag" => "1"],
            ["forwarding_agent_id" => "2", "forwarding_agent_type" => "binsyu_kbn", "forwarding_agent_type_id" => "5", "forwarding_agent_type_name" => "name5", "forwarding_agent_display_order" => "4", "forwarding_agent_deleted_flag" => "1"],
        ];
        // 有効なデータのみ
        $data = $this->_create_provider_object();
        $data->name = Utility_Master::MASTER_NAME_FORWARDINGAGENT;
        $data->path = Client_Neapi::PATH_RECEIVEORDER_FORWARDINGAGENT_SEARCH;
        $data->fields = 'forwarding_agent_id,forwarding_agent_type,forwarding_agent_type_id,forwarding_agent_type_name,forwarding_agent_display_order,forwarding_agent_deleted_flag';
        $data->api_response = $api_response;
        $result = [
            self::get_forwarding_agent_unique_key($api_response[1]) => new Domain_Value_Master('2', 'name2', false, ["forwarding_agent_id" => "1", "forwarding_agent_type" => "binsyu_kbn", "forwarding_agent_display_order" => "1", "forwarding_agent_deleted_flag" => "0"]),
            self::get_forwarding_agent_unique_key($api_response[0]) => new Domain_Value_Master('1', 'name1', false, ["forwarding_agent_id" => "1", "forwarding_agent_type" => "binsyu_kbn", "forwarding_agent_display_order" => "5", "forwarding_agent_deleted_flag" => "0"]),
        ];
        $data_provider[] = [$data, 1, 'forwarding_agent_binsyu', $result];

        // 全てのデータ
        $data = $this->_create_provider_object();
        $data->name = Utility_Master::MASTER_NAME_FORWARDINGAGENT;
        $data->path = Client_Neapi::PATH_RECEIVEORDER_FORWARDINGAGENT_SEARCH;
        $data->fields = 'forwarding_agent_id,forwarding_agent_type,forwarding_agent_type_id,forwarding_agent_type_name,forwarding_agent_display_order,forwarding_agent_deleted_flag';
        $data->api_response = $api_response;
        $data->enabled = false;
        $result = [
            self::get_forwarding_agent_unique_key($api_response[1]) => new Domain_Value_Master('2', 'name2', false, ["forwarding_agent_id" => "1", "forwarding_agent_type" => "binsyu_kbn", "forwarding_agent_display_order" => "1", "forwarding_agent_deleted_flag" => "0"]),
            self::get_forwarding_agent_unique_key($api_response[2]) => new Domain_Value_Master('3', 'name3', true, ["forwarding_agent_id" => "1", "forwarding_agent_type" => "binsyu_kbn", "forwarding_agent_display_order" => "3", "forwarding_agent_deleted_flag" => "1"]),
            self::get_forwarding_agent_unique_key($api_response[0]) => new Domain_Value_Master('1', 'name1', false, ["forwarding_agent_id" => "1", "forwarding_agent_type" => "binsyu_kbn", "forwarding_agent_display_order" => "5", "forwarding_agent_deleted_flag" => "0"]),
        ];
        $data_provider[] = [$data, 1, 'forwarding_agent_binsyu', $result];

        // nameを指定しなかった場合
        $data = $this->_create_provider_object();
        $data->name = Utility_Master::MASTER_NAME_FORWARDINGAGENT;
        $data->path = Client_Neapi::PATH_RECEIVEORDER_FORWARDINGAGENT_SEARCH;
        $data->fields = 'forwarding_agent_id,forwarding_agent_type,forwarding_agent_type_id,forwarding_agent_type_name,forwarding_agent_display_order,forwarding_agent_deleted_flag';
        $data->api_response = $api_response;
        $data->enabled = false;
        $result = [
            self::get_forwarding_agent_unique_key($api_response[1]) => new Domain_Value_Master('2', 'name2', false, ["forwarding_agent_id" => "1", "forwarding_agent_type" => "binsyu_kbn", "forwarding_agent_display_order" => "1", "forwarding_agent_deleted_flag" => "0"]),
            self::get_forwarding_agent_unique_key($api_response[3]) => new Domain_Value_Master('4', 'name4', true, ["forwarding_agent_id" => "1", "forwarding_agent_type" => "eigyosyo_dome_kbn", "forwarding_agent_display_order" => "2", "forwarding_agent_deleted_flag" => "1"]),
            self::get_forwarding_agent_unique_key($api_response[2]) => new Domain_Value_Master('3', 'name3', true, ["forwarding_agent_id" => "1", "forwarding_agent_type" => "binsyu_kbn", "forwarding_agent_display_order" => "3", "forwarding_agent_deleted_flag" => "1"]),
            self::get_forwarding_agent_unique_key($api_response[0]) => new Domain_Value_Master('1', 'name1', false, ["forwarding_agent_id" => "1", "forwarding_agent_type" => "binsyu_kbn", "forwarding_agent_display_order" => "5", "forwarding_agent_deleted_flag" => "0"]),
        ];
        $data_provider[] = [$data, 1, null, $result];

        // 存在しないnameだった場合場合
        $data = $this->_create_provider_object();
        $data->name = Utility_Master::MASTER_NAME_FORWARDINGAGENT;
        $data->path = Client_Neapi::PATH_RECEIVEORDER_FORWARDINGAGENT_SEARCH;
        $data->fields = 'forwarding_agent_id,forwarding_agent_type,forwarding_agent_type_id,forwarding_agent_type_name,forwarding_agent_display_order,forwarding_agent_deleted_flag';
        $data->api_response = $api_response;
        $data_provider[] = [$data, 1, 'none', []];

        // _get_masterの戻り値が空配列だった
        $data = $this->_create_provider_object();
        $data->name = Utility_Master::MASTER_NAME_FORWARDINGAGENT;
        $data->path = Client_Neapi::PATH_RECEIVEORDER_FORWARDINGAGENT_SEARCH;
        $data->fields = 'forwarding_agent_id,forwarding_agent_type,forwarding_agent_type_id,forwarding_agent_type_name,forwarding_agent_display_order,forwarding_agent_deleted_flag';
        $data->api_response = [];
        $data_provider[] = [$data, 1, null, []];

        return $data_provider;
    }
}

/**
 * データプロバイダーのオブジェクトクラス
 *
 * Class Masterprovider
 */
class Masterprovider {
    public $name;
    public $path;
    public $fields = null;
    public $disabled_flags = [];
    public $api_response = [];
    public $sort_response = [];
    public $id_key = null;
    public $name_key = null;
    public $enabled = true;
}