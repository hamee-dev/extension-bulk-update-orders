<?php
class Model_Bulkupdatesettingprovider
{
    public function data_provider_get_setting()
    {
        return [
            [
                // 更新する項目が複数ある場合
                'company_id' => Test_Model_bulkupdatesetting::DUMMY_COMPANY_ID1,
                'bulk_update_setting_id' => Test_Model_bulkupdatesetting::DUMMY_BULK_UPDATE_SETTING_ID1,
                'column_order' => [
                    Test_Model_bulkupdatesetting::DUMMY_BULK_UPDATE_COLUMN_ID1,
                    Test_Model_bulkupdatesetting::DUMMY_BULK_UPDATE_COLUMN_ID2,
                    Test_Model_bulkupdatesetting::DUMMY_BULK_UPDATE_COLUMN_ID3],
            ],
            [
                // 更新する項目が１つの場合
                'company_id' => Test_Model_bulkupdatesetting::DUMMY_COMPANY_ID1,
                'bulk_update_setting_id' => Test_Model_bulkupdatesetting::DUMMY_BULK_UPDATE_SETTING_ID2,
                'column_order' => [Test_Model_bulkupdatesetting::DUMMY_BULK_UPDATE_COLUMN_ID5],
            ],
        ];
    }

    public function data_provider_get_settings_for_top()
    {
        return [
            [
                'company_id' => Test_Model_bulkupdatesetting::DUMMY_COMPANY_ID1,
                'result_count' => 3,
                'setting_order' => [
                    Test_Model_bulkupdatesetting::DUMMY_BULK_UPDATE_SETTING_ID1,
                    Test_Model_bulkupdatesetting::DUMMY_BULK_UPDATE_SETTING_ID5,
                    Test_Model_bulkupdatesetting::DUMMY_BULK_UPDATE_SETTING_ID2],
                'column_order' => [
                    Test_Model_bulkupdatesetting::DUMMY_BULK_UPDATE_COLUMN_ID1,
                    Test_Model_bulkupdatesetting::DUMMY_BULK_UPDATE_COLUMN_ID2,
                    Test_Model_bulkupdatesetting::DUMMY_BULK_UPDATE_COLUMN_ID3],
            ],
            [
                'company_id' => Test_Model_bulkupdatesetting::DUMMY_COMPANY_ID2,
                'result_count' => 1,
                'setting_order' => [Test_Model_bulkupdatesetting::DUMMY_BULK_UPDATE_SETTING_ID4],
                'column_order' => [Test_Model_bulkupdatesetting::DUMMY_BULK_UPDATE_COLUMN_ID4],
            ],
        ];
    }

    public function data_provider_get_validation_params_by_bulk_update_setting_id() {
        return [
            [
                'company_id' => Test_Model_bulkupdatesetting::DUMMY_COMPANY_ID1,
                'bulk_update_setting_id' => Test_Model_bulkupdatesetting::DUMMY_BULK_UPDATE_SETTING_ID1,
                'expected' => [
                    BULK_UPDATE_SETTING_ID => (string)Test_Model_bulkupdatesetting::DUMMY_BULK_UPDATE_SETTING_ID1,
                    'name' => 'TEST1',
                    Domain_Model_Updatesetting::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME => '0',
                    Domain_Model_Updatesetting::UPDATE_YAHOO_CANCEL_ELEMENT_NAME => '0',
                    Domain_Model_Updatesetting::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME => '0',
                    Domain_Model_Updatesetting::REFLECT_ORDER_AMOUNT => '0',
                    Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => ['5', '1', '2'],
                    Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => ['1' => Model_Updatemethod::OVERWRITE, '2' => Model_Updatemethod::OVERWRITE, '5' => Model_Updatemethod::ADDWRITE],
                    Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME => ['1' => 'TEST_VALUE2'],
                    Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME => ['2' => 'TEST_VALUE3', '5' => 'TEST_VALUE1']
                    ]
            ],
            [
                'company_id' => Test_Model_bulkupdatesetting::DUMMY_COMPANY_ID1,
                'bulk_update_setting_id' => Test_Model_bulkupdatesetting::DUMMY_BULK_UPDATE_SETTING_ID2,
                'expected' => [
                    BULK_UPDATE_SETTING_ID => (string)Test_Model_bulkupdatesetting::DUMMY_BULK_UPDATE_SETTING_ID2,
                    'name' => 'TEST2',
                    Domain_Model_Updatesetting::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME => '0',
                    Domain_Model_Updatesetting::UPDATE_YAHOO_CANCEL_ELEMENT_NAME => '0',
                    Domain_Model_Updatesetting::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME => '1',
                    Domain_Model_Updatesetting::REFLECT_ORDER_AMOUNT => '0',
                    Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => ['7'],
                    Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => ['7' => Model_Updatemethod::OVERWRITE],
                    Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME => ['7' => 'TEST_VALUE5'],
                    Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME => []
                ]
            ],
            [
                'company_id' => Test_Model_bulkupdatesetting::DUMMY_COMPANY_ID2,
                'bulk_update_setting_id' => Test_Model_bulkupdatesetting::DUMMY_BULK_UPDATE_SETTING_ID4,
                'expected' => [
                    BULK_UPDATE_SETTING_ID => (string)Test_Model_bulkupdatesetting::DUMMY_BULK_UPDATE_SETTING_ID4,
                    'name' => 'TEST4',
                    Domain_Model_Updatesetting::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME => '1',
                    Domain_Model_Updatesetting::UPDATE_YAHOO_CANCEL_ELEMENT_NAME => '1',
                    Domain_Model_Updatesetting::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME => '1',
                    Domain_Model_Updatesetting::REFLECT_ORDER_AMOUNT => '1',
                    Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => ['3'],
                    Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => ['3' => Model_Updatemethod::OVERWRITE],
                    Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME => [],
                    Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME => ['3' => 'TEST_VALUE4']
                ]
            ],
        ];
    }

    public function data_provider_is_selected_option() {
        return [
            [
                'allow_update_shipment_confirmed' => '0',
                'allow_update_yahoo_cancel' => '0',
                'allow_optimistic_lock_update_retry' => '0',
                'allow_reflect_order_amount' => '0',
                'is_selected_option' => false,
            ],
            [
                'allow_update_shipment_confirmed' => '1',
                'allow_update_yahoo_cancel' => '0',
                'allow_optimistic_lock_update_retry' => '0',
                'allow_reflect_order_amount' => '0',
                'is_selected_option' => true,
            ],
            [
                'allow_update_shipment_confirmed' => '0',
                'allow_update_yahoo_cancel' => '1',
                'allow_optimistic_lock_update_retry' => '0',
                'allow_reflect_order_amount' => '0',
                'is_selected_option' => true,
            ],
            [
                'allow_update_shipment_confirmed' => '0',
                'allow_update_yahoo_cancel' => '0',
                'allow_optimistic_lock_update_retry' => '1',
                'allow_reflect_order_amount' => '0',
                'is_selected_option' => true,
            ],
            [
                'allow_update_shipment_confirmed' => '0',
                'allow_update_yahoo_cancel' => '0',
                'allow_optimistic_lock_update_retry' => '0',
                'allow_reflect_order_amount' => '1',
                'is_selected_option' => true,
            ],
            [
                'allow_update_shipment_confirmed' => '1',
                'allow_update_yahoo_cancel' => '1',
                'allow_optimistic_lock_update_retry' => '1',
                'allow_reflect_order_amount' => '1',
                'is_selected_option' => true,
            ],
        ];
    }

}