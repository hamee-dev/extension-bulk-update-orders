<?php
/**
 * users
 * メイン機能単位のアプリ利用ユーザー情報
 *
 * Class Model_User
 */
class Model_User extends Model_Base {
    protected static $_table_name = 'users';

    protected static $_properties = [
        'id',
        'company_id',
        'uid',
        'pic_id',
        'pic_ne_id',
        'pic_name',
        'pic_kana',
        'access_token',
        'access_token_end_date',
        'refresh_token',
        'refresh_token_end_date',
        'created_at',
        'updated_at'
    ];

    protected static $_belongs_to = [
        'company' => [
            'model_to'       => 'Model_Company',
            'key_from'       => 'company_id',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ]
    ];
    protected static $_has_many = [
        'execution_bulk_update_settings' => [
            'model_to'       => 'Model_Executionbulkupdatesetting',
            'key_from'       => 'id',
            'key_to'         => 'user_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ],
        'bulk_update_settings_created' => [
            'model_to'       => 'Model_Bulkupdatesetting',
            'key_from'       => 'id',
            'key_to'         => 'created_user_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ],
        'bulk_update_settings_last_updated' => [
            'model_to'       => 'Model_Bulkupdatesetting',
            'key_from'       => 'id',
            'key_to'         => 'last_updated_user_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ],
    ];
}