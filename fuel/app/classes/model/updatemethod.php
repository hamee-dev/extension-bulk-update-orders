<?php
/**
 * update_methods
 * 更新方法
 *
 * Class Model_Updatemethod
 */
class Model_Updatemethod extends Model_Base
{
    const OVERWRITE      = '1'; // 上書き
    const ADDWRITE       = '2'; // 追記
    const ADDITION       = '3'; // 加算
    const SUBTRACTION    = '4'; // 減算
    const MULTIPLICATION = '5'; // 乗算
    const DIVISION       = '6'; // 除算

    protected static $_table_name = 'update_methods';

    protected static $_properties = [
        'id',
        'name',
        'created_at',
        'updated_at',
    ];

    protected static $_has_many = [
        'column_types_update_methods' => [
            'model_to'       => 'Model_Columntypesupdatemethod',
            'key_from'       => 'id',
            'key_to'         => 'update_method_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ],
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
     * 渡された引数に対して指定の更新をして結果を返す
     * @param string $update_method_id 更新方法ID
     * @param mix $ne_value 更新するメイン機能の値
     * @param mix $update_value 設定された更新用の値
     * @return mix 更新方法によって様々
     * @throws InvalidArgumentException
     */
    public static function evaluate(string $update_method_id, $ne_value, $update_value)
    {
        switch ($update_method_id) {
            case self::OVERWRITE:
                return $update_value;

            case self::ADDWRITE:
                return (string)$ne_value . (string)$update_value;

            case self::ADDITION:
                if(!is_numeric($ne_value) || !is_numeric($update_value)){
                    throw new InvalidArgumentException("数値でないもので加算処理をしようとしたため処理を終了します。ne_value={$ne_value}, update_value={$update_value}");
                }
                return bcadd($ne_value, $update_value, Utility_Calculator::DEFAULT_DIGIT);

            case self::SUBTRACTION:
                if(!is_numeric($ne_value) || !is_numeric($update_value)){
                    throw new InvalidArgumentException("数値でないもので減算処理をしようとしたため処理を終了します。ne_value={$ne_value}, update_value={$update_value}");
                }
                return bcsub($ne_value, $update_value, Utility_Calculator::DEFAULT_DIGIT);

            case self::MULTIPLICATION:
                if(!is_numeric($ne_value) || !is_numeric($update_value)){
                    throw new InvalidArgumentException("数値でないもので乗算処理をしようとしたため処理を終了します。ne_value={$ne_value}, update_value={$update_value}");
                }
                return bcmul($ne_value, $update_value, Utility_Calculator::DEFAULT_DIGIT);

            case self::DIVISION:
                if(!is_numeric($ne_value) || !is_numeric($update_value)){
                    throw new InvalidArgumentException("数値でないもので除算処理をしようとしたため処理を終了します。ne_value={$ne_value}, update_value={$update_value}");
                }
                // 0除算が指定された場合は例外を投げる
                if($update_value === 0 || $update_value === '0'){
                    throw new InvalidArgumentException('0除算が指定されました。計算できないため処理を終了します。');
                }
                return bcdiv($ne_value, $update_value, Utility_Calculator::DEFAULT_DIGIT);

            default:
                throw new InvalidArgumentException("意図しない更新方法が指定されました。update_method_id={$update_method_id}");
        }
    }

    /**
     * 上書き型かどうか
     *
     * @return bool
     */
    public function is_overwrite() : bool {
        return $this->id === self::OVERWRITE;
    }

    /**
     * 追記型かどうか
     *
     * @return bool
     */
    public function is_addwrite() : bool {
        return $this->id === self::ADDWRITE;
    }

    /**
     * 計算型かどうか
     *
     * @return bool
     */
    public function is_calc() : bool {
        return $this->id === self::ADDITION ||
            $this->id === self::SUBTRACTION ||
            $this->id === self::MULTIPLICATION ||
            $this->id === self::DIVISION;
    }
}