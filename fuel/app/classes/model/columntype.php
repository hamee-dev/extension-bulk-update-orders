<?php
/**
 * column_types
 * 項目のタイプ（型）
 *
 * Class Model_Columntype
 */
class Model_Columntype extends Model_Base
{
    const STRING    = '1';  // テキスト型
    const TEXT_AREA = '2';  // テキストエリア型
    const EMAIL     = '3';  // Eメール型
    const NUMBER    = '4';  // 数値型
    const DATE      = '5';  // 日付型
    const BOOL      = '6';  // ブール型
    const MASTER    = '7';  // マスタ選択型
    const TAG       = '8';  // タグ型
    const TELEPHONE = '9';  // 電話番号型
    const ZIP       = '10'; // 郵便番号型

    protected static $_table_name = 'column_types';

    protected static $_properties = [
        'id',
        'name',
        'created_at',
        'updated_at',
    ];

    protected static $_has_many = [
        'receive_order_columns' => [
            'model_to'       => 'Model_Receiveordercolumn',
            'key_from'       => 'id',
            'key_to'         => 'column_type_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ],
        'column_types_update_methods' => [
            'model_to'       => 'Model_Columntypesupdatemethod',
            'key_from'       => 'id',
            'key_to'         => 'column_type_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ],
    ];

    /**
     * マスタ型かどうか
     *
     * @return bool
     */
    public function is_master() : bool {
        return $this->id === self::MASTER;
    }

    /**
     * テキスト型かどうか
     *
     * @return bool
     */
    public function is_string() : bool {
        return $this->id === self::STRING;
    }

    /**
     * テキストエリア型かどうか
     *
     * @return bool
     */
    public function is_textarea() : bool {
        return $this->id === self::TEXT_AREA;
    }

    /**
     * 数値型かどうか
     *
     * @return bool
     */
    public function is_number() : bool {
        return $this->id === self::NUMBER;
    }

    /**
     * email型かどうか
     *
     * @return bool
     */
    public function is_email() : bool {
        return $this->id === self::EMAIL;
    }

    /**
     * bool型かどうか
     *
     * @return bool
     */
    public function is_bool() : bool {
        return $this->id === self::BOOL;
    }

    /**
     * タグ型かどうか
     *
     * @return bool
     */
    public function is_tag() : bool {
        return $this->id === self::TAG;
    }

    /**
     * 電話番号型かどうか
     *
     * @return bool
     */
    public function is_telephone() : bool {
        return $this->id === self::TELEPHONE;
    }

    /**
     * 郵便番号型かどうか
     *
     * @return bool
     */
    public function is_zip() : bool {
        return $this->id === self::ZIP;
    }

    /**
     * 日付型かどうか
     *
     * @return bool
     */
    public function is_date() : bool {
        return $this->id === self::DATE;
    }

    /**
     * 項目のタイプ一覧を取得する
     *
     * @return array Model_Columntypeの配列
     * @throws FuelException
     */
    public static function get_all() : array {
        $options = [
                    'related' => [
                        'column_types_update_methods' => [
                            'related' => ['update_method']
                        ],
                    ],
        ];
        return self::findAll([], $options);
    }
}