<?php
/**
 * execution_bulk_update_columns
 * 実行する一括更新項目情報
 *
 * Class Model_Executionbulkupdatecolumn
 */
class Model_Executionbulkupdatecolumn extends Model_Base
{
    protected static $_table_name = 'execution_bulk_update_columns' ;

    protected static $_properties = [
        'id',
        'execution_bulk_update_setting_id',
        'receive_order_column_id',
        'update_method_id',
        'update_value',
        'created_at',
        'updated_at',
    ];

    protected static $_belongs_to = [
        'execution_bulk_update_setting' => [
            'model_to'       => 'Model_Executionbulkupdatesetting',
            'key_from'       => 'execution_bulk_update_setting_id',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ],
        'receive_order_column' => [
            'model_to'       => 'Model_Receiveordercolumn',
            'key_from'       => 'receive_order_column_id',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ],
        'update_method' => [
            'model_to'       => 'Model_Updatemethod',
            'key_from'       => 'update_method_id',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ],
    ];
}