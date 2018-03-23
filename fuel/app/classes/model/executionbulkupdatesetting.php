<?php
/**
 * execution_bulk_update_settings
 * 実行する一括更新設定情報
 *
 * Class Model_Executionbulkupdatesetting
 */
class Model_Executionbulkupdatesetting extends Model_Base
{
    protected static $_table_name = 'execution_bulk_update_settings' ;

    protected static $_properties = [
        'id',
        'request_key',
        'user_id',
        'extension_execution_id',
        'target_order_count' => [
            'default' => 0,
        ],
        'executed' => [
            'default' => 0,
        ],
        'company_id',
        'name',
        'allow_update_shipment_confirmed' => [
            'default' => 0,
        ],
        'allow_update_yahoo_cancel' => [
            'default' => 0,
        ],
        'allow_reflect_order_amount' => [
            'default' => 0,
        ],
        'allow_optimistic_lock_update_retry' => [
            'default' => 0,
        ],
        'created_at',
        'updated_at',
    ];

    protected static $_belongs_to = [
        'company' => [
            'model_to'       => 'Model_Company',
            'key_from'       => 'company_id',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ],
        'user' => [
            'model_to'       => 'Model_User',
            'key_from'       => 'user_id',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ],
    ];

    protected static $_has_many = [
        'execution_bulk_update_columns' => [
            'model_to'       => 'Model_Executionbulkupdatecolumn',
            'key_from'       => 'id',
            'key_to'         => 'execution_bulk_update_setting_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ],
        'excluded_receive_orders' => [
            'model_to'       => 'Model_Excludedreceiveorder',
            'key_from'       => 'id',
            'key_to'         => 'execution_bulk_update_setting_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ],
    ];

    /**
     * 実行ユーザーIDを返す
     *
     * @return string ユーザーID
     */
    public function get_execution_user_id() : string {
        return $this->user_id;
    }

    /**
     * タスク一覧画面用のレコードを取得する
     *
     * @param string $company_id タスク一覧を取得する企業ID
     * @return array 未実行のタスク一覧
     */
    public static function get_tasklist(string $company_id) : array {
        $result = self::query()
            ->where('company_id', $company_id)
            ->where('executed', 0)
            ->related('user')
            ->order_by('created_at')
            ->get();
        return $result;
    }
}