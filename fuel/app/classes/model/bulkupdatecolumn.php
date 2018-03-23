<?php
/**
 * bulk_update_columns
 * 一括更新項目情報
 *
 * Class Model_Bulkupdatecolumn
 */
class Model_Bulkupdatecolumn extends Model_Base {
    // NOTE: 暫定値なので負荷が問題なければ都度調整して良い
    const SETTING_COUNT_MAX = 20;

    protected static $_table_name = 'bulk_update_columns' ;

    protected static $_properties = [
        'id',
        'bulk_update_setting_id',
        'receive_order_column_id',
        'update_method_id',
        'update_value',
        'created_at',
        'updated_at',
    ];

    protected static $_belongs_to = [
        'bulk_update_setting' => [
            'model_to'       => 'Model_Bulkupdatesetting',
            'key_from'       => 'bulk_update_setting_id',
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

    /**
     * 比較対象から除外するカラム名の配列（詳細は親クラス参照）
     *
     * @var array カラム名
     */
    protected static $exclude_comparison_columns = [
        'id',
        'bulk_update_setting_id',
        'created_at',
        'updated_at',
    ];

    /**
     * $bulk_update_setting_idを指定してdeleteを行う
     *
     * @param string $bulk_update_setting_id 削除したい設定ID
     * @return bool
     */
    public static function delete_by_bulk_update_setting_id(string $bulk_update_setting_id) : bool{
        return self::query()
            ->where('bulk_update_setting_id', $bulk_update_setting_id)
            ->delete();
    }
}