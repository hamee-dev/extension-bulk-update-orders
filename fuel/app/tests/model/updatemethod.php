<?php
class Test_Model_Updatemethod extends Testbase {
    /**
     * @dataProvider Model_Updatemethoddataprovider::data_provider_evaluate_valid
     */
    public function test_evaluate_意図した結果を返すこと($update_method_id, $ne_value, $update_value, $expect){
        // dataproviderに記載されているパターンを網羅すること
        $result = Model_Updatemethod::evaluate($update_method_id, $ne_value, $update_value);
        $this->assertEquals($expect, $result);
    }

    /**
     * @dataProvider Model_Updatemethoddataprovider::data_provider_evaluate_invalid
     * @expectedException InvalidArgumentException
     */
    public function test_evaluate_意図しない値が渡された場合に例外を投げること($update_method_id, $ne_value, $update_value){
        // dataproviderに記載されているパターンを網羅すること
        $result = Model_Updatemethod::evaluate($update_method_id, $ne_value, $update_value);
    }

    public function test_is_overwrite_上書き型の場合trueを返すこと(){
        $model_updatemethod = Model_Updatemethod::find(Model_Updatemethod::OVERWRITE);
        $result = $model_updatemethod->is_overwrite();
        $this->assertTrue($result);
    }

    public function test_is_overwrite_上書き型でない場合falseを返すこと(){
        $model_updatemethod = Model_Updatemethod::find(Model_Updatemethod::ADDWRITE);
        $result = $model_updatemethod->is_overwrite();
        $this->assertFalse($result);
    }

    public function test_is_addwrite_追記型の場合trueを返すこと(){
        $model_updatemethod = Model_Updatemethod::find(Model_Updatemethod::ADDWRITE);
        $result = $model_updatemethod->is_addwrite();
        $this->assertTrue($result);
    }

    public function test_is_addwrite_追記型でない場合falseを返すこと(){
        $model_updatemethod = Model_Updatemethod::find(Model_Updatemethod::OVERWRITE);
        $result = $model_updatemethod->is_addwrite();
        $this->assertFalse($result);
    }

    public function test_is_calc_計算型の場合trueを返すこと(){
        $model_updatemethod = Model_Updatemethod::find(Model_Updatemethod::ADDITION);
        $result = $model_updatemethod->is_calc();
        $this->assertTrue($result);
    }

    public function test_is_calc_計算型でない場合falseを返すこと(){
        $model_updatemethod = Model_Updatemethod::find(Model_Updatemethod::OVERWRITE);
        $result = $model_updatemethod->is_calc();
        $this->assertFalse($result);
    }

}