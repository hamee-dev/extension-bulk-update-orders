<?php
/**
 * receive_order_columns
 * 受注伝票の項目情報
 *
 * Class Model_Receiveordercolumn
 */
class Model_Receiveordercolumn extends Model_Base
{
    // 受注伝票項目のID定義
    const COLUMN_ID_SHOP          = '1';  // 店舗
    const COLUMN_ID_ORDER_DATE    = '3';  // 受注日
    const COLUMN_ID_CONFIRM_IDS   = '4';  // 確認内容
    const COLUMN_ID_CONFIRM_CHECK = '5';  // 確認チェック
    const COLUMN_ID_DELIVERY      = '8';  // 発送方法
    const COLUMN_ID_PAYMENT       = '9';  // 支払方法
    const COLUMN_ID_POINT_AMOUNT  = '15'; // ポイント数
    const COLUMN_ID_TOTAL_AMOUNT  = '16'; // 総合計
    const COLUMN_ID_SEAL_1        = '31'; // シール1
    const COLUMN_ID_SEAL_2        = '32'; // シール2
    const COLUMN_ID_SEAL_3        = '33'; // シール3
    const COLUMN_ID_SEAL_4        = '34'; // シール4

    // 日付の入力方法
    const DATE_SELECT_TYPE_INPUT       = 'input'; // 直接日付入力
    const DATE_SELECT_TYPE_TODAY       = 'today'; // 今日
    const DATE_SELECT_TYPE_TOMORROW    = 'tomorrow'; // 明日
    const DATE_SELECT_TYPE_PLUS_TWO_DAYS   = '+2 day'; // 明後日

    protected static $_table_name = 'receive_order_columns';

    protected static $_properties = [
        'id',
        'receive_order_section_id',
        'column_type_id',
        'physical_name',
        'logical_name',
        'input_min_length',
        'input_max_length',
        'master_name',
        'order_amount',
        'payment',
        'delivery',
        'display_order',
        'disabled',
        'created_at',
        'updated_at',
    ];

    protected static $_belongs_to = [
        'receive_order_section' => [
            'model_to'       => 'Model_Receiveordersection',
            'key_from'       => 'receive_order_section_id',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ],
        'column_type' => [
            'model_to'       => 'Model_Columntype',
            'key_from'       => 'column_type_id',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ],
    ];

    protected static $_has_many = [
        'bulk_update_columns' => [
            'model_to'       => 'Model_Bulkupdatecolumn',
            'key_from'       => 'id',
            'key_to'         => 'receive_order_column_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ],
        'execution_bulk_update_columns' => [
            'model_to'       => 'Model_Executionbulkupdatecolumn',
            'key_from'       => 'id',
            'key_to'         => 'receive_order_column_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ],
    ];

    /**
     * ユーザーが任意で更新可能な項目以外で必要なカラムを取得する
     *
     * receive_order_columnsテーブルに存在しない または
     * receive_order_columnsテーブルに存在するがdisabled(無効)なカラムにおいて処理上必要になるカラムを返す
     * @return array
     */
    public static function get_additional_columns() : array {
        return [
            'receive_order_id' => '伝票番号',
            'receive_order_last_modified_date' => '最終更新日',
            'receive_order_order_status_id' => '受注状態区分',
            'receive_order_confirm_ids' => '受注確認内容',
        ];
    }

    /**
     * 日付の入力方法を取得する
     *
     * @return array
     */
    public static function get_date_select_types() : array {
        $date_list = [self::DATE_SELECT_TYPE_INPUT => '日付を入力'];
        return array_merge($date_list, self::get_relative_date_list());
    }

    /**
     * 相対的な日付指定のリスト
     * 「今日」「明日」「明後日」
     *
     * @return array
     */
    public static function get_relative_date_list() : array {
        return [
            self::DATE_SELECT_TYPE_TODAY => '今日',
            self::DATE_SELECT_TYPE_TOMORROW => '明日',
            self::DATE_SELECT_TYPE_PLUS_TWO_DAYS => '明後日'
        ];
    }

    /**
     * 更新する値が今日、明日、明後日のどれかか
     *
     * @param string 更新する値(bulk_update_columns.update_value)
     * @return bool
     */
    public static function is_date_select_relative_date(string $value) : bool {
        $relative_date_list = self::get_relative_date_list();
        if (isset($relative_date_list[$value])) {
            return true;
        }
        return false;
    }

    /**
     * physical_nameの配列を返す
     * API実行時パラメータのfielsで使用
     * @param bool $with_disabled 更新できないカラムを含むかどうか true: 含む, false: 含まない
     * @param string $section_id 受注伝票のセクションを区別するid
     * @return array 例: ['receive_order_shop_id', 'receive_order_shop_cut_form_id', ...]
     */
    public static function get_physical_names(bool $with_disabled = true, string $section_id = null): array{
        $query = self::query()->select('physical_name');
        if(!$with_disabled){
            $query->where('disabled', false);
        }
        if($section_id !== null){
            $query->where('receive_order_section_id', $section_id);
        }
        $result = $query->get();
        return Arr::pluck($result, 'physical_name');
    }

    /**
     * すべての項目を取得する
     *
     * @param bool $with_disabled 更新できないカラムを含むかどうか true: 含む, false: 含まない
     * @return array Model_Receiveordercolumnの配列
     * @throws FuelException
     */
    public static function get_all_columns(bool $with_disabled = true) : array {
        $where = !$with_disabled ? [['disabled' => 0]] : [];
        $options = [
            // display_orderの昇順に並べる
            'order_by' => 'display_order',
            // 一括更新の内容はreceive_order_columnのdisplay_orderの昇順で並べる
            'related' => [
                'receive_order_section',
                'column_type',
            ],
        ];
        $columns = self::findAll($where, $options);

        // fuelのバグにより、has_manyでfrom_cache=false にすると関連レコードが1件しか取得されないため、手動で設定する
        // @see https://wiki.fuelphp1st.com/wiki/index/FuelPHP%20%E3%81%AE%E3%83%90%E3%82%B0%E6%83%85%E5%A0%B1
        $column_types = [];
        foreach (Model_Columntype::get_all() as $column_type) {
            $column_types[$column_type->id] = $column_type;
        }

        foreach ($columns as &$column) {
            $column->column_type = $column_types[$column->column_type->id];
        }

        return $columns;
    }

    /**
     * 支払関連情報かどうか
     *
     * @return bool
     */
    public function is_payment() : bool {
        return $this->payment === '1';
    }

    /**
     * 発送関連情報かどうか
     *
     * @return bool
     */
    public function is_delivery() : bool {
        return $this->delivery === '1';
    }

    /**
     * 受注金額関連情報かどうか
     *
     * @return bool
     */
    public function is_order_amount() : bool {
        return $this->order_amount === '1';
    }

    /**
     * 項目が支払方法かどうか
     *
     * @return bool
     */
    public function is_payment_method_id() : bool {
        return $this->id === self::COLUMN_ID_PAYMENT;
    }

    /**
     * 項目がポイント数かどうか
     *
     * @return bool
     */
    public function is_point_amount() : bool {
        return $this->id === self::COLUMN_ID_POINT_AMOUNT;
    }

    /**
     * 項目が総合計かどうか
     *
     * @return bool
     */
    public function is_total_amount() : bool {
        return $this->id === self::COLUMN_ID_TOTAL_AMOUNT;
    }

    /**
     * 項目が受注日かどうか
     *
     * @return bool
     */
    public function is_order_date() : bool {
        return $this->id === self::COLUMN_ID_ORDER_DATE;
    }

    /**
     * 項目が発送方法関連のシールかどうか
     *
     * @return bool
     */
    public function is_seal() : bool
    {
        return in_array($this->id, [self::COLUMN_ID_SEAL_1, self::COLUMN_ID_SEAL_2, self::COLUMN_ID_SEAL_3, self::COLUMN_ID_SEAL_4]);
    }
}