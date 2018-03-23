<?php
class Test_Model_Excludedreceiveorder extends Testbase {

    protected $dataset_filenames = ['model/executionbulkupdatesetting_base.yml' ,'model/excludedreceiveorder.yml'];

    public function test_get_excluded_receive_orders_引数に渡したsetting_idの設定の除外伝票一覧が配列で取得できること(){
        $result = Model_Excludedreceiveorder::get_excluded_receive_orders(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $this->assertEquals([1,11], $result);
    }

    public function test_get_excluded_receive_orders_引数に渡したsetting_idの設定が存在しない場合は空配列を返すこと(){
        $result = Model_Excludedreceiveorder::get_excluded_receive_orders(99999);
        $this->assertEquals([], $result);
    }
}