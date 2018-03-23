<?php
class Utility_Calculatordataprovider{
    /**
     * 端数計算の12パターンの値を返す
     * ・端数が333... or 666...
     * ・小数点以下0桁 or 2桁
     * ・四捨五入 or 切り捨て or 切り上げ
     * 計2パターン * 2パターン * 3パターン = 12パターン
     */
    public function data_provider_get_variable_round() {
        return [
            ['params' => ['value' => '333.333', 'fraction_id' => Client_Neapi::ROUND,      'valid_scale' => Utility_Calculator::DIGIT_INTEGER], 'expect' => 333],
            ['params' => ['value' => '166.666', 'fraction_id' => Client_Neapi::ROUND,      'valid_scale' => Utility_Calculator::DIGIT_INTEGER], 'expect' => 167],
            ['params' => ['value' => '333.333', 'fraction_id' => Client_Neapi::ROUND_DOWN, 'valid_scale' => Utility_Calculator::DIGIT_INTEGER], 'expect' => 333],
            ['params' => ['value' => '166.666', 'fraction_id' => Client_Neapi::ROUND_DOWN, 'valid_scale' => Utility_Calculator::DIGIT_INTEGER], 'expect' => 166],
            ['params' => ['value' => '333.333', 'fraction_id' => Client_Neapi::ROUND_UP,   'valid_scale' => Utility_Calculator::DIGIT_INTEGER], 'expect' => 334],
            ['params' => ['value' => '166.666', 'fraction_id' => Client_Neapi::ROUND_UP,   'valid_scale' => Utility_Calculator::DIGIT_INTEGER], 'expect' => 167],
            ['params' => ['value' => '333.333', 'fraction_id' => Client_Neapi::ROUND,      'valid_scale' => Utility_Calculator::DIGIT_DECIMAL], 'expect' => 333.33],
            ['params' => ['value' => '166.666', 'fraction_id' => Client_Neapi::ROUND,      'valid_scale' => Utility_Calculator::DIGIT_DECIMAL], 'expect' => 166.67],
            ['params' => ['value' => '333.333', 'fraction_id' => Client_Neapi::ROUND_DOWN, 'valid_scale' => Utility_Calculator::DIGIT_DECIMAL], 'expect' => 333.33],
            ['params' => ['value' => '166.666', 'fraction_id' => Client_Neapi::ROUND_DOWN, 'valid_scale' => Utility_Calculator::DIGIT_DECIMAL], 'expect' => 166.66],
            ['params' => ['value' => '333.333', 'fraction_id' => Client_Neapi::ROUND_UP,   'valid_scale' => Utility_Calculator::DIGIT_DECIMAL], 'expect' => 333.34],
            ['params' => ['value' => '166.666', 'fraction_id' => Client_Neapi::ROUND_UP,   'valid_scale' => Utility_Calculator::DIGIT_DECIMAL], 'expect' => 166.67],
        ];
    }
}