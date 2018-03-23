<?php
/**
 * excluded_receive_orders
 * 一括更新の除外受注伝票情報
 *
 * Class Model_Excludedreceiveorder
 */
class Model_Excludedreceiveorder extends Model_Base
{
    protected static $_table_name = 'excluded_receive_orders';

    protected static $_properties = [
        'id',
        'execution_bulk_update_setting_id',
        'receive_order_id',
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
    ];

    /**
     * 除外設定の伝票番号一覧の配列を取得する
     * @param  string $execution_bulk_update_setting_id
     * @return array 例: [1, 2]
     */
    public static function get_excluded_receive_orders(string $execution_bulk_update_setting_id) : array {
        $result = $excluded_receive_orders = Model_Excludedreceiveorder::query()
            ->select('receive_order_id')
            ->where('execution_bulk_update_setting_id', $execution_bulk_update_setting_id)
            ->get();
        return Arr::pluck($result, 'receive_order_id');
    }
}