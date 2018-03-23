<?php

class Test_Model_Columntype extends Testbase
{
    public function test_is_master_マスタ型の場合はtrueで返ること() {
        $column_type = Model_Columntype::findOne(['id' => Model_Columntype::MASTER]);
        $this->assertTrue($column_type->is_master());
    }

    public function test_is_master_マスタ型ではない場合はfalseで返ること() {
        $column_type = Model_Columntype::findOne(['id' => Model_Columntype::STRING]);
        $this->assertFalse($column_type->is_master());
    }

    public function test_is_string_文字列型の場合はtrueで返ること() {
        $column_type = Model_Columntype::findOne(['id' => Model_Columntype::STRING]);
        $this->assertTrue($column_type->is_string());
    }

    public function test_is_string_文字列型ではない場合はfalseで返ること() {
        $column_type = Model_Columntype::findOne(['id' => Model_Columntype::TEXT_AREA]);
        $this->assertFalse($column_type->is_string());
    }

    public function test_is_textarea_テキストエリア型の場合はtrueで返ること() {
        $column_type = Model_Columntype::findOne(['id' => Model_Columntype::TEXT_AREA]);
        $this->assertTrue($column_type->is_textarea());
    }

    public function test_is_textarea_テキストエリア型ではない場合はfalseで返ること() {
        $column_type = Model_Columntype::findOne(['id' => Model_Columntype::NUMBER]);
        $this->assertFalse($column_type->is_textarea());
    }

    public function test_is_number_数値型の場合はtrueで返ること() {
        $column_type = Model_Columntype::findOne(['id' => Model_Columntype::NUMBER]);
        $this->assertTrue($column_type->is_number());
    }

    public function test_is_number_数値型ではない場合はfalseで返ること() {
        $column_type = Model_Columntype::findOne(['id' => Model_Columntype::STRING]);
        $this->assertFalse($column_type->is_number());
    }

    public function test_is_email_Eメール型の場合はtrueで返ること() {
        $column_type = Model_Columntype::findOne(['id' => Model_Columntype::EMAIL]);
        $this->assertTrue($column_type->is_email());
    }

    public function test_is_email_Eメール型ではない場合はfalseで返ること() {
        $column_type = Model_Columntype::findOne(['id' => Model_Columntype::NUMBER]);
        $this->assertFalse($column_type->is_email());
    }

    public function test_is_bool_ブール型の場合はtrueで返ること() {
        $column_type = Model_Columntype::findOne(['id' => Model_Columntype::BOOL]);
        $this->assertTrue($column_type->is_bool());
    }

    public function test_is_bool_ブール型ではない場合はfalseで返ること() {
        $column_type = Model_Columntype::findOne(['id' => Model_Columntype::TEXT_AREA]);
        $this->assertFalse($column_type->is_bool());
    }

    public function test_is_tag_タグ型の場合はtrueで返ること() {
        $column_type = Model_Columntype::findOne(['id' => Model_Columntype::TAG]);
        $this->assertTrue($column_type->is_tag());
    }

    public function test_is_tag_タグ型ではない場合はfalseで返ること() {
        $column_type = Model_Columntype::findOne(['id' => Model_Columntype::DATE]);
        $this->assertFalse($column_type->is_tag());
    }

    public function test_is_telephone_電話番号型の場合はtrueで返ること() {
        $column_type = Model_Columntype::findOne(['id' => Model_Columntype::TELEPHONE]);
        $this->assertTrue($column_type->is_telephone());
    }

    public function test_is_telephone_電話番号型ではない場合はfalseで返ること() {
        $column_type = Model_Columntype::findOne(['id' => Model_Columntype::TAG]);
        $this->assertFalse($column_type->is_telephone());
    }

    public function test_is_zip_郵便番号型の場合はtrueで返ること() {
        $column_type = Model_Columntype::findOne(['id' => Model_Columntype::ZIP]);
        $this->assertTrue($column_type->is_zip());
    }

    public function test_is_zip_郵便番号型ではない場合はfalseで返ること() {
        $column_type = Model_Columntype::findOne(['id' => Model_Columntype::EMAIL]);
        $this->assertFalse($column_type->is_zip());
    }

    public function test_is_date_日付型の場合はtrueで返ること() {
        $column_type = Model_Columntype::findOne(['id' => Model_Columntype::DATE]);
        $this->assertTrue($column_type->is_date());
    }

    public function test_is_date_日付型ではない場合はfalseで返ること() {
        $column_type = Model_Columntype::findOne(['id' => Model_Columntype::ZIP]);
        $this->assertFalse($column_type->is_date());
    }

    public function test_get_all_項目のタイプ一覧とその更新方法が取得できること() {
        $columntypes = Model_Columntype::get_all();
        $names = [];
        foreach ($columntypes as $columntype) {
            $names[] = $columntype->name;
        }
        $name_expected = [
            'テキスト型',
            'テキストエリア型',
            'Eメール型',
            '数値型',
            '日付型',
            'ブール型',
            'マスタ選択型',
            'タグ型',
            '電話番号型',
            '郵便番号型'
        ];
        $this->assertEquals($name_expected ,$names);

        // 各タイプの更新方法が取得できていることの確認（すべてまでは見ない）
        $update_method_names = [];
        foreach (array_shift($columntypes)->column_types_update_methods as $column_types_update_method) {
            $update_method_names[] = $column_types_update_method->update_method->name;
        }
        $update_method_name_expected = [
            '次の値で上書き',
            '次の値を追記',
        ];
        $this->assertEquals($update_method_name_expected ,$update_method_names);
    }
}