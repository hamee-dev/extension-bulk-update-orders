<?php

class Test_Model_Requestkey extends Testbase {

    protected $fetch_init_yaml = false;

    protected $dataset_filenames = ['model/requestkey.yml'];
    
    public function test_get_task_id_タスクIDが取得できること() {
        $request_key = new Model_Requestkey();
        $request_key->request_date = '2018-06-01';
        $request_key->request_number = 2;
        $this->assertEquals('20180601-2', $request_key->get_task_id());
    }

    public function test_create_request_key_object_リクエストキーが無い場合は新規に作成しそれを返すこと() {
        $request_key_count = count(Model_Requestkey::findAll([]));
        $request_key = Model_Requestkey::create_request_key_object(self::DUMMY_COMPANY_ID2);
        $this->assertEquals(1, $request_key->request_number);
        // Model_Requestkey::create_request_key_objectと、ここのdate("Y-m-d")が実行されるタイミングによってはテストが失敗する場合があり得る
        // staticのためスタブ化が難しく、また日付までなので発生する可能性は低いため、このままの実装とする
        $this->assertEquals(date("Y-m-d"), $request_key->request_date);
        $this->assertEquals(self::DUMMY_COMPANY_ID2, $request_key->company_id);
        // リクエストキーのレコードが増えていること
        $this->assertEquals($request_key_count + 1, count(Model_Requestkey::findAll([])));
    }

    public function test_create_request_key_object_リクエストキーがあり日付が同じ場合はrequest_numberがインクリメントされること() {
        Model_Requestkey::create_request_key_object(self::DUMMY_COMPANY_ID2);

        $request_key_count = count(Model_Requestkey::findAll([]));
        $request_key = Model_Requestkey::create_request_key_object(self::DUMMY_COMPANY_ID2);
        $this->assertEquals(2, $request_key->request_number);
        // Model_Requestkey::create_request_key_objectと、ここのdate("Y-m-d")が実行されるタイミングによってはテストが失敗する場合があり得る
        // staticのためスタブ化が難しく、また日付までなので発生する可能性は低いため、このままの実装とする
        $this->assertEquals(date("Y-m-d"), $request_key->request_date);
        // 新しいレコードができていないこと
        $this->assertEquals($request_key_count, count(Model_Requestkey::findAll([])));
    }

    public function test_create_request_key_object_リクエストキーがあり日付が変わった場合はrequest_dateが変わりrequest_numberが1になること() {
        $request_key = Model_Requestkey::findOne(['company_id' => self::DUMMY_COMPANY_ID1]);
        $old_date = $request_key->request_date;

        $request_key_count = count(Model_Requestkey::findAll([]));
        $request_key = Model_Requestkey::create_request_key_object(self::DUMMY_COMPANY_ID1);
        $this->assertEquals(1, $request_key->request_number);
        $this->assertNotEquals($old_date, $request_key->request_date);
        // Model_Requestkey::create_request_key_objectと、ここのdate("Y-m-d")が実行されるタイミングによってはテストが失敗する場合があり得る
        // staticのためスタブ化が難しく、また日付までなので発生する可能性は低いため、このままの実装とする
        $this->assertEquals(date("Y-m-d"), $request_key->request_date);
        // 新しいレコードができていないこと
        $this->assertEquals($request_key_count, count(Model_Requestkey::findAll([])));
    }
}