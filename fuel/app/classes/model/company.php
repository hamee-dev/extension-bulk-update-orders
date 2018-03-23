<?php
/**
 * companies
 * メイン機能単位の企業情報
 *
 * Class Model_Company
 */
class Model_Company extends Model_Base {
    protected static $_table_name = 'companies';

    protected static $_properties = [
        'id',
        'main_function_id',
        'company_ne_id',
        'name',
        'name_kana',
        'stoped_at',
        'created_at',
        'updated_at',
    ];

    protected static $_has_many = [
        'users' => [
            'model_to'       => 'Model_User',
            'key_from'       => 'id',
            'key_to'         => 'company_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ],
        'bulk_update_settings' => [
            'model_to'       => 'Model_Bulkupdatesetting',
            'key_from'       => 'id',
            'key_to'         => 'company_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ],
        'execution_bulk_update_settings' => [
            'model_to'       => 'Model_Executionbulkupdatesetting',
            'key_from'       => 'id',
            'key_to'         => 'company_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ],
        'request_keys' => [
            'model_to'       => 'Model_Requestkey',
            'key_from'       => 'id',
            'key_to'         => 'company_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ],
    ];
}