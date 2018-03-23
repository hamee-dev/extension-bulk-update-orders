<?php
class Test_Model_Executionbulkupdatesetting extends Testbase {

    protected $dataset_filenames = ['model/executionbulkupdatesetting_base.yml'];

    public function test_get_execution_user_id_対象レコードのuser_idを返すこと(){
        $setting = Model_Executionbulkupdatesetting::findOne(['id' => self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1]);
        $setting->user_id = '999';
        $this->assertSame('999', $setting->get_execution_user_id());
    }

    public function test_get_tasklist_引数で渡した企業のレコードのみを取得すること(){
        $execution_bulk_update_settings = Model_Executionbulkupdatesetting::get_tasklist(self::DUMMY_COMPANY_ID1);
        foreach($execution_bulk_update_settings as $execution_bulk_update_setting){
            $this->assertEquals(self::DUMMY_COMPANY_ID1, $execution_bulk_update_setting->company_id);
        }
    }

    public function test_get_tasklist_未実行のレコードのみを取得すること(){
        // 1つ実行済みにしておく
        $executed_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $executed_setting->executed = 1;
        $executed_setting->save();

        $execution_bulk_update_settings = Model_Executionbulkupdatesetting::get_tasklist(self::DUMMY_COMPANY_ID1);
        foreach($execution_bulk_update_settings as $execution_bulk_update_setting){
            $this->assertEquals(0, $execution_bulk_update_setting->executed);
        }
    }

    public function test_get_tasklist_該当のレコードがない場合は空配列を返すこと(){
        // DUMMY_COMPANY_ID1の企業のレコードを全て実行済みにする
        $settings = Model_Executionbulkupdatesetting::findAll(['company_id' => self::DUMMY_COMPANY_ID1]);
        foreach($settings as $setting){
            $setting->executed = 1;
            $setting->save();
        }

        $execution_bulk_update_settings = Model_Executionbulkupdatesetting::get_tasklist(self::DUMMY_COMPANY_ID1);
        $this->assertEquals([], $execution_bulk_update_settings);
    }

}