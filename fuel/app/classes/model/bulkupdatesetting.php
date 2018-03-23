<?php
/**
 * bulk_update_settings
 * 一括更新設定情報
 * NOTE: このモデルは論理削除のモデルだがModel_Softを継承するのを諦めて自前で実装している
 * findメソッドを通ればdeleted_atを考慮してレコードを取得する
 * NOTE: deleteメソッドを使えば論理削除される
 * 物理削除したい場合にはhard_deleteメソッドを使用すること
 *
 * Class Model_Bulkupdatesetting
 */
class Model_Bulkupdatesetting extends Model_Base {
    const SETTING_COUNT_MAX = 50;

    protected static $_table_name = 'bulk_update_settings' ;

    protected static $_properties = [
        'id',
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
        'temporary' => [
            'default' => 1,
        ],
        'original_bulk_update_setting_id',
        'created_user_id',
        'last_updated_user_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected static $_belongs_to = [
        'company' => [
            'model_to'       => 'Model_Company',
            'key_from'       => 'company_id',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ],
        'bulk_update_setting' => [
            'model_to'       => 'Model_Bulkupdatesetting',
            'key_from'       => 'original_bulk_update_setting_id',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ],
        'created_user' => [
            'model_to'       => 'Model_User',
            'key_from'       => 'created_user_id',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ],
        'last_updated_user' => [
            'model_to'       => 'Model_User',
            'key_from'       => 'last_updated_user_id',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ],
    ];

    protected static $_has_many = [
        'bulk_update_settings' => [
            'model_to'       => 'Model_Bulkupdatesetting',
            'key_from'       => 'id',
            'key_to'         => 'original_bulk_update_setting_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ],
        'bulk_update_columns' => [
            'model_to'       => 'Model_Bulkupdatecolumn',
            'key_from'       => 'id',
            'key_to'         => 'bulk_update_setting_id',
            'cascade_save'   => true,
            'cascade_delete' => true,
        ],
    ];

    /**
     * 更新設定を取得する際のoption
     *
     * @var array
     */
    protected static $default_options = [
        'related' => [
            'bulk_update_columns' => [
                'related' => [
                    'receive_order_column' => [
                        'related' => [
                            'column_type'
                        ]
                    ],
                    'update_method'
                ],
                'order_by' => 'id',
            ],
            'created_user',
            'last_updated_user',
        ],
    ];

    /**
     * 比較対象から除外するカラム名の配列（詳細は親クラス参照）
     *
     * @var array カラム名
     */
    protected static $exclude_comparison_columns = [
        'id',
        'company_id',
        'temporary',
        'original_bulk_update_setting_id',
        'created_user_id',
        'last_updated_user_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * 論理削除されたレコードを除いて取得する
     *
     * @param int|null $id
     * @param array $options
     * @return  Model|Model[]
     */
    public static function find($id = null, array $options = []) {
        if(!isset($options['where'])) $options['where'] = [];
        $options['where'] = array_merge($options['where'], ['deleted_at' => null]);
        return parent::find($id, $options);
    }

    /**
     * 論理削除する
     * NOTE: coreのdeleteをoverrideするために未使用だが引数を定義している
     *
     * @param   mixed $cascade
     *     null = use default config,
     *     bool = force/prevent cascade,
     *     array cascades only the relations that are in the array
     * @param bool $use_transaction
     * @return bool 削除に成功したかどうか
     */
    public function delete($cascade = null, $use_transaction = false) : bool {
        $this->deleted_at = date("Y-m-d H:i:s");
        return $this->save();
    }

    /**
     * 物理削除する
     *
     * @param   mixed $cascade
     *     null = use default config,
     *     bool = force/prevent cascade,
     *     array cascades only the relations that are in the array
     * @param bool $use_transaction
     * @return Model_Bulkupdatesetting
     */
    public function hard_delete($cascade = null, $use_transaction = false) : Model_Bulkupdatesetting {
        // このクラスでdeleteメソッドはoverrideしているため親クラスのdeleteを利用する
        return parent::delete($cascade, $use_transaction);
    }


    /**
     * 実行ユーザーIDを返す
     * bulk_update_settingに関する実行ユーザーは、実行時に作成したtemporary=1のレコードのcreated_user_id
     * NOTE: Model_Executionbulkupdatesettingと同名のメソッドにしておくこと、タスク内で同一オブジェクトとして扱っています
     *
     * @return string ユーザーID
     */
    public function get_execution_user_id() : string {
        return $this->created_user_id;
    }

    /**
     * 「登録されている一括更新設定の内容」と「プレビュー時の一時的な一括更新設定の内容」が異なるかを判定
     *
     * @return bool true:異なる、false:同じ
     */
    public function is_different_original() : bool {
        if($this->is_new()) {
            // 未保存の場合
            return true;
        }

        if($this->is_changed()) {
            // 変更されている場合
            return true;
        }

        if(!$this->temporary) {
            // 一時的な一括更新設定ではない場合
            return false;
        }

        if(is_null($this->original_bulk_update_setting_id)) {
            // 新規作成の場合
            return true;
        }

        // 登録されている一括更新設定を取得
        $original_bulk_update_setting = self::get_setting($this->company_id, $this->original_bulk_update_setting_id);

        if(is_null($original_bulk_update_setting)) {
            // 登録されている一括更新設定が存在しない場合
            return true;
        }

        if($this->get_comparison_columns() !== $original_bulk_update_setting->get_comparison_columns()) {
            // 一括更新設定の更新項目以外の設定内容が異なる場合
            return true;
        }

        if($this->get_comparison_bulk_update_columns() !== $original_bulk_update_setting->get_comparison_bulk_update_columns()) {
            // 一括更新設定の更新項目の設定内容が異なる場合
            return true;
        }

        return false;
    }

    /**
     * 一括更新設定の項目の比較対象情報を取得
     *
     * @return array 一括更新設定の項目の比較対象情報
     */
    public function get_comparison_bulk_update_columns() : array {
        $comparison_columns = [];

        foreach($this->bulk_update_columns as $bulk_update_column) {
            // 一括更新設定の項目の比較対象のカラム値の取得
            $comparison_columns[] = $bulk_update_column->get_comparison_columns();
        }

        return $comparison_columns;
    }

    /**
     * 一括更新設定に伝票に関する高度な更新設定のいずれかが含まれているかを判定する
     *
     * @return bool true:含まれている、false:含まれていない
     */
    public function is_selected_option() : bool {
        if ($this->allow_update_shipment_confirmed === '1') {
            return true;
        }
        if ($this->allow_update_yahoo_cancel === '1') {
            return true;
        }
        if ($this->allow_optimistic_lock_update_retry === '1') {
            return true;
        }
        if ($this->allow_reflect_order_amount === '1') {
            return true;
        }
        return false;
    }

    /**
     * 一括更新設定の項目にタグ型の項目が含まれているかを判定する
     *
     * @return bool true:含まれている、false:含まれていない
     */
    public function is_selected_type_tag() : bool {
        foreach($this->bulk_update_columns as $bulk_update_column) {
            if($bulk_update_column->receive_order_column->column_type->is_tag()) {
                // タグ型項目の場合
                return true;
            }
        }
        return false;
    }

    /**
     * 一括更新設定の項目に発送関連の項目が含まれているかを判定する
     *
     * @return bool true:含まれている、false:含まれていない
     */
    public function is_selected_delivery() : bool {
        foreach($this->bulk_update_columns as $bulk_update_column) {
            if($bulk_update_column->receive_order_column->is_delivery()) {
                // 発送関連項目の場合
                return true;
            }
        }
        return false;
    }

    /**
     * 一括更新設定の項目に支払関連の項目が含まれているかを判定する
     *
     * @return bool true:含まれている、false:含まれていない
     */
    public function is_selected_payment() : bool {
        foreach($this->bulk_update_columns as $bulk_update_column) {
            if($bulk_update_column->receive_order_column->is_payment()) {
                // 支払関連項目の場合
                return true;
            }
        }
        return false;
    }

    /**
     * 一括更新設定の項目に支払方法の項目が含まれているかを判定する
     *
     * @return bool true:含まれている、false:含まれていない
     */
    public function is_selected_payment_method_id() : bool {
        foreach($this->bulk_update_columns as $bulk_update_column) {
            if($bulk_update_column->receive_order_column->is_payment_method_id()) {
                // 支払方法の場合
                return true;
            }
        }
        return false;
    }

    /**
     * 一括更新設定の項目に受注金額関連の項目が含まれているかを判定する
     *
     * @return bool true:含まれている、false:含まれていない
     */
    public function is_selected_order_amount() : bool {
        foreach($this->bulk_update_columns as $bulk_update_column) {
            if($bulk_update_column->receive_order_column->is_order_amount()) {
                // 受注金額関連項目の場合
                return true;
            }
        }
        return false;
    }

    /**
     * 一括更新設定の項目に総合計の項目が含まれているかを判定する
     *
     * @return bool true:含まれている、false:含まれていない
     */
    public function is_selected_total_amount() : bool {
        foreach($this->bulk_update_columns as $bulk_update_column) {
            if($bulk_update_column->receive_order_column->is_total_amount()) {
                // 総合計の場合
                return true;
            }
        }
        return false;
    }

    /**
     * TOP画面表示用の更新設定一覧を取得する
     *
     * @param string $company_id 更新設定一覧を取得する企業ID
     * @return array 更新設定一覧
     * @throws FuelException
     */
    public static function get_settings_for_top(string $company_id) : array {
        $options = self::$default_options;
        // 作成日の昇順に並べる
        $options['order_by'] = 'created_at';
        return self::findAll(['company_id' => $company_id, 'temporary' => 0], $options);
    }

    /**
     * 更新設定を取得する
     *
     * @param string $company_id 更新設定を取得する企業ID
     * @param string $bulk_update_setting_id 更新設定ID
     * @param array $option その他の検索条件の連想配列
     * @return Model_Bulkupdatesetting 更新設定
     * @throws FuelException
     */
    public static function get_setting(string $company_id, string $bulk_update_setting_id, array $option = []) {
        $where = array_merge(['company_id' => $company_id, 'id' => $bulk_update_setting_id], $option);
        return self::findOne($where, self::$default_options);
    }

    /**
     * 更新設定をバリデーションできる形に整形して返す
     *
     * @param string $company_id 企業ID
     * @param string $bulk_update_setting_id 設定ID
     * @return array 更新設定をバリデーションできる形に整形した連想配列
     * @throws FuelException
     */
    public static function get_validation_params_by_bulk_update_setting_id(string $company_id, string $bulk_update_setting_id) {
        $bulk_update_setting = Model_Bulkupdatesetting::get_setting($company_id, $bulk_update_setting_id);
        if (is_null($bulk_update_setting)) {
            return [];
        }
        $params = [
            BULK_UPDATE_SETTING_ID => $bulk_update_setting->id,
            'name' => $bulk_update_setting->name,
            Domain_Model_Updatesetting::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME => $bulk_update_setting->allow_optimistic_lock_update_retry,
            Domain_Model_Updatesetting::UPDATE_YAHOO_CANCEL_ELEMENT_NAME => $bulk_update_setting->allow_update_yahoo_cancel,
            Domain_Model_Updatesetting::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME => $bulk_update_setting->allow_update_shipment_confirmed,
            Domain_Model_Updatesetting::REFLECT_ORDER_AMOUNT => $bulk_update_setting->allow_reflect_order_amount,
            Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => [],
            Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => [],
            Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME => [],
            Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME => []
        ];
        foreach ($bulk_update_setting->bulk_update_columns as $bulk_update_column) {
            $params[Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME][] = $bulk_update_column->receive_order_column_id;
            $params[Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME][$bulk_update_column->receive_order_column_id] = $bulk_update_column->update_method_id;
            if (!$bulk_update_column->receive_order_column->column_type->is_master()) {
                $params[Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME][$bulk_update_column->receive_order_column_id] = $bulk_update_column->update_value;
            }else{
                $params[Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME][$bulk_update_column->receive_order_column_id] = $bulk_update_column->update_value;
            }
        }
        return $params;
    }

    /**
     * 指定したoriginal_bulk_update_setting_idを全てnullに更新する
     * 更新設定の削除時に外部キー制約で消せなくなるのを防ぐため
     *
     * @param string $company_id 更新設定を取得する企業ID
     * @param string $original_bulk_update_setting_id 更新設定ID
     * @return  bool 全件成功したらtrue、1件でも失敗していたらfalse
     */
    public static function update_null_original_bulk_update_setting_id(string $company_id, string $original_bulk_update_setting_id) : bool {
        return self::query()
            ->set(['original_bulk_update_setting_id' => null])
            ->where(['company_id' => $company_id, 'original_bulk_update_setting_id' => $original_bulk_update_setting_id])
            ->update();
    }
}