<?php
class Model_Updatemethoddataprovider{
    /**
     * 更新方法の反映の正常系パターン
     */
    public function data_provider_evaluate_valid() {
        return [
            ['update_method_id' => Model_Updatemethod::OVERWRITE, 'ne_value' => '元の値', 'update_value' => '上書きする値', 'expect' => '上書きする値'],
            ['update_method_id' => Model_Updatemethod::ADDWRITE,  'ne_value' => '元の値', 'update_value' => '追記する値',   'expect' => '元の値追記する値'],

            // 加算
            ['update_method_id' => Model_Updatemethod::ADDITION, 'ne_value' => 100,     'update_value' => 2,        'expect' => 102],
            ['update_method_id' => Model_Updatemethod::ADDITION, 'ne_value' => '100',   'update_value' => '2',      'expect' => 102],
            ['update_method_id' => Model_Updatemethod::ADDITION, 'ne_value' => 100.5,   'update_value' => 100.25,   'expect' => 200.75],
            ['update_method_id' => Model_Updatemethod::ADDITION, 'ne_value' => '100.5', 'update_value' => '100.25', 'expect' => 200.75],
            ['update_method_id' => Model_Updatemethod::ADDITION, 'ne_value' => -1,      'update_value' => -2,       'expect' => -3],
            ['update_method_id' => Model_Updatemethod::ADDITION, 'ne_value' => '-1',    'update_value' => '-2',     'expect' => -3],
            ['update_method_id' => Model_Updatemethod::ADDITION, 'ne_value' => '1',     'update_value' => '0002',   'expect' => 3],

            // 減算
            ['update_method_id' => Model_Updatemethod::SUBTRACTION, 'ne_value' => 100,     'update_value' => 2,        'expect' => 98],
            ['update_method_id' => Model_Updatemethod::SUBTRACTION, 'ne_value' => '100',   'update_value' => '2',      'expect' => 98],
            ['update_method_id' => Model_Updatemethod::SUBTRACTION, 'ne_value' => 100.5,   'update_value' => 100.25,   'expect' => 0.25],
            ['update_method_id' => Model_Updatemethod::SUBTRACTION, 'ne_value' => '100.5', 'update_value' => '100.25', 'expect' => 0.25],
            ['update_method_id' => Model_Updatemethod::SUBTRACTION, 'ne_value' => 50,      'update_value' => 100,      'expect' => -50],
            ['update_method_id' => Model_Updatemethod::SUBTRACTION, 'ne_value' => '50',    'update_value' => '100',    'expect' => -50],
            ['update_method_id' => Model_Updatemethod::SUBTRACTION, 'ne_value' => '50',    'update_value' => '0020',   'expect' => 30],

            // 乗算
            ['update_method_id' => Model_Updatemethod::MULTIPLICATION, 'ne_value' => 100,     'update_value' => 2,        'expect' => 200],
            ['update_method_id' => Model_Updatemethod::MULTIPLICATION, 'ne_value' => '100',   'update_value' => '2',      'expect' => 200],
            ['update_method_id' => Model_Updatemethod::MULTIPLICATION, 'ne_value' => 100.5,   'update_value' => 100.25,   'expect' => 10075.125],
            ['update_method_id' => Model_Updatemethod::MULTIPLICATION, 'ne_value' => '100.5', 'update_value' => '100.25', 'expect' => 10075.125],
            ['update_method_id' => Model_Updatemethod::MULTIPLICATION, 'ne_value' => 100,     'update_value' => -2,       'expect' => -200],
            ['update_method_id' => Model_Updatemethod::MULTIPLICATION, 'ne_value' => '100',   'update_value' => '-2',     'expect' => -200],
            ['update_method_id' => Model_Updatemethod::MULTIPLICATION, 'ne_value' => '100',   'update_value' => '00000',  'expect' => 0],

            // 除算
            ['update_method_id' => Model_Updatemethod::DIVISION, 'ne_value' => 100,     'update_value' => 2,        'expect' => 50],
            ['update_method_id' => Model_Updatemethod::DIVISION, 'ne_value' => '100',   'update_value' => '2',      'expect' => 50],
            ['update_method_id' => Model_Updatemethod::DIVISION, 'ne_value' => 100.5,   'update_value' => 100.25,   'expect' => 1.00249376559],
            ['update_method_id' => Model_Updatemethod::DIVISION, 'ne_value' => '100.5', 'update_value' => '100.25', 'expect' => 1.00249376559],
            ['update_method_id' => Model_Updatemethod::DIVISION, 'ne_value' => 100,     'update_value' => -2,       'expect' => -50],
            ['update_method_id' => Model_Updatemethod::DIVISION, 'ne_value' => '100',   'update_value' => '-2',     'expect' => -50],
            ['update_method_id' => Model_Updatemethod::DIVISION, 'ne_value' => '100',   'update_value' => '02',     'expect' => 50],
        ];
    }

    /**
     * 更新方法の反映の異常系パターン
     */
    public function data_provider_evaluate_invalid() {
        return [
            // 四則演算に数値でないものを入れた時の例外パターン
            ['update_method_id' => Model_Updatemethod::ADDITION, 'ne_value' => '数値でない値', 'update_value' => 2],
            ['update_method_id' => Model_Updatemethod::ADDITION, 'ne_value' => 100,            'update_value' => '数値でない値'],
            ['update_method_id' => Model_Updatemethod::ADDITION, 'ne_value' => '数値でない値', 'update_value' => '数値でない値'],
            ['update_method_id' => Model_Updatemethod::SUBTRACTION, 'ne_value' => '数値でない値', 'update_value' => 2],
            ['update_method_id' => Model_Updatemethod::SUBTRACTION, 'ne_value' => 100,            'update_value' => '数値でない値'],
            ['update_method_id' => Model_Updatemethod::SUBTRACTION, 'ne_value' => '数値でない値', 'update_value' => '数値でない値'],
            ['update_method_id' => Model_Updatemethod::MULTIPLICATION, 'ne_value' => '数値でない値', 'update_value' => 2],
            ['update_method_id' => Model_Updatemethod::MULTIPLICATION, 'ne_value' => 100,            'update_value' => '数値でない値'],
            ['update_method_id' => Model_Updatemethod::MULTIPLICATION, 'ne_value' => '数値でない値', 'update_value' => '数値でない値'],
            ['update_method_id' => Model_Updatemethod::DIVISION, 'ne_value' => '数値でない値', 'update_value' => 2],
            ['update_method_id' => Model_Updatemethod::DIVISION, 'ne_value' => 100,            'update_value' => '数値でない値'],
            ['update_method_id' => Model_Updatemethod::DIVISION, 'ne_value' => '数値でない値', 'update_value' => '数値でない値'],

            // 0除算の例外パターン
            ['update_method_id' => Model_Updatemethod::DIVISION, 'ne_value' => 100, 'update_value' => 0],
            ['update_method_id' => Model_Updatemethod::DIVISION, 'ne_value' => 100, 'update_value' => '0'],

            // 意図しない更新方法の指定による例外パターン
            ['update_method_id' => '存在しない更新方法', 'ne_value' => 100, 'update_value' => 100],
        ];
    }
}