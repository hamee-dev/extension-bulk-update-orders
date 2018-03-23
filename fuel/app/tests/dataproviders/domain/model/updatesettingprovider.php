<?php
class Domain_model_Updatesettingprovider{
    public function data_provider_get_setting_for_validation_error() {
        return [
            [
                'company_id' => Test_Domain_Model_Updatesetting::DUMMY_COMPANY_ID1,
                'user_id' => Test_Domain_Model_Updatesetting::DUMMY_USER_ID1,
                'post_params' => [
                    'name' => '設定名1',
                    Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => [1, 2],
                    Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => [1 => Model_Updatemethod::OVERWRITE, 2 => Model_Updatemethod::ADDWRITE],
                    Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME => [1 => 4],
                    Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME => [2 => 5],
                    Domain_Model_Updatesetting::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::UPDATE_YAHOO_CANCEL_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::REFLECT_ORDER_AMOUNT => 0,
                ],
                'result_column_count' => 2
            ]
        ];
    }

    public function data_provider_save_for_success() {
        return [
            [
                // 設定新規作成で保存した場合
                'company_id' => Test_Domain_Model_Updatesetting::DUMMY_COMPANY_ID1,
                'user_id' => Test_Domain_Model_Updatesetting::DUMMY_USER_ID1,
                'post_params' => [
                    'name' => '設定名2',
                    Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => [1, 2],
                    Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => [1 => Model_Updatemethod::OVERWRITE, 2 => Model_Updatemethod::ADDWRITE],
                    Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME => [1 => 4],
                    Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME => [2 => 5],
                    Domain_Model_Updatesetting::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::UPDATE_YAHOO_CANCEL_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::REFLECT_ORDER_AMOUNT => 0,
                ],
                'is_temporary' => false,
                'result_column_count' => 2
            ],
            [
                // 設定編集で保存した場合
                'company_id' => Test_Domain_Model_Updatesetting::DUMMY_COMPANY_ID1,
                'user_id' => Test_Domain_Model_Updatesetting::DUMMY_USER_ID1,
                'post_params' => [
                    'name' => '設定名3',
                    'bulk_update_setting_id' => Test_Domain_Model_Updatesetting::DUMMY_BULK_UPDATE_SETTING_ID1,
                    Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => [1, 2],
                    Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => [1 => Model_Updatemethod::OVERWRITE, 2 => Model_Updatemethod::ADDWRITE],
                    Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME => [1 => 4],
                    Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME => [2 => 5],
                    Domain_Model_Updatesetting::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::UPDATE_YAHOO_CANCEL_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::REFLECT_ORDER_AMOUNT => 0,
                ],
                'is_temporary' => false,
                'result_column_count' => 2
            ],
            [
                // 設定編集からプレビュー画面に遷移する場合の保存（is_temporaryがtrueでbulk_update_setting_idがある場合）
                'company_id' => Test_Domain_Model_Updatesetting::DUMMY_COMPANY_ID1,
                'user_id' => Test_Domain_Model_Updatesetting::DUMMY_USER_ID1,
                'post_params' => [
                    'name' => '設定名4',
                    'bulk_update_setting_id' => Test_Domain_Model_Updatesetting::DUMMY_BULK_UPDATE_SETTING_ID1,
                    Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => [1, 2],
                    Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => [1 => Model_Updatemethod::OVERWRITE, 2 => Model_Updatemethod::ADDWRITE],
                    Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME => [1 => 4],
                    Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME => [2 => 5],
                    Domain_Model_Updatesetting::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::UPDATE_YAHOO_CANCEL_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::REFLECT_ORDER_AMOUNT => 0,
                ],
                'is_temporary' => true,
                'result_column_count' => 2
            ],
            [
                // 設定新規作成からプレビュー画面に遷移する場合の保存（is_temporaryがtrueでbulk_update_setting_idがない場合）
                'company_id' => Test_Domain_Model_Updatesetting::DUMMY_COMPANY_ID1,
                'user_id' => Test_Domain_Model_Updatesetting::DUMMY_USER_ID1,
                'post_params' => [
                    'name' => '設定名5',
                    Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => [1, 2],
                    Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => [1 => Model_Updatemethod::OVERWRITE, 2 => Model_Updatemethod::ADDWRITE],
                    Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME => [1 => 4],
                    Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME => [2 => 5],
                    Domain_Model_Updatesetting::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::UPDATE_YAHOO_CANCEL_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::REFLECT_ORDER_AMOUNT => 0,
                ],
                'is_temporary' => true,
                'result_column_count' => 2
            ]
        ];
    }

    public function data_provider_create_setting()
    {
        return [
            [
                // 設定新規作成で保存した場合(設定が1項目の場合)
                'company_id' => Test_Domain_Model_Updatesetting::DUMMY_COMPANY_ID1,
                'user_id' => Test_Domain_Model_Updatesetting::DUMMY_USER_ID1,
                'post_params' => [
                    'name' => '設定名6',
                    Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => [1],
                    Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => [1 => Model_Updatemethod::OVERWRITE],
                    Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME => [1 => 5],
                    Domain_Model_Updatesetting::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::UPDATE_YAHOO_CANCEL_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::REFLECT_ORDER_AMOUNT => 0,
                ],
                'is_save' => true,
                'is_temporary' => false,
                'result_column_count' => 1
            ],
            [
                // 設定新規作成で保存した場合(設定が複数項目の場合)
                'company_id' => Test_Domain_Model_Updatesetting::DUMMY_COMPANY_ID1,
                'user_id' => Test_Domain_Model_Updatesetting::DUMMY_USER_ID1,
                'post_params' => [
                    'name' => '設定名6',
                    Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => [1, 2],
                    Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => [1 => Model_Updatemethod::OVERWRITE, 2 => Model_Updatemethod::ADDWRITE],
                    Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME => [1 => 4],
                    Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME => [2 => 5],
                    Domain_Model_Updatesetting::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::UPDATE_YAHOO_CANCEL_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::REFLECT_ORDER_AMOUNT => 0,
                ],
                'is_save' => true,
                'is_temporary' => false,
                'result_column_count' => 2
            ],
            [
                // 設定編集で保存した場合
                'company_id' => Test_Domain_Model_Updatesetting::DUMMY_COMPANY_ID1,
                'user_id' => Test_Domain_Model_Updatesetting::DUMMY_USER_ID1,
                'post_params' => [
                    'name' => '設定名7',
                    'bulk_update_setting_id' => Test_Domain_Model_Updatesetting::DUMMY_BULK_UPDATE_SETTING_ID1,
                    Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => [1, 2],
                    Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => [1 => Model_Updatemethod::OVERWRITE, 2 => Model_Updatemethod::ADDWRITE],
                    Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME => [1 => 4],
                    Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME => [2 => 5],
                    Domain_Model_Updatesetting::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::UPDATE_YAHOO_CANCEL_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::REFLECT_ORDER_AMOUNT => 0,
                ],
                'is_save' => true,
                'is_temporary' => false,
                'result_column_count' => 2
            ],
            [
                // 設定編集からプレビュー画面に遷移する場合の保存（is_temporaryがtrueでbulk_update_setting_idがある場合）
                'company_id' => Test_Domain_Model_Updatesetting::DUMMY_COMPANY_ID1,
                'user_id' => Test_Domain_Model_Updatesetting::DUMMY_USER_ID1,
                'post_params' => [
                    'name' => '設定名8',
                    'bulk_update_setting_id' => Test_Domain_Model_Updatesetting::DUMMY_BULK_UPDATE_SETTING_ID1,
                    Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => [1, 2],
                    Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => [1 => Model_Updatemethod::OVERWRITE, 2 => Model_Updatemethod::ADDWRITE],
                    Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME => [1 => 4],
                    Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME => [2 => 5],
                    Domain_Model_Updatesetting::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::UPDATE_YAHOO_CANCEL_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::REFLECT_ORDER_AMOUNT => 0,
                ],
                'is_save' => true,
                'is_temporary' => true,
                'result_column_count' => 2
            ],
            [
                // 設定新規作成からプレビュー画面に遷移する場合の保存（is_temporaryがtrueでbulk_update_setting_idがない場合）
                'company_id' => Test_Domain_Model_Updatesetting::DUMMY_COMPANY_ID1,
                'user_id' => Test_Domain_Model_Updatesetting::DUMMY_USER_ID1,
                'post_params' => [
                    'name' => '設定名9',
                    Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => [1, 2],
                    Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => [1 => Model_Updatemethod::OVERWRITE, 2 => Model_Updatemethod::ADDWRITE],
                    Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME => [1 => 4],
                    Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME => [2 => 5],
                    Domain_Model_Updatesetting::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::UPDATE_YAHOO_CANCEL_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::REFLECT_ORDER_AMOUNT => 0,
                ],
                'is_save' => true,
                'is_temporary' => true,
                'result_column_count' => 2
            ],
            [
                // is_saveがfalseだった場合
                'company_id' => Test_Domain_Model_Updatesetting::DUMMY_COMPANY_ID1,
                'user_id' => Test_Domain_Model_Updatesetting::DUMMY_USER_ID1,
                'post_params' => [
                    'name' => '設定名10',
                    Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => [1, 2],
                    Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => [1 => Model_Updatemethod::OVERWRITE, 2 => Model_Updatemethod::ADDWRITE],
                    Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME => [1 => 4],
                    Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME => [2 => 5],
                    Domain_Model_Updatesetting::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::UPDATE_YAHOO_CANCEL_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::REFLECT_ORDER_AMOUNT => 0,
                ],
                'is_save' => false,
                'is_temporary' => false,
                'result_column_count' => 2
            ],
            [
                // 別名で保存した場合（createが1だった場合、bulk_update_setting_idがあっても新規作成する）
                'company_id' => Test_Domain_Model_Updatesetting::DUMMY_COMPANY_ID1,
                'user_id' => Test_Domain_Model_Updatesetting::DUMMY_USER_ID1,
                'post_params' => [
                    'name' => '設定名11',
                    'bulk_update_setting_id' => Test_Domain_Model_Updatesetting::DUMMY_BULK_UPDATE_SETTING_ID1,
                    'create' => '1',
                    Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => [1, 2],
                    Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => [1 => Model_Updatemethod::OVERWRITE, 2 => Model_Updatemethod::ADDWRITE],
                    Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME => [1 => 4],
                    Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME => [2 => 5],
                    Domain_Model_Updatesetting::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::UPDATE_YAHOO_CANCEL_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::REFLECT_ORDER_AMOUNT => 0,
                ],
                'is_save' => false,
                'is_temporary' => false,
                'result_column_count' => 2
            ],
        ];
    }

    public function data_provider_create_setting_exception()
    {
        return [
            [
                'post_params' => [
                    'name' => '設定名12',
                    Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => [1 => Model_Updatemethod::OVERWRITE],
                    Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME => [1 => 5],
                ],
                'exception_message' => __em('update_column_empty'),
            ],
            [
                'post_params' => [
                    'name' => '設定名13',
                    Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => [1],
                    Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME => [1 => 5],
                ],
                'exception_message' => __em('update_method_empty'),
            ],
            [
                'post_params' => [
                    'name' => '設定名14',
                    Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => [1],
                    Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => [1 => Model_Updatemethod::OVERWRITE],
                ],
                'exception_message' => __em('update_value_empty'),
            ]
        ];
    }

    public function data_provider_get_bulk_update_setting_and_set_params() {
        return [
            [
                // 通常の設定
                'name' => '設定名15',
                'user_id' => Test_Domain_Model_Updatesetting::DUMMY_USER_ID1,
                'post_params' => [
                    Domain_Model_Updatesetting::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME => 1,
                    Domain_Model_Updatesetting::UPDATE_YAHOO_CANCEL_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME => 0,
                    Domain_Model_Updatesetting::REFLECT_ORDER_AMOUNT => 1,
                ],
                'allow_update_shipment_confirmed' => '1',
                'allow_update_yahoo_cancel' => '0',
                'allow_optimistic_lock_update_retry' => '0',
                'allow_reflect_order_amount' => '1'
            ],
            [
                // 高度な設定の項目がpost_paramsになかった場合すべて0になること
                'name' => '設定名16',
                'user_id' => Test_Domain_Model_Updatesetting::DUMMY_USER_ID1,
                'post_params' => [],
                'allow_update_shipment_confirmed' => '0',
                'allow_update_yahoo_cancel' => '0',
                'allow_optimistic_lock_update_retry' => '0',
                'allow_reflect_order_amount' => '0',
            ],
        ];
    }

    public function data_provider_get_bulk_update_column_and_set_params() {
        return [
            [
                // １件の場合
                'receive_order_column_id' => 1,
                'post_params' => [
                    Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => [1],
                    Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => [1 => Model_Updatemethod::OVERWRITE],
                    Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME => [1 => 5],
                ],
                'update_method_id' => Model_Updatemethod::OVERWRITE,
                'update_value' => 5,
            ],
            [
                // マスタデータの場合
                'receive_order_column_id' => 2,
                'post_params' => [
                    Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => [2],
                    Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => [2 => Model_Updatemethod::OVERWRITE],
                    Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME => [2 => 4],
                ],
                'update_method_id' => Model_Updatemethod::OVERWRITE,
                'update_value' => 4,
            ],
            [
                // 複数件ある場合
                'receive_order_column_id' => 3,
                'post_params' => [
                    Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => [1, 3],
                    Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => [1 => Model_Updatemethod::OVERWRITE, 3 => Model_Updatemethod::ADDWRITE],
                    Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME => [1 => 4],
                    Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME => [3 => 10],
                ],
                'update_method_id' => Model_Updatemethod::ADDWRITE,
                'update_value' => 10,
            ],
        ];
    }

    public function data_provider_get_bulk_update_column_and_set_params_exception() {
        return [
            [
                // 対応する更新方法が無いの場合
                'receive_order_column_id' => 1,
                'post_params' => [
                    Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => [1],
                    Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => [2 => Model_Updatemethod::OVERWRITE],
                    Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME => [1 => 5],
                ],
                'exception_message' => __em('update_method_empty'),
            ],
            [
                // 対応する値が無いの場合
                'receive_order_column_id' => 2,
                'post_params' => [
                    Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => [2],
                    Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => [2 => Model_Updatemethod::OVERWRITE],
                    Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME => [3 => 4],
                ],
                'exception_message' => __em('update_value_empty'),
            ],
        ];
    }

    public function data_provider_convert_set_params_exception() {
        return [
            ['unset_column' => 'receive_order_id'],
            ['unset_column' => 'receive_order_shop_id'],
            ['unset_column' => 'receive_order_gruoping_tag'],
            ['unset_column' => 'receive_order_shop_cut_form_id'],
            ['unset_column' => 'receive_order_order_status_id'],
            ['unset_column' => 'receive_order_last_modified_date'],
        ];
    }

    public function data_provider__convert_set_params_exception() {
        return [
            ['unset_colum' => 'receive_order_shop_id'],
            ['unset_colum' => 'receive_order_shop_cut_form_id'],
            ['unset_colum' => 'receive_order_date'],
            ['unset_colum' => 'receive_order_confirm_ids'],
            ['unset_colum' => 'receive_order_confirm_check_id'],
            ['unset_colum' => 'receive_order_gruoping_tag'],
            ['unset_colum' => 'receive_order_cancel_type_id'],
            ['unset_colum' => 'receive_order_delivery_id'],
            ['unset_colum' => 'receive_order_payment_method_id'],
            ['unset_colum' => 'receive_order_goods_amount'],
            ['unset_colum' => 'receive_order_tax_amount'],
            ['unset_colum' => 'receive_order_charge_amount'],
            ['unset_colum' => 'receive_order_delivery_fee_amount'],
            ['unset_colum' => 'receive_order_other_amount'],
            ['unset_colum' => 'receive_order_point_amount'],
            ['unset_colum' => 'receive_order_total_amount'],
            ['unset_colum' => 'receive_order_deposit_amount'],
            ['unset_colum' => 'receive_order_deposit_type_id'],
            ['unset_colum' => 'receive_order_deposit_date'],
            ['unset_colum' => 'receive_order_note'],
            ['unset_colum' => 'receive_order_statement_delivery_instruct_printing_date'],
            ['unset_colum' => 'receive_order_statement_delivery_text'],
            ['unset_colum' => 'receive_order_worker_text'],
            ['unset_colum' => 'receive_order_picking_instruct'],
            ['unset_colum' => 'receive_order_hope_delivery_date'],
            ['unset_colum' => 'receive_order_hope_delivery_time_slot_id'],
            ['unset_colum' => 'receive_order_delivery_method_id'],
            ['unset_colum' => 'receive_order_business_office_stop_id'],
            ['unset_colum' => 'receive_order_invoice_id'],
            ['unset_colum' => 'receive_order_temperature_id'],
            ['unset_colum' => 'receive_order_seal1_id'],
            ['unset_colum' => 'receive_order_seal2_id'],
            ['unset_colum' => 'receive_order_seal3_id'],
            ['unset_colum' => 'receive_order_seal4_id'],
            ['unset_colum' => 'receive_order_gift_flag'],
            ['unset_colum' => 'receive_order_delivery_cut_form_id'],
            ['unset_colum' => 'receive_order_delivery_cut_form_note'],
            ['unset_colum' => 'receive_order_credit_type_id'],
            ['unset_colum' => 'receive_order_credit_approval_no'],
            ['unset_colum' => 'receive_order_credit_approval_amount'],
            ['unset_colum' => 'receive_order_credit_approval_type_id'],
            ['unset_colum' => 'receive_order_credit_approval_type_name'],
            ['unset_colum' => 'receive_order_credit_approval_date'],
            ['unset_colum' => 'receive_order_customer_type_id'],
            ['unset_colum' => 'receive_order_customer_id'],
            ['unset_colum' => 'receive_order_purchaser_name'],
            ['unset_colum' => 'receive_order_purchaser_kana'],
            ['unset_colum' => 'receive_order_purchaser_zip_code'],
            ['unset_colum' => 'receive_order_purchaser_address1'],
            ['unset_colum' => 'receive_order_purchaser_address2'],
            ['unset_colum' => 'receive_order_purchaser_tel'],
            ['unset_colum' => 'receive_order_purchaser_mail_address'],
            ['unset_colum' => 'receive_order_consignee_name'],
            ['unset_colum' => 'receive_order_consignee_kana'],
            ['unset_colum' => 'receive_order_consignee_zip_code'],
            ['unset_colum' => 'receive_order_consignee_address1'],
            ['unset_colum' => 'receive_order_consignee_address2'],
            ['unset_colum' => 'receive_order_consignee_tel'],
            ['unset_colum' => 'receive_order_important_check_id'],
            ['unset_colum' => 'receive_order_statement_delivery_printing_date'],
            ['unset_colum' => 'receive_order_credit_number_payments'],
            ['unset_colum' => 'receive_order_send_plan_date'],
            ['unset_colum' => 'receive_order_option_single_word_memo'],
            ['unset_colum' => 'receive_order_option_message'],
            ['unset_colum' => 'receive_order_option_noshi'],
            ['unset_colum' => 'receive_order_option_rapping'],
            ['unset_colum' => 'receive_order_option_1'],
            ['unset_colum' => 'receive_order_option_2'],
            ['unset_colum' => 'receive_order_option_3'],
            ['unset_colum' => 'receive_order_option_4'],
            ['unset_colum' => 'receive_order_option_5'],
            ['unset_colum' => 'receive_order_option_6'],
            ['unset_colum' => 'receive_order_option_7'],
            ['unset_colum' => 'receive_order_option_8'],
            ['unset_colum' => 'receive_order_option_9'],
            ['unset_colum' => 'receive_order_option_10'],
        ];
    }

    public function data_provider__get_execution_notice_content_for_error() {

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $reflectionClass = new ReflectionClass(Domain_Model_Updatesetting::class);
        $property = $reflectionClass->getProperty('notice_change_message_for_code');
        $property->setAccessible(true);
        $notice_change_message_for_code = $property->getValue($domain_model_updatesetting);

        return [
            // エラーが無い場合、空文字が返ること
            [
                'bulkupdate_response_messages' => [],
                'expected' => "",
            ],
            // エラーがある場合、そのメッセージが返ること
            [
                'bulkupdate_response_messages' => [['receive_order_id' => 1, 'code' => '1', 'message' => 'エラーメッセージ']],
                'expected' => "伝票番号：1\n原因：エラーメッセージ\n\n",
            ],
            // 同じエラーがある場合、同じエラー内容ごとにまとめられること
            [
                'bulkupdate_response_messages' => [
                    ['receive_order_id' => 1, 'code' => '1', 'message' => 'エラーメッセージ1'],
                    ['receive_order_id' => 2, 'code' => '1', 'message' => 'エラーメッセージ1'],
                    ['receive_order_id' => 3, 'code' => '2', 'message' => 'エラーメッセージ2']
                ],
                'expected' => "伝票番号：1,2\n原因：エラーメッセージ1\n\n伝票番号：3\n原因：エラーメッセージ2\n\n",
            ],
            // error_notice_change_message_for_codeに定義されているcodeの場合メッセージがそれに変わっていること
            [
                'bulkupdate_response_messages' => [['receive_order_id' => 1, 'code' => Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => 'TEST']],
                'expected' => "伝票番号：1\n原因：" . $notice_change_message_for_code[Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE] . "\n\n",
            ],
        ];
    }

    public function data_provider__get_execution_notice_content_for_excluded() {
        return [
            // 除外が無い場合は空文字が返ること
            [
                'excluded_id_and_reason' => [],
                'expected' => '',
            ],
            // 除外がある場合、その除外理由が返ること
            [
                'excluded_id_and_reason' => [['receive_order_id' => 1, 'excluded_reason' => '除外メッセージ']],
                'expected' => "伝票番号：1\n理由：除外メッセージ\n\n",
            ],
            // 同じ除外理由がある場合、そ同じ除外理由ごとにまとめられていること
            [
                'excluded_id_and_reason' => [
                    ['receive_order_id' => 1, 'excluded_reason' => '除外メッセージ1'],
                    ['receive_order_id' => 2, 'excluded_reason' => '除外メッセージ1'],
                    ['receive_order_id' => 3, 'excluded_reason' => '除外メッセージ2'],
                ],
                'expected' => "伝票番号：1,2\n理由：除外メッセージ1\n\n伝票番号：3\n理由：除外メッセージ2\n\n",
            ],
        ];
    }

    public function data_provider_convert_for_receive_order_gruoping_tag() {
        return [
            /*
             条件
             ・プレビュー時
             ・選択している伝票すべての受注状態がメール取込済
             ・受注分類タグを更新しない設定
             期待する結果
             ・receive_order_gruoping_tagが無いこと
             */
            [
                'setting_model_name' => 'Model_Bulkupdatesetting',
                'setting_id' => Test_Domain_Model_Updatesetting::DUMMY_BULK_UPDATE_SETTING_ID1,
                'receive_orders' => [
                    ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '1', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                    ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '1', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                ],
                'update_target_orders' => [
                    '1' => [
                        'receive_order_shop_id'            => 'TEST_VALUE1',
                        'receive_order_shop_cut_form_id'   => 'TEST_VALUE2',
                        'receive_order_id'                 => '1',
                        'receive_order_last_modified_date' => '2018-05-11 13:06:00',
                    ],
                    '2' => [
                        'receive_order_shop_id'            => 'TEST_VALUE1',
                        'receive_order_shop_cut_form_id'   => 'TEST_VALUE2',
                        'receive_order_id'                 => '2',
                        'receive_order_last_modified_date' => '2018-05-11 13:06:00',
                    ],
                ],
            ],
            /*
             条件
             ・プレビュー時
             ・選択している伝票すべての受注状態がメール取込済
             ・受注分類タグを更新する設定
             期待する結果
             ・receive_order_gruoping_tagがあること
             ・実行済みの受注分類タグが無いこと
             */
            [
                'setting_model_name' => 'Model_Bulkupdatesetting',
                'setting_id' => Test_Domain_Model_Updatesetting::DUMMY_BULK_UPDATE_SETTING_ID3,
                'receive_orders' => [
                    ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '1', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                    ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '1', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                ],
                'update_target_orders' => [
                    '1' => [
                        'receive_order_gruoping_tag'       => '[tag]',
                        'receive_order_id'                 => '1',
                        'receive_order_last_modified_date' => '2018-05-11 13:06:00',
                    ],
                    '2' => [
                        'receive_order_gruoping_tag'       => '[tag]',
                        'receive_order_id'                 => '2',
                        'receive_order_last_modified_date' => '2018-05-11 13:06:00',
                    ],
                ],
            ],
            /*
             条件
             ・プレビュー時
             ・選択している伝票に受注状態がメール取込済とそれ以外が混在している
             ・受注分類タグを更新しない設定
             期待する結果
             ・receive_order_gruoping_tagがあること
             ・受注状態がメール取込済の伝票に実行済みの受注分類タグが無いこと
             ・受注状態がメール取込済ではない伝票に実行済みの受注分類タグがあること
             */
            [
                'setting_model_name' => 'Model_Bulkupdatesetting',
                'setting_id' => Test_Domain_Model_Updatesetting::DUMMY_BULK_UPDATE_SETTING_ID1,
                'receive_orders' => [
                    ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '1', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                    ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                ],
                'update_target_orders' => [
                    '1' => [
                        'receive_order_gruoping_tag'       => '[dummy_tag]',
                        'receive_order_shop_id'            => 'TEST_VALUE1',
                        'receive_order_shop_cut_form_id'   => 'TEST_VALUE2',
                        'receive_order_id'                 => '1',
                        'receive_order_last_modified_date' => '2018-05-11 13:06:00',
                    ],
                    '2' => [
                        'receive_order_gruoping_tag'       => '[dummy_tag][【済】設定名]',
                        'receive_order_shop_id'            => 'TEST_VALUE1',
                        'receive_order_shop_cut_form_id'   => 'TEST_VALUE2',
                        'receive_order_id'                 => '2',
                        'receive_order_last_modified_date' => '2018-05-11 13:06:00',
                    ],
                ],
            ],
            /*
             条件
             ・プレビュー時
             ・選択している伝票に受注状態がメール取込済とそれ以外が混在している
             ・受注分類タグを更新する設定
             期待する結果
             ・receive_order_gruoping_tagがあること
             ・受注状態がメール取込済の伝票に実行済みの受注分類タグが無いこと
             ・受注状態がメール取込済ではない伝票に実行済みの受注分類タグがあること
             */
            [
                'setting_model_name' => 'Model_Bulkupdatesetting',
                'setting_id' => Test_Domain_Model_Updatesetting::DUMMY_BULK_UPDATE_SETTING_ID3,
                'receive_orders' => [
                    ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '1', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                    ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                ],
                'update_target_orders' => [
                    '1' => [
                        'receive_order_gruoping_tag'       => '[tag]',
                        'receive_order_id'                 => '1',
                        'receive_order_last_modified_date' => '2018-05-11 13:06:00',
                    ],
                    '2' => [
                        'receive_order_gruoping_tag'       => '[tag][【済】TEST3]',
                        'receive_order_id'                 => '2',
                        'receive_order_last_modified_date' => '2018-05-11 13:06:00',
                    ],
                ],
            ],
            /*
             条件
             ・実行時
             ・選択している伝票すべての受注状態がメール取込済
             ・受注分類タグを更新しない設定
             期待する結果
             ・receive_order_gruoping_tagが無いこと
             */
            [
                'setting_model_name' => 'Model_Executionbulkupdatesetting',
                'setting_id' => Test_Domain_Model_Updatesetting::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1,
                'receive_orders' => [
                    ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '1', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                    ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '1', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                ],
                'update_target_orders' => [
                    '1' => [
                        'receive_order_shop_id'            => '適当な値',
                        'receive_order_shop_cut_form_id'   => '適当な値',
                        'receive_order_id'                 => '1',
                        'receive_order_last_modified_date' => '2018-05-11 13:06:00',
                    ],
                    '2' => [
                        'receive_order_shop_id'            => '適当な値',
                        'receive_order_shop_cut_form_id'   => '適当な値',
                        'receive_order_id'                 => '2',
                        'receive_order_last_modified_date' => '2018-05-11 13:06:00',
                    ],
                ],
            ],
            /*
             条件
             ・実行時
             ・選択している伝票すべての受注状態がメール取込済
             ・受注分類タグを更新する設定
             期待する結果
             ・receive_order_gruoping_tagがあること
             ・実行済みの受注分類タグが無いこと
             */
            [
                'setting_model_name' => 'Model_Executionbulkupdatesetting',
                'setting_id' => Test_Domain_Model_Updatesetting::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID4,
                'receive_orders' => [
                    ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '1', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                    ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '1', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                ],
                'update_target_orders' => [
                    '1' => [
                        'receive_order_gruoping_tag'       => '[tag]',
                        'receive_order_id'                 => '1',
                        'receive_order_last_modified_date' => '2018-05-11 13:06:00',
                    ],
                    '2' => [
                        'receive_order_gruoping_tag'       => '[tag]',
                        'receive_order_id'                 => '2',
                        'receive_order_last_modified_date' => '2018-05-11 13:06:00',
                    ],
                ],
            ],
            /*
             条件
             ・実行時
             ・選択している伝票に受注状態がメール取込済とそれ以外が混在している
             ・受注分類タグを更新しない設定
             期待する結果
             ・receive_order_gruoping_tagがあること
             ・受注状態がメール取込済の伝票に実行済みの受注分類タグが無いこと
             ・受注状態がメール取込済ではない伝票に実行済みの受注分類タグがあること
             */
            [
                'setting_model_name' => 'Model_Executionbulkupdatesetting',
                'setting_id' => Test_Domain_Model_Updatesetting::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1,
                'receive_orders' => [
                    ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '1', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                    ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                ],
                'update_target_orders' => [
                    '1' => [
                        'receive_order_shop_id'            => '適当な値',
                        'receive_order_shop_cut_form_id'   => '適当な値',
                        'receive_order_id'                 => '1',
                        'receive_order_last_modified_date' => '2018-05-11 13:06:00',
                    ],
                    '2' => [
                        'receive_order_gruoping_tag'       => '[dummy_tag][【済】テスト実行1]',
                        'receive_order_shop_id'            => '適当な値',
                        'receive_order_shop_cut_form_id'   => '適当な値',
                        'receive_order_id'                 => '2',
                        'receive_order_last_modified_date' => '2018-05-11 13:06:00',
                    ],
                ],
            ],
            /*
             条件
             ・プレビュー時
             ・選択している伝票に受注状態がメール取込済とそれ以外が混在している
             ・受注分類タグを更新する設定
             期待する結果
             ・receive_order_gruoping_tagがあること
             ・受注状態がメール取込済の伝票に実行済みの受注分類タグが無いこと
             ・受注状態がメール取込済ではない伝票に実行済みの受注分類タグがあること
             */
            [
                'setting_model_name' => 'Model_Executionbulkupdatesetting',
                'setting_id' => Test_Domain_Model_Updatesetting::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID4,
                'receive_orders' => [
                    ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '1', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                    ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                ],
                'update_target_orders' => [
                    '1' => [
                        'receive_order_gruoping_tag'       => '[tag]',
                        'receive_order_id'                 => '1',
                        'receive_order_last_modified_date' => '2018-05-11 13:06:00',
                    ],
                    '2' => [
                        'receive_order_gruoping_tag'       => '[tag][【済】テスト実行4]',
                        'receive_order_id'                 => '2',
                        'receive_order_last_modified_date' => '2018-05-11 13:06:00',
                    ],
                ],
            ],
        ];
    }
}