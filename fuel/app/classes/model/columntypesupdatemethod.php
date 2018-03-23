<?php
/**
 * column_types_update_methods
 * 項目のタイプに対する更新方法
 *
 * Class Model_Columntypesupdatemethod
 */
class Model_Columntypesupdatemethod extends Model_Base
{
    protected static $_table_name = 'column_types_update_methods';

    protected static $_properties = [
        'id',
        'column_type_id',
        'update_method_id',
        'created_at',
        'updated_at',
    ];

    protected static $_belongs_to = [
        'column_type' => [
            'model_to'       => 'Model_Columntype',
            'key_from'       => 'column_type_id',
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