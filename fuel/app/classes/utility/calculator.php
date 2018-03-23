<?php
/**
 * 計算まわりのユーティリティ
 */
class Utility_Calculator
{
    // 小数点以下桁数
    const DIGIT_INTEGER = 0;
    const DIGIT_DECIMAL = 2;

    // 四則演算時のデフォルト精度
    const DEFAULT_DIGIT = 10;

    /**
     * $valueを$fraction_idに指定した方法で丸める
     * 端数処理を行う関数
     *
     * @param string $value 丸めたい値
     * @param string $fraction_id 丸める方法 Client_Neapi::ROUND_DOWN, Client_Neapi::ROUND, Client_Neapi::ROUND_UPが使用可能
     * @param int $valid_scale 小数点以下の桁数 2を指定すれば小数点第二位までの精度で返す
     * @param int $original_digits ROUND_DOWN, ROUND_UP時に見る桁数。たとえば2を設定した場合、0.001が来ても小数第3位は見ずに0として扱う
     * @return string 丸めた値
     * @throws InvalidArgumentException
     */
    public static function get_variable_round(string $value, string $fraction_id, int $valid_scale = self::DIGIT_INTEGER, int $original_digits = self::DEFAULT_DIGIT) : string {
        // ROUND_DOWN, ROUND_UPで何倍して計算するか（たとえば100倍して計算すれば小数第二位を残し、小数第三位を丸めることになる）
        $valid_pow = pow(10, $valid_scale);

        //切り上げの場合、0.0000001ドルでも発生したら0.01ドルにしなくてはならないので、ここでは一切桁落ちが発生してはならない。
        switch($fraction_id){
        //【ROUND_DOWNとROUND_UPで何をしているか】
        // floorとceilは小数点以下の桁数が指定できず、整数になってしまうので、精度が必要な桁ぶん、小数点より左に来るようにしている。
        // たとえば123.4567ドルを切り捨てる場合だったら、100を掛けて一旦12345.67ドルとして計算し（この際小数点以下にはみ出すのはどんなに多くても、
        // 消費税、卸掛け率の小数部分の桁数を超えることはない。よって有効桁数は$original_digitsとしている)、.67を切り捨てて、12345.00ドルになったところで
        // 今度は100で割り、123.45ドルを得ることが出来る。
        case Client_Neapi::ROUND_DOWN:
            $value = bcmul($value, $valid_pow, $original_digits);
            $value = floor($value);
            $value = bcdiv($value, $valid_pow, $valid_scale);
            break;
        case Client_Neapi::ROUND:
            $value = round($value, $valid_scale);
            break;
        case Client_Neapi::ROUND_UP:
            $value = bcmul($value, $valid_pow, $original_digits);
            $value = ceil($value);
            $value = bcdiv($value, $valid_pow, $valid_scale);
            break;
        default:
            throw new InvalidArgumentException("意図しない端数処理方法が指定されました。fraction_id={$fraction_id}");
        }
        return $value;
    }
}