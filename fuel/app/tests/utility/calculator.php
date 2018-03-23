<?php
class Test_Utility_Calculator extends Testbase {
    /**
     * @dataProvider Utility_Calculatordataprovider::data_provider_get_variable_round
     */
    public function test_get_variable_round_意図した端数処理を行うこと($params, $expect){
        // dataProviderにあるパターンを全て網羅すること
        $value = $params['value'];
        $fraction_id = $params['fraction_id'];
        $valid_scale = $params['valid_scale'];
        $result = Utility_Calculator::get_variable_round($value, $fraction_id, $valid_scale);
        $this->assertEquals($expect, $result);
    }

    public function test_get_variable_round_計算する際の桁数を指定するとその桁数での計算結果を返すこと_valid_scale0の場合(){
        // 10桁で計算すると小数第10位の値でも切り上げをすること
        $value = '0.0000000001';
        $fraction_id = Client_Neapi::ROUND_UP;
        $valid_scale = Utility_Calculator::DIGIT_INTEGER;
        $original_digits = 10;
        $result = Utility_Calculator::get_variable_round($value, $fraction_id, $valid_scale, $original_digits);
        $expect = '1';
        $this->assertEquals($expect, $result);

        // 9桁で計算すると小数第10位の値が無視されること
        $value = '0.0000000001';
        $fraction_id = Client_Neapi::ROUND_UP;
        $valid_scale = Utility_Calculator::DIGIT_INTEGER;
        $original_digits = 9;
        $result = Utility_Calculator::get_variable_round($value, $fraction_id, $valid_scale, $original_digits);
        $expect = '0';
        $this->assertEquals($expect, $result);
    }

    public function test_get_variable_round_計算する際の桁数を指定するとその桁数での計算結果を返すこと_valid_scale2の場合(){
        // 10桁(valid_scaleが2桁なのでoriginal_digitsは8桁を指定)で
        // 計算すると小数第10位の値でも切り上げをすること
        $value = '0.0000000001';
        $fraction_id = Client_Neapi::ROUND_UP;
        $valid_scale = Utility_Calculator::DIGIT_DECIMAL;
        $original_digits = 8;
        $result = Utility_Calculator::get_variable_round($value, $fraction_id, $valid_scale, $original_digits);
        $expect = '0.01';
        $this->assertEquals($expect, $result);

        // 0桁(valid_scaleが2桁なのでoriginal_digitsは7桁を指定)で
        // 計算すると小数第10位の値が無視されること
        $value = '0.0000000001';
        $fraction_id = Client_Neapi::ROUND_UP;
        $valid_scale = Utility_Calculator::DIGIT_DECIMAL;
        $original_digits = 7;
        $result = Utility_Calculator::get_variable_round($value, $fraction_id, $valid_scale, $original_digits);
        $expect = '0.00';
        $this->assertEquals($expect, $result);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_get_variable_round_意図しない端数処理方法を指定すると例外が発生すること(){
        $value = '333.3333333';
        $fraction_id = '存在しないfraction_id';
        $valid_scale = Utility_Calculator::DIGIT_DECIMAL;
        $result = Utility_Calculator::get_variable_round($value, $fraction_id, $valid_scale);
    }
}