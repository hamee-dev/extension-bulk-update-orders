<?php
class Test_Domain_Model_Preview extends Testbase
{
    protected $dataset_filenames = [
        'domain/model/updatesetting.yml'
    ];

    protected $fetch_init_yaml = false;

    public function test_get_bulk_update_columns_渡したパラメータによって意図した形の配列を返すこと() {
        // 手数料に対しての更新設定を組んでいた場合、確認チェック・確認内容・備考欄・総合計などにも影響があるのでそれらの項目も合わせて返していること
        $update_target_orders = [
            '1' => [
                'receive_order_gruoping_tag'       => '[dummy_tag][【済】テスト実行1]',
                'receive_order_shop_id'            => 'TEST_VALUE1',
                'receive_order_shop_cut_form_id'   => 'TEST_VALUE2',
                'receive_order_charge_amount'      => '1',
                'receive_order_confirm_ids'        => 'AD',
                'receive_order_confirm_check_id'   => '0',
                'receive_order_note'               => '手数料,総合計が更新されています',
                'receive_order_total_amount'       => '2',
                'receive_order_id'                 => '1',
                'receive_order_last_modified_date' => '2018-05-11 13:06:00',
            ],
            '2' => [
                'receive_order_gruoping_tag'       => '[dummy_tag][【済】テスト実行1]',
                'receive_order_shop_id'            => 'TEST_VALUE1',
                'receive_order_shop_cut_form_id'   => 'TEST_VALUE2',
                'receive_order_charge_amount'      => '1',
                'receive_order_confirm_ids'        => 'AD',
                'receive_order_confirm_check_id'   => '0',
                'receive_order_note'               => '手数料,総合計が更新されています',
                'receive_order_total_amount'       => '2',
                'receive_order_id'                 => '2',
                'receive_order_last_modified_date' => '2018-05-11 13:06:00',
            ],
        ];
        $excluded_id_and_reason = [
            '1' => ['receive_order_id' => '1', 'excluded_reason' => '除外理由'],
        ];
        $domain_value_convert_result = new Domain_Value_Convertresult($update_target_orders, $excluded_id_and_reason);

        // 手数料に対して1足す更新設定
        $setting = Model_Bulkupdatesetting::findOne(['id' => self::DUMMY_BULK_UPDATE_SETTING_ID1]);
        $bulk_update_column = new Model_Bulkupdatecolumn();
        $bulk_update_column->bulk_update_setting_id = self::DUMMY_BULK_UPDATE_SETTING_ID1;
        $bulk_update_column->receive_order_column_id = 12;
        $bulk_update_column->update_method_id = Model_Updatemethod::ADDITION;
        $bulk_update_column->update_value = 1;;
        $setting->bulk_update_columns[] = $bulk_update_column;
        $result = Domain_Model_Preview::get_bulk_update_columns($domain_value_convert_result, $setting);
        $result_column_names = [];
        foreach($result as $bulk_update_column){
            $result_column_names[] = $bulk_update_column->receive_order_column->physical_name;
        }

        // 元の更新設定＋システムが更新した項目に対する設定を返すこと
        // 受注確認内容(receive_order_confirm_ids)は出ていないこと
        $expect = [
            'receive_order_shop_id',
            'receive_order_shop_cut_form_id',
            'receive_order_charge_amount',
            'receive_order_confirm_check_id',
            'receive_order_gruoping_tag',
            'receive_order_note',
            'receive_order_total_amount',
        ];
        $this->assertSame($expect, $result_column_names);
    }

    public function test_get_display_value_渡したパラメータによって意図した形の配列を返すこと() {
        // マスタデータを使うのでキャッシュを配置
        self::_create_master_data_cache('master_shop.cache');

        $update_target_orders = [
            '1' => [
                'receive_order_gruoping_tag'       => '[dummy_tag][【済】テスト実行1]',
                'receive_order_shop_id'            => '2',
                'receive_order_shop_cut_form_id'   => 'TEST_VALUE2',
                'receive_order_id'                 => '1',
                'receive_order_last_modified_date' => '2018-05-11 13:06:00',
            ],
            '2' => [
                'receive_order_gruoping_tag'       => '[dummy_tag][【済】テスト実行1]',
                'receive_order_shop_id'            => '2',
                'receive_order_shop_cut_form_id'   => 'TEST_VALUE2',
                'receive_order_id'                 => '2',
                'receive_order_last_modified_date' => '2018-05-11 13:06:00',
            ],
        ];
        $excluded_id_and_reason = [
            '1' => ['receive_order_id' => '1', 'excluded_reason' => '除外理由'],
        ];
        $domain_value_convert_result = new Domain_Value_Convertresult($update_target_orders, $excluded_id_and_reason);
        $receive_order_list = [
            ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
            ['receive_order_id' => '2', 'receive_order_shop_id' => '3', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
        ];
        $setting = Model_Bulkupdatesetting::findOne(['id' => self::DUMMY_BULK_UPDATE_SETTING_ID1]);
        $bulk_update_columns = $setting->bulk_update_columns;
        $master = new Utility_Master(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1);
        $result = Domain_Model_Preview::get_display_value($domain_value_convert_result, $receive_order_list, $bulk_update_columns, $master);
        $expect = [
            '1' => [
                'excluded_reason' => '除外理由',
                'receive_order_shop_id' => [
                    'before_value' => '1 : 店舗1',
                    'after_value'  => '2 : 店舗2',
                ],
                'receive_order_shop_cut_form_id' => [
                    'before_value' => '1',
                    'after_value'  => 'TEST_VALUE2',
                ],
            ],
            '2' => [
                'excluded_reason' => '',
                'receive_order_shop_id' => [
                    'before_value' => '3 : 店舗3',
                    'after_value'  => '2 : 店舗2',
                ],
                'receive_order_shop_cut_form_id' => [
                    'before_value' => '2',
                    'after_value'  => 'TEST_VALUE2',
                ],
            ],
        ];
        $this->assertSame($expect, $result);
    }
}