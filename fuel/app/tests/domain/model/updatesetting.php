<?php
class Test_Domain_Model_Updatesetting extends Testbase
{
    const DUMMY_REQUEST_KEY1 = 'REQUEST_KEY1';

    protected $dataset_filenames = [
        'domain/model/updatesetting.yml',
        'model/executionbulkupdatesetting_base.yml',
    ];

    protected $fetch_init_yaml = false;

    public function test_delete_指定した一括更新設定と関連テーブルのデータが論理削除されること() {
        $before_column_count = Model_Bulkupdatecolumn::count(
            [
                'bulk_update_setting_id' => self::DUMMY_BULK_UPDATE_SETTING_ID1
            ]
        );

        // テスト対象の削除処理
        Domain_Model_Updatesetting::delete(
            self::DUMMY_COMPANY_ID1,
            self::DUMMY_BULK_UPDATE_SETTING_ID1
        );

        // 一括更新設定が論理削除されていること
        $bulk_update_setting = Model_Bulkupdatesetting::find(
            self::DUMMY_BULK_UPDATE_SETTING_ID1
        );
        $this->assertNull($bulk_update_setting);

        // 一括更新設定の更新項目は削除されていないこと
        $after_column_count = Model_Bulkupdatecolumn::count(
            [
                'bulk_update_setting_id' => self::DUMMY_BULK_UPDATE_SETTING_ID1
            ]
        );
        $this->assertEquals($before_column_count, $after_column_count);
    }

    public function test_delete_指定した一括更新設定が存在しない場合例外エラーが発生しないで処理が終了すること() {

        // 削除処理前の一括更新設定の更新項目のレコード数を保持
        $before_column_count = Model_Bulkupdatecolumn::count(
            [
                'bulk_update_setting_id' => self::DUMMY_BULK_UPDATE_SETTING_ID1
            ]
        );

        // テスト対象の削除処理
        Domain_Model_Updatesetting::delete(
            self::DUMMY_COMPANY_ID2, // 異なる企業IDを設定
            self::DUMMY_BULK_UPDATE_SETTING_ID1
        );

        // 一括更新設定が削除されていないこと
        $bulk_update_setting = Model_Bulkupdatesetting::find(
            self::DUMMY_BULK_UPDATE_SETTING_ID1
        );
        $this->assertEquals(self::DUMMY_BULK_UPDATE_SETTING_ID1, $bulk_update_setting->id);

        // 一括更新設定の更新項目が削除されていないこと
        $after_column_count = Model_Bulkupdatecolumn::count(
            [
                'bulk_update_setting_id' => self::DUMMY_BULK_UPDATE_SETTING_ID1
            ]
        );
        $this->assertEquals($before_column_count, $after_column_count);
    }

    public function test_delete_指定した一括更新設定が他の設定のoriginal_bulk_update_setting_idに設定されている場合でも一括更新設定が論理削除できること() {
        $before_column_count = Model_Bulkupdatecolumn::count(
            [
                'bulk_update_setting_id' => self::DUMMY_BULK_UPDATE_SETTING_ID1
            ]
        );

        // original_bulk_update_setting_idにsetting1のIDを設定しておく
        $bulk_update_setting2 = Model_Bulkupdatesetting::find(
            self::DUMMY_BULK_UPDATE_SETTING_ID2
        );
        $bulk_update_setting2->original_bulk_update_setting_id = self::DUMMY_BULK_UPDATE_SETTING_ID1;
        $bulk_update_setting2->save();

        // テスト対象の削除処理
        Domain_Model_Updatesetting::delete(
            self::DUMMY_COMPANY_ID1,
            self::DUMMY_BULK_UPDATE_SETTING_ID1
        );

        // 設定2のoriginal_bulk_update_setting_idがnullになっていないこと
        $bulk_update_setting2 = Model_Bulkupdatesetting::find(
            self::DUMMY_BULK_UPDATE_SETTING_ID2
        );
        $this->assertNotEquals(null, $bulk_update_setting2->original_bulk_update_setting_id);

        // 一括更新設定が論理削除されていること
        $bulk_update_setting1 = Model_Bulkupdatesetting::find(
            self::DUMMY_BULK_UPDATE_SETTING_ID1
        );
        $this->assertNull($bulk_update_setting1);

        // 一括更新設定の更新項目が削除されていないこと
        $after_column_count = Model_Bulkupdatecolumn::count(
            [
                'bulk_update_setting_id' => self::DUMMY_BULK_UPDATE_SETTING_ID1
            ]
        );
        $this->assertEquals($before_column_count, $after_column_count);
    }

    public function test_hard_delete_指定した一括更新設定と関連テーブルのデータが物理削除されること() {
        $before_column_count = Model_Bulkupdatecolumn::count(
            [
                'bulk_update_setting_id' => self::DUMMY_BULK_UPDATE_SETTING_ID1
            ]
        );

        // テスト対象の削除処理
        Domain_Model_Updatesetting::hard_delete(
            self::DUMMY_COMPANY_ID1,
            self::DUMMY_BULK_UPDATE_SETTING_ID1
        );

        // 一括更新設定が物理削除されていること
        $bulk_update_setting = Model_Bulkupdatesetting::find(
            self::DUMMY_BULK_UPDATE_SETTING_ID1
        );
        $this->assertNull($bulk_update_setting);

        // 一括更新設定の更新項目が削除されていること
        $bulk_update_columns = Model_Bulkupdatecolumn::findAll(
            [
                'bulk_update_setting_id' => self::DUMMY_BULK_UPDATE_SETTING_ID1
            ]
        );
        $this->assertEquals([], $bulk_update_columns);
    }

    public function test_hard_delete_指定した一括更新設定が存在しない場合例外エラーが発生しないで処理が終了すること() {

        // 削除処理前の一括更新設定の更新項目のレコード数を保持
        $before_column_count = Model_Bulkupdatecolumn::count(
            [
                'bulk_update_setting_id' => self::DUMMY_BULK_UPDATE_SETTING_ID1
            ]
        );

        // テスト対象の削除処理
        Domain_Model_Updatesetting::hard_delete(
            self::DUMMY_COMPANY_ID2, // 異なる企業IDを設定
            self::DUMMY_BULK_UPDATE_SETTING_ID1
        );

        // 一括更新設定が削除されていないこと
        $bulk_update_setting = Model_Bulkupdatesetting::find(
            self::DUMMY_BULK_UPDATE_SETTING_ID1
        );
        $this->assertEquals(self::DUMMY_BULK_UPDATE_SETTING_ID1, $bulk_update_setting->id);

        // 一括更新設定の更新項目が削除されていないこと
        $after_column_count = Model_Bulkupdatecolumn::count(
            [
                'bulk_update_setting_id' => self::DUMMY_BULK_UPDATE_SETTING_ID1
            ]
        );
        $this->assertEquals($before_column_count, $after_column_count);
    }

    public function test_hard_delete_指定した一括更新設定が他の設定のoriginal_bulk_update_setting_idに設定されている場合、original_bulk_update_setting_idをnullにし指定した一括更新設定が削除できること() {
        $before_column_count = Model_Bulkupdatecolumn::count(
            [
                'bulk_update_setting_id' => self::DUMMY_BULK_UPDATE_SETTING_ID1
            ]
        );

        // original_bulk_update_setting_idにsetting1のIDを設定しておく
        $bulk_update_setting2 = Model_Bulkupdatesetting::find(
            self::DUMMY_BULK_UPDATE_SETTING_ID2
        );
        $bulk_update_setting2->original_bulk_update_setting_id = self::DUMMY_BULK_UPDATE_SETTING_ID1;
        $bulk_update_setting2->save();

        // テスト対象の削除処理
        Domain_Model_Updatesetting::hard_delete(
            self::DUMMY_COMPANY_ID1,
            self::DUMMY_BULK_UPDATE_SETTING_ID1
        );

        // 設定2のoriginal_bulk_update_setting_idがnullになっていること
        $bulk_update_setting2 = Model_Bulkupdatesetting::find(
            self::DUMMY_BULK_UPDATE_SETTING_ID2
        );
        $this->assertNull($bulk_update_setting2->original_bulk_update_setting_id);

        // 一括更新設定が物理削除されていること
        $bulk_update_setting1 = Model_Bulkupdatesetting::find(
            self::DUMMY_BULK_UPDATE_SETTING_ID1
        );
        $this->assertNull($bulk_update_setting1);

        // 一括更新設定の更新項目が削除されていること
        $bulk_update_columns = Model_Bulkupdatecolumn::findAll(
            [
                'bulk_update_setting_id' => self::DUMMY_BULK_UPDATE_SETTING_ID1
            ]
        );
        $this->assertEquals([], $bulk_update_columns);
    }

    public function test_delete_指定した一括更新設定と関連テーブルのデータ削除に失敗した場合は例外エラーが発生すること() {
        $this->markTestSkipped('staticメソッドのためスタブ化が難しいためスキップします');
    }

    public function test_copy_更新設定が複製されること(){
        $name = '複製後の設定名';
        [$result, $message] = Domain_Model_Updatesetting::copy(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1, self::DUMMY_BULK_UPDATE_SETTING_ID1, $name);
        $this->assertTrue($result);
        $this->assertEquals(\Lang::get('message.success.copy'), $message);
        // データが新規作成(複製)されていること
        $search_bulk_update_setting = Model_Bulkupdatesetting::findOne(['name' => $name]);

        // 保存されたデータが元のレコードとnot_copy_columns以外等価であること
        $bulk_update_setting = Model_Bulkupdatesetting::find(self::DUMMY_BULK_UPDATE_SETTING_ID1);
        $not_copy_columns = ['id', 'name', 'created_user_id', 'last_updated_user_id', 'created_at', 'updated_at'];
        foreach($bulk_update_setting as $column => $value){
            if(in_array($column, $not_copy_columns, true)) continue;
            $this->assertEquals($value, $search_bulk_update_setting[$column]);
        }
        $this->assertEquals($name, $search_bulk_update_setting['name']);
        $this->assertEquals(self::DUMMY_USER_ID1, $search_bulk_update_setting['created_user_id']);
        $this->assertEquals(self::DUMMY_USER_ID1, $search_bulk_update_setting['last_updated_user_id']);
        $this->assertEquals(count($bulk_update_setting->bulk_update_columns), count($search_bulk_update_setting->bulk_update_columns));
    }

    public function test_copy_複製元のレコードが見つからない場合はemptyのエラーを返すこと(){
        $name = '複製後の設定名';
        [$result, $message] = Domain_Model_Updatesetting::copy(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1, 'not_exist_id', $name);
        $this->assertFalse($result);
        $this->assertEquals(\Lang::get('message.error.bulk_update_setting_empty'), $message);
    }

    public function test_copy_複製に失敗した場合はfalseとエラーメッセージを返すこと() {
        $this->markTestSkipped('staticメソッドのためスタブ化が難しいためスキップします');
    }

    public function test_update_name_更新成功した場合名称が変わっていてtrueと成功の文言を返すこと(){
        $name = '変更後の設定名';
        $before_bulk_update_setting = Model_Bulkupdatesetting::find(self::DUMMY_BULK_UPDATE_SETTING_ID1);
        [$result, $message] = Domain_Model_Updatesetting::update_name(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1, self::DUMMY_BULK_UPDATE_SETTING_ID1, $name);
        $this->assertTrue($result);
        $this->assertEquals(\Lang::get('message.success.name_update', [$before_bulk_update_setting->name, $name]), $message);

        $after_bulk_update_setting = Model_Bulkupdatesetting::find(self::DUMMY_BULK_UPDATE_SETTING_ID1);
        $this->assertEquals($name, $after_bulk_update_setting->name);
    }

    public function test_update_name_更新成功した場合、最終更新日と最終更新者が更新されていること(){
        // すぐに更新すると時間が変わらない場合があるのでスリープを行う
        sleep(1);
        $name = '変更後の設定名';
        $before_bulk_update_setting = Model_Bulkupdatesetting::find(self::DUMMY_BULK_UPDATE_SETTING_ID1);
        Domain_Model_Updatesetting::update_name(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID2, self::DUMMY_BULK_UPDATE_SETTING_ID1, $name);
        $after_bulk_update_setting = Model_Bulkupdatesetting::find(self::DUMMY_BULK_UPDATE_SETTING_ID1);

        $this->assertEquals(self::DUMMY_USER_ID2, $after_bulk_update_setting->last_updated_user_id);
        $this->assertNotEquals($before_bulk_update_setting->updated_at, $after_bulk_update_setting->updated_at);
    }

    public function test_update_name_更新対象のレコードが見つからない場合はemptyのエラーを返すこと(){
        $name = '変更後の設定名';
        [$result, $message] = Domain_Model_Updatesetting::update_name(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1, 'not_exist_id', $name);
        $this->assertFalse($result);
        $this->assertEquals(\Lang::get('message.error.bulk_update_setting_empty'), $message);
    }

    public function test_update_name_更新に失敗した場合はfalseとエラーメッセージを返すこと() {
        $this->markTestSkipped('staticメソッドのためスタブ化が難しいためスキップします');
    }

    public function test_execution_受注伝票検索に失敗した場合は処理を中止し失敗した状態を表すDomain_Value_Executionresultオブジェクトを返すこと(){
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);

        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['apiExecute'])
            ->getMock();

        // 受注伝票検索のスタブ(検索に失敗した場合)
        $fields = implode(\Model_Receiveordercolumn::get_physical_names(), ',');
        $fields = $fields.',receive_order_id,receive_order_last_modified_date,receive_order_order_status_id,receive_order_confirm_ids';
        $search_params = ['fields' => $fields, 'extension_execution_id' => $execution_bulk_update_setting->extension_execution_id];
        $search_api_response = [
            'result'  => Client_Neapi::RESULT_ERROR,
            'code'    => Client_Neapi::ERROR_CODE_EXCEPTION,
            'message' => '原因不明のエラーです',
        ];
        $ne_api_stub->expects($this->once())
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_SEARCH), $this->equalTo($search_params))
            ->will($this->returnValue($search_api_response));

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        $stub = $this->getMockBuilder(Domain_Model_Updatesetting::class)
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->with($this->equalTo(self::DUMMY_USER_ID1))
            ->will($this->returnValue($ne_api_stub));

        $domain_value_executionresult = $stub->execution($execution_bulk_update_setting);
        $api_response = [
            'code' => Client_Neapi::ERROR_CODE_EXCEPTION,
            'message' => '原因不明のエラーです',
        ];
        $sent_count = 0;
        $excluded_id_and_reason = [];
        $expect = new Domain_Value_Executionresult($api_response, $sent_count, $excluded_id_and_reason);
        $this->assertEquals($expect, $domain_value_executionresult);
    }

    public function test_execution_受注伝票検索で検索結果が0件だった場合には処理を中止し失敗した状態をDomain_Value_Executionresultオブジェクトを返すこと(){
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);

        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['apiExecute'])
            ->getMock();

        // 受注伝票検索のスタブ(検索結果が0件だった場合)
        $fields = implode(\Model_Receiveordercolumn::get_physical_names(), ',');
        $fields = $fields.',receive_order_id,receive_order_last_modified_date,receive_order_order_status_id,receive_order_confirm_ids';
        $search_params = ['fields' => $fields, 'extension_execution_id' => $execution_bulk_update_setting->extension_execution_id];
        $search_api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'count'  => 0,
        ];
        $ne_api_stub->expects($this->once())
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_SEARCH), $this->equalTo($search_params))
            ->will($this->returnValue($search_api_response));

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        $stub = $this->getMockBuilder(Domain_Model_Updatesetting::class)
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->with($this->equalTo(self::DUMMY_USER_ID1))
            ->will($this->returnValue($ne_api_stub));

        $domain_value_executionresult = $stub->execution($execution_bulk_update_setting);
        $api_response = [
            'code'    => Client_Neapi::ERROR_CODE_EXCEPTION,
            'message' => '有効な受注伝票が1件もありませんでした',
        ];
        $sent_count = 0;
        $excluded_id_and_reason = [];
        $expect = new Domain_Value_Executionresult($api_response, $sent_count, $excluded_id_and_reason);
        $this->assertEquals($expect, $domain_value_executionresult);
    }

    public function test_execution_一括更新処理に成功した場合その状態のDomain_Value_Executionresultオブジェクトを返すこと(){
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $execution_bulk_update_setting->allow_optimistic_lock_update_retry = 0;

        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['apiExecute'])
            ->getMock();

        // 受注伝票検索のスタブ
        $fields = implode(\Model_Receiveordercolumn::get_physical_names(), ',');
        $fields = $fields.',receive_order_id,receive_order_last_modified_date,receive_order_order_status_id,receive_order_confirm_ids';
        $search_params = ['fields' => $fields, 'extension_execution_id' => $execution_bulk_update_setting->extension_execution_id];
        $search_api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'count'  => 2,
            'data'   => [
                ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
            ],
        ];

        // 一括更新のスタブ
        $receive_order_bulkupdate_xml = '<?xml version="1.0" encoding="utf-8"?>
<root><receiveorder receive_order_id="1" receive_order_last_modified_date="2018-05-11 13:06:00"><receiveorder_base><receive_order_shop_id>適当な値</receive_order_shop_id><receive_order_shop_cut_form_id>適当な値</receive_order_shop_cut_form_id><receive_order_gruoping_tag>[dummy_tag][【済】テスト実行1]</receive_order_gruoping_tag></receiveorder_base><receiveorder_option/></receiveorder><receiveorder receive_order_id="2" receive_order_last_modified_date="2018-05-11 13:06:00"><receiveorder_base><receive_order_shop_id>適当な値</receive_order_shop_id><receive_order_shop_cut_form_id>適当な値</receive_order_shop_cut_form_id><receive_order_gruoping_tag>[dummy_tag][【済】テスト実行1]</receive_order_gruoping_tag></receiveorder_base><receiveorder_option/></receiveorder></root>
';

        $bulkupdate_params = [
            'data_type' => 'gz',
            'data' => gzencode($receive_order_bulkupdate_xml),
            'receive_order_shipped_update_flag' => $execution_bulk_update_setting->allow_update_shipment_confirmed
        ];
        $bulkupdate_api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'message' => '',
        ];

        // apiExecuteに対して引数が異なる複数の設定を行うことはできないためat(0), at(1)のように呼ばれる順番でスタブ化することとする
        // @see https://code.i-harness.com/ja/q/53b03a
        $ne_api_stub->expects($this->at(0))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_SEARCH), $this->equalTo($search_params))
            ->will($this->returnValue($search_api_response));
        $ne_api_stub->expects($this->at(1))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_BULKUPDATE), $this->equalTo($bulkupdate_params))
            ->will($this->returnValue($bulkupdate_api_response));

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        $stub = $this->getMockBuilder(Domain_Model_Updatesetting::class)
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->with($this->equalTo(self::DUMMY_USER_ID1))
            ->will($this->returnValue($ne_api_stub));

        $domain_value_executionresult = $stub->execution($execution_bulk_update_setting);
        $api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'message' => '',
        ];
        $sent_count = 2;
        $excluded_id_and_reason = [];
        $expect = new Domain_Value_Executionresult($api_response, $sent_count, $excluded_id_and_reason);
        $this->assertEquals($expect, $domain_value_executionresult);
    }

    public function test_execution_リトライフラグoffの場合はリトライせずにAPIの結果を反映させたDomain_Value_Executionresultオブジェクトを返すこと(){
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $execution_bulk_update_setting->allow_optimistic_lock_update_retry = 0;

        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['apiExecute'])
            ->getMock();

        // 受注伝票検索のスタブ
        $fields = implode(\Model_Receiveordercolumn::get_physical_names(), ',');
        $fields = $fields.',receive_order_id,receive_order_last_modified_date,receive_order_order_status_id,receive_order_confirm_ids';
        $search_params = ['fields' => $fields, 'extension_execution_id' => $execution_bulk_update_setting->extension_execution_id];
        $search_api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'count'  => 2,
            'data'   => [
                ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
            ],
        ];

        // 一括更新のスタブ
        $receive_order_bulkupdate_xml = '<?xml version="1.0" encoding="utf-8"?>
<root><receiveorder receive_order_id="1" receive_order_last_modified_date="2018-05-11 13:06:00"><receiveorder_base><receive_order_shop_id>適当な値</receive_order_shop_id><receive_order_shop_cut_form_id>適当な値</receive_order_shop_cut_form_id><receive_order_gruoping_tag>[dummy_tag][【済】テスト実行1]</receive_order_gruoping_tag></receiveorder_base><receiveorder_option/></receiveorder><receiveorder receive_order_id="2" receive_order_last_modified_date="2018-05-11 13:06:00"><receiveorder_base><receive_order_shop_id>適当な値</receive_order_shop_id><receive_order_shop_cut_form_id>適当な値</receive_order_shop_cut_form_id><receive_order_gruoping_tag>[dummy_tag][【済】テスト実行1]</receive_order_gruoping_tag></receiveorder_base><receiveorder_option/></receiveorder></root>
';
        $bulkupdate_params = [
            'data_type' => 'gz',
            'data' => gzencode($receive_order_bulkupdate_xml),
            'receive_order_shipped_update_flag' => $execution_bulk_update_setting->allow_update_shipment_confirmed
        ];
        $bulkupdate_api_response = [
            'result' => Client_Neapi::RESULT_ERROR,
            'code' => Client_Neapi::ERROR_CODE_BULKUPDATE,
            'message' => [
                ['receive_order_id' => 1, 'code' => Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => '最終更新日が更新されています'],
                ['receive_order_id' => 2, 'code' => Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => '最終更新日が更新されています'],
            ],
        ];

        // apiExecuteに対して引数が異なる複数の設定を行うことはできないためat(0), at(1)のように呼ばれる順番でスタブ化することとする
        // @see https://code.i-harness.com/ja/q/53b03a
        $ne_api_stub->expects($this->at(0))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_SEARCH), $this->equalTo($search_params))
            ->will($this->returnValue($search_api_response));
        $ne_api_stub->expects($this->at(1))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_BULKUPDATE), $this->equalTo($bulkupdate_params))
            ->will($this->returnValue($bulkupdate_api_response));

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        $stub = $this->getMockBuilder(Domain_Model_Updatesetting::class)
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->with($this->equalTo(self::DUMMY_USER_ID1))
            ->will($this->returnValue($ne_api_stub));

        $domain_value_executionresult = $stub->execution($execution_bulk_update_setting);
        $api_response = [
            'result' => Client_Neapi::RESULT_ERROR,
            'code' => Client_Neapi::ERROR_CODE_BULKUPDATE,
            'message' => [
                ['receive_order_id' => 1, 'code' => Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => '最終更新日が更新されています'],
                ['receive_order_id' => 2, 'code' => Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => '最終更新日が更新されています'],
            ],
        ];
        $sent_count = 2;
        $excluded_id_and_reason = [];
        $expect = new Domain_Value_Executionresult($api_response, $sent_count, $excluded_id_and_reason);
        $this->assertEquals($expect, $domain_value_executionresult);
    }

    public function test_execution_リトライフラグonでリトライ対象があり1回目で全て成功した場合はそのリトライ結果を反映させたDomain_Value_Executionresultオブジェクトを返すこと(){
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $execution_bulk_update_setting->allow_optimistic_lock_update_retry = 1;

        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['apiExecute'])
            ->getMock();

        // 受注伝票検索のスタブ
        $fields = implode(\Model_Receiveordercolumn::get_physical_names(), ',');
        $fields = $fields.',receive_order_id,receive_order_last_modified_date,receive_order_order_status_id,receive_order_confirm_ids';
        $search_params = ['fields' => $fields, 'extension_execution_id' => $execution_bulk_update_setting->extension_execution_id];
        $search_api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'count'  => 2,
            'data'   => [
                ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
            ],
        ];

        // 一括更新のスタブ
        $receive_order_bulkupdate_xml = '<?xml version="1.0" encoding="utf-8"?>
<root><receiveorder receive_order_id="1" receive_order_last_modified_date="2018-05-11 13:06:00"><receiveorder_base><receive_order_shop_id>適当な値</receive_order_shop_id><receive_order_shop_cut_form_id>適当な値</receive_order_shop_cut_form_id><receive_order_gruoping_tag>[dummy_tag][【済】テスト実行1]</receive_order_gruoping_tag></receiveorder_base><receiveorder_option/></receiveorder><receiveorder receive_order_id="2" receive_order_last_modified_date="2018-05-11 13:06:00"><receiveorder_base><receive_order_shop_id>適当な値</receive_order_shop_id><receive_order_shop_cut_form_id>適当な値</receive_order_shop_cut_form_id><receive_order_gruoping_tag>[dummy_tag][【済】テスト実行1]</receive_order_gruoping_tag></receiveorder_base><receiveorder_option/></receiveorder></root>
';
        $bulkupdate_params = [
            'data_type' => 'gz',
            'data' => gzencode($receive_order_bulkupdate_xml),
            'receive_order_shipped_update_flag' => $execution_bulk_update_setting->allow_update_shipment_confirmed
        ];
        $bulkupdate_api_response = [
            'result' => Client_Neapi::RESULT_ERROR,
            'code' => Client_Neapi::ERROR_CODE_BULKUPDATE,
            'message' => [
                ['receive_order_id' => 1, 'code' => Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => '最終更新日が更新されています'],
                ['receive_order_id' => 2, 'code' => Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => '最終更新日が更新されています'],
            ],
        ];

        // リトライ1回目の受注伝票件数のスタブ
        $retry1_search_params = ['fields' => $fields, 'receive_order_id-in' => '1,2'];
        $retry1_search_api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'count'  => 2,
            'data'   => [
                ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
            ],
        ];

        // リトライ1回目の一括更新のスタブ
        $retry1_bulkupdate_api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'message' => '',
        ];

        // apiExecuteに対して引数が異なる複数の設定を行うことはできないためat(0), at(1)のように呼ばれる順番でスタブ化することとする
        // @see https://code.i-harness.com/ja/q/53b03a
        $ne_api_stub->expects($this->at(0))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_SEARCH), $this->equalTo($search_params))
            ->will($this->returnValue($search_api_response));
        $ne_api_stub->expects($this->at(1))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_BULKUPDATE), $this->equalTo($bulkupdate_params))
            ->will($this->returnValue($bulkupdate_api_response));
        $ne_api_stub->expects($this->at(2))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_SEARCH), $this->equalTo($retry1_search_params))
            ->will($this->returnValue($retry1_search_api_response));
        $ne_api_stub->expects($this->at(3))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_BULKUPDATE), $this->equalTo($bulkupdate_params))
            ->will($this->returnValue($retry1_bulkupdate_api_response));

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        $stub = $this->getMockBuilder(Domain_Model_Updatesetting::class)
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->with($this->equalTo(self::DUMMY_USER_ID1))
            ->will($this->returnValue($ne_api_stub));

        $domain_value_executionresult = $stub->execution($execution_bulk_update_setting);
        $api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'message' => '',
        ];
        $sent_count = 2;
        $excluded_id_and_reason = [];
        $expect = new Domain_Value_Executionresult($api_response, $sent_count, $excluded_id_and_reason);
        $this->assertEquals($expect, $domain_value_executionresult);
    }

    public function test_execution_リトライフラグonでリトライ対象があり1回目でそのリトライ対象が除外になった場合はそのリトライ結果を反映させたDomain_Value_Executionresultオブジェクトを返すこと(){
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $execution_bulk_update_setting->allow_optimistic_lock_update_retry = 1;

        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['apiExecute'])
            ->getMock();

        // 受注伝票検索のスタブ
        $fields = implode(\Model_Receiveordercolumn::get_physical_names(), ',');
        $fields = $fields.',receive_order_id,receive_order_last_modified_date,receive_order_order_status_id,receive_order_confirm_ids';
        $search_params = ['fields' => $fields, 'extension_execution_id' => $execution_bulk_update_setting->extension_execution_id];
        // 1件成功1件失敗
        $search_api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'count'  => 2,
            'data'   => [
                ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
            ],
        ];

        // 一括更新のスタブ
        $receive_order_bulkupdate_xml = '<?xml version="1.0" encoding="utf-8"?>
<root><receiveorder receive_order_id="1" receive_order_last_modified_date="2018-05-11 13:06:00"><receiveorder_base><receive_order_shop_id>適当な値</receive_order_shop_id><receive_order_shop_cut_form_id>適当な値</receive_order_shop_cut_form_id><receive_order_gruoping_tag>[dummy_tag][【済】テスト実行1]</receive_order_gruoping_tag></receiveorder_base><receiveorder_option/></receiveorder><receiveorder receive_order_id="2" receive_order_last_modified_date="2018-05-11 13:06:00"><receiveorder_base><receive_order_shop_id>適当な値</receive_order_shop_id><receive_order_shop_cut_form_id>適当な値</receive_order_shop_cut_form_id><receive_order_gruoping_tag>[dummy_tag][【済】テスト実行1]</receive_order_gruoping_tag></receiveorder_base><receiveorder_option/></receiveorder></root>
';
        $bulkupdate_params = [
            'data_type' => 'gz',
            'data' => gzencode($receive_order_bulkupdate_xml),
            'receive_order_shipped_update_flag' => $execution_bulk_update_setting->allow_update_shipment_confirmed
        ];
        $bulkupdate_api_response = [
            'result' => Client_Neapi::RESULT_ERROR,
            'code' => Client_Neapi::ERROR_CODE_BULKUPDATE,
            'message' => [
                ['receive_order_id' => 2, 'code' => Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => '最終更新日が更新されています'],
            ],
        ];

        // リトライ1回目の受注伝票件数のスタブ
        // リトライ後、実行済みタグがついているため除外となる
        $retry1_search_params = ['fields' => $fields, 'receive_order_id-in' => '2'];
        $retry1_search_api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'count'  => 1,
            'data'   => [
                ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[【済】テスト実行1]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
            ],
        ];

        // apiExecuteに対して引数が異なる複数の設定を行うことはできないためat(0), at(1)のように呼ばれる順番でスタブ化することとする
        // @see https://code.i-harness.com/ja/q/53b03a
        $ne_api_stub->expects($this->at(0))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_SEARCH), $this->equalTo($search_params))
            ->will($this->returnValue($search_api_response));
        $ne_api_stub->expects($this->at(1))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_BULKUPDATE), $this->equalTo($bulkupdate_params))
            ->will($this->returnValue($bulkupdate_api_response));
        $ne_api_stub->expects($this->at(2))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_SEARCH), $this->equalTo($retry1_search_params))
            ->will($this->returnValue($retry1_search_api_response));

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        $stub = $this->getMockBuilder(Domain_Model_Updatesetting::class)
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->with($this->equalTo(self::DUMMY_USER_ID1))
            ->will($this->returnValue($ne_api_stub));

        $domain_value_executionresult = $stub->execution($execution_bulk_update_setting);
        $api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'message' => '',
        ];
        // sent_countが1件になっていること（リトライ後除外されたものを総数から引いていること）
        $sent_count = 1;
        $excluded_id_and_reason = [
            '2' => ['receive_order_id' => '2', 'excluded_reason' => __em('excluded_reason.duplicate_execution')],
        ];
        $expect = new Domain_Value_Executionresult($api_response, $sent_count, $excluded_id_and_reason);
        // 1件成功1件除外の状態になっていること
        $this->assertEquals($expect, $domain_value_executionresult);
    }

    public function test_execution_リトライフラグonでリトライ対象があり1回目で受注伝票検索に失敗した場合、リトライ前のレスポンスのDomain_Value_Executionresultオブジェクトを返すこと(){
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $execution_bulk_update_setting->allow_optimistic_lock_update_retry = 1;

        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['apiExecute'])
            ->getMock();

        // 受注伝票検索のスタブ
        $fields = implode(\Model_Receiveordercolumn::get_physical_names(), ',');
        $fields = $fields.',receive_order_id,receive_order_last_modified_date,receive_order_order_status_id,receive_order_confirm_ids';
        $search_params = ['fields' => $fields, 'extension_execution_id' => $execution_bulk_update_setting->extension_execution_id];
        // 1件成功1件失敗
        $search_api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'count'  => 2,
            'data'   => [
                ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
            ],
        ];

        // 一括更新のスタブ
        $receive_order_bulkupdate_xml = '<?xml version="1.0" encoding="utf-8"?>
<root><receiveorder receive_order_id="1" receive_order_last_modified_date="2018-05-11 13:06:00"><receiveorder_base><receive_order_shop_id>適当な値</receive_order_shop_id><receive_order_shop_cut_form_id>適当な値</receive_order_shop_cut_form_id><receive_order_gruoping_tag>[dummy_tag][【済】テスト実行1]</receive_order_gruoping_tag></receiveorder_base><receiveorder_option/></receiveorder><receiveorder receive_order_id="2" receive_order_last_modified_date="2018-05-11 13:06:00"><receiveorder_base><receive_order_shop_id>適当な値</receive_order_shop_id><receive_order_shop_cut_form_id>適当な値</receive_order_shop_cut_form_id><receive_order_gruoping_tag>[dummy_tag][【済】テスト実行1]</receive_order_gruoping_tag></receiveorder_base><receiveorder_option/></receiveorder></root>
';
        $bulkupdate_params = [
            'data_type' => 'gz',
            'data' => gzencode($receive_order_bulkupdate_xml),
            'receive_order_shipped_update_flag' => $execution_bulk_update_setting->allow_update_shipment_confirmed
        ];
        $bulkupdate_api_response = [
            'result' => Client_Neapi::RESULT_ERROR,
            'code' => Client_Neapi::ERROR_CODE_BULKUPDATE,
            'message' => [
                ['receive_order_id' => 2, 'code' => Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => '最終更新日が更新されています'],
            ],
        ];

        // リトライ1回目の受注伝票件数のスタブ
        // API通信に失敗させる
        $retry1_search_params = ['fields' => $fields, 'receive_order_id-in' => '2'];
        $retry1_search_api_response = [
            'result' => Client_Neapi::RESULT_ERROR,
            'code' => Client_Neapi::ERROR_CODE_EXCEPTION,
            'message' => '原因不明のエラー',
        ];

        // apiExecuteに対して引数が異なる複数の設定を行うことはできないためat(0), at(1)のように呼ばれる順番でスタブ化することとする
        // @see https://code.i-harness.com/ja/q/53b03a
        $ne_api_stub->expects($this->at(0))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_SEARCH), $this->equalTo($search_params))
            ->will($this->returnValue($search_api_response));
        $ne_api_stub->expects($this->at(1))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_BULKUPDATE), $this->equalTo($bulkupdate_params))
            ->will($this->returnValue($bulkupdate_api_response));
        $ne_api_stub->expects($this->at(2))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_SEARCH), $this->equalTo($retry1_search_params))
            ->will($this->returnValue($retry1_search_api_response));

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        $stub = $this->getMockBuilder(Domain_Model_Updatesetting::class)
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->with($this->equalTo(self::DUMMY_USER_ID1))
            ->will($this->returnValue($ne_api_stub));

        $domain_value_executionresult = $stub->execution($execution_bulk_update_setting);
        $sent_count = 2;
        $expect = new Domain_Value_Executionresult($bulkupdate_api_response, $sent_count, []);
        // リトライ前のレスポンスを返していること
        $this->assertEquals($expect, $domain_value_executionresult);
    }

    public function test_execution_リトライフラグonでリトライ対象があり1回目で受注伝票検索結果が0件だった場合、リトライ前のレスポンスのDomain_Value_Executionresultオブジェクトを返すこと(){
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $execution_bulk_update_setting->allow_optimistic_lock_update_retry = 1;

        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['apiExecute'])
            ->getMock();

        // 受注伝票検索のスタブ
        $fields = implode(\Model_Receiveordercolumn::get_physical_names(), ',');
        $fields = $fields.',receive_order_id,receive_order_last_modified_date,receive_order_order_status_id,receive_order_confirm_ids';
        $search_params = ['fields' => $fields, 'extension_execution_id' => $execution_bulk_update_setting->extension_execution_id];
        // 1件成功1件失敗
        $search_api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'count'  => 2,
            'data'   => [
                ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
            ],
        ];

        // 一括更新のスタブ
        $receive_order_bulkupdate_xml = '<?xml version="1.0" encoding="utf-8"?>
<root><receiveorder receive_order_id="1" receive_order_last_modified_date="2018-05-11 13:06:00"><receiveorder_base><receive_order_shop_id>適当な値</receive_order_shop_id><receive_order_shop_cut_form_id>適当な値</receive_order_shop_cut_form_id><receive_order_gruoping_tag>[dummy_tag][【済】テスト実行1]</receive_order_gruoping_tag></receiveorder_base><receiveorder_option/></receiveorder><receiveorder receive_order_id="2" receive_order_last_modified_date="2018-05-11 13:06:00"><receiveorder_base><receive_order_shop_id>適当な値</receive_order_shop_id><receive_order_shop_cut_form_id>適当な値</receive_order_shop_cut_form_id><receive_order_gruoping_tag>[dummy_tag][【済】テスト実行1]</receive_order_gruoping_tag></receiveorder_base><receiveorder_option/></receiveorder></root>
';
        $bulkupdate_params = [
            'data_type' => 'gz',
            'data' => gzencode($receive_order_bulkupdate_xml),
            'receive_order_shipped_update_flag' => $execution_bulk_update_setting->allow_update_shipment_confirmed
        ];
        $bulkupdate_api_response = [
            'result' => Client_Neapi::RESULT_ERROR,
            'code' => Client_Neapi::ERROR_CODE_BULKUPDATE,
            'message' => [
                ['receive_order_id' => 2, 'code' => Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => '最終更新日が更新されています'],
            ],
        ];

        // リトライ1回目の受注伝票件数のスタブ
        // 有効な伝票が1件もない状態
        $retry1_search_params = ['fields' => $fields, 'receive_order_id-in' => '2'];
        $retry1_search_api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'count'  => 0,
            'data'   => [],
        ];

        // apiExecuteに対して引数が異なる複数の設定を行うことはできないためat(0), at(1)のように呼ばれる順番でスタブ化することとする
        // @see https://code.i-harness.com/ja/q/53b03a
        $ne_api_stub->expects($this->at(0))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_SEARCH), $this->equalTo($search_params))
            ->will($this->returnValue($search_api_response));
        $ne_api_stub->expects($this->at(1))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_BULKUPDATE), $this->equalTo($bulkupdate_params))
            ->will($this->returnValue($bulkupdate_api_response));
        $ne_api_stub->expects($this->at(2))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_SEARCH), $this->equalTo($retry1_search_params))
            ->will($this->returnValue($retry1_search_api_response));

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        $stub = $this->getMockBuilder(Domain_Model_Updatesetting::class)
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->with($this->equalTo(self::DUMMY_USER_ID1))
            ->will($this->returnValue($ne_api_stub));

        $domain_value_executionresult = $stub->execution($execution_bulk_update_setting);
        $sent_count = 2;
        $expect = new Domain_Value_Executionresult($bulkupdate_api_response, $sent_count, []);
        // リトライ前のレスポンスを返していること
        $this->assertEquals($expect, $domain_value_executionresult);
    }

    public function test_execution_リトライフラグonでリトライ対象があり2回目で全て成功した場合はそのリトライ結果を反映させたDomain_Value_Executionresultオブジェクトを返すこと(){
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $execution_bulk_update_setting->allow_optimistic_lock_update_retry = 1;

        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['apiExecute'])
            ->getMock();

        // 受注伝票検索のスタブ
        $fields = implode(\Model_Receiveordercolumn::get_physical_names(), ',');
        $fields = $fields.',receive_order_id,receive_order_last_modified_date,receive_order_order_status_id,receive_order_confirm_ids';
        $search_params = ['fields' => $fields, 'extension_execution_id' => $execution_bulk_update_setting->extension_execution_id];
        $search_api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'count'  => 2,
            'data'   => [
                ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
            ],
        ];

        // 一括更新のスタブ
        $receive_order_bulkupdate_xml = '<?xml version="1.0" encoding="utf-8"?>
<root><receiveorder receive_order_id="1" receive_order_last_modified_date="2018-05-11 13:06:00"><receiveorder_base><receive_order_shop_id>適当な値</receive_order_shop_id><receive_order_shop_cut_form_id>適当な値</receive_order_shop_cut_form_id><receive_order_gruoping_tag>[dummy_tag][【済】テスト実行1]</receive_order_gruoping_tag></receiveorder_base><receiveorder_option/></receiveorder><receiveorder receive_order_id="2" receive_order_last_modified_date="2018-05-11 13:06:00"><receiveorder_base><receive_order_shop_id>適当な値</receive_order_shop_id><receive_order_shop_cut_form_id>適当な値</receive_order_shop_cut_form_id><receive_order_gruoping_tag>[dummy_tag][【済】テスト実行1]</receive_order_gruoping_tag></receiveorder_base><receiveorder_option/></receiveorder></root>
';
        $bulkupdate_params = [
            'data_type' => 'gz',
            'data' => gzencode($receive_order_bulkupdate_xml),
            'receive_order_shipped_update_flag' => $execution_bulk_update_setting->allow_update_shipment_confirmed
        ];
        $bulkupdate_api_response = [
            'result' => Client_Neapi::RESULT_ERROR,
            'code' => Client_Neapi::ERROR_CODE_BULKUPDATE,
            'message' => [
                ['receive_order_id' => 1, 'code' => Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => '最終更新日が更新されています'],
                ['receive_order_id' => 2, 'code' => Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => '最終更新日が更新されています'],
            ],
        ];

        // リトライ1回目の受注伝票件数のスタブ
        $retry1_search_params = ['fields' => $fields, 'receive_order_id-in' => '1,2'];
        $retry1_search_api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'count'  => 2,
            'data'   => [
                ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
            ],
        ];

        // リトライ1回目の一括更新のスタブ
        $retry1_bulkupdate_api_response = [
            'result' => Client_Neapi::RESULT_ERROR,
            'code' => Client_Neapi::ERROR_CODE_BULKUPDATE,
            'message' => [
                ['receive_order_id' => 2, 'code' => Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => '最終更新日が更新されています'],
            ],
        ];

        // リトライ2回目の受注伝票件数のスタブ
        $retry2_search_params = ['fields' => $fields, 'receive_order_id-in' => '2'];
        $retry2_search_api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'count'  => 1,
            'data'   => [
                ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
            ],
        ];

        // リトライ2回目の一括更新のスタブ
        $retry2_receive_order_bulkupdate_xml = '<?xml version="1.0" encoding="utf-8"?>
<root><receiveorder receive_order_id="2" receive_order_last_modified_date="2018-05-11 13:06:00"><receiveorder_base><receive_order_shop_id>適当な値</receive_order_shop_id><receive_order_shop_cut_form_id>適当な値</receive_order_shop_cut_form_id><receive_order_gruoping_tag>[dummy_tag][【済】テスト実行1]</receive_order_gruoping_tag></receiveorder_base><receiveorder_option/></receiveorder></root>
';
        $retry2_bulkupdate_params = [
            'data_type' => 'gz',
            'data' => gzencode($retry2_receive_order_bulkupdate_xml),
            'receive_order_shipped_update_flag' => $execution_bulk_update_setting->allow_update_shipment_confirmed
        ];
        $retry2_bulkupdate_api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'message' => '',
        ];

        // apiExecuteに対して引数が異なる複数の設定を行うことはできないためat(0), at(1)のように呼ばれる順番でスタブ化することとする
        // @see https://code.i-harness.com/ja/q/53b03a
        $ne_api_stub->expects($this->at(0))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_SEARCH), $this->equalTo($search_params))
            ->will($this->returnValue($search_api_response));
        $ne_api_stub->expects($this->at(1))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_BULKUPDATE), $this->equalTo($bulkupdate_params))
            ->will($this->returnValue($bulkupdate_api_response));
        $ne_api_stub->expects($this->at(2))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_SEARCH), $this->equalTo($retry1_search_params))
            ->will($this->returnValue($retry1_search_api_response));
        $ne_api_stub->expects($this->at(3))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_BULKUPDATE), $this->equalTo($bulkupdate_params))
            ->will($this->returnValue($retry1_bulkupdate_api_response));
        $ne_api_stub->expects($this->at(4))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_SEARCH), $this->equalTo($retry2_search_params))
            ->will($this->returnValue($retry2_search_api_response));
        $ne_api_stub->expects($this->at(5))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_BULKUPDATE), $this->equalTo($retry2_bulkupdate_params))
            ->will($this->returnValue($retry2_bulkupdate_api_response));

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        $stub = $this->getMockBuilder(Domain_Model_Updatesetting::class)
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->with($this->equalTo(self::DUMMY_USER_ID1))
            ->will($this->returnValue($ne_api_stub));

        $domain_value_executionresult = $stub->execution($execution_bulk_update_setting);
        $api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'message' => '',
        ];
        $sent_count = 2;
        $excluded_id_and_reason = [];
        $expect = new Domain_Value_Executionresult($api_response, $sent_count, $excluded_id_and_reason);
        $this->assertEquals($expect, $domain_value_executionresult);
    }

    public function test_execution_リトライフラグonでリトライ対象があり2回目でAPIで何らかのエラーが起きた場合はリトライ1回目の結果を反映したDomain_Value_Executionresultオブジェクトを返すこと(){
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $execution_bulk_update_setting->allow_optimistic_lock_update_retry = 1;

        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['apiExecute'])
            ->getMock();

        // 受注伝票検索のスタブ
        $fields = implode(\Model_Receiveordercolumn::get_physical_names(), ',');
        $fields = $fields.',receive_order_id,receive_order_last_modified_date,receive_order_order_status_id,receive_order_confirm_ids';
        $search_params = ['fields' => $fields, 'extension_execution_id' => $execution_bulk_update_setting->extension_execution_id];
        $search_api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'count'  => 2,
            'data'   => [
                ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
            ],
        ];

        // 一括更新のスタブ
        $receive_order_bulkupdate_xml = '<?xml version="1.0" encoding="utf-8"?>
<root><receiveorder receive_order_id="1" receive_order_last_modified_date="2018-05-11 13:06:00"><receiveorder_base><receive_order_shop_id>適当な値</receive_order_shop_id><receive_order_shop_cut_form_id>適当な値</receive_order_shop_cut_form_id><receive_order_gruoping_tag>[dummy_tag][【済】テスト実行1]</receive_order_gruoping_tag></receiveorder_base><receiveorder_option/></receiveorder><receiveorder receive_order_id="2" receive_order_last_modified_date="2018-05-11 13:06:00"><receiveorder_base><receive_order_shop_id>適当な値</receive_order_shop_id><receive_order_shop_cut_form_id>適当な値</receive_order_shop_cut_form_id><receive_order_gruoping_tag>[dummy_tag][【済】テスト実行1]</receive_order_gruoping_tag></receiveorder_base><receiveorder_option/></receiveorder></root>
';
        $bulkupdate_params = [
            'data_type' => 'gz',
            'data' => gzencode($receive_order_bulkupdate_xml),
            'receive_order_shipped_update_flag' => $execution_bulk_update_setting->allow_update_shipment_confirmed
        ];
        $bulkupdate_api_response = [
            'result' => Client_Neapi::RESULT_ERROR,
            'code' => Client_Neapi::ERROR_CODE_BULKUPDATE,
            'message' => [
                ['receive_order_id' => 1, 'code' => Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => '最終更新日が更新されています'],
                ['receive_order_id' => 2, 'code' => Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => '最終更新日が更新されています'],
            ],
        ];

        // リトライ1回目の受注伝票件数のスタブ
        $retry1_search_params = ['fields' => $fields, 'receive_order_id-in' => '1,2'];
        $retry1_search_api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'count'  => 2,
            'data'   => [
                ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
            ],
        ];

        // リトライ1回目の一括更新のスタブ
        $retry1_bulkupdate_api_response = [
            'result' => Client_Neapi::RESULT_ERROR,
            'code' => Client_Neapi::ERROR_CODE_BULKUPDATE,
            'message' => [
                ['receive_order_id' => 2, 'code' => Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => '最終更新日が更新されています'],
            ],
        ];

        // リトライ2回目の受注伝票件数のスタブ
        $retry2_search_params = ['fields' => $fields, 'receive_order_id-in' => '2'];
        $retry2_search_api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'count'  => 1,
            'data'   => [
                ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
            ],
        ];

        // リトライ2回目の一括更新のスタブ
        $retry2_receive_order_bulkupdate_xml = '<?xml version="1.0" encoding="utf-8"?>
<root><receiveorder receive_order_id="2" receive_order_last_modified_date="2018-05-11 13:06:00"><receiveorder_base><receive_order_shop_id>適当な値</receive_order_shop_id><receive_order_shop_cut_form_id>適当な値</receive_order_shop_cut_form_id><receive_order_gruoping_tag>[dummy_tag][【済】テスト実行1]</receive_order_gruoping_tag></receiveorder_base><receiveorder_option/></receiveorder></root>
';
        $retry2_bulkupdate_params = [
            'data_type' => 'gz',
            'data' => gzencode($retry2_receive_order_bulkupdate_xml),
            'receive_order_shipped_update_flag' => $execution_bulk_update_setting->allow_update_shipment_confirmed
        ];
        $retry2_bulkupdate_api_response = [
            'result' => Client_Neapi::RESULT_ERROR,
            'code' => Client_Neapi::ERROR_CODE_MAIN_FUNCTION_MAINTENANCE,
            'message' => 'メイン機能がメンテナンス中です',
        ];

        // apiExecuteに対して引数が異なる複数の設定を行うことはできないためat(0), at(1)のように呼ばれる順番でスタブ化することとする
        // @see https://code.i-harness.com/ja/q/53b03a
        $ne_api_stub->expects($this->at(0))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_SEARCH), $this->equalTo($search_params))
            ->will($this->returnValue($search_api_response));
        $ne_api_stub->expects($this->at(1))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_BULKUPDATE), $this->equalTo($bulkupdate_params))
            ->will($this->returnValue($bulkupdate_api_response));
        $ne_api_stub->expects($this->at(2))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_SEARCH), $this->equalTo($retry1_search_params))
            ->will($this->returnValue($retry1_search_api_response));
        $ne_api_stub->expects($this->at(3))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_BULKUPDATE), $this->equalTo($bulkupdate_params))
            ->will($this->returnValue($retry1_bulkupdate_api_response));
        $ne_api_stub->expects($this->at(4))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_SEARCH), $this->equalTo($retry2_search_params))
            ->will($this->returnValue($retry2_search_api_response));
        $ne_api_stub->expects($this->at(5))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_BULKUPDATE), $this->equalTo($retry2_bulkupdate_params))
            ->will($this->returnValue($retry2_bulkupdate_api_response));

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        $stub = $this->getMockBuilder(Domain_Model_Updatesetting::class)
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->with($this->equalTo(self::DUMMY_USER_ID1))
            ->will($this->returnValue($ne_api_stub));

        $domain_value_executionresult = $stub->execution($execution_bulk_update_setting);
        $api_response = [
            'result' => Client_Neapi::RESULT_ERROR,
            'code' => Client_Neapi::ERROR_CODE_BULKUPDATE,
            'message' => [
                ['receive_order_id' => 2, 'code' => Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => '最終更新日が更新されています'],
            ],
        ];
        $sent_count = 2;
        $excluded_id_and_reason = [];
        $expect = new Domain_Value_Executionresult($api_response, $sent_count, $excluded_id_and_reason);
        $this->assertEquals($expect, $domain_value_executionresult);
    }

    public function test_execution_リトライフラグonでリトライ対象があり3回リトライしたが3回とも失敗した場合、そのリトライ結果を反映させたDomain_Value_Executionresultオブジェクトを返すこと(){
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $execution_bulk_update_setting->allow_optimistic_lock_update_retry = 1;

        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['apiExecute'])
            ->getMock();

        // 受注伝票検索のスタブ
        $fields = implode(\Model_Receiveordercolumn::get_physical_names(), ',');
        $fields = $fields.',receive_order_id,receive_order_last_modified_date,receive_order_order_status_id,receive_order_confirm_ids';
        $search_params = ['fields' => $fields, 'extension_execution_id' => $execution_bulk_update_setting->extension_execution_id];
        $search_api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'count'  => 2,
            'data'   => [
                ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
            ],
        ];

        // 一括更新のスタブ
        $receive_order_bulkupdate_xml = '<?xml version="1.0" encoding="utf-8"?>
<root><receiveorder receive_order_id="1" receive_order_last_modified_date="2018-05-11 13:06:00"><receiveorder_base><receive_order_shop_id>適当な値</receive_order_shop_id><receive_order_shop_cut_form_id>適当な値</receive_order_shop_cut_form_id><receive_order_gruoping_tag>[dummy_tag][【済】テスト実行1]</receive_order_gruoping_tag></receiveorder_base><receiveorder_option/></receiveorder><receiveorder receive_order_id="2" receive_order_last_modified_date="2018-05-11 13:06:00"><receiveorder_base><receive_order_shop_id>適当な値</receive_order_shop_id><receive_order_shop_cut_form_id>適当な値</receive_order_shop_cut_form_id><receive_order_gruoping_tag>[dummy_tag][【済】テスト実行1]</receive_order_gruoping_tag></receiveorder_base><receiveorder_option/></receiveorder></root>
';
        $bulkupdate_params = [
            'data_type' => 'gz',
            'data' => gzencode($receive_order_bulkupdate_xml),
            'receive_order_shipped_update_flag' => $execution_bulk_update_setting->allow_update_shipment_confirmed
        ];
        $bulkupdate_api_response = [
            'result' => Client_Neapi::RESULT_ERROR,
            'code' => Client_Neapi::ERROR_CODE_BULKUPDATE,
            'message' => [
                ['receive_order_id' => 1, 'code' => Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => '最終更新日が更新されています'],
                ['receive_order_id' => 2, 'code' => Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => '最終更新日が更新されています'],
            ],
        ];

        // リトライ1回目の受注伝票件数のスタブ
        $retry1_search_params = ['fields' => $fields, 'receive_order_id-in' => '1,2'];
        $retry1_search_api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'count'  => 2,
            'data'   => [
                ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
                ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
            ],
        ];

        // apiExecuteに対して引数が異なる複数の設定を行うことはできないためat(0), at(1)のように呼ばれる順番でスタブ化することとする
        // @see https://code.i-harness.com/ja/q/53b03a
        $ne_api_stub->expects($this->at(0))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_SEARCH), $this->equalTo($search_params))
            ->will($this->returnValue($search_api_response));
        $ne_api_stub->expects($this->at(1))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_BULKUPDATE), $this->equalTo($bulkupdate_params))
            ->will($this->returnValue($bulkupdate_api_response));
        $ne_api_stub->expects($this->at(2))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_SEARCH), $this->equalTo($retry1_search_params))
            ->will($this->returnValue($retry1_search_api_response));
        $ne_api_stub->expects($this->at(3))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_BULKUPDATE), $this->equalTo($bulkupdate_params))
            ->will($this->returnValue($bulkupdate_api_response));
        $ne_api_stub->expects($this->at(4))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_SEARCH), $this->equalTo($retry1_search_params))
            ->will($this->returnValue($retry1_search_api_response));
        $ne_api_stub->expects($this->at(5))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_BULKUPDATE), $this->equalTo($bulkupdate_params))
            ->will($this->returnValue($bulkupdate_api_response));
        $ne_api_stub->expects($this->at(6))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_SEARCH), $this->equalTo($retry1_search_params))
            ->will($this->returnValue($retry1_search_api_response));
        $ne_api_stub->expects($this->at(7))
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_BULKUPDATE), $this->equalTo($bulkupdate_params))
            ->will($this->returnValue($bulkupdate_api_response));

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        $stub = $this->getMockBuilder(Domain_Model_Updatesetting::class)
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->with($this->equalTo(self::DUMMY_USER_ID1))
            ->will($this->returnValue($ne_api_stub));

        $domain_value_executionresult = $stub->execution($execution_bulk_update_setting);
        $api_response = [
            'result' => Client_Neapi::RESULT_ERROR,
            'code' => Client_Neapi::ERROR_CODE_BULKUPDATE,
            'message' => [
                ['receive_order_id' => 1, 'code' => Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => '最終更新日が更新されています'],
                ['receive_order_id' => 2, 'code' => Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => '最終更新日が更新されています'],
            ],
        ];
        $sent_count = 2;
        $excluded_id_and_reason = [];
        $expect = new Domain_Value_Executionresult($api_response, $sent_count, $excluded_id_and_reason);
        $this->assertEquals($expect, $domain_value_executionresult);
    }

    public function test__request_receive_order_bulk_update_指定されてパラメータのdata項目の内容を圧縮してリクエストすること() {
        // テスト対象のメソッドの引数に指定する更新する受注伝票の情報
        $update_target_orders = [
            [
                'receive_order_total_amount' => '10000',
                'receive_order_note' => '備考欄を上書きする設定',
                'receive_order_worker_text' => '作業用欄に適当なテキスト',
                'receive_order_option_noshi' => 'のし欄に適当なテキスト',
                'receive_order_id' => '1',
                'receive_order_last_modified_date' => '2018-04-26 17:22:25',
                'receive_order_order_status_id' => '10',
                'receive_order_confirm_ids' => 'AG',
            ],
            [
                'receive_order_total_amount' => '10000',
                'receive_order_note' => '備考欄を上書きする設定',
                'receive_order_worker_text' => '作業用欄に適当なテキスト',
                'receive_order_option_noshi' => 'のし欄に適当なテキスト',
                'receive_order_id' => '2',
                'receive_order_last_modified_date' => '2018-04-26 17:22:52',
                'receive_order_order_status_id' => '50',
                'receive_order_confirm_ids' => 'AH',
            ],
        ];

        // テスト対象のメソッドの引数に指定する出荷確定済みの受注伝票の更新許可情報
        $allow_update_shipment_confirmed = '123456';

        // テスト対象のメソッドの中で_get_receive_order_bulkupdate_xmlの戻り値想定
        // （_get_receive_order_bulkupdate_xmlがprivateメソッドのためモック化ができない）
        $receive_order_bulkupdate_xml = '<?xml version="1.0" encoding="utf-8"?>
<root><receiveorder receive_order_id="1" receive_order_last_modified_date="2018-04-26 17:22:25"><receiveorder_base><receive_order_total_amount>10000</receive_order_total_amount><receive_order_note>備考欄を上書きする設定</receive_order_note><receive_order_worker_text>作業用欄に適当なテキスト</receive_order_worker_text><receive_order_confirm_ids>AG</receive_order_confirm_ids></receiveorder_base><receiveorder_option><receive_order_option_noshi>のし欄に適当なテキスト</receive_order_option_noshi></receiveorder_option></receiveorder><receiveorder receive_order_id="2" receive_order_last_modified_date="2018-04-26 17:22:52"><receiveorder_base><receive_order_total_amount>10000</receive_order_total_amount><receive_order_note>備考欄を上書きする設定</receive_order_note><receive_order_worker_text>作業用欄に適当なテキスト</receive_order_worker_text><receive_order_confirm_ids>AH</receive_order_confirm_ids></receiveorder_base><receiveorder_option><receive_order_option_noshi>のし欄に適当なテキスト</receive_order_option_noshi></receiveorder_option></receiveorder></root>
';

        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['apiExecute'])
            ->getMock();

        // Client_NeapiのapiExecuteメソッドに指定するパラメータ
        $bulkupdate_params = [
            'data_type' => 'gz',
            'data' => gzencode($receive_order_bulkupdate_xml),
            'receive_order_shipped_update_flag' => $allow_update_shipment_confirmed
        ];

        $bulkupdate_api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'message' => '',
        ];

        $ne_api_stub->expects($this->once())
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_RECEIVEORDER_BASE_BULKUPDATE), $this->equalTo($bulkupdate_params))
            ->will($this->returnValue($bulkupdate_api_response));

        $args = [
            $ne_api_stub,
            $update_target_orders,
            $allow_update_shipment_confirmed
        ];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_request_receive_order_bulk_update');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        $this->assertEquals($bulkupdate_api_response, $result);
    }

    public function test_convert_更新設定を反映した結果のDomain_Value_Convertresultオブジェクトを返すこと(){
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $receive_orders = [
            ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
            ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
        ];
        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $domain_value_convertresult = $domain_model_updatesetting->convert($execution_bulk_update_setting, $receive_orders);

        $update_target_orders = [
            '1' => [
                'receive_order_gruoping_tag'       => '[dummy_tag][【済】テスト実行1]',
                'receive_order_shop_id'            => '適当な値',
                'receive_order_shop_cut_form_id'   => '適当な値',
                'receive_order_id'                 => '1',
                'receive_order_last_modified_date' => '2018-05-11 13:06:00',
            ],
            '2' => [
                'receive_order_gruoping_tag'       => '[dummy_tag][【済】テスト実行1]',
                'receive_order_shop_id'            => '適当な値',
                'receive_order_shop_cut_form_id'   => '適当な値',
                'receive_order_id'                 => '2',
                'receive_order_last_modified_date' => '2018-05-11 13:06:00',
            ],
        ];
        $excluded_id_and_reason = [];
        $expect = new Domain_Value_Convertresult($update_target_orders, $excluded_id_and_reason);
        $this->assertEquals($expect, $domain_value_convertresult);
    }

    public function test_convert_引数の受注伝票情報が存在しない場合に更新対象が空のDomain_Value_Convertresultオブジェクトを返すこと(){
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $receive_orders = [];
        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $domain_value_convertresult = $domain_model_updatesetting->convert($execution_bulk_update_setting, $receive_orders);

        $update_target_orders = [];
        $excluded_id_and_reason = [];
        $expect = new Domain_Value_Convertresult($update_target_orders, $excluded_id_and_reason);
        $this->assertEquals($expect, $domain_value_convertresult);
    }

    /**
     * @dataProvider Domain_model_Updatesettingprovider::data_provider_convert_set_params_exception
     * @expectedException PhpErrorException
     */
    public function test_convert_引数の受注伝票情報の項目が不足している場合に例外が発生すること($unset_column) {
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $receive_orders = [
            ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
        ];
        unset($receive_orders[0][$unset_column]);

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $domain_value_convertresult = $domain_model_updatesetting->convert($execution_bulk_update_setting, $receive_orders);

    }

    public function test_convert_除外テーブルに登録されている伝票を除外して更新設定を反映した結果のDomain_Value_Convertresultオブジェクトを返すこと(){
        // 除外設定を保存
        $excluded_receive_order = new Model_Excludedreceiveorder();
        $excluded_receive_order->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        $excluded_receive_order->receive_order_id = 1;
        $excluded_receive_order->save();

        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $receive_orders = [
            ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
            ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
        ];
        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $domain_value_convertresult = $domain_model_updatesetting->convert($execution_bulk_update_setting, $receive_orders);
        // 使い終わった除外設定を除去
        $excluded_receive_order->delete();

        $update_target_orders = [
            '2' => [
                'receive_order_gruoping_tag'       => '[dummy_tag][【済】テスト実行1]',
                'receive_order_shop_id'            => '適当な値',
                'receive_order_shop_cut_form_id'   => '適当な値',
                'receive_order_id'                 => '2',
                'receive_order_last_modified_date' => '2018-05-11 13:06:00',
            ],
        ];
        $excluded_id_and_reason = [
            '1' => ['receive_order_id' => '1', 'excluded_reason' => __em('excluded_reason.user_selection')],
        ];
        $expect = new Domain_Value_Convertresult($update_target_orders, $excluded_id_and_reason);
        $this->assertEquals($expect, $domain_value_convertresult);
    }

    public function test_convert_対象が全て除外されている場合、全て除外した結果のDomain_Value_Convertresultオブジェクトを返すこと(){
        // 除外設定を保存
        $excluded_receive_order1 = new Model_Excludedreceiveorder();
        $excluded_receive_order1->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        $excluded_receive_order1->receive_order_id = 1;
        $excluded_receive_order1->save();
        $excluded_receive_order2 = new Model_Excludedreceiveorder();
        $excluded_receive_order2->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        $excluded_receive_order2->receive_order_id = 2;
        $excluded_receive_order2->save();

        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $receive_orders = [
            ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
            ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
        ];
        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $domain_value_convertresult = $domain_model_updatesetting->convert($execution_bulk_update_setting, $receive_orders);
        // 使い終わった除外設定を除去
        $excluded_receive_order1->delete();
        $excluded_receive_order2->delete();

        $update_target_orders = [];
        $excluded_id_and_reason = [
            '1' => ['receive_order_id' => '1', 'excluded_reason' => __em('excluded_reason.user_selection')],
            '2' => ['receive_order_id' => '2', 'excluded_reason' => __em('excluded_reason.user_selection')],
        ];
        $expect = new Domain_Value_Convertresult($update_target_orders, $excluded_id_and_reason);
        $this->assertEquals($expect, $domain_value_convertresult);
    }

    public function test_convert_出荷確定済み伝票を更新しない設定の時、出荷確定済み伝票を除外し更新設定を反映した結果のDomain_Value_Convertresultオブジェクトを返すこと(){
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        // 受注伝票1が出荷確定済み
        $receive_orders = [
            ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '50', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
            ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
        ];
        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $domain_value_convertresult = $domain_model_updatesetting->convert($execution_bulk_update_setting, $receive_orders);

        $update_target_orders = [
            '2' => [
                'receive_order_gruoping_tag'       => '[dummy_tag][【済】テスト実行1]',
                'receive_order_shop_id'            => '適当な値',
                'receive_order_shop_cut_form_id'   => '適当な値',
                'receive_order_id'                 => '2',
                'receive_order_last_modified_date' => '2018-05-11 13:06:00',
            ],
        ];
        $excluded_id_and_reason = [
            '1' => ['receive_order_id' => '1', 'excluded_reason' => __em('excluded_reason.shipped')],
        ];
        $expect = new Domain_Value_Convertresult($update_target_orders, $excluded_id_and_reason);
        $this->assertEquals($expect, $domain_value_convertresult);
    }

    public function test_convert_出荷確定済み伝票の更新を許可する設定の時、出荷確定済み伝票を除外せず更新設定を反映した結果のDomain_Value_Convertresultオブジェクトを返すこと(){
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $execution_bulk_update_setting->allow_update_shipment_confirmed = Client_Neapi::RECEIVE_ORDER_SHIPPED_UPDATE_FLAG_TRUE;
        // 受注伝票1が出荷確定済み
        $receive_orders = [
            ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '50', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
            ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
        ];
        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $domain_value_convertresult = $domain_model_updatesetting->convert($execution_bulk_update_setting, $receive_orders);

        $update_target_orders = [
            '1' => [
                'receive_order_gruoping_tag'       => '[dummy_tag][【済】テスト実行1]',
                'receive_order_shop_id'            => '適当な値',
                'receive_order_shop_cut_form_id'   => '適当な値',
                'receive_order_id'                 => '1',
                'receive_order_last_modified_date' => '2018-05-11 13:06:00',
            ],
            '2' => [
                'receive_order_gruoping_tag'       => '[dummy_tag][【済】テスト実行1]',
                'receive_order_shop_id'            => '適当な値',
                'receive_order_shop_cut_form_id'   => '適当な値',
                'receive_order_id'                 => '2',
                'receive_order_last_modified_date' => '2018-05-11 13:06:00',
            ],
        ];
        $excluded_id_and_reason = [];
        $expect = new Domain_Value_Convertresult($update_target_orders, $excluded_id_and_reason);
        $this->assertEquals($expect, $domain_value_convertresult);
    }

    public function test_convert_同一名の一括更新を実行済みの場合は除外し、更新設定を反映した結果のDomain_Value_Convertresultオブジェクトを返すこと(){
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        // 受注伝票2は実行済み
        $receive_orders = [
            ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
            ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag][【済】テスト実行1]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
        ];
        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $domain_value_convertresult = $domain_model_updatesetting->convert($execution_bulk_update_setting, $receive_orders);

        $update_target_orders = [
            '1' => [
                'receive_order_gruoping_tag'       => '[dummy_tag][【済】テスト実行1]',
                'receive_order_shop_id'            => '適当な値',
                'receive_order_shop_cut_form_id'   => '適当な値',
                'receive_order_id'                 => '1',
                'receive_order_last_modified_date' => '2018-05-11 13:06:00',
            ],
        ];
        $excluded_id_and_reason = [
            '2' => ['receive_order_id' => '2', 'excluded_reason' => __em('excluded_reason.duplicate_execution')],
        ];
        $expect = new Domain_Value_Convertresult($update_target_orders, $excluded_id_and_reason);
        $this->assertEquals($expect, $domain_value_convertresult);
    }

    public function test_convert_実行名が空であれば実行済みタグをつけず更新設定を反映した結果のDomain_Value_Convertresultオブジェクトを返すこと(){
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        // 一時的にnameを空文字にする
        $execution_bulk_update_setting->name = '';
        $receive_orders = [
            ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
            ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
        ];
        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $domain_value_convertresult = $domain_model_updatesetting->convert($execution_bulk_update_setting, $receive_orders);

        // 実行済みタグがつかないこと
        $update_target_orders = [
            '1' => [
                'receive_order_shop_id'            => '適当な値',
                'receive_order_shop_cut_form_id'   => '適当な値',
                'receive_order_id'                 => '1',
                'receive_order_last_modified_date' => '2018-05-11 13:06:00',
            ],
            '2' => [
                'receive_order_shop_id'            => '適当な値',
                'receive_order_shop_cut_form_id'   => '適当な値',
                'receive_order_id'                 => '2',
                'receive_order_last_modified_date' => '2018-05-11 13:06:00',
            ],
        ];
        $excluded_id_and_reason = [];
        $expect = new Domain_Value_Convertresult($update_target_orders, $excluded_id_and_reason);
        $this->assertEquals($expect, $domain_value_convertresult);
    }

    public function test_convert_プレビュー画面時にはbulk_update_settingの方のレコードで処理を行うこと(){
        $bulk_update_setting = Model_Bulkupdatesetting::find(self::DUMMY_BULK_UPDATE_SETTING_ID1);
        $receive_orders = [
            ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
            ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
        ];
        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $domain_value_convertresult = $domain_model_updatesetting->convert($bulk_update_setting, $receive_orders);

        $update_target_orders = [
            '1' => [
                'receive_order_gruoping_tag'       => '[dummy_tag][【済】設定名]',
                'receive_order_shop_id'            => 'TEST_VALUE1',
                'receive_order_shop_cut_form_id'   => 'TEST_VALUE2',
                'receive_order_id'                 => '1',
                'receive_order_last_modified_date' => '2018-05-11 13:06:00',
            ],
            '2' => [
                'receive_order_gruoping_tag'       => '[dummy_tag][【済】設定名]',
                'receive_order_shop_id'            => 'TEST_VALUE1',
                'receive_order_shop_cut_form_id'   => 'TEST_VALUE2',
                'receive_order_id'                 => '2',
                'receive_order_last_modified_date' => '2018-05-11 13:06:00',
            ],
        ];
        $excluded_id_and_reason = [];
        $expect = new Domain_Value_Convertresult($update_target_orders, $excluded_id_and_reason);
        $this->assertEquals($expect, $domain_value_convertresult);
    }

    public function test_convert_プレビュー画面時には除外対象でもそこで処理を切り上げずにexcluded_id_and_reasonに理由を入れた後に更新設定を反映した結果を返すこと(){
        $bulk_update_setting = Model_Bulkupdatesetting::find(self::DUMMY_BULK_UPDATE_SETTING_ID1);
        // 受注伝票2は出荷確定済み
        $receive_orders = [
            ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
            ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '50', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
        ];
        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $domain_value_convertresult = $domain_model_updatesetting->convert($bulk_update_setting, $receive_orders);

        $update_target_orders = [
            '1' => [
                'receive_order_gruoping_tag'       => '[dummy_tag][【済】設定名]',
                'receive_order_shop_id'            => 'TEST_VALUE1',
                'receive_order_shop_cut_form_id'   => 'TEST_VALUE2',
                'receive_order_id'                 => '1',
                'receive_order_last_modified_date' => '2018-05-11 13:06:00',
            ],
            '2' => [
                'receive_order_gruoping_tag'       => '[dummy_tag][【済】設定名]',
                'receive_order_shop_id'            => 'TEST_VALUE1',
                'receive_order_shop_cut_form_id'   => 'TEST_VALUE2',
                'receive_order_id'                 => '2',
                'receive_order_last_modified_date' => '2018-05-11 13:06:00',
            ],
        ];
        $excluded_id_and_reason = [
            '2' => ['receive_order_id' => '2', 'excluded_reason' => '出荷確定済みによる除外'],
        ];
        $expect = new Domain_Value_Convertresult($update_target_orders, $excluded_id_and_reason);
        $this->assertEquals($expect, $domain_value_convertresult);
    }

    public function test_convert_プレビュー画面時には1伝票に除外理由が複数ある場合には先勝ちになり理由は1つしか入っていないこと(){
        $bulk_update_setting = Model_Bulkupdatesetting::find(self::DUMMY_BULK_UPDATE_SETTING_ID1);
        // 受注伝票2は出荷確定済み かつ 実行済み
        $receive_orders = [
            ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_order_status_id' => '10', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
            ['receive_order_id' => '2', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag][【済】設定名]', 'receive_order_shop_cut_form_id' => '2', 'receive_order_order_status_id' => '50', 'receive_order_last_modified_date' => '2018-05-11 13:06:00'],
        ];
        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $domain_value_convertresult = $domain_model_updatesetting->convert($bulk_update_setting, $receive_orders);

        $update_target_orders = [
            '1' => [
                'receive_order_gruoping_tag'       => '[dummy_tag][【済】設定名]',
                'receive_order_shop_id'            => 'TEST_VALUE1',
                'receive_order_shop_cut_form_id'   => 'TEST_VALUE2',
                'receive_order_id'                 => '1',
                'receive_order_last_modified_date' => '2018-05-11 13:06:00',
            ],
            '2' => [
                'receive_order_gruoping_tag'       => '[dummy_tag][【済】設定名][【済】設定名]',
                'receive_order_shop_id'            => 'TEST_VALUE1',
                'receive_order_shop_cut_form_id'   => 'TEST_VALUE2',
                'receive_order_id'                 => '2',
                'receive_order_last_modified_date' => '2018-05-11 13:06:00',
            ],
        ];
        $excluded_id_and_reason = [
            '2' => ['receive_order_id' => '2', 'excluded_reason' => '出荷確定済みによる除外'],
        ];
        $expect = new Domain_Value_Convertresult($update_target_orders, $excluded_id_and_reason);
        $this->assertEquals($expect, $domain_value_convertresult);
    }

    public function test__convert_更新内容を反映した受注伝票を返すこと(){
        $update_columns = Model_Executionbulkupdatecolumn::findAll(['execution_bulk_update_setting_id' => self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1]);
        $receive_order = ['receive_order_id' => '1', 'receive_order_shop_id' => '1', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1'];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = null;
        $fraction_id = null;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $execution_bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        // テスト実行1は店舗と受注番号を「適当な値」に上書きする設定
        $expect = [
            'receive_order_shop_id'          => '適当な値',
            'receive_order_shop_cut_form_id' => '適当な値',
        ];
        $this->assertEquals($expect, $result);
    }

    /**
     * @dataProvider Domain_model_Updatesettingprovider::data_provider__convert_set_params_exception
     * @expectedException PhpErrorException
     */
    public function test__convert_引数の受注伝票情報の項目が不足している場合に例外が発生すること($unset_column) {
        $update_columns = [];
        $receive_order_columns = Model_Receiveordercolumn::get_all_columns();
        foreach($receive_order_columns as $receive_order_column) {
            $update_column = new Model_Executionbulkupdatecolumn();
            $update_column->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
            $update_column->receive_order_column_id = $receive_order_column->id;
            $update_column->update_method_id = Model_Updatemethod::OVERWRITE;
            $update_column->update_value = '1';
            $update_columns[] = $update_column;
        }
        $physical_names = array_column($receive_order_columns, 'physical_name');
        $additional_column_physical_names = array_keys(Model_Receiveordercolumn::get_additional_columns());
        $physical_names = array_merge($physical_names, $additional_column_physical_names);

        $receive_order = array_fill_keys($physical_names, '2');
        unset($receive_order[$unset_column]);

        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = ['1'];
        $fraction_id = Client_Neapi::ROUND;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $execution_bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);

    }

    public function test__convert_支払方法の項目が更新不可の場合は除外配列に内容を入れておき空配列を返すこと(){

        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        // 支払方法
        $update_column->receive_order_column_id = Model_Receiveordercolumn::COLUMN_ID_PAYMENT;
        $update_column->update_method_id = Model_Updatemethod::OVERWRITE;
        $update_column->update_value = '1';

        $update_columns = [$update_column];
        // 受注状態が起票済み、納品書印刷待ち以外にする
        $receive_order = [
            'receive_order_id' => '1',
            'receive_order_order_status_id' => \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_SHIPPED,
            'receive_order_shop_id' => '2',
            'receive_order_payment_method_id' => '0',
            'receive_order_gruoping_tag' => '[dummy_tag]',
            'receive_order_shop_cut_form_id' => '1',
            'receive_order_cancel_type_id' => '0',
            'receive_order_total_amount' => Client_Neapi::RECEIVE_ORDER_NUMBER_COLUMN_MAX
        ];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = ['1'];
        $fraction_id = Client_Neapi::ROUND;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $execution_bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        $expect = [];
        $this->assertEquals($expect, $result);
        // 除外配列に中身が入っていること
        $expect_excluded_id_and_reason = [
            '1' => ['receive_order_id' => '1', 'excluded_reason' => __em('excluded_reason.payment_method_update_condition')]
        ];
        $this->assertEquals($expect_excluded_id_and_reason, $excluded_id_and_reason);
    }

    public function test__convert_支払方法の項目が更新可かつ更新項目として入金状況と承認状況がない場合に更新項目として入金状況と承認状況を設定した状態で受注伝票を返すこと(){

        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        // 支払方法
        $update_column->receive_order_column_id = Model_Receiveordercolumn::COLUMN_ID_PAYMENT;
        $update_column->update_method_id = Model_Updatemethod::OVERWRITE;
        $update_column->update_value = '1';

        $update_columns = [$update_column];
        // 受注状態が起票済みにする
        $receive_order = [
            'receive_order_id' => '1',
            'receive_order_order_status_id' => \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_ISSUED,
            'receive_order_confirm_ids' => '',
            'receive_order_note' => '',
            'receive_order_shop_id' => '2',
            'receive_order_payment_method_id' => '0',
            'receive_order_gruoping_tag' => '[dummy_tag]',
            'receive_order_shop_cut_form_id' => '1',
            'receive_order_cancel_type_id' => '0',
            'receive_order_total_amount' => Client_Neapi::RECEIVE_ORDER_NUMBER_COLUMN_MAX
        ];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = ['1'];
        $fraction_id = Client_Neapi::ROUND;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $execution_bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        // 確認チェック、確認内容、備考欄、入金状況、承認状況が更新されていること
        $expect = [
            'receive_order_confirm_check_id' => '0',
            'receive_order_confirm_ids' => Client_Neapi::RECEIVE_ORDER_CONFIRM_ID_LOOK_NOTE,
            'receive_order_note' => '支払方法が更新されています。',
            'receive_order_payment_method_id'   => '1',
            'receive_order_deposit_type_id' => \Client_Neapi::RECEIVE_ORDER_DEPOSIT_TYPE_ID_NOT,
            'receive_order_credit_approval_type_id' => \Client_Neapi::RECEIVE_ORDER_CREDIT_APPROVAL_TYPE_ID_NOT,
        ];
        $this->assertEquals($expect, $result);
    }

    public function test__convert_支払方法の項目が更新可かつ更新対象の受注伝票の確認内容と備考欄にすでに記載がある場合は追記した状態で受注伝票を返すこと(){

        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        // 支払方法
        $update_column->receive_order_column_id = Model_Receiveordercolumn::COLUMN_ID_PAYMENT;
        $update_column->update_method_id = Model_Updatemethod::OVERWRITE;
        $update_column->update_value = '1';

        $update_columns = [$update_column];
        // 受注状態が起票済みにする
        $receive_order = [
            'receive_order_id' => '1',
            'receive_order_order_status_id' => \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_ISSUED,
            'receive_order_confirm_ids' => '確認内容記載あり',
            'receive_order_note' => '備考欄記載あり',
            'receive_order_shop_id' => '2',
            'receive_order_payment_method_id' => '0',
            'receive_order_gruoping_tag' => '[dummy_tag]',
            'receive_order_shop_cut_form_id' => '1',
            'receive_order_cancel_type_id' => '0',
            'receive_order_total_amount' => Client_Neapi::RECEIVE_ORDER_NUMBER_COLUMN_MAX
        ];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = ['1'];
        $fraction_id = Client_Neapi::ROUND;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $execution_bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        // 確認チェック、および確認内容、備考欄が追記で更新されていること
        // 入金状況、承認状況が更新されていること
        $expect = [
            'receive_order_confirm_check_id' => '0',
            'receive_order_confirm_ids' => '確認内容記載あり'.':'.Client_Neapi::RECEIVE_ORDER_CONFIRM_ID_LOOK_NOTE,
            'receive_order_note' => "備考欄記載あり\n支払方法が更新されています。",
            'receive_order_payment_method_id'   => '1',
            'receive_order_deposit_type_id' => \Client_Neapi::RECEIVE_ORDER_DEPOSIT_TYPE_ID_NOT,
            'receive_order_credit_approval_type_id' => \Client_Neapi::RECEIVE_ORDER_CREDIT_APPROVAL_TYPE_ID_NOT,
        ];
        $this->assertEquals($expect, $result);
    }

    public function test__convert_支払方法の項目が更新可かつ更新項目として備考欄がある場合に備考欄の更新値に追記した状態で受注伝票を返すこと(){

        $update_columns = [];
        // 支払方法
        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        $update_column->receive_order_column_id = Model_Receiveordercolumn::COLUMN_ID_PAYMENT;
        $update_column->update_method_id = Model_Updatemethod::OVERWRITE;
        $update_column->update_value = '1';
        $update_columns[] = $update_column;

        // 備考欄
        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        $update_column->receive_order_column_id = '20';
        $update_column->update_method_id = Model_Updatemethod::OVERWRITE;
        $update_column->update_value = '更新項目として設定した備考欄の値';
        $update_columns[] = $update_column;

        // 受注状態が起票済みにする
        $receive_order = [
            'receive_order_id' => '1',
            'receive_order_order_status_id' => \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_ISSUED,
            'receive_order_confirm_ids' => '確認内容記載あり',
            'receive_order_note' => '備考欄記載あり',
            'receive_order_shop_id' => '2',
            'receive_order_payment_method_id' => '0',
            'receive_order_gruoping_tag' => '[dummy_tag]',
            'receive_order_shop_cut_form_id' => '1',
            'receive_order_cancel_type_id' => '0',
            'receive_order_total_amount' => Client_Neapi::RECEIVE_ORDER_NUMBER_COLUMN_MAX
        ];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = ['1'];
        $fraction_id = Client_Neapi::ROUND;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $execution_bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        // 確認チェック、および確認内容、備考欄が追記で更新されていること
        // 入金状況、承認状況が更新されていること
        $expect = [
            'receive_order_confirm_check_id' => '0',
            'receive_order_confirm_ids' => '確認内容記載あり'.':'.Client_Neapi::RECEIVE_ORDER_CONFIRM_ID_LOOK_NOTE,
            'receive_order_note' => "更新項目として設定した備考欄の値\n支払方法が更新されています。",
            'receive_order_payment_method_id'   => '1',
            'receive_order_deposit_type_id' => \Client_Neapi::RECEIVE_ORDER_DEPOSIT_TYPE_ID_NOT,
            'receive_order_credit_approval_type_id' => \Client_Neapi::RECEIVE_ORDER_CREDIT_APPROVAL_TYPE_ID_NOT,
        ];
        $this->assertEquals($expect, $result);
    }

    public function test__convert_支払方法の項目が更新可かつ更新対象の受注伝票の確認内容と備考欄にすでに同内容更新の記載がある場合は確認内容と備考欄には追記しない状態で受注伝票を返すこと(){

        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        // 支払方法
        $update_column->receive_order_column_id = Model_Receiveordercolumn::COLUMN_ID_PAYMENT;
        $update_column->update_method_id = Model_Updatemethod::OVERWRITE;
        $update_column->update_value = '1';

        $update_columns = [$update_column];
        // 受注状態が起票済みにする
        $receive_order = [
            'receive_order_id' => '1',
            'receive_order_order_status_id' => \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_ISSUED,
            'receive_order_confirm_ids' => 'AG:AH',
            'receive_order_note' => "支払方法が更新されています。\nその他の備考内容",
            'receive_order_shop_id' => '2',
            'receive_order_payment_method_id' => '0',
            'receive_order_gruoping_tag' => '[dummy_tag]',
            'receive_order_shop_cut_form_id' => '1',
            'receive_order_cancel_type_id' => '0',
            'receive_order_total_amount' => Client_Neapi::RECEIVE_ORDER_NUMBER_COLUMN_MAX
        ];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = ['1'];
        $fraction_id = Client_Neapi::ROUND;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $execution_bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        // 確認チェック、および確認内容、備考欄が更新されていないこと
        // 入金状況、承認状況が更新されていること
        $expect = [
            'receive_order_confirm_check_id' => '0',
            'receive_order_payment_method_id'   => '1',
            'receive_order_deposit_type_id' => \Client_Neapi::RECEIVE_ORDER_DEPOSIT_TYPE_ID_NOT,
            'receive_order_credit_approval_type_id' => \Client_Neapi::RECEIVE_ORDER_CREDIT_APPROVAL_TYPE_ID_NOT,
            'receive_order_confirm_ids' => 'AG:AH',
            'receive_order_note' => "支払方法が更新されています。\nその他の備考内容",
        ];
        $this->assertEquals($expect, $result);
    }

    public function test__convert_受注金額関連の項目が更新不可の場合は除外配列に内容を入れておき空配列を返すこと(){

        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        // 総合計
        $update_column->receive_order_column_id = Model_Receiveordercolumn::COLUMN_ID_TOTAL_AMOUNT;
        $update_column->update_method_id = Model_Updatemethod::ADDITION;
        $update_column->update_value = '1';

        $update_columns = [$update_column];
        // 受注状態が起票済み以外にする
        $receive_order = [
            'receive_order_id' => '1',
            'receive_order_order_status_id' => \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_SHIPPED,
            'receive_order_shop_id' => '2',
            'receive_order_gruoping_tag' => '[dummy_tag]',
            'receive_order_shop_cut_form_id' => '1',
            'receive_order_cancel_type_id' => '0',
            'receive_order_total_amount' => Client_Neapi::RECEIVE_ORDER_NUMBER_COLUMN_MAX
        ];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = ['1'];
        $fraction_id = Client_Neapi::ROUND;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $execution_bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        $expect = [];
        $this->assertEquals($expect, $result);
        // 除外配列に中身が入っていること
        $expect_excluded_id_and_reason = [
            '1' => ['receive_order_id' => '1', 'excluded_reason' => __em('excluded_reason.order_amount_update_condition')]
        ];
        $this->assertEquals($expect_excluded_id_and_reason, $excluded_id_and_reason);
    }

    public function test__convert_受注金額関連の項目が更新可かつ総合計への反映が許可時に総合計の計算結果が最大値を超える場合は除外配列に内容を入れておき空配列を返すこと(){

        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        // 他費用
        $update_column->receive_order_column_id = 14;
        $update_column->update_method_id = Model_Updatemethod::ADDITION;
        $update_column->update_value = '1';

        $update_columns = [$update_column];
        // 受注状態が起票済みにする
        $receive_order = [
            'receive_order_id' => '1',
            'receive_order_order_status_id' => \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_ISSUED,
            'receive_order_shop_id' => '2',
            'receive_order_gruoping_tag' => '[dummy_tag]',
            'receive_order_shop_cut_form_id' => '1',
            'receive_order_cancel_type_id' => '0',
            'receive_order_other_amount' => '0',
            'receive_order_total_amount' => (Client_Neapi::RECEIVE_ORDER_NUMBER_COLUMN_MAX - 1 + 0.001)
        ];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        // 総合計への反映を許可する設定
        $execution_bulk_update_setting->allow_reflect_order_amount = '1';
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = ['1'];
        $fraction_id = Client_Neapi::ROUND;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $execution_bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        $expect = [];
        $this->assertEquals($expect, $result);
        // 除外配列に中身が入っていること
        $expect_excluded_id_and_reason = [
            '1' => ['receive_order_id' => '1', 'excluded_reason' => __em('excluded_reason.total_amount_out_of_range')]
        ];
        $this->assertEquals($expect_excluded_id_and_reason, $excluded_id_and_reason);
    }

    public function test__convert_受注金額関連の項目が更新可かつ総合計への反映が許可時に総合計の計算結果が最大値の場合は更新内容を反映した受注伝票を返すこと(){

        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        // 他費用
        $update_column->receive_order_column_id = 14;
        $update_column->update_method_id = Model_Updatemethod::ADDITION;
        $update_column->update_value = '1';

        $update_columns = [$update_column];
        // 受注状態が起票済みにする
        $receive_order = [
            'receive_order_id' => '1',
            'receive_order_order_status_id' => \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_ISSUED,
            'receive_order_confirm_ids' => '',
            'receive_order_note' => '',
            'receive_order_shop_id' => '2',
            'receive_order_gruoping_tag' => '[dummy_tag]',
            'receive_order_shop_cut_form_id' => '1',
            'receive_order_cancel_type_id' => '0',
            'receive_order_other_amount' => '0',
            'receive_order_total_amount' => (Client_Neapi::RECEIVE_ORDER_NUMBER_COLUMN_MAX - 1)
        ];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        // 総合計への反映を許可する設定
        $execution_bulk_update_setting->allow_reflect_order_amount = '1';
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = ['1'];
        $fraction_id = Client_Neapi::ROUND;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $execution_bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        // 他費用、総合計、確認チェック、確認内容、備考欄が更新されていること
        $expect = [
            'receive_order_other_amount'   => '1',
            'receive_order_total_amount' => Client_Neapi::RECEIVE_ORDER_NUMBER_COLUMN_MAX,
            'receive_order_confirm_check_id' => '0',
            'receive_order_confirm_ids' => Client_Neapi::RECEIVE_ORDER_CONFIRM_ID_LOOK_NOTE,
            'receive_order_note' => '他費用,総合計が更新されています。',
        ];
        $this->assertEquals($expect, $result);
    }

    public function test__convert_受注金額関連の項目が更新可かつ総合計への反映が許可時に総合計の計算結果が最小値を超える場合は除外配列に内容を入れておき空配列を返すこと(){

        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        // 他費用
        $update_column->receive_order_column_id = 14;
        $update_column->update_method_id = Model_Updatemethod::SUBTRACTION;
        $update_column->update_value = '1';

        $update_columns = [$update_column];
        // 受注状態が起票済みにする
        $receive_order = [
            'receive_order_id' => '1',
            'receive_order_order_status_id' => \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_ISSUED,
            'receive_order_shop_id' => '2',
            'receive_order_gruoping_tag' => '[dummy_tag]',
            'receive_order_shop_cut_form_id' => '1',
            'receive_order_cancel_type_id' => '0',
            'receive_order_other_amount' => '0',
            'receive_order_total_amount' => (Client_Neapi::RECEIVE_ORDER_NUMBER_COLUMN_MIN + 1 - 0.001)
        ];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        // 総合計への反映を許可する設定
        $execution_bulk_update_setting->allow_reflect_order_amount = '1';
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = ['1'];
        $fraction_id = Client_Neapi::ROUND;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $execution_bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        $expect = [];
        $this->assertEquals($expect, $result);
        // 除外配列に中身が入っていること
        $expect_excluded_id_and_reason = [
            '1' => ['receive_order_id' => '1', 'excluded_reason' => __em('excluded_reason.total_amount_out_of_range')]
        ];
        $this->assertEquals($expect_excluded_id_and_reason, $excluded_id_and_reason);
    }

    public function test__convert_受注金額関連の項目が更新可かつ総合計への反映が許可時に総合計の計算結果が最小値の場合は更新内容を反映した受注伝票を返すこと(){

        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        // 他費用
        $update_column->receive_order_column_id = 14;
        $update_column->update_method_id = Model_Updatemethod::SUBTRACTION;
        $update_column->update_value = '1';

        $update_columns = [$update_column];
        // 受注状態が起票済みにする
        $receive_order = [
            'receive_order_id' => '1',
            'receive_order_order_status_id' => \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_ISSUED,
            'receive_order_confirm_ids' => '',
            'receive_order_note' => '',
            'receive_order_shop_id' => '2',
            'receive_order_gruoping_tag' => '[dummy_tag]',
            'receive_order_shop_cut_form_id' => '1',
            'receive_order_cancel_type_id' => '0',
            'receive_order_other_amount' => '0',
            'receive_order_total_amount' => (Client_Neapi::RECEIVE_ORDER_NUMBER_COLUMN_MIN + 1)
        ];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        // 総合計への反映を許可する設定
        $execution_bulk_update_setting->allow_reflect_order_amount = '1';
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = ['1'];
        $fraction_id = Client_Neapi::ROUND;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $execution_bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        // 他費用、総合計、確認チェック、確認内容、備考欄が更新されていること
        $expect = [
            'receive_order_other_amount'   => '-1',
            'receive_order_total_amount' => Client_Neapi::RECEIVE_ORDER_NUMBER_COLUMN_MIN,
            'receive_order_confirm_check_id' => '0',
            'receive_order_confirm_ids' => Client_Neapi::RECEIVE_ORDER_CONFIRM_ID_LOOK_NOTE,
            'receive_order_note' => '他費用,総合計が更新されています。',
        ];
        $this->assertEquals($expect, $result);
    }

    public function test__convert_受注金額関連の項目が更新可かつ総合計への反映が許可時に更新項目がポイント数の場合はマイナス値として総合計に反映した受注伝票を返すこと(){

        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        // 他費用
        $update_column->receive_order_column_id = Model_Receiveordercolumn::COLUMN_ID_POINT_AMOUNT;
        $update_column->update_method_id = Model_Updatemethod::ADDITION;
        $update_column->update_value = '100';

        $update_columns = [$update_column];
        // 受注状態が起票済みにする
        $receive_order = [
            'receive_order_id' => '1',
            'receive_order_order_status_id' => \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_ISSUED,
            'receive_order_confirm_ids' => '',
            'receive_order_note' => '',
            'receive_order_shop_id' => '2',
            'receive_order_gruoping_tag' => '[dummy_tag]',
            'receive_order_shop_cut_form_id' => '1',
            'receive_order_cancel_type_id' => '0',
            'receive_order_point_amount' => '40',
            'receive_order_total_amount' => '1000'
        ];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        // 総合計への反映を許可する設定
        $execution_bulk_update_setting->allow_reflect_order_amount = '1';
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = ['1'];
        $fraction_id = Client_Neapi::ROUND;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $execution_bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        // ポイント数、総合計、確認チェック、確認内容、備考欄が更新されていること
        $expect = [
            'receive_order_point_amount'   => '140',
            'receive_order_total_amount' => '900',
            'receive_order_confirm_check_id' => '0',
            'receive_order_confirm_ids' => Client_Neapi::RECEIVE_ORDER_CONFIRM_ID_LOOK_NOTE,
            'receive_order_note' => 'ポイント数,総合計が更新されています。',
        ];
        $this->assertEquals($expect, $result);
    }

    public function test__convert_受注金額関連の項目が更新可かつ総合計への反映が許可されていない場合は総合計に反映せずに受注伝票を返すこと(){

        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        // 他費用
        $update_column->receive_order_column_id = Model_Receiveordercolumn::COLUMN_ID_POINT_AMOUNT;
        $update_column->update_method_id = Model_Updatemethod::ADDITION;
        $update_column->update_value = '100';

        $update_columns = [$update_column];
        // 受注状態が起票済みにする
        $receive_order = [
            'receive_order_id' => '1',
            'receive_order_order_status_id' => \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_ISSUED,
            'receive_order_confirm_ids' => '',
            'receive_order_note' => '',
            'receive_order_shop_id' => '2',
            'receive_order_gruoping_tag' => '[dummy_tag]',
            'receive_order_shop_cut_form_id' => '1',
            'receive_order_cancel_type_id' => '0',
            'receive_order_point_amount' => '40',
            'receive_order_total_amount' => '1000'
        ];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        // 総合計への反映を許可しない設定
        $execution_bulk_update_setting->allow_reflect_order_amount = '0';
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = ['1'];
        $fraction_id = Client_Neapi::ROUND;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $execution_bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        // ポイント数、確認チェック、確認内容、備考欄が更新されていること
        $expect = [
            'receive_order_point_amount'   => '140',
            'receive_order_confirm_check_id' => '0',
            'receive_order_confirm_ids' => Client_Neapi::RECEIVE_ORDER_CONFIRM_ID_LOOK_NOTE,
            'receive_order_note' => 'ポイント数が更新されています。',
        ];
        $this->assertEquals($expect, $result);
    }

    public function test__convert_受注金額関連の項目が更新可かつ更新対象の受注伝票の確認内容と備考欄にすでに記載がある場合は追記した状態で受注伝票を返すこと(){

        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        // 他費用
        $update_column->receive_order_column_id = Model_Receiveordercolumn::COLUMN_ID_POINT_AMOUNT;
        $update_column->update_method_id = Model_Updatemethod::ADDITION;
        $update_column->update_value = '100';

        $update_columns = [$update_column];
        // 受注状態が起票済みにする
        $receive_order = [
            'receive_order_id' => '1',
            'receive_order_order_status_id' => \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_ISSUED,
            'receive_order_confirm_ids' => '確認内容記載あり',
            'receive_order_note' => '備考欄記載あり',
            'receive_order_shop_id' => '2',
            'receive_order_gruoping_tag' => '[dummy_tag]',
            'receive_order_shop_cut_form_id' => '1',
            'receive_order_cancel_type_id' => '0',
            'receive_order_point_amount' => '40',
            'receive_order_total_amount' => '1000'
        ];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        // 総合計への反映を許可しない設定
        $execution_bulk_update_setting->allow_reflect_order_amount = '0';
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = ['1'];
        $fraction_id = Client_Neapi::ROUND;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $execution_bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        // ポイント数、確認チェック、および確認内容、備考欄が追記で更新されていること
        $expect = [
            'receive_order_point_amount'   => '140',
            'receive_order_confirm_check_id' => '0',
            'receive_order_confirm_ids' => '確認内容記載あり'.':'.Client_Neapi::RECEIVE_ORDER_CONFIRM_ID_LOOK_NOTE,
            'receive_order_note' => "備考欄記載あり\nポイント数が更新されています。",
        ];
        $this->assertEquals($expect, $result);
    }

    public function test__convert_受注金額関連の項目が更新可かつ更新対象の受注伝票の確認内容と備考欄にすでに同内容更新の記載がある場合は確認内容と備考欄には追記しない状態で受注伝票を返すこと(){

        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        // 他費用
        $update_column->receive_order_column_id = Model_Receiveordercolumn::COLUMN_ID_POINT_AMOUNT;
        $update_column->update_method_id = Model_Updatemethod::ADDITION;
        $update_column->update_value = '100';

        $update_columns = [$update_column];
        // 受注状態が起票済みにする
        $receive_order = [
            'receive_order_id' => '1',
            'receive_order_order_status_id' => \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_ISSUED,
            'receive_order_confirm_ids' => 'AG:AH',
            'receive_order_note' => "ポイント数が更新されています。\nその他の備考内容",
            'receive_order_shop_id' => '2',
            'receive_order_gruoping_tag' => '[dummy_tag]',
            'receive_order_shop_cut_form_id' => '1',
            'receive_order_cancel_type_id' => '0',
            'receive_order_point_amount' => '40',
            'receive_order_total_amount' => '1000'
        ];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        // 総合計への反映を許可しない設定
        $execution_bulk_update_setting->allow_reflect_order_amount = '0';
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = ['1'];
        $fraction_id = Client_Neapi::ROUND;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $execution_bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        // ポイント数、確認チェック、および確認内容、備考欄が追記で更新されていること
        $expect = [
            'receive_order_point_amount'   => '140',
            'receive_order_confirm_check_id' => '0',
            'receive_order_confirm_ids' => 'AG:AH',
            'receive_order_note' => "ポイント数が更新されています。\nその他の備考内容",
        ];
        $this->assertEquals($expect, $result);
    }

    public function test__convert_受注金額関連の項目が更新可かつ総合計への反映が許可時に更新項目が総合計の場合は更新内容を反映した受注伝票を返すこと(){

        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        // 他費用
        $update_column->receive_order_column_id = Model_Receiveordercolumn::COLUMN_ID_TOTAL_AMOUNT;
        $update_column->update_method_id = Model_Updatemethod::ADDITION;
        $update_column->update_value = '100';

        $update_columns = [$update_column];
        // 受注状態が起票済みにする
        $receive_order = [
            'receive_order_id' => '1',
            'receive_order_order_status_id' => \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_ISSUED,
            'receive_order_confirm_ids' => '',
            'receive_order_note' => '',
            'receive_order_shop_id' => '2',
            'receive_order_gruoping_tag' => '[dummy_tag]',
            'receive_order_shop_cut_form_id' => '1',
            'receive_order_cancel_type_id' => '0',
            'receive_order_point_amount' => '40',
            'receive_order_total_amount' => '1000'
        ];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        // 総合計への反映を許可する設定
        $execution_bulk_update_setting->allow_reflect_order_amount = '1';
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = ['1'];
        $fraction_id = Client_Neapi::ROUND;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $execution_bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        // 合計、確認チェック、確認内容、備考欄が更新されていること
        $expect = [
            'receive_order_total_amount' => '1100',
            'receive_order_confirm_check_id' => '0',
            'receive_order_confirm_ids' => Client_Neapi::RECEIVE_ORDER_CONFIRM_ID_LOOK_NOTE,
            'receive_order_note' => '総合計が更新されています。',
        ];
        $this->assertEquals($expect, $result);
    }

    public function test__convert_もしYahooの受注キャンセルの設定だった場合かつキャンセルを更新しない設定にしている場合は除外配列に内容を入れておき空配列を返すこと(){
        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        // 受注キャンセルの設定
        $update_column->receive_order_column_id = 7;
        $update_column->update_method_id = Model_Updatemethod::OVERWRITE;
        $update_column->update_value = '1';

        $update_columns = [$update_column];
        $receive_order = ['receive_order_id' => '1', 'receive_order_shop_id' => '2', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_cancel_type_id' => '0'];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = null;
        $fraction_id = null;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $execution_bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        $expect = [];
        $this->assertEquals($expect, $result);
        // 除外配列に中身が入っていること
        $expect_excluded_id_and_reason = [
            '1' => ['receive_order_id' => '1', 'excluded_reason' => __em('excluded_reason.yahoo_cancel')]
        ];
        $this->assertEquals($expect_excluded_id_and_reason, $excluded_id_and_reason);
    }

    public function test__convert_もし更新後の店舗がyahooでなければ除外されず更新内容を反映した受注伝票を返すこと(){
        // 受注キャンセルの設定
        $update_column1 = new Model_Executionbulkupdatecolumn();
        $update_column1->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        $update_column1->receive_order_column_id = 7;
        $update_column1->update_method_id = Model_Updatemethod::OVERWRITE;
        $update_column1->update_value = '1';

        // 店舗をyahoo以外の店舗に変更する設定
        $update_column2 = new Model_Executionbulkupdatecolumn();
        $update_column2->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        $update_column2->receive_order_column_id = Model_Receiveordercolumn::COLUMN_ID_SHOP;
        $update_column2->update_method_id = Model_Updatemethod::OVERWRITE;
        $update_column2->update_value = '3';

        $update_columns = [$update_column1, $update_column2];
        $receive_order = ['receive_order_id' => '1', 'receive_order_shop_id' => '2', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_cancel_type_id' => '0'];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = null;
        $fraction_id = null;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $execution_bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        // 受注キャンセルが1に更新されていること
        $expect = [
            'receive_order_cancel_type_id' => '1',
            'receive_order_shop_id'        => '3',
        ];
        $this->assertEquals($expect, $result);
    }

    public function test__convert_もし更新後の店舗もYahoo店舗だった場合かつYahooの受注キャンセルの設定だった場合かつキャンセルを更新しない設定にしている場合は除外配列に内容を入れておき空配列を返すこと(){
        // 受注キャンセルの設定
        $update_column1 = new Model_Executionbulkupdatecolumn();
        $update_column1->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        $update_column1->receive_order_column_id = 7;
        $update_column1->update_method_id = Model_Updatemethod::OVERWRITE;
        $update_column1->update_value = '1';

        // 店舗をyahooの店舗に変更する設定
        $update_column2 = new Model_Executionbulkupdatecolumn();
        $update_column2->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        $update_column2->receive_order_column_id = Model_Receiveordercolumn::COLUMN_ID_SHOP;
        $update_column2->update_method_id = Model_Updatemethod::OVERWRITE;
        $update_column2->update_value = '6';

        $update_columns = [$update_column1, $update_column2];
        $receive_order = ['receive_order_id' => '1', 'receive_order_shop_id' => '2', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_cancel_type_id' => '0'];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = null;
        $fraction_id = null;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $execution_bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        $expect = [];
        $this->assertEquals($expect, $result);
        // 除外配列に中身が入っていること
        $expect_excluded_id_and_reason = [
            '1' => ['receive_order_id' => '1', 'excluded_reason' => __em('excluded_reason.yahoo_cancel')]
        ];
        $this->assertEquals($expect_excluded_id_and_reason, $excluded_id_and_reason);
    }

    public function test__convert_もしYahooの受注キャンセルの設定だった場合かつキャンセルを更新しても良い設定にしている場合は更新内容を反映した受注伝票を返すこと(){
        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        // 受注キャンセルの設定
        $update_column->receive_order_column_id = 7;
        $update_column->update_method_id = Model_Updatemethod::OVERWRITE;
        $update_column->update_value = '1';

        $update_columns = [$update_column];
        $receive_order = ['receive_order_id' => '1', 'receive_order_shop_id' => '2', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_cancel_type_id' => '0'];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        // Yahooの受注キャンセルを更新しても良い設定
        $execution_bulk_update_setting->allow_update_yahoo_cancel = '1';
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = null;
        $fraction_id = null;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $execution_bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        // 受注キャンセルが1に更新されていること
        $expect = [
            'receive_order_cancel_type_id' => '1',
        ];
        $this->assertEquals($expect, $result);
    }

    public function test__convert_受注分類タグに上書きの更新設定の場合受注分類タグに上書き結果を反映させて返すこと(){
        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        // 受注分類の上書き設定
        $update_column->receive_order_column_id = 6;
        $update_column->update_method_id = Model_Updatemethod::OVERWRITE;
        $update_column->update_value = '[overwrite_tag]';

        $update_columns = [$update_column];
        $receive_order = ['receive_order_id' => '1', 'receive_order_shop_id' => '2', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_cancel_type_id' => '0'];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = null;
        $fraction_id = null;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $execution_bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        $expect = [
            'receive_order_gruoping_tag'   => '[overwrite_tag]',
        ];
        $this->assertEquals($expect, $result);
    }

    public function test__convert_数値型のカラムであれば端数処理を適用すること(){
        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        // 総合計に対して割る3する設定
        $update_column->receive_order_column_id = Model_Receiveordercolumn::COLUMN_ID_TOTAL_AMOUNT;
        $update_column->update_method_id = Model_Updatemethod::DIVISION;
        $update_column->update_value = '3';

        $update_columns = [$update_column];
        $receive_order = [
            'receive_order_id' => '1',
            'receive_order_order_status_id' => \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_ISSUED,
            'receive_order_confirm_ids' => '',
            'receive_order_note' => '',
            'receive_order_shop_id' => '2',
            'receive_order_gruoping_tag' => '[dummy_tag]',
            'receive_order_shop_cut_form_id' => '1',
            'receive_order_cancel_type_id' => '0',
            'receive_order_total_amount' => '1000'
        ];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = ['2'];
        $fraction_id = Client_Neapi::ROUND;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $execution_bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        // 1000/3 = 333.33で日本円の店舗の四捨五入処理なので333となる
        $expect = [
            'receive_order_total_amount'   => '333',
            'receive_order_confirm_check_id' => '0',
            'receive_order_confirm_ids' => 'AG',
            'receive_order_note' => '総合計が更新されています。'
        ];
        $this->assertEquals($expect, $result);
    }

    public function test__convert_店舗も同時に更新する場合、更新後の店舗の値で端数処理を行うこと(){
        // 総合計に対して割る3する設定
        $update_column1 = new Model_Executionbulkupdatecolumn();
        $update_column1->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        $update_column1->receive_order_column_id = Model_Receiveordercolumn::COLUMN_ID_TOTAL_AMOUNT;
        $update_column1->update_method_id = Model_Updatemethod::DIVISION;
        $update_column1->update_value = '3';

        // 店舗を日本円以外の店舗に変更する設定
        $update_column2 = new Model_Executionbulkupdatecolumn();
        $update_column2->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        $update_column2->receive_order_column_id = Model_Receiveordercolumn::COLUMN_ID_SHOP;
        $update_column2->update_method_id = Model_Updatemethod::OVERWRITE;
        $update_column2->update_value = '3';

        $update_columns = [$update_column1, $update_column2];
        $receive_order = [
            'receive_order_id' => '1',
            'receive_order_order_status_id' => \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_ISSUED,
            'receive_order_confirm_ids' => '',
            'receive_order_note' => '',
            'receive_order_shop_id' => '2',
            'receive_order_gruoping_tag' => '[dummy_tag]',
            'receive_order_shop_cut_form_id' => '1',
            'receive_order_cancel_type_id' => '0',
            'receive_order_total_amount' => '1000'
        ];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = ['2'];
        $fraction_id = Client_Neapi::ROUND;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $execution_bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        // 1000/3 = 333.33で日本円以外の店舗の四捨五入処理なので333.33となる
        $expect = [
            'receive_order_total_amount'   => '333.33',
            'receive_order_shop_id'        => '3',
            'receive_order_confirm_check_id' => '0',
            'receive_order_confirm_ids' => 'AG',
            'receive_order_note' => '総合計が更新されています。'
        ];
        $this->assertEquals($expect, $result);
    }

    public function test__convert_数値型のカラムでなければ端数処理を適用しないこと(){
        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        // 作業用欄に対して333.3333を上書きする設定
        $update_column->receive_order_column_id = 23;
        $update_column->update_method_id = Model_Updatemethod::OVERWRITE;
        $update_column->update_value = '333.3333';

        $update_columns = [$update_column];
        $receive_order = ['receive_order_id' => '1', 'receive_order_shop_id' => '2', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_cancel_type_id' => '0', 'receive_order_worker_text' => '1000'];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = ['2'];
        $fraction_id = Client_Neapi::ROUND;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $execution_bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        // 作業用欄は数値型のカラムではないので端数処理がされないこと
        $expect = [
            'receive_order_worker_text'    => '333.3333',
        ];
        $this->assertEquals($expect, $result);
    }

    public function test__convert_数値型のカラムで最大値を超えている場合は除外配列に内容を入れておき空配列を返すこと(){
        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        // 総合計に対して足す1する設定
        $update_column->receive_order_column_id = Model_Receiveordercolumn::COLUMN_ID_TOTAL_AMOUNT;
        $update_column->update_method_id = Model_Updatemethod::ADDITION;
        $update_column->update_value = '1';

        $update_columns = [$update_column];
        $receive_order = [
            'receive_order_id' => '1',
            'receive_order_order_status_id' => \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_ISSUED,
            'receive_order_shop_id' => '2',
            'receive_order_gruoping_tag' => '[dummy_tag]',
            'receive_order_shop_cut_form_id' => '1',
            'receive_order_cancel_type_id' => '0',
            'receive_order_total_amount' => Client_Neapi::RECEIVE_ORDER_NUMBER_COLUMN_MAX
        ];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = ['1'];
        $fraction_id = Client_Neapi::ROUND;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $execution_bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        // 999999999.99+1 = 1000000000.99なので最大値を超える
        $expect = [];
        $this->assertEquals($expect, $result);
        // 除外配列に中身が入っていること
        $expect_excluded_id_and_reason = [
            '1' => ['receive_order_id' => '1', 'excluded_reason' => __em('excluded_reason.numeric_out_of_range')]
        ];
        $this->assertEquals($expect_excluded_id_and_reason, $excluded_id_and_reason);
    }

    public function test__convert_数値型のカラムで最小値を超えている場合は除外配列に内容を入れておき空配列を返すこと(){
        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        // 総合計から1を引く設定
        $update_column->receive_order_column_id = Model_Receiveordercolumn::COLUMN_ID_TOTAL_AMOUNT;
        $update_column->update_method_id = Model_Updatemethod::SUBTRACTION;
        $update_column->update_value = '1';

        $update_columns = [$update_column];
        $receive_order = [
            'receive_order_id' => '1',
            'receive_order_order_status_id' => \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_ISSUED,
            'receive_order_shop_id' => '2',
            'receive_order_gruoping_tag' => '[dummy_tag]',
            'receive_order_shop_cut_form_id' => '1',
            'receive_order_cancel_type_id' => '0',
            'receive_order_total_amount' => Client_Neapi::RECEIVE_ORDER_NUMBER_COLUMN_MIN
        ];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = ['1'];
        $fraction_id = Client_Neapi::ROUND;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $execution_bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        // -999999999.99-1 = -1000000000.99なので最小値を超える
        $expect = [];
        $this->assertEquals($expect, $result);
        // 除外配列に中身が入っていること
        $expect_excluded_id_and_reason = [
            '1' => ['receive_order_id' => '1', 'excluded_reason' => __em('excluded_reason.numeric_out_of_range')]
        ];
        $this->assertEquals($expect_excluded_id_and_reason, $excluded_id_and_reason);
    }

    public function test__convert_数値型のカラムではなく999999999点99を超えるような値を入れようとしている場合除外されないこと(){
        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->execution_bulk_update_setting_id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1;
        // 作業用欄に対して1000000000.99を上書きする設定
        $update_column->receive_order_column_id = 23;
        $update_column->update_method_id = Model_Updatemethod::OVERWRITE;
        $update_column->update_value = \Client_Neapi::RECEIVE_ORDER_NUMBER_COLUMN_MAX+1;

        $update_columns = [$update_column];
        $receive_order = ['receive_order_id' => '1', 'receive_order_shop_id' => '2', 'receive_order_gruoping_tag' => '[dummy_tag]', 'receive_order_shop_cut_form_id' => '1', 'receive_order_cancel_type_id' => '0', 'receive_order_worker_text' => '1000'];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = ['1'];
        $fraction_id = Client_Neapi::ROUND;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $execution_bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        // 作業用欄は数値型のカラムではないので最大値・最大値判定がされないこと
        $expect = [
            'receive_order_worker_text'    => '1000000000.99',
        ];
        $this->assertEquals($expect, $result);
    }

    public function test__convert_プレビュー画面時には1伝票に除外理由が複数ある場合には先勝ちになり理由は1つしか入っていないこと、そこで処理を終了せずに更新設定を反映した結果を返すこと(){
        $update_column = new Model_Bulkupdatecolumn();
        $update_column->bulk_update_setting_id = self::DUMMY_BULK_UPDATE_SETTING_ID1;
        // 総合計に対して足す1する設定
        $update_column->receive_order_column_id = 16;
        $update_column->update_method_id = Model_Updatemethod::ADDITION;
        $update_column->update_value = '1';

        $update_columns = [$update_column];
        $receive_order = [
            'receive_order_id' => '1',
            'receive_order_order_status_id' => \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_SHIPPED,
            'receive_order_shop_id' => '2',
            'receive_order_gruoping_tag' => '[dummy_tag]',
            'receive_order_shop_cut_form_id' => '1',
            'receive_order_cancel_type_id' => '0',
            'receive_order_total_amount' => Client_Neapi::RECEIVE_ORDER_NUMBER_COLUMN_MAX,
            'receive_order_confirm_ids'  => '',
            'receive_order_note'         => '',
        ];
        $bulk_update_setting = Model_Bulkupdatesetting::find(self::DUMMY_BULK_UPDATE_SETTING_ID1);
        $yahoo_shop_ids = ['2', '4', '6'];
        $japanese_yen_shop_ids = ['1'];
        $fraction_id = Client_Neapi::ROUND;
        $excluded_id_and_reason = [];
        $args = [$update_columns, $receive_order, $bulk_update_setting, &$yahoo_shop_ids, &$japanese_yen_shop_ids, &$fraction_id, &$excluded_id_and_reason];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_convert');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        // 999999999.99+1 = 1000000000.99なので最大値を超える
        $expect = [
            'receive_order_total_amount'   => '1000000000.99',
            'receive_order_confirm_check_id' => '0',
            'receive_order_confirm_ids' => 'AG',
            'receive_order_note' => '総合計が更新されています。'
        ];
        $this->assertEquals($expect, $result);
        // 除外配列に中身が入っていること
        // 「起票済み以外のステータスの時に金額系のカラムを触っていることによる除外」と「最大値を超えていることによる除外」の2つに該当するが先勝ちになっていること
        $expect_excluded_id_and_reason = [
            '1' => ['receive_order_id' => '1', 'excluded_reason' => '受注状態が起票済み(CSV/手入力)以外の受注伝票は「商品計、税金、手数料、発送代、他費用、ポイント数、総合計」を更新しないため除外']
        ];
        $this->assertEquals($expect_excluded_id_and_reason, $excluded_id_and_reason);
    }

    public function test__reflect_order_amount_受注金額関連項目に関する更新内容を反映した伝票の更新情報が取得できること() {
        $this->markTestSkipped('_convertでテストが行われているためスキップします');
    }

    public function test__set_wait_for_confirmation_更新対象の受注伝票が確認待ちの状態となるように_確認チェック_確認内容_備考欄への反映した伝票の更新情報が取得できること() {
        $this->markTestSkipped('_convertでテストが行われているためスキップします');
    }

    public function test__get_fraction_id_第二引数にキャッシュを渡している場合はキャッシュを返すこと(){
        $fraction_id = \Client_Neapi::ROUND;
        $args = [self::DUMMY_USER_ID1, &$fraction_id];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_get_fraction_id');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        $this->assertEquals($fraction_id, $result);
    }

    public function test__get_fraction_id_キャッシュがない場合は新規で取得しキャッシュにとっておくこと(){
        // Client_Neapiのスタブを作成する
        $company_info = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'data' => [
                ['company_fraction_id' => Client_Neapi::ROUND_DOWN]
            ]
        ];
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['apiExecute'])
            ->getMock();
        $ne_api_stub->expects($this->once())
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_LOGIN_COMPANY_INFO))
            ->will($this->returnValue($company_info));

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        $stub = $this->getMockBuilder(Domain_Model_Updatesetting::class)
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->with($this->equalTo(self::DUMMY_USER_ID1))
            ->will($this->returnValue($ne_api_stub));

        $fraction_id = null;
        $args = [self::DUMMY_USER_ID1, &$fraction_id];
        $method = $this->getMethod(get_class($stub), '_get_fraction_id');
        $result = $method->invokeArgs($stub, $args);
        $this->assertEquals(Client_Neapi::ROUND_DOWN, $result);
        $this->assertEquals(Client_Neapi::ROUND_DOWN, $fraction_id);
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function test__get_fraction_id_APIの通信に失敗した場合には例外を投げること(){
        // Client_Neapiのスタブを作成する
        $company_info = [
            'result' => Client_Neapi::RESULT_ERROR,
            'code' => Client_Neapi::ERROR_CODE_EXCEPTION,
            'message' => '原因不明のエラー',
        ];
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['apiExecute'])
            ->getMock();
        $ne_api_stub->expects($this->once())
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_LOGIN_COMPANY_INFO))
            ->will($this->returnValue($company_info));

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        $stub = $this->getMockBuilder(Domain_Model_Updatesetting::class)
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->with($this->equalTo(self::DUMMY_USER_ID1))
            ->will($this->returnValue($ne_api_stub));

        $fraction_id = null;
        $args = [self::DUMMY_USER_ID1, &$fraction_id];
        $method = $this->getMethod(get_class($stub), '_get_fraction_id');
        $result = $method->invokeArgs($stub, $args);
    }

    public function test__get_valid_scale_第三引数にキャッシュを渡している場合API通信を行わずキャッシュを使って値を返すこと(){
        $japanese_yen_shop_ids = ['1', '3', '5'];
        $shop_id = '1';
        $args = [self::DUMMY_USER_ID1, $shop_id, &$japanese_yen_shop_ids];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_get_valid_scale');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        $expect = Utility_Calculator::DIGIT_INTEGER;
        $this->assertEquals($expect, $result);
    }

    public function test__get_valid_scale_キャッシュがない場合は値を取得しその値を使って判定、店舗一覧をキャッシュ化すること(){
        // Client_Neapiのスタブを作成する
        $japanese_yen_shop_ids = ['1', '3', '5'];
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['get_shop_ids'])
            ->getMock();
        $ne_api_stub->expects($this->once())
            ->method('get_shop_ids')
            ->with($this->equalTo(['shop_currency_unit_id-eq' => \Client_Neapi::JAPANESE_YEN]))
            ->will($this->returnValue($japanese_yen_shop_ids));

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        $stub = $this->getMockBuilder(Domain_Model_Updatesetting::class)
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->with($this->equalTo(self::DUMMY_USER_ID1))
            ->will($this->returnValue($ne_api_stub));

        $shop_id = '1';
        $given_japanese_yen_shop_ids = null;
        $args = [self::DUMMY_USER_ID1, $shop_id, &$given_japanese_yen_shop_ids];
        $method = $this->getMethod(get_class($stub), '_get_valid_scale');
        $result = $method->invokeArgs($stub, $args);
        $this->assertEquals(Utility_Calculator::DIGIT_INTEGER, $result);
        $this->assertEquals($japanese_yen_shop_ids, $given_japanese_yen_shop_ids);
    }

    public function test__get_valid_scale_通貨単位区分が円以外の店舗はDIGIT_DECIMALを返すこと(){
        // Client_Neapiのスタブを作成する
        $japanese_yen_shop_ids = ['1', '3', '5'];
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['get_shop_ids'])
            ->getMock();
        $ne_api_stub->expects($this->once())
            ->method('get_shop_ids')
            ->with($this->equalTo(['shop_currency_unit_id-eq' => \Client_Neapi::JAPANESE_YEN]))
            ->will($this->returnValue($japanese_yen_shop_ids));

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        $stub = $this->getMockBuilder(Domain_Model_Updatesetting::class)
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->with($this->equalTo(self::DUMMY_USER_ID1))
            ->will($this->returnValue($ne_api_stub));

        // 「円」以外の店舗
        $shop_id = '2';
        $given_japanese_yen_shop_ids = null;
        $args = [self::DUMMY_USER_ID1, $shop_id, &$given_japanese_yen_shop_ids];
        $method = $this->getMethod(get_class($stub), '_get_valid_scale');
        $result = $method->invokeArgs($stub, $args);
        $this->assertEquals(Utility_Calculator::DIGIT_DECIMAL, $result);
    }


    public function test_add_notice_for_bulkupdate_全て成功していた場合全て成功のお知らせを配信しtrueを返すこと(){
        $notice_add_params = [
            'execution_notice_success' => Client_Neapi::EXECUTION_NOTICE_SUCCESS_TRUE,
            'execution_notice_title'   => '受注一括更新が成功しました',
            'execution_notice_content' => "実行タスクID：20180427-1\n一括更新設定名称：テスト実行1\n\n更新成功受注伝票件数：5件\n除外した受注伝票件数：0件",
        ];
        $notice_api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'message' => '',
            'access_token' => 'dummy_access_token',
            'access_token_end_date' => 'dummy_access_token_end_date',
            'refresh_token' => 'dummy_refresh_token',
            'refresh_token_end_date' => 'dummy_refresh_token_end_date',
        ];
        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['apiExecute'])
            ->getMock();
        $ne_api_stub->expects($this->once())
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_NOTICE_EXECUTION_ADD), $this->equalTo($notice_add_params))
            ->will($this->returnValue($notice_api_response));

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        $stub = $this->getMockBuilder(Domain_Model_Updatesetting::class)
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->with($this->equalTo(self::DUMMY_USER_ID1))
            ->will($this->returnValue($ne_api_stub));

        $api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'message' => '',
            'access_token' => 'dummy_access_token',
            'access_token_end_date' => 'dummy_access_token_end_date',
            'refresh_token' => 'dummy_refresh_token',
            'refresh_token_end_date' => 'dummy_refresh_token_end_date',
        ];
        $sent_count = 5;
        $excluded_id_and_reason = [];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $execution_result = new Domain_Value_Executionresult($api_response, $sent_count, $excluded_id_and_reason);

        $result = $stub->add_notice_for_bulkupdate($execution_bulk_update_setting, $execution_result);
        $this->assertTrue($result);
    }

    public function test_add_notice_for_bulkupdate_全て失敗していた場合全て失敗のお知らせを配信しtrueを返すこと(){
        $notice_add_params = [
            'execution_notice_success' => Client_Neapi::EXECUTION_NOTICE_SUCCESS_FALSE,
            'execution_notice_title'   => '受注一括更新が失敗しました',
            'execution_notice_content' => "実行タスクID：20180427-1\n一括更新設定名称：テスト実行1\n\n更新失敗受注伝票件数：5件\n除外した受注伝票件数：0件\n\n伝票番号：1,2,3,4,5\n原因：[総合計]半角数字ではありません。",
        ];
        $notice_api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'message' => '',
            'access_token' => 'dummy_access_token',
            'access_token_end_date' => 'dummy_access_token_end_date',
            'refresh_token' => 'dummy_refresh_token',
            'refresh_token_end_date' => 'dummy_refresh_token_end_date',
        ];
        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['apiExecute'])
            ->getMock();
        $ne_api_stub->expects($this->once())
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_NOTICE_EXECUTION_ADD), $this->equalTo($notice_add_params))
            ->will($this->returnValue($notice_api_response));

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        $stub = $this->getMockBuilder(Domain_Model_Updatesetting::class)
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->with($this->equalTo(self::DUMMY_USER_ID1))
            ->will($this->returnValue($ne_api_stub));

        $api_response = [
            'result' => Client_Neapi::RESULT_ERROR,
            'code' => Client_Neapi::ERROR_CODE_BULKUPDATE,
            'message' => [
                ['receive_order_id' => '1', 'code' => '020015', 'message' => '[receive_order_total_amount]半角数字ではありません。'],
                ['receive_order_id' => '2', 'code' => '020015', 'message' => '[receive_order_total_amount]半角数字ではありません。'],
                ['receive_order_id' => '3', 'code' => '020015', 'message' => '[receive_order_total_amount]半角数字ではありません。'],
                ['receive_order_id' => '4', 'code' => '020015', 'message' => '[receive_order_total_amount]半角数字ではありません。'],
                ['receive_order_id' => '5', 'code' => '020015', 'message' => '[receive_order_total_amount]半角数字ではありません。'],
            ],
            'access_token' => 'dummy_access_token',
            'access_token_end_date' => 'dummy_access_token_end_date',
            'refresh_token' => 'dummy_refresh_token',
            'refresh_token_end_date' => 'dummy_refresh_token_end_date',
        ];
        $sent_count = 5;
        $excluded_id_and_reason = [];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $execution_result = new Domain_Value_Executionresult($api_response, $sent_count, $excluded_id_and_reason);

        $result = $stub->add_notice_for_bulkupdate($execution_bulk_update_setting, $execution_result);
        $this->assertTrue($result);
    }

    public function test_add_notice_for_bulkupdate_一部失敗していた場合一部失敗のお知らせを配信しtrueを返すこと(){
        $notice_add_params = [
            'execution_notice_success' => Client_Neapi::EXECUTION_NOTICE_SUCCESS_FALSE,
            'execution_notice_title'   => '受注一括更新が一部失敗しました',
            'execution_notice_content' => "実行タスクID：20180427-1\n一括更新設定名称：テスト実行1\n\n更新成功受注伝票件数：3件\n更新失敗受注伝票件数：2件\n除外した受注伝票件数：0件\n\n伝票番号：4,5\n原因：[総合計]半角数字ではありません。",
        ];
        $notice_api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'message' => '',
            'access_token' => 'dummy_access_token',
            'access_token_end_date' => 'dummy_access_token_end_date',
            'refresh_token' => 'dummy_refresh_token',
            'refresh_token_end_date' => 'dummy_refresh_token_end_date',
        ];
        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['apiExecute'])
            ->getMock();
        $ne_api_stub->expects($this->once())
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_NOTICE_EXECUTION_ADD), $this->equalTo($notice_add_params))
            ->will($this->returnValue($notice_api_response));

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        $stub = $this->getMockBuilder(Domain_Model_Updatesetting::class)
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->with($this->equalTo(self::DUMMY_USER_ID1))
            ->will($this->returnValue($ne_api_stub));

        $api_response = [
            'result' => Client_Neapi::RESULT_ERROR,
            'code' => Client_Neapi::ERROR_CODE_BULKUPDATE,
            'message' => [
                ['receive_order_id' => '4', 'code' => '020015', 'message' => '[receive_order_total_amount]半角数字ではありません。'],
                ['receive_order_id' => '5', 'code' => '020015', 'message' => '[receive_order_total_amount]半角数字ではありません。'],
            ],
            'access_token' => 'dummy_access_token',
            'access_token_end_date' => 'dummy_access_token_end_date',
            'refresh_token' => 'dummy_refresh_token',
            'refresh_token_end_date' => 'dummy_refresh_token_end_date',
        ];
        $sent_count = 5;
        $excluded_id_and_reason = [];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $execution_result = new Domain_Value_Executionresult($api_response, $sent_count, $excluded_id_and_reason);

        $result = $stub->add_notice_for_bulkupdate($execution_bulk_update_setting, $execution_result);
        $this->assertTrue($result);
    }

    public function test_add_notice_for_bulkupdate_除外した伝票が合った場合はその旨も載せたお知らせを配信しtrueを返すこと(){
        $notice_add_params = [
            'execution_notice_success' => Client_Neapi::EXECUTION_NOTICE_SUCCESS_FALSE,
            'execution_notice_title'   => '受注一括更新が一部失敗しました',
            'execution_notice_content' => "実行タスクID：20180427-1\n一括更新設定名称：テスト実行1\n\n更新成功受注伝票件数：1件\n更新失敗受注伝票件数：2件\n除外した受注伝票件数：2件\n\n伝票番号：4,5\n原因：[総合計]半角数字ではありません。\n\n伝票番号：1,2\n理由：Yahoo店舗の受注はキャンセルできないため除外",
        ];
        $notice_api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'message' => '',
            'access_token' => 'dummy_access_token',
            'access_token_end_date' => 'dummy_access_token_end_date',
            'refresh_token' => 'dummy_refresh_token',
            'refresh_token_end_date' => 'dummy_refresh_token_end_date',
        ];
        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['apiExecute'])
            ->getMock();
        $ne_api_stub->expects($this->once())
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_NOTICE_EXECUTION_ADD), $this->equalTo($notice_add_params))
            ->will($this->returnValue($notice_api_response));

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        $stub = $this->getMockBuilder(Domain_Model_Updatesetting::class)
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->with($this->equalTo(self::DUMMY_USER_ID1))
            ->will($this->returnValue($ne_api_stub));

        $api_response = [
            'result' => Client_Neapi::RESULT_ERROR,
            'code' => Client_Neapi::ERROR_CODE_BULKUPDATE,
            'message' => [
                ['receive_order_id' => '4', 'code' => '020015', 'message' => '[receive_order_total_amount]半角数字ではありません。'],
                ['receive_order_id' => '5', 'code' => '020015', 'message' => '[receive_order_total_amount]半角数字ではありません。'],
            ],
            'access_token' => 'dummy_access_token',
            'access_token_end_date' => 'dummy_access_token_end_date',
            'refresh_token' => 'dummy_refresh_token',
            'refresh_token_end_date' => 'dummy_refresh_token_end_date',
        ];
        $sent_count = 3;
        $excluded_id_and_reason = [
            ['receive_order_id' => '1', 'excluded_reason' => __em('excluded_reason.yahoo_cancel')],
            ['receive_order_id' => '2', 'excluded_reason' => __em('excluded_reason.yahoo_cancel')],
        ];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $execution_result = new Domain_Value_Executionresult($api_response, $sent_count, $excluded_id_and_reason);

        $result = $stub->add_notice_for_bulkupdate($execution_bulk_update_setting, $execution_result);
        $this->assertTrue($result);
    }

    public function test_add_notice_for_bulkupdate_お知らせ配信に失敗した場合falseを返すこと(){
        $notice_add_params = [
            'execution_notice_success' => Client_Neapi::EXECUTION_NOTICE_SUCCESS_TRUE,
            'execution_notice_title'   => '受注一括更新が成功しました',
            'execution_notice_content' => "実行タスクID：20180427-1\n一括更新設定名称：テスト実行1\n\n更新成功受注伝票件数：5件\n除外した受注伝票件数：0件",
        ];
        $notice_api_response = [
            'result' => Client_Neapi::RESULT_ERROR,
            'code' => Client_Neapi::ERROR_CODE_MAIN_FUNCTION_MAINTENANCE,
            'message' => 'メイン機能がメンテナンス中です',
            'access_token' => 'dummy_access_token',
            'access_token_end_date' => 'dummy_access_token_end_date',
            'refresh_token' => 'dummy_refresh_token',
            'refresh_token_end_date' => 'dummy_refresh_token_end_date',
        ];
        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['apiExecute'])
            ->getMock();
        $ne_api_stub->expects($this->once())
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_NOTICE_EXECUTION_ADD), $this->equalTo($notice_add_params))
            ->will($this->returnValue($notice_api_response));

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        $stub = $this->getMockBuilder(Domain_Model_Updatesetting::class)
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->with($this->equalTo(self::DUMMY_USER_ID1))
            ->will($this->returnValue($ne_api_stub));

        $api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'message' => '',
            'access_token' => 'dummy_access_token',
            'access_token_end_date' => 'dummy_access_token_end_date',
            'refresh_token' => 'dummy_refresh_token',
            'refresh_token_end_date' => 'dummy_refresh_token_end_date',
        ];
        $sent_count = 5;
        $excluded_id_and_reason = [];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $execution_result = new Domain_Value_Executionresult($api_response, $sent_count, $excluded_id_and_reason);

        $result = $stub->add_notice_for_bulkupdate($execution_bulk_update_setting, $execution_result);
        $this->assertFalse($result);
    }

    public function test_add_notice_for_bulkupdate_名称が空の場合は固定名称でお知らせを配信すること(){
        $notice_add_params = [
            'execution_notice_success' => Client_Neapi::EXECUTION_NOTICE_SUCCESS_TRUE,
            'execution_notice_title'   => '受注一括更新が成功しました',
            'execution_notice_content' => "実行タスクID：20180427-1\n一括更新設定名称：名称なし（未保存の設定による実行）\n\n更新成功受注伝票件数：5件\n除外した受注伝票件数：0件",
        ];
        $notice_api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'message' => '',
            'access_token' => 'dummy_access_token',
            'access_token_end_date' => 'dummy_access_token_end_date',
            'refresh_token' => 'dummy_refresh_token',
            'refresh_token_end_date' => 'dummy_refresh_token_end_date',
        ];
        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['apiExecute'])
            ->getMock();
        $ne_api_stub->expects($this->once())
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_NOTICE_EXECUTION_ADD), $this->equalTo($notice_add_params))
            ->will($this->returnValue($notice_api_response));

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        $stub = $this->getMockBuilder(Domain_Model_Updatesetting::class)
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->with($this->equalTo(self::DUMMY_USER_ID1))
            ->will($this->returnValue($ne_api_stub));

        $api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'message' => '',
            'access_token' => 'dummy_access_token',
            'access_token_end_date' => 'dummy_access_token_end_date',
            'refresh_token' => 'dummy_refresh_token',
            'refresh_token_end_date' => 'dummy_refresh_token_end_date',
        ];
        $sent_count = 5;
        $excluded_id_and_reason = [];
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $execution_bulk_update_setting->name = '';
        $execution_result = new Domain_Value_Executionresult($api_response, $sent_count, $excluded_id_and_reason);

        $result = $stub->add_notice_for_bulkupdate($execution_bulk_update_setting, $execution_result);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider Domain_model_Updatesettingprovider::data_provider__get_execution_notice_content_for_error
     */
    public function test__get_execution_notice_content_for_error(array $bulkupdate_response_messages, string $expected) {
        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $physical_names = \Model_Receiveordercolumn::pluck('physical_name', ['order_by' => 'id']);
        $logical_names  = \Model_Receiveordercolumn::pluck('logical_name',  ['order_by' => 'id']);
        $result = self::invoke_method($domain_model_updatesetting, '_get_execution_notice_content_for_error', [$bulkupdate_response_messages, $physical_names, $logical_names]);
        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider Domain_model_Updatesettingprovider::data_provider__get_execution_notice_content_for_excluded
     */
    public function test__get_execution_notice_content_for_excluded(array $excluded_id_and_reason, string $expected) {
        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $result = self::invoke_method($domain_model_updatesetting, '_get_execution_notice_content_for_excluded', [$excluded_id_and_reason]);
        $this->assertEquals($expected, $result);
    }

    public function test_get_executed_tag_設定名に【済】をつけて返すこと(){
        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), 'get_executed_tag');
        $result = $method->invoke(null, 'TEST');
        $this->assertSame('[【済】TEST]', $result);
    }

    public function test_get_executed_tag_設定名がnullの場合空文字を返すこと(){
        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), 'get_executed_tag');
        $result = $method->invoke(null, null);
        $this->assertSame('', $result);
    }

    public function test_get_executed_tag_設定名が空文字の場合空文字を返すこと(){
        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), 'get_executed_tag');
        $result = $method->invoke(null, '');
        $this->assertSame('', $result);
    }

    public function test__is_execution_setting_渡された設定が実行時のものであればtrueを返すこと(){
        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_is_execution_setting');
        $result = $method->invoke(null, new Model_Executionbulkupdatesetting());
        $this->assertTrue($result);
    }

    public function test__is_execution_setting_渡された設定がプレビュー時のものであればfalseを返すこと(){
        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_is_execution_setting');
        $result = $method->invoke(null, new Model_Bulkupdatesetting());
        $this->assertFalse($result);
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function test__is_execution_setting_渡された設定が全く関係ないオブジェクトの場合例外になること(){
        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_is_execution_setting');
        $result = $method->invoke(null, new Model_Executionbulkupdatecolumn());
    }

    public function test__can_update_payment_method_更新項目が支払方法以外の場合にtrueを返すこと() {

        // 受注伝票情報
        $receive_order = [
            'receive_order_id' => '1',
            'receive_order_order_status_id' => \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_SHIPPED
        ];

        // 受注伝票項目のスタブ
        $receive_order_column_stub = $this->getMockBuilder(Model_Receiveordercolumn::class)
            ->setMethods(['is_payment_method_id'])
            ->getMock();
        $receive_order_column_stub->expects($this->once())
            ->method('is_payment_method_id')
            ->will($this->returnValue(false));

        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->receive_order_column = $receive_order_column_stub;

        $args = [$receive_order, $update_column];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_can_update_payment_method');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        $this->assertTrue($result);
    }

    public function test__can_update_payment_method_更新項目が支払方法かつ受注伝票の受注状態が起票済みの場合にtrueを返すこと() {

        // 受注伝票情報
        $receive_order = [
            'receive_order_id' => '1',
            'receive_order_order_status_id' => \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_ISSUED
        ];

        // 受注伝票項目のスタブ
        $receive_order_column_stub = $this->getMockBuilder(Model_Receiveordercolumn::class)
            ->setMethods(['is_payment_method_id'])
            ->getMock();
        $receive_order_column_stub->expects($this->once())
            ->method('is_payment_method_id')
            ->will($this->returnValue(true));

        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->receive_order_column = $receive_order_column_stub;

        $args = [$receive_order, $update_column];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_can_update_payment_method');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        $this->assertTrue($result);
    }

    public function test__can_update_payment_method_更新項目が支払方法かつ受注伝票の受注状態が納品書印刷待ちの場合にtrueを返すこと() {

        // 受注伝票情報
        $receive_order = [
            'receive_order_id' => '1',
            'receive_order_order_status_id' => \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_WAIT_FOR_PRINT
        ];

        // 受注伝票項目のスタブ
        $receive_order_column_stub = $this->getMockBuilder(Model_Receiveordercolumn::class)
            ->setMethods(['is_payment_method_id'])
            ->getMock();
        $receive_order_column_stub->expects($this->once())
            ->method('is_payment_method_id')
            ->will($this->returnValue(true));

        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->receive_order_column = $receive_order_column_stub;

        $args = [$receive_order, $update_column];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_can_update_payment_method');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        $this->assertTrue($result);
    }

    public function test__can_update_payment_method_更新項目が支払方法かつ受注伝票の受注状態が起票済み_納品書印刷待ち以外の場合にfalseを返すこと() {

        // 受注伝票情報
        $receive_order = [
            'receive_order_id' => '1',
            'receive_order_order_status_id' => \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_SHIPPED
        ];

        // 受注伝票項目のスタブ
        $receive_order_column_stub = $this->getMockBuilder(Model_Receiveordercolumn::class)
            ->setMethods(['is_payment_method_id'])
            ->getMock();
        $receive_order_column_stub->expects($this->once())
            ->method('is_payment_method_id')
            ->will($this->returnValue(true));

        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->receive_order_column = $receive_order_column_stub;

        $args = [$receive_order, $update_column];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_can_update_payment_method');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        $this->assertFalse($result);
    }

    public function test__can_update_order_amount_更新項目が受注金額関連項目以外の場合にtrueを返すこと() {

        // 受注伝票情報
        $receive_order = [
            'receive_order_id' => '1',
            'receive_order_order_status_id' => \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_SHIPPED
        ];

        // 受注伝票項目のスタブ
        $receive_order_column_stub = $this->getMockBuilder(Model_Receiveordercolumn::class)
            ->setMethods(['is_order_amount'])
            ->getMock();
        $receive_order_column_stub->expects($this->once())
            ->method('is_order_amount')
            ->will($this->returnValue(false));

        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->receive_order_column = $receive_order_column_stub;

        $args = [$receive_order, $update_column];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_can_update_order_amount');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        $this->assertTrue($result);
    }

    /**
     * @expectedException PhpErrorException
     */
    public function test__can_update_payment_method_引数の受注伝票情報の項目が不足している場合に例外が発生すること() {

        // 受注伝票情報
        $receive_order = [
            'receive_order_id' => '1',
        ];

        // 受注伝票項目のスタブ
        $receive_order_column_stub = $this->getMockBuilder(Model_Receiveordercolumn::class)
            ->setMethods(['is_payment_method_id'])
            ->getMock();
        $receive_order_column_stub->expects($this->once())
            ->method('is_payment_method_id')
            ->will($this->returnValue(true));

        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->receive_order_column = $receive_order_column_stub;

        $args = [$receive_order, $update_column];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_can_update_payment_method');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
    }

    public function test__can_update_order_amount_更新項目が受注金額関連項目かつ受注伝票の受注状態が起票済みの場合にtrueを返すこと() {

        // 受注伝票情報
        $receive_order = [
            'receive_order_id' => '1',
            'receive_order_order_status_id' => \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_ISSUED
        ];

        // 受注伝票項目のスタブ
        $receive_order_column_stub = $this->getMockBuilder(Model_Receiveordercolumn::class)
            ->setMethods(['is_order_amount'])
            ->getMock();
        $receive_order_column_stub->expects($this->once())
            ->method('is_order_amount')
            ->will($this->returnValue(true));

        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->receive_order_column = $receive_order_column_stub;

        $args = [$receive_order, $update_column];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_can_update_order_amount');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        $this->assertTrue($result);
    }

    public function test__can_update_order_amount_更新項目が受注金額関連項目かつ受注伝票の受注状態が起票済み以外の場合にfalseを返すこと() {

        // 受注伝票情報
        $receive_order = [
            'receive_order_id' => '1',
            'receive_order_order_status_id' => \Client_Neapi::STATUS_CODE_RECEIVE_ORDER_SHIPPED
        ];

        // 受注伝票項目のスタブ
        $receive_order_column_stub = $this->getMockBuilder(Model_Receiveordercolumn::class)
            ->setMethods(['is_order_amount'])
            ->getMock();
        $receive_order_column_stub->expects($this->once())
            ->method('is_order_amount')
            ->will($this->returnValue(true));

        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->receive_order_column = $receive_order_column_stub;

        $args = [$receive_order, $update_column];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_can_update_order_amount');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        $this->assertFalse($result);
    }

    /**
     * @expectedException PhpErrorException
     */
    public function test__can_update_order_amount_引数の受注伝票情報の項目が不足している場合に例外が発生すること() {

        // 受注伝票情報
        $receive_order = [
            'receive_order_id' => '1',
        ];

        // 受注伝票項目のスタブ
        $receive_order_column_stub = $this->getMockBuilder(Model_Receiveordercolumn::class)
            ->setMethods(['is_order_amount'])
            ->getMock();
        $receive_order_column_stub->expects($this->once())
            ->method('is_order_amount')
            ->will($this->returnValue(true));

        $update_column = new Model_Executionbulkupdatecolumn();
        $update_column->receive_order_column = $receive_order_column_stub;

        $args = [$receive_order, $update_column];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_can_update_order_amount');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
    }

    public function test__can_update_yahoo_cancel_yahooの受注伝票キャンセル更新許可フラグが1の場合更新して良いのでtrueを返すこと(){
        $shop_id = '1';
        $physical_name = 'receive_order_cancel_type_id';
        $update_value = '0';
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $execution_bulk_update_setting->allow_update_yahoo_cancel = '1';
        $yahoo_shop_ids = ['2', '4', '6'];
        $args = [$shop_id, $physical_name, $update_value, $execution_bulk_update_setting, &$yahoo_shop_ids];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_can_update_yahoo_cancel');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        $this->assertTrue($result);
    }

    public function test__can_update_yahoo_cancel_physical_nameが受注キャンセルでない場合更新して良いのでtrueを返すこと(){
        $shop_id = '1';
        $physical_name = 'receive_order_date';
        $update_value = '0';
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $execution_bulk_update_setting->allow_update_yahoo_cancel = '0';
        $yahoo_shop_ids = ['2', '4', '6'];
        $args = [$shop_id, $physical_name, $update_value, $execution_bulk_update_setting, &$yahoo_shop_ids];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_can_update_yahoo_cancel');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        $this->assertTrue($result);
    }

    public function test__can_update_yahoo_cancel_更新する値が0である場合更新して良いのでtrueを返すこと(){
        $shop_id = '1';
        $physical_name = 'receive_order_date';
        $update_value = '0';
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $execution_bulk_update_setting->allow_update_yahoo_cancel = '0';
        $yahoo_shop_ids = ['2', '4', '6'];
        $args = [$shop_id, $physical_name, $update_value, $execution_bulk_update_setting, &$yahoo_shop_ids];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_can_update_yahoo_cancel');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        $this->assertTrue($result);
    }

    public function test__can_update_yahoo_cancel_対象の受注伝票がyahooの店舗のものでない場合更新して良いのでtrueを返すこと(){
        $shop_id = '1';
        $physical_name = 'receive_order_date';
        $update_value = '1';
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $execution_bulk_update_setting->allow_update_yahoo_cancel = '0';
        $yahoo_shop_ids = ['2', '4', '6'];
        $args = [$shop_id, $physical_name, $update_value, $execution_bulk_update_setting, &$yahoo_shop_ids];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_can_update_yahoo_cancel');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        $this->assertTrue($result);
    }

    public function test__can_update_yahoo_cancel_yahoo更新フラグがfalseで対象の受注伝票がyahoo店舗のもので受注キャンセル区分を0以外にしようとしている場合は更新してはいけないのでfalseを返す(){
        $shop_id = '2';
        $physical_name = 'receive_order_cancel_type_id';
        $update_value = '1';
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $execution_bulk_update_setting->allow_update_yahoo_cancel = '0';
        $yahoo_shop_ids = ['2', '4', '6'];
        $args = [$shop_id, $physical_name, $update_value, $execution_bulk_update_setting, &$yahoo_shop_ids];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_can_update_yahoo_cancel');
        $result = $method->invokeArgs($domain_model_updatesetting, $args);
        $this->assertFalse($result);
    }

    public function test__can_update_yahoo_cancel_引数のyahoo_shop_idsにnullを渡すとapi経由でyahooの店舗を自動で取得しアドレス参照で呼び出し元のyahoo_shop_idsにも値が入っていること(){
        $yahoo_shop_ids = ['2', '4', '6', '8'];
        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['get_shop_ids'])
            ->getMock();
        $ne_api_stub->expects($this->once())
            ->method('get_shop_ids')
            ->with($this->equalTo(['shop_mall_id-eq' => Client_Neapi::MALL_CODE_YAHOO]))
            ->will($this->returnValue($yahoo_shop_ids));

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        $stub = $this->getMockBuilder(Domain_Model_Updatesetting::class)
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->with($this->equalTo(self::DUMMY_USER_ID1))
            ->will($this->returnValue($ne_api_stub));

        $shop_id = '1';
        $physical_name = 'receive_order_cancel_type_id';
        $update_value = '1';
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $execution_bulk_update_setting->allow_update_yahoo_cancel = '0';
        $given_yahoo_shop_ids = null;
        $args = [$shop_id, $physical_name, $update_value, $execution_bulk_update_setting, &$given_yahoo_shop_ids];
        $method = $this->getMethod(get_class($stub), '_can_update_yahoo_cancel');
        $method->setAccessible(true);
        $result = $method->invokeArgs($stub, $args);
        $this->assertEquals($yahoo_shop_ids, $given_yahoo_shop_ids);
        // receive_order_shop_idがyahoo以外の店舗なのでtrue
        $this->assertTrue($result);
    }

    public function test__get_retry_orders_リトライ対象伝票が2件ある場合その2件を返すこと(){
        $bulkupdate_response = [
            'result'  => \Client_Neapi::RESULT_ERROR,
            'code'    => \Client_Neapi::ERROR_CODE_BULKUPDATE,
            'message' => [
                ['receive_order_id' => '1', 'code' => 'dummy_code'],
                ['receive_order_id' => '2', 'code' => \Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE],
                ['receive_order_id' => '4', 'code' => \Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE],
            ],
        ];
        $retry_codes = [\Client_Neapi::ERROR_CODE_RECEIVE_ORDER_IMPORTING, \Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_get_retry_orders');
        $result = $method->invoke(null, $bulkupdate_response, $retry_codes);
        $this->assertEquals(['2', '4'], $result);
    }

    public function test__get_retry_orders_異なるリトライコードでリトライ対象が2件ある場合その2件を返すこと(){
        $bulkupdate_response = [
            'result'  => \Client_Neapi::RESULT_ERROR,
            'code'    => \Client_Neapi::ERROR_CODE_BULKUPDATE,
            'message' => [
                ['receive_order_id' => '1', 'code' => 'dummy_code'],
                ['receive_order_id' => '2', 'code' => \Client_Neapi::ERROR_CODE_RECEIVE_ORDER_IMPORTING],
                ['receive_order_id' => '4', 'code' => \Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE],
            ],
        ];
        $retry_codes = [\Client_Neapi::ERROR_CODE_RECEIVE_ORDER_IMPORTING, \Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_get_retry_orders');
        $result = $method->invoke(null, $bulkupdate_response, $retry_codes);
        $this->assertEquals(['2', '4'], $result);
    }

    public function test__get_retry_orders_リトライ対象コードが空配列の場合空配列を返すこと(){
        $bulkupdate_response = [
            'result'  => \Client_Neapi::RESULT_ERROR,
            'code'    => \Client_Neapi::ERROR_CODE_BULKUPDATE,
            'message' => [
                ['receive_order_id' => '1', 'code' => 'dummy_code'],
                ['receive_order_id' => '2', 'code' => \Client_Neapi::ERROR_CODE_RECEIVE_ORDER_IMPORTING],
                ['receive_order_id' => '4', 'code' => \Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE],
            ],
        ];
        $retry_codes = [];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_get_retry_orders');
        $result = $method->invoke(null, $bulkupdate_response, $retry_codes);
        $this->assertEquals([], $result);
    }

    public function test__get_retry_orders_リトライ対象伝票が1件もない場合は空配列を返すこと(){
        $bulkupdate_response = [
            'result'  => \Client_Neapi::RESULT_ERROR,
            'code'    => \Client_Neapi::ERROR_CODE_BULKUPDATE,
            'message' => [
                ['receive_order_id' => '1', 'code' => 'dummy_code1'],
                ['receive_order_id' => '2', 'code' => 'dummy_code2'],
                ['receive_order_id' => '3', 'code' => 'dummy_code3'],
            ],
        ];
        $retry_codes = [\Client_Neapi::ERROR_CODE_RECEIVE_ORDER_IMPORTING, \Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_get_retry_orders');
        $result = $method->invoke(null, $bulkupdate_response, $retry_codes);
        $this->assertEquals([], $result);
    }

    public function test__get_retry_orders_全件成功してる場合は空配列を返すこと(){
        $bulkupdate_response = [
            'result'  => \Client_Neapi::RESULT_SUCCESS,
            'message' => '',
        ];
        $retry_codes = [\Client_Neapi::ERROR_CODE_RECEIVE_ORDER_IMPORTING, \Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_get_retry_orders');
        $result = $method->invoke(null, $bulkupdate_response, $retry_codes);
        $this->assertEquals([], $result);
    }

    public function test__get_retry_orders_そもそも一括更新処理で失敗してる場合は空配列を返すこと(){
        $bulkupdate_response = [
            'result'  => \Client_Neapi::RESULT_ERROR,
            'code'    => \Client_Neapi::ERROR_CODE_PLATFORM_MAINTENANCE,
            'message' => 'メンテナンス中です',
        ];
        $retry_codes = [\Client_Neapi::ERROR_CODE_RECEIVE_ORDER_IMPORTING, \Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_get_retry_orders');
        $result = $method->invoke(null, $bulkupdate_response, $retry_codes);
        $this->assertEquals([], $result);
    }

    public function test__get_retry_codes_他者更新伝票のリトライフラグがoffの場合デフォルトでリトライ対象となるもののコードの配列を返すこと(){
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $execution_bulk_update_setting->allow_optimistic_lock_update_retry = '0';
        $expect = [\Client_Neapi::ERROR_CODE_RECEIVE_ORDER_IMPORTING];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_get_retry_codes');
        $result = $method->invoke(null, $execution_bulk_update_setting);
        $this->assertEquals($expect, $result);
    }

    public function test__get_retry_codes_他者更新伝票のリトライフラグがonの場合デフォルトでリトライ対象となるもののコード＋他者更新伝票のコードの配列を返すこと(){
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1);
        $execution_bulk_update_setting->allow_optimistic_lock_update_retry = '1';
        $expect = [\Client_Neapi::ERROR_CODE_RECEIVE_ORDER_IMPORTING, \Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_get_retry_codes');
        $result = $method->invoke(null, $execution_bulk_update_setting);
        $this->assertEquals($expect, $result);
    }

    public function test__merge_retry_bulkupdate_response_第一引数と第二引数がそれぞれエラーを含むレスポンスだった場合それらを統合した結果を返すこと(){
        $bulkupdate_response = [
            'result'  => \Client_Neapi::RESULT_ERROR,
            'code'    => \Client_Neapi::ERROR_CODE_BULKUPDATE,
            'message' => [
                ['receive_order_id' => '1', 'code' => 'dummy_code', 'message' => 'dummy message'],
                ['receive_order_id' => '2', 'code' => \Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => 'receive_order_last_modified_dateが更新されています。再度受注伝票を検索してAPIを実行して下さい。'],
                ['receive_order_id' => '3', 'code' => '020015', 'message' => '[receive_order_date]日付の形式ではありません。'],
                ['receive_order_id' => '4', 'code' => \Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => 'receive_order_last_modified_dateが更新されています。再度受注伝票を検索してAPIを実行して下さい。'],
            ],
        ];

        // 伝票番号4はリトライしても失敗したというケース
        $retry_bulkupdate_response = [
            'result'  => \Client_Neapi::RESULT_ERROR,
            'code'    => \Client_Neapi::ERROR_CODE_BULKUPDATE,
            'message' => [
                ['receive_order_id' => '4', 'code' => \Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => 'receive_order_last_modified_dateが更新されています。再度受注伝票を検索してAPIを実行して下さい。'],
            ],
        ];

        $failure_orders = ['2', '4'];
        $expect = [
            'result'  => \Client_Neapi::RESULT_ERROR,
            'code'    => \Client_Neapi::ERROR_CODE_BULKUPDATE,
            'message' => [
                ['receive_order_id' => '4', 'code' => \Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => 'receive_order_last_modified_dateが更新されています。再度受注伝票を検索してAPIを実行して下さい。'],
                ['receive_order_id' => '1', 'code' => 'dummy_code', 'message' => 'dummy message'],
                ['receive_order_id' => '3', 'code' => '020015', 'message' => '[receive_order_date]日付の形式ではありません。'],
            ],
        ];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_merge_retry_bulkupdate_response');
        $result = $method->invoke(null, $bulkupdate_response, $retry_bulkupdate_response, $failure_orders);
        $this->assertEquals($expect, $result);
    }

    public function test__merge_retry_bulkupdate_response_第二引数が成功レスポンスの場合は第一引数レスポンスからリトライで成功したものを除いた結果を返すこと(){
        $bulkupdate_response = [
            'result'  => \Client_Neapi::RESULT_ERROR,
            'code'    => \Client_Neapi::ERROR_CODE_BULKUPDATE,
            'message' => [
                ['receive_order_id' => '1', 'code' => 'dummy_code', 'message' => 'dummy message'],
                ['receive_order_id' => '2', 'code' => \Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => 'receive_order_last_modified_dateが更新されています。再度受注伝票を検索してAPIを実行して下さい。'],
                ['receive_order_id' => '3', 'code' => '020015', 'message' => '[receive_order_date]日付の形式ではありません。'],
                ['receive_order_id' => '4', 'code' => \Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => 'receive_order_last_modified_dateが更新されています。再度受注伝票を検索してAPIを実行して下さい。'],
            ],
        ];

        // リトライ後全て成功した場合
        $retry_bulkupdate_response = [
            'result'  => \Client_Neapi::RESULT_SUCCESS,
            'message' => '',
        ];

        $failure_orders = ['2', '4'];
        $expect = [
            'result'  => \Client_Neapi::RESULT_ERROR,
            'code'    => \Client_Neapi::ERROR_CODE_BULKUPDATE,
            'message' => [
                ['receive_order_id' => '1', 'code' => 'dummy_code', 'message' => 'dummy message'],
                ['receive_order_id' => '3', 'code' => '020015', 'message' => '[receive_order_date]日付の形式ではありません。'],
            ],
        ];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_merge_retry_bulkupdate_response');
        $result = $method->invoke(null, $bulkupdate_response, $retry_bulkupdate_response, $failure_orders);
        $this->assertEquals($expect, $result);
    }

    public function test__merge_retry_bulkupdate_response_第一引数と第二引数がどちらもエラーのないものの場合成功のレスポンスとして返すこと(){
        $bulkupdate_response = [
            'result'  => \Client_Neapi::RESULT_SUCCESS,
            'message' => '',
        ];

        $retry_bulkupdate_response = [
            'result'  => \Client_Neapi::RESULT_SUCCESS,
            'message' => '',
        ];

        $failure_orders = [];
        $expect = [
            'result'  => \Client_Neapi::RESULT_SUCCESS,
            'message' => '',
        ];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_merge_retry_bulkupdate_response');
        $result = $method->invoke(null, $bulkupdate_response, $retry_bulkupdate_response, $failure_orders);
        $this->assertEquals($expect, $result);
    }

    public function test__merge_retry_bulkupdate_response_第一引数が何かしらエラーのあるレスポンスの場合第一引数をそのまま返すこと(){
        $bulkupdate_response = [
            'result'  => \Client_Neapi::RESULT_ERROR,
            'code'    => \Client_Neapi::ERROR_CODE_MAIN_FUNCTION_MAINTENANCE ,
            'message' => 'メイン機能がメンテナンス中でした',
        ];

        $retry_bulkupdate_response = [
            'result'  => \Client_Neapi::RESULT_ERROR,
            'code'    => \Client_Neapi::ERROR_CODE_BULKUPDATE,
            'message' => [
                ['receive_order_id' => '4', 'code' => \Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => 'receive_order_last_modified_dateが更新されています。再度受注伝票を検索してAPIを実行して下さい。'],
            ],
        ];

        $failure_orders = ['2', '4'];

        $expect = [
            'result'  => \Client_Neapi::RESULT_ERROR,
            'code'    => \Client_Neapi::ERROR_CODE_MAIN_FUNCTION_MAINTENANCE ,
            'message' => 'メイン機能がメンテナンス中でした',
        ];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_merge_retry_bulkupdate_response');
        $result = $method->invoke(null, $bulkupdate_response, $retry_bulkupdate_response, $failure_orders);
        $this->assertEquals($expect, $result);
    }

    public function test__merge_retry_bulkupdate_response_第二引数が何かしらのエラーのあるレスポンスの場合第一引数をそのまま返すこと(){
        $bulkupdate_response = [
            'result'  => \Client_Neapi::RESULT_ERROR,
            'code'    => \Client_Neapi::ERROR_CODE_BULKUPDATE,
            'message' => [
                ['receive_order_id' => '1', 'code' => 'dummy_code', 'message' => 'dummy message'],
                ['receive_order_id' => '2', 'code' => \Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => 'receive_order_last_modified_dateが更新されています。再度受注伝票を検索してAPIを実行して下さい。'],
                ['receive_order_id' => '3', 'code' => '020015', 'message' => '[receive_order_date]日付の形式ではありません。'],
                ['receive_order_id' => '4', 'code' => \Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => 'receive_order_last_modified_dateが更新されています。再度受注伝票を検索してAPIを実行して下さい。'],
            ],
        ];

        $retry_bulkupdate_response = [
            'result'  => \Client_Neapi::RESULT_ERROR,
            'code'    => \Client_Neapi::ERROR_CODE_MAIN_FUNCTION_MAINTENANCE ,
            'message' => 'メイン機能がメンテナンス中でした',
        ];

        $failure_orders = ['2', '4'];
        $expect = [
            'result'  => \Client_Neapi::RESULT_ERROR,
            'code'    => \Client_Neapi::ERROR_CODE_BULKUPDATE,
            'message' => [
                ['receive_order_id' => '1', 'code' => 'dummy_code', 'message' => 'dummy message'],
                ['receive_order_id' => '2', 'code' => \Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => 'receive_order_last_modified_dateが更新されています。再度受注伝票を検索してAPIを実行して下さい。'],
                ['receive_order_id' => '3', 'code' => '020015', 'message' => '[receive_order_date]日付の形式ではありません。'],
                ['receive_order_id' => '4', 'code' => \Client_Neapi::ERROR_CODE_BULKUPDATE_LAST_MODIFIED_DATE, 'message' => 'receive_order_last_modified_dateが更新されています。再度受注伝票を検索してAPIを実行して下さい。'],
            ],
        ];

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_merge_retry_bulkupdate_response');
        $result = $method->invoke(null, $bulkupdate_response, $retry_bulkupdate_response, $failure_orders);
        $this->assertEquals($expect, $result);
    }

    public function test__get_receive_order_bulkupdate_xml_意図した形のxmlが生成されること(){
        $receive_orders = [
            [
                'receive_order_total_amount' => '10000',
                'receive_order_note' => '備考欄を上書きする設定',
                'receive_order_worker_text' => '作業用欄に適当なテキスト',
                'receive_order_option_noshi' => 'のし欄に適当なテキスト',
                'receive_order_id' => '1',
                'receive_order_last_modified_date' => '2018-04-26 17:22:25',
                'receive_order_order_status_id' => '10',
                'receive_order_confirm_ids' => 'AG',
            ],
            [
                'receive_order_total_amount' => '10000',
                'receive_order_note' => '備考欄を上書きする設定',
                'receive_order_worker_text' => '作業用欄に適当なテキスト',
                'receive_order_option_noshi' => 'のし欄に適当なテキスト',
                'receive_order_id' => '2',
                'receive_order_last_modified_date' => '2018-04-26 17:22:52',
                'receive_order_order_status_id' => '50',
                'receive_order_confirm_ids' => 'AH',
            ],
        ];
        $expect = '<?xml version="1.0" encoding="utf-8"?>
<root><receiveorder receive_order_id="1" receive_order_last_modified_date="2018-04-26 17:22:25"><receiveorder_base><receive_order_total_amount>10000</receive_order_total_amount><receive_order_note>備考欄を上書きする設定</receive_order_note><receive_order_worker_text>作業用欄に適当なテキスト</receive_order_worker_text><receive_order_confirm_ids>AG</receive_order_confirm_ids></receiveorder_base><receiveorder_option><receive_order_option_noshi>のし欄に適当なテキスト</receive_order_option_noshi></receiveorder_option></receiveorder><receiveorder receive_order_id="2" receive_order_last_modified_date="2018-04-26 17:22:52"><receiveorder_base><receive_order_total_amount>10000</receive_order_total_amount><receive_order_note>備考欄を上書きする設定</receive_order_note><receive_order_worker_text>作業用欄に適当なテキスト</receive_order_worker_text><receive_order_confirm_ids>AH</receive_order_confirm_ids></receiveorder_base><receiveorder_option><receive_order_option_noshi>のし欄に適当なテキスト</receive_order_option_noshi></receiveorder_option></receiveorder></root>
';

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_get_receive_order_bulkupdate_xml');
        $result = $method->invoke(null, $receive_orders);
        $this->assertEquals($expect, $result);
    }

    public function test__get_receive_order_bulkupdate_xml_アンパサンドが含まれた文字列はエスケープされたxmlを返すこと(){
        $receive_orders = [
            [
                'receive_order_total_amount' => '10000',
                'receive_order_note' => '&&&&&&&備考欄を上書きする設定',
                'receive_order_worker_text' => '作業用欄に適当なテキスト&&&&&&&',
                'receive_order_option_noshi' => 'のし欄に適当な&&&&&&&&テキスト',
                'receive_order_id' => '1',
                'receive_order_last_modified_date' => '2018-04-26 17:22:25',
                'receive_order_order_status_id' => '10',
            ],
            [
                'receive_order_total_amount' => '10000',
                'receive_order_note' => '備考欄を上書きする設定',
                'receive_order_worker_text' => '作業用欄に適当なテキスト',
                'receive_order_option_noshi' => '&のし欄&に適当&なテキスト&',
                'receive_order_id' => '2',
                'receive_order_last_modified_date' => '2018-04-26 17:22:52',
                'receive_order_order_status_id' => '50',
            ],
        ];
        $expect = '<?xml version="1.0" encoding="utf-8"?>
<root><receiveorder receive_order_id="1" receive_order_last_modified_date="2018-04-26 17:22:25"><receiveorder_base><receive_order_total_amount>10000</receive_order_total_amount><receive_order_note>&amp;&amp;&amp;&amp;&amp;&amp;&amp;備考欄を上書きする設定</receive_order_note><receive_order_worker_text>作業用欄に適当なテキスト&amp;&amp;&amp;&amp;&amp;&amp;&amp;</receive_order_worker_text></receiveorder_base><receiveorder_option><receive_order_option_noshi>のし欄に適当な&amp;&amp;&amp;&amp;&amp;&amp;&amp;&amp;テキスト</receive_order_option_noshi></receiveorder_option></receiveorder><receiveorder receive_order_id="2" receive_order_last_modified_date="2018-04-26 17:22:52"><receiveorder_base><receive_order_total_amount>10000</receive_order_total_amount><receive_order_note>備考欄を上書きする設定</receive_order_note><receive_order_worker_text>作業用欄に適当なテキスト</receive_order_worker_text></receiveorder_base><receiveorder_option><receive_order_option_noshi>&amp;のし欄&amp;に適当&amp;なテキスト&amp;</receive_order_option_noshi></receiveorder_option></receiveorder></root>
';

        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $method = $this->getMethod(get_class($domain_model_updatesetting), '_get_receive_order_bulkupdate_xml');
        $result = $method->invoke(null, $receive_orders);
        $this->assertEquals($expect, $result);
    }

    /**
     * @dataProvider Domain_model_Updatesettingprovider::data_provider_get_setting_for_validation_error
     */
    public function test_get_setting_for_validation_error_取得した設定情報のtemporaryが0になっておりDBに保存されてないこと($company_id, $user_id, $post_params, $result_column_count) {

        $bulk_update_setting = Domain_Model_Updatesetting::get_setting_for_validation_error($company_id, $user_id, $post_params);
        $this->assertEquals(0, $bulk_update_setting->temporary);
        $this->assertNull(Model_Bulkupdatesetting::findOne(['id' => $bulk_update_setting->id]));

        // 保存されてないこと
        $serach_bulk_update_setting = Model_Bulkupdatesetting::findOne(['name' => $post_params['name']]);
        $this->assertNull($serach_bulk_update_setting);

        // データが取得できていること
        self::_bulk_update_setting_params_check($post_params, $company_id, $user_id, 0, null, $result_column_count, $bulk_update_setting);
    }

    /**
     * @dataProvider Domain_model_Updatesettingprovider::data_provider_get_setting_for_validation_error
     */
    public function test_get_setting_for_validation_error_バリデーションエラーの際には元の伝票の作成日、更新日、作成者、最終更新者が入ること($company_id, $user_id, $post_params, $result_column_count) {

        $post_params['bulk_update_setting_id'] = self::DUMMY_BULK_UPDATE_SETTING_ID2;
        $bulk_update_setting = Domain_Model_Updatesetting::get_setting_for_validation_error($company_id, $user_id, $post_params);

        // id2の設定を取得
        $bulk_update_setting_id2 = Model_Bulkupdatesetting::findOne(['id' => self::DUMMY_BULK_UPDATE_COLUMN_ID2]);

        // bulk_update_settingの値が取れていて正しい値が入っていること
        $this->assertEquals($bulk_update_setting->name, $bulk_update_setting_id2->name);
        $this->assertEquals($bulk_update_setting->created_at, $bulk_update_setting_id2->created_at);
        $this->assertEquals($bulk_update_setting->updated_at, $bulk_update_setting_id2->updated_at);
        $this->assertEquals($bulk_update_setting->created_user, $bulk_update_setting_id2->created_user);
        $this->assertEquals($bulk_update_setting->last_updated_user, $bulk_update_setting_id2->last_updated_user);
    }

    /**
     * @dataProvider Domain_model_Updatesettingprovider::data_provider_save_for_success
     */
    public function test_save_保存に成功した場合その更新設定のIDが返ること($company_id, $user_id, $post_params, $is_temporary, $result_column_count) {
        $bulk_update_setting_id = Domain_Model_Updatesetting::save($company_id, $user_id, $post_params, $is_temporary);
        // データが保存されていること
        $serach_bulk_update_setting = Model_Bulkupdatesetting::findOne(['name' => $post_params['name']]);
        $this->assertEquals($serach_bulk_update_setting->id, $bulk_update_setting_id);
        // 保存されたデータが正しいこと
        self::_bulk_update_setting_params_check(
            $post_params,
            $company_id,
            $user_id,
            $is_temporary ? 1 : 0,
            $is_temporary && isset($post_params['bulk_update_setting_id']) ? $post_params['bulk_update_setting_id'] : null,
            $result_column_count,
            $serach_bulk_update_setting);
    }

    public function test_save_更新した場合最終更新日と最終更新者が更新されること() {
        // すぐに更新すると時間が変わらない場合があるのでスリープを行う
        sleep(1);
        $post_params = [
            'name' => '設定名_更新',
            'bulk_update_setting_id' => self::DUMMY_BULK_UPDATE_SETTING_ID1,
            Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => [1, 2],
            Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => [1 => Model_Updatemethod::OVERWRITE, 2 => Model_Updatemethod::ADDWRITE],
            Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME => [1 => 4],
            Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME => [2 => 5],
            Domain_Model_Updatesetting::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME => 0,
            Domain_Model_Updatesetting::UPDATE_YAHOO_CANCEL_ELEMENT_NAME => 0,
            Domain_Model_Updatesetting::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME => 0,
            Domain_Model_Updatesetting::REFLECT_ORDER_AMOUNT => 0,
        ];

        $before_bulk_update_setting = Model_Bulkupdatesetting::findOne(['id' => self::DUMMY_BULK_UPDATE_SETTING_ID1]);
        Domain_Model_Updatesetting::save(self::DUMMY_USER_ID1, self::DUMMY_USER_ID2, $post_params);
        $after_bulk_update_setting = Model_Bulkupdatesetting::findOne(['id' => self::DUMMY_BULK_UPDATE_SETTING_ID1]);
        $this->assertEquals(self::DUMMY_USER_ID2, $after_bulk_update_setting->last_updated_user_id);
        $this->assertNotEquals($before_bulk_update_setting->updated_at, $after_bulk_update_setting->updated_at);
    }

    public function test_save_保存に失敗した場合は例外エラーが発生すること() {
        $this->markTestSkipped('staticメソッドのため例外を発生させられないためスキップします');
    }

    private function _bulk_update_setting_params_check(array $post_params, string $company_id, string $user_id, string $temporary, $original_bulk_update_setting_id, int $result_column_count,  Model_Bulkupdatesetting $bulk_update_setting) {
        $this->assertEquals($post_params['name'], $bulk_update_setting->name);
        $this->assertEquals($post_params['allow_update_shipment_confirmed'], $bulk_update_setting->allow_update_shipment_confirmed);
        $this->assertEquals($post_params['allow_update_yahoo_cancel'], $bulk_update_setting->allow_update_yahoo_cancel);
        $this->assertEquals($post_params['allow_optimistic_lock_update_retry'], $bulk_update_setting->allow_optimistic_lock_update_retry);
        $this->assertEquals($post_params['allow_reflect_order_amount'], $bulk_update_setting->allow_reflect_order_amount);
        $this->assertEquals($company_id, $bulk_update_setting->company_id);
        $this->assertEquals($temporary, $bulk_update_setting->temporary);
        $this->assertEquals($original_bulk_update_setting_id, $bulk_update_setting->original_bulk_update_setting_id);
        $this->assertEquals($user_id, $bulk_update_setting->created_user_id);
        $this->assertEquals($user_id, $bulk_update_setting->last_updated_user_id);

        $this->assertEquals($result_column_count, count($bulk_update_setting->bulk_update_columns));

        $bulk_update_columns = $bulk_update_setting->bulk_update_columns;
        for ($i = 0; $i < count($bulk_update_setting->bulk_update_columns); $i++) {
            $bulk_update_column = array_shift($bulk_update_columns);
            $this->assertEquals($bulk_update_setting->id, $bulk_update_column->bulk_update_setting_id);
            $column_id = $post_params[Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME][$i];
            $this->assertEquals($column_id, $bulk_update_column->receive_order_column_id);
            $this->assertEquals($post_params[Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME][$column_id], $bulk_update_column->update_method_id);
            if (isset($post_params[Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME][$column_id])) {
                $this->assertEquals($post_params[Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME][$column_id], $bulk_update_column->update_value);
            }else{
                $this->assertEquals($post_params[Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME][$column_id], $bulk_update_column->update_value);
            }
        }
    }

    /**
     * @dataProvider Domain_model_Updatesettingprovider::data_provider_create_setting
     */
    public function test__create_setting_正しく登録もしくは取得できること($company_id, $user_id, $post_params, $is_temporary, $is_save, $result_column_count) {

        // private staticのメソッドを実行したいためクロージャを使う
        // @see https://atuweb.net/201612_php-test-closure-private-static/
        Closure::bind(function() use ($company_id, $user_id, $post_params, $is_temporary, $is_save, $result_column_count) {
            $bulk_update_setting = self::_create_setting($company_id, $user_id, $post_params, $is_temporary, $is_save);

            $temporary = $is_temporary ? 1 : 0;
            $original_bulk_update_setting_id = $is_temporary && isset($post_params['bulk_update_setting_id']) ? $post_params['bulk_update_setting_id'] : null;
            Closure::bind(function() use ($post_params, $company_id, $user_id, $temporary, $original_bulk_update_setting_id, $is_save, $result_column_count, $bulk_update_setting) {

                // is_saveがtrueの場合は保存され、falseの場合は保存されてないこと
                if (isset($post_params['name'])) {
                    $serach_bulk_update_setting = Model_Bulkupdatesetting::findOne(['name' => $post_params['name']]);
                    if ($is_save) {
                        $this->assertNotNull($serach_bulk_update_setting);
                    }else{
                        $this->assertNull($serach_bulk_update_setting);
                    }
                }

                // データが保存されていること
                self::_bulk_update_setting_params_check(
                    $post_params,
                    $company_id,
                    $user_id,
                    $temporary,
                    $original_bulk_update_setting_id,
                    $result_column_count,
                    $bulk_update_setting);
            }, $this, Test_Domain_Model_Updatesetting::class)->__invoke();

        }, $this, Domain_Model_Updatesetting::class)->__invoke();
    }

    /**
     * @dataProvider Domain_model_Updatesettingprovider::data_provider_create_setting_exception
     * @expectedException InvalidArgumentException
     */
    public function test__create_setting_post_paramsに値がなかった場合例外が発生すること($post_params, $exception_message) {
        $company_id = self::DUMMY_COMPANY_ID1;
        $user_id = self::DUMMY_USER_ID1;
        Closure::bind(function() use ($company_id, $user_id, $post_params, $exception_message) {
            try {
                self::_create_setting($company_id, $user_id, $post_params);
            }catch (Exception $e) {
                $this->assertEquals($exception_message, $e->getMessage());
                throw $e;
            }

        }, $this, Domain_Model_Updatesetting::class)->__invoke();
    }

    public function test__get_insert_bulk_update_setting_Model_Bulkupdatesettingオブジェクトを取得できること() {
        $this->markTestSkipped('_get_bulk_update_setting_and_set_paramsでテストが行われているためスキップします');
    }

    public function test__get_update_bulk_update_setting_Model_Bulkupdatesettingオブジェクトを取得できること() {
        $this->markTestSkipped('Model_Bulkupdatesetting::get_settingおよび_get_bulk_update_setting_and_set_paramsでテストが行われているためスキップします');
    }

    /**
     * @dataProvider Domain_model_Updatesettingprovider::data_provider_get_bulk_update_setting_and_set_params
     */
    public function test__get_bulk_update_setting_and_set_params_Model_Bulkupdatesettingオブジェクトを取得できること(
        $name,
        $user_id,
        $post_params,
        $allow_update_shipment_confirmed,
        $allow_update_yahoo_cancel,
        $allow_optimistic_lock_update_retry,
        $allow_reflect_order_amount
    ) {
        Closure::bind(function() use (
            $name,
            $user_id,
            $post_params,
            $allow_update_shipment_confirmed,
            $allow_update_yahoo_cancel,
            $allow_optimistic_lock_update_retry,
            $allow_reflect_order_amount
        ) {
            $bulk_update_setting = new Model_Bulkupdatesetting();
            $create_bulk_update_setting = self::_get_bulk_update_setting_and_set_params($bulk_update_setting, $name, $user_id, $post_params);

            $this->assertEquals($name, $create_bulk_update_setting->name);
            $this->assertEquals($allow_update_shipment_confirmed, $create_bulk_update_setting->allow_update_shipment_confirmed);
            $this->assertEquals($allow_update_yahoo_cancel, $create_bulk_update_setting->allow_update_yahoo_cancel);
            $this->assertEquals($allow_optimistic_lock_update_retry, $create_bulk_update_setting->allow_optimistic_lock_update_retry);
            $this->assertEquals($allow_reflect_order_amount, $create_bulk_update_setting->allow_reflect_order_amount);
            $this->assertEquals($user_id, $create_bulk_update_setting->last_updated_user_id);

        }, $this, Domain_Model_Updatesetting::class)->__invoke();
    }

    public function test__get_create_bulk_update_column_Model_Bulkupdatecolumnオブジェクトを取得できること() {
        $this->markTestSkipped('_get_bulk_update_column_and_set_paramsでテストが行われているためスキップします');
    }

    public function test__get_update_bulk_update_column_Model_Bulkupdatecolumnオブジェクトを取得できること() {
        $this->markTestSkipped('_get_bulk_update_column_and_set_paramsでテストが行われているためスキップします');
    }

    /**
     * @dataProvider Domain_model_Updatesettingprovider::data_provider_get_bulk_update_column_and_set_params
     */
    public function test__get_bulk_update_column_and_set_params_Model_Bulkupdatecolumnオブジェクトを取得できること($receive_order_column_id, $post_params, $update_method_id, $update_value) {
        Closure::bind(function() use ($receive_order_column_id, $post_params, $update_method_id, $update_value) {
            $bulk_update_column = new Model_Bulkupdatecolumn();
            $bulk_update_column->receive_order_column_id = $receive_order_column_id;
            $create_bulk_update_column = self::_get_bulk_update_column_and_set_params($bulk_update_column, $post_params);

            $this->assertEquals($update_method_id, $create_bulk_update_column->update_method_id);
            $this->assertEquals($update_value, $create_bulk_update_column->update_value);

        }, $this, Domain_Model_Updatesetting::class)->__invoke();
    }

    /**
     * @dataProvider Domain_model_Updatesettingprovider::data_provider_get_bulk_update_column_and_set_params_exception
     * @expectedException InvalidArgumentException
     */
    public function test__get_bulk_update_column_and_set_params_値が取得できなかった場合例外エラーが発生すること($receive_order_column_id, $post_params, $exception_message) {
        Closure::bind(function() use ($receive_order_column_id, $post_params, $exception_message) {
            $bulk_update_column = new Model_Bulkupdatecolumn();
            $bulk_update_column->receive_order_column_id = $receive_order_column_id;

            try {
                self::_get_bulk_update_column_and_set_params($bulk_update_column, $post_params);
            }catch (Exception $e) {
                $this->assertEquals($exception_message, $e->getMessage());
                throw $e;
            }

        }, $this, Domain_Model_Updatesetting::class)->__invoke();
    }

    public function test_execution_enque_除外対象が無い状態で実行されること() {

        $mock = new class extends Domain_Model_Updatesetting {
            public static $request_receiveorder_search_data = ['receive_order_shop_id' => 1, 'receive_order_shop_cut_form_id' => 2];

            public static function request_receiveorder_search(Client_Neapi $client_neapi, string $extension_execution_id) : array {
                return ['result' => \Client_Neapi::RESULT_SUCCESS, 'count' => 1, 'data' => self::$request_receiveorder_search_data];
            }

            public static function enque_sqs(string $company_id, string $execution_bulk_update_setting_id) {}
        };

        // 無名関数をそのままモックにするとエラーが発生するためエイリアスを作成する（おそらく無名関数名に禁止文字が入っているため）
        $stub = $this->getMockBuilder(self::_get_alias($mock))
            ->setMethods(['get_execution_bulk_update_setting', 'convert'])
            ->getMock();
        $dummy_params = Model_Bulkupdatesetting::get_validation_params_by_bulk_update_setting_id(self::DUMMY_COMPANY_ID1, self::DUMMY_BULK_UPDATE_SETTING_ID1);
        $execution_bulk_update_setting = new Model_Executionbulkupdatesetting();
        $execution_bulk_update_setting->id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID3;
        $execution_bulk_update_setting->request_key = self::DUMMY_REQUEST_KEY1;
        $execution_bulk_update_setting->user_id = self::DUMMY_USER_ID1;
        $execution_bulk_update_setting->company_id = self::DUMMY_COMPANY_ID1;
        $execution_bulk_update_setting->name = 'TEST';
        $execution_bulk_update_setting->extension_execution_id = self::DUMMY_EXTENSION_EXCUTION_ID3;
        $stub->expects($this->once())
            ->method('get_execution_bulk_update_setting')
            ->with(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1, self::DUMMY_EXTENSION_EXCUTION_ID3, $dummy_params)
            ->will($this->returnValue($execution_bulk_update_setting));
        $convert_result = new \Domain_Value_Convertresult(['DUMMY2', 'DUMMY3', 'DUMMY4'], []);
        $stub->expects($this->once())
            ->method('convert')
            ->with($execution_bulk_update_setting, $mock::$request_receiveorder_search_data)
            ->will($this->returnValue($convert_result));
        $result = $stub->execution_enque(self::DUMMY_EXTENSION_EXCUTION_ID3, self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1, $dummy_params, []);

        $this->assertTrue($result->get_result());
        $this->assertEquals(self::DUMMY_BULK_UPDATE_SETTING_ID3, $result->get_execution_bulkupdate_setting()->id);
        $this->assertEquals(3, $result->get_execution_bulkupdate_setting()->target_order_count);
    }

    public function test_execution_enque_除外対象がある状態で実行されること() {

        $mock = new class extends Domain_Model_Updatesetting {
            public static $request_receiveorder_search_data = ['receive_order_shop_id' => 1, 'receive_order_shop_cut_form_id' => 2];

            public static function request_receiveorder_search(Client_Neapi $client_neapi, string $extension_execution_id) : array {
                return ['result' => \Client_Neapi::RESULT_SUCCESS, 'count' => 2, 'data' => self::$request_receiveorder_search_data];
            }

            public static function enque_sqs(string $company_id, string $execution_bulk_update_setting_id) {}

            public static function get_task_id(string $company_id) : string {
                return Test_Domain_Model_Updatesetting::DUMMY_REQUEST_KEY1;
            }

            protected static function bulk_insert_excluded_receive_orders(Model_Executionbulkupdatesetting $execution_bulkupdate_setting, array $exclude_orders) : bool {
                return true;
            }
        };

        // 無名関数をそのままモックにするとエラーが発生するためエイリアスを作成する（おそらく無名関数名に禁止文字が入っているため）
        $stub = $this->getMockBuilder(self::_get_alias($mock))
            ->setMethods(['get_execution_bulk_update_setting', 'convert'])
            ->getMock();
        $dummy_params = Model_Bulkupdatesetting::get_validation_params_by_bulk_update_setting_id(self::DUMMY_COMPANY_ID1, self::DUMMY_BULK_UPDATE_SETTING_ID1);
        $execution_bulk_update_setting = new Model_Executionbulkupdatesetting();
        $execution_bulk_update_setting->id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID3;
        $execution_bulk_update_setting->request_key = self::DUMMY_REQUEST_KEY1;
        $execution_bulk_update_setting->user_id = self::DUMMY_USER_ID1;
        $execution_bulk_update_setting->company_id = self::DUMMY_COMPANY_ID1;
        $execution_bulk_update_setting->name = 'TEST';
        $execution_bulk_update_setting->extension_execution_id = self::DUMMY_EXTENSION_EXCUTION_ID3;
        $stub->expects($this->once())
            ->method('get_execution_bulk_update_setting')
            ->with(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1, self::DUMMY_EXTENSION_EXCUTION_ID3, $dummy_params)
            ->will($this->returnValue($execution_bulk_update_setting));
        $convert_result = new \Domain_Value_Convertresult(['DUMMY2', 'DUMMY3', 'DUMMY4'], []);
        $stub->expects($this->once())
            ->method('convert')
            ->with($execution_bulk_update_setting, $mock::$request_receiveorder_search_data)
            ->will($this->returnValue($convert_result));
        $result = $stub->execution_enque(self::DUMMY_EXTENSION_EXCUTION_ID3, self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1, $dummy_params, ['DUMMY']);

        $this->assertTrue($result->get_result());
        $this->assertEquals(self::DUMMY_BULK_UPDATE_SETTING_ID3, $result->get_execution_bulkupdate_setting()->id);
        $this->assertEquals(3, $result->get_execution_bulkupdate_setting()->target_order_count);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_execution_enque_paramsにbulk_update_setting_idがない場合、例外エラーが発生すること() {
        $params = Model_Bulkupdatesetting::get_validation_params_by_bulk_update_setting_id(self::DUMMY_COMPANY_ID1, self::DUMMY_BULK_UPDATE_SETTING_ID1);
        unset($params[BULK_UPDATE_SETTING_ID]);
        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $domain_model_updatesetting->execution_enque(self::DUMMY_EXTENSION_EXCUTION_ID3, self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1, $params, []);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_execution_enque_paramsにselect_columnがない場合、例外エラーが発生すること() {
        $params = Model_Bulkupdatesetting::get_validation_params_by_bulk_update_setting_id(self::DUMMY_COMPANY_ID1, self::DUMMY_BULK_UPDATE_SETTING_ID1);
        unset($params[Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME]);
        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $domain_model_updatesetting->execution_enque(self::DUMMY_EXTENSION_EXCUTION_ID3, self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1, $params, []);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_execution_enque_paramsにselect_columnが0件の場合、例外エラーが発生すること() {
        $params = Model_Bulkupdatesetting::get_validation_params_by_bulk_update_setting_id(self::DUMMY_COMPANY_ID1, self::DUMMY_BULK_UPDATE_SETTING_ID1);
        $params[Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME] = [];
        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $domain_model_updatesetting->execution_enque(self::DUMMY_EXTENSION_EXCUTION_ID3, self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1, $params, []);
    }

    public function test_execution_enque_タスク登録済みの実行IDだった場合実行されないこと() {
        $params = Model_Bulkupdatesetting::get_validation_params_by_bulk_update_setting_id(self::DUMMY_COMPANY_ID1, self::DUMMY_BULK_UPDATE_SETTING_ID1);
        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $result = $domain_model_updatesetting->execution_enque(self::DUMMY_EXTENSION_EXCUTION_ID2, self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1, $params, []);
        $expected = new Domain_Value_Enqueresult(false, null, __em('extension_execution_id_executed'));
        $this->assertEquals($expected, $result);
    }

    public function test_execution_enque_受注伝票検索APIでエラーが発生した場合実行されないこと() {

        $mock = new class extends Domain_Model_Updatesetting {
            public static $request_receiveorder_search_data = ['receive_order_shop_id' => 1, 'receive_order_shop_cut_form_id' => 2];

            public static function request_receiveorder_search(Client_Neapi $client_neapi, string $extension_execution_id) : array {
                return ['result' => \Client_Neapi::RESULT_ERROR];
            }
        };

        // 無名関数をそのままモックにするとエラーが発生するためエイリアスを作成する（おそらく無名関数名に禁止文字が入っているため）
        $stub = $this->getMockBuilder(self::_get_alias($mock))
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->with(self::DUMMY_USER_ID1, false)
            ->will($this->returnValue(new Client_Neapi()));

        $params = Model_Bulkupdatesetting::get_validation_params_by_bulk_update_setting_id(self::DUMMY_COMPANY_ID1, self::DUMMY_BULK_UPDATE_SETTING_ID1);
        $result = $stub->execution_enque(self::DUMMY_EXTENSION_EXCUTION_ID3, self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1, $params, []);
        $expected = new Domain_Value_Enqueresult(false, null, __em('execution_receiveorder_serach_error'));
        $this->assertEquals($expected, $result);
    }

    public function test_execution_enque_受注伝票検索APIで検索結果が0件だった場合実行されないこと() {

        $mock = new class extends Domain_Model_Updatesetting {
            public static $request_receiveorder_search_data = ['receive_order_shop_id' => 1, 'receive_order_shop_cut_form_id' => 2];

            public static function request_receiveorder_search(Client_Neapi $client_neapi, string $extension_execution_id) : array {
                return ['result' => \Client_Neapi::RESULT_SUCCESS, 'count' => '0'];
            }
        };

        // 無名関数をそのままモックにするとエラーが発生するためエイリアスを作成する（おそらく無名関数名に禁止文字が入っているため）
        $stub = $this->getMockBuilder(self::_get_alias($mock))
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->with(self::DUMMY_USER_ID1, false)
            ->will($this->returnValue(new Client_Neapi()));

        $params = Model_Bulkupdatesetting::get_validation_params_by_bulk_update_setting_id(self::DUMMY_COMPANY_ID1, self::DUMMY_BULK_UPDATE_SETTING_ID1);
        $result = $stub->execution_enque(self::DUMMY_EXTENSION_EXCUTION_ID3, self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1, $params, []);
        $expected = new Domain_Value_Enqueresult(false, null, __em('execution_receiveorder_serach_empty'));
        $this->assertEquals($expected, $result);
    }

    public function test_execution_enque_受注伝票検索APIで検索結果の件数と除外件数が一致した場合実行されないこと() {

        $mock = new class extends Domain_Model_Updatesetting {
            public static $request_receiveorder_search_data = ['receive_order_shop_id' => 1, 'receive_order_shop_cut_form_id' => 2];

            public static function request_receiveorder_search(Client_Neapi $client_neapi, string $extension_execution_id) : array {
                return ['result' => \Client_Neapi::RESULT_SUCCESS, 'count' => '2'];
            }
        };

        // 無名関数をそのままモックにするとエラーが発生するためエイリアスを作成する（おそらく無名関数名に禁止文字が入っているため）
        $stub = $this->getMockBuilder(self::_get_alias($mock))
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->with(self::DUMMY_USER_ID1, false)
            ->will($this->returnValue(new Client_Neapi()));

        $params = Model_Bulkupdatesetting::get_validation_params_by_bulk_update_setting_id(self::DUMMY_COMPANY_ID1, self::DUMMY_BULK_UPDATE_SETTING_ID1);
        $result = $stub->execution_enque(self::DUMMY_EXTENSION_EXCUTION_ID3, self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1, $params, ['DUMMY1', 'DUMMY2']);
        $expected = new Domain_Value_Enqueresult(false, null, __em('execution_receiveorder_all_exclude'));
        $this->assertEquals($expected, $result);
    }

    public function test_execution_enque_すべて除外された場合、実行されないこと() {

        $mock = new class extends Domain_Model_Updatesetting {
            public static $request_receiveorder_search_data = ['receive_order_shop_id' => 1, 'receive_order_shop_cut_form_id' => 2];

            public static function request_receiveorder_search(Client_Neapi $client_neapi, string $extension_execution_id) : array {
                return ['result' => \Client_Neapi::RESULT_SUCCESS, 'count' => 1, 'data' => self::$request_receiveorder_search_data];
            }
        };

        // 無名関数をそのままモックにするとエラーが発生するためエイリアスを作成する（おそらく無名関数名に禁止文字が入っているため）
        $stub = $this->getMockBuilder(self::_get_alias($mock))
            ->setMethods(['get_execution_bulk_update_setting', 'convert'])
            ->getMock();
        $dummy_params = Model_Bulkupdatesetting::get_validation_params_by_bulk_update_setting_id(self::DUMMY_COMPANY_ID1, self::DUMMY_BULK_UPDATE_SETTING_ID1);
        $execution_bulk_update_setting = new Model_Executionbulkupdatesetting();
        $execution_bulk_update_setting->id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID3;
        $execution_bulk_update_setting->request_key = self::DUMMY_REQUEST_KEY1;
        $execution_bulk_update_setting->user_id = self::DUMMY_USER_ID1;
        $execution_bulk_update_setting->company_id = self::DUMMY_COMPANY_ID1;
        $execution_bulk_update_setting->name = 'TEST';
        $execution_bulk_update_setting->extension_execution_id = self::DUMMY_EXTENSION_EXCUTION_ID3;
        $stub->expects($this->once())
            ->method('get_execution_bulk_update_setting')
            ->with(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1, self::DUMMY_EXTENSION_EXCUTION_ID3, $dummy_params)
            ->will($this->returnValue($execution_bulk_update_setting));
        $convert_result = new \Domain_Value_Convertresult([], []);
        $stub->expects($this->once())
            ->method('convert')
            ->with($execution_bulk_update_setting, $mock::$request_receiveorder_search_data)
            ->will($this->returnValue($convert_result));
        $result = $stub->execution_enque(self::DUMMY_EXTENSION_EXCUTION_ID3, self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1, $dummy_params, []);

        $expected = new Domain_Value_Enqueresult(false, null, __em('execution_receiveorder_empty'));
        $this->assertEquals($expected, $result);
    }

    public function test_execution_enque_除外レコードのinsertに失敗した場合、実行されないこと() {

        $mock = new class extends Domain_Model_Updatesetting {
            public static $request_receiveorder_search_data = ['receive_order_shop_id' => 1, 'receive_order_shop_cut_form_id' => 2];

            public static function request_receiveorder_search(Client_Neapi $client_neapi, string $extension_execution_id) : array {
                return ['result' => \Client_Neapi::RESULT_SUCCESS, 'count' => 2, 'data' => self::$request_receiveorder_search_data];
            }

            protected static function bulk_insert_excluded_receive_orders(Model_Executionbulkupdatesetting $execution_bulkupdate_setting, array $exclude_orders) : bool {
                return false;
            }
        };

        // 無名関数をそのままモックにするとエラーが発生するためエイリアスを作成する（おそらく無名関数名に禁止文字が入っているため）
        $stub = $this->getMockBuilder(self::_get_alias($mock))
            ->setMethods(['get_execution_bulk_update_setting'])
            ->getMock();
        $dummy_params = Model_Bulkupdatesetting::get_validation_params_by_bulk_update_setting_id(self::DUMMY_COMPANY_ID1, self::DUMMY_BULK_UPDATE_SETTING_ID1);
        $execution_bulk_update_setting = new Model_Executionbulkupdatesetting();
        $execution_bulk_update_setting->id = self::DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID3;
        $execution_bulk_update_setting->request_key = self::DUMMY_REQUEST_KEY1;
        $execution_bulk_update_setting->user_id = self::DUMMY_USER_ID1;
        $execution_bulk_update_setting->company_id = self::DUMMY_COMPANY_ID1;
        $execution_bulk_update_setting->name = 'TEST';
        $execution_bulk_update_setting->extension_execution_id = self::DUMMY_EXTENSION_EXCUTION_ID3;
        $stub->expects($this->once())
            ->method('get_execution_bulk_update_setting')
            ->with(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1, self::DUMMY_EXTENSION_EXCUTION_ID3, $dummy_params)
            ->will($this->returnValue($execution_bulk_update_setting));
        $result = $stub->execution_enque(self::DUMMY_EXTENSION_EXCUTION_ID3, self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1, $dummy_params, ['DUMMY']);

        $expected = new Domain_Value_Enqueresult(false, null, __em('exclude_receiveorder'));
        $this->assertEquals($expected, $result);
    }

    public function test_bulk_insert_excluded_receive_orders_除外伝票が空配列の場合falseを返すこと() {
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_BULK_UPDATE_SETTING_ID1);
        $exclude_orders = [];
        Closure::bind(function() use ($execution_bulk_update_setting, $exclude_orders) {
            $result = self::bulk_insert_excluded_receive_orders($execution_bulk_update_setting, $exclude_orders);
            $this->assertFalse($result);
        }, $this, Domain_Model_Updatesetting::class)->__invoke();
    }

    public function test_bulk_insert_excluded_receive_orders_除外伝票がある場合はその除外伝票分exclude_ordersのレコードが増えること() {
        $execution_bulk_update_setting = Model_Executionbulkupdatesetting::find(self::DUMMY_BULK_UPDATE_SETTING_ID1);
        $exclude_orders = ['100', '200', '300'];
        Closure::bind(function() use ($execution_bulk_update_setting, $exclude_orders) {
            $before_count = Model_Excludedreceiveorder::query()->count();
            $result = self::bulk_insert_excluded_receive_orders($execution_bulk_update_setting, $exclude_orders);
            $this->assertTrue($result);
            $after_count = Model_Excludedreceiveorder::query()->count();
            // exclude_order分レコードが増えていること
            $count = count($exclude_orders);
            $this->assertEquals($before_count+$count, $after_count);
        }, $this, Domain_Model_Updatesetting::class)->__invoke();
    }

    public function test_get_task_id_日付とインクリメントされた数字のタスクIDが取得できること() {
        $domain_model_udatesetting = new Domain_Model_Updatesetting();
        $result = $this->invoke_method($domain_model_udatesetting, 'get_task_id', [self::DUMMY_COMPANY_ID1]);
        $now_date = date('Ymd', strtotime('now'));
        $this->assertEquals($now_date . '-1', $result);

        $result = $this->invoke_method($domain_model_udatesetting, 'get_task_id', [self::DUMMY_COMPANY_ID1]);
        $this->assertEquals($now_date . '-2', $result);

        // 企業ごとにインクリメントしていること
        $result = $this->invoke_method($domain_model_udatesetting, 'get_task_id', [self::DUMMY_COMPANY_ID2]);
        $this->assertEquals($now_date . '-1', $result);

        $result = $this->invoke_method($domain_model_udatesetting, 'get_task_id', [self::DUMMY_COMPANY_ID1]);
        $this->assertEquals($now_date . '-3', $result);

        $result = $this->invoke_method($domain_model_udatesetting, 'get_task_id', [self::DUMMY_COMPANY_ID2]);
        $this->assertEquals($now_date . '-2', $result);
    }

    public function test_enque_sqs() {
        $this->markTestSkipped('sqsに登録されたかどうかをテストすることができないためスキップします');
    }

    public function test_get_execution_bulk_update_setting_更新設定情報オブジェクトが取得できること() {

        $receive_orders = ['DUMMY1'];
        $params = [
            'name' => 'TEST_NAME',
            Domain_Model_Updatesetting::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME => 1,
            Domain_Model_Updatesetting::UPDATE_YAHOO_CANCEL_ELEMENT_NAME => 2,
            Domain_Model_Updatesetting::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME => 3,
            Domain_Model_Updatesetting::REFLECT_ORDER_AMOUNT => 4,
            Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => [5,6],
            Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => [5 => 1, 6 => 2],
            Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME => [5 => 1],
            Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME => [6 => 'VALUE'],
        ];

        $execution_bulk_update_column1 = new Model_Executionbulkupdatecolumn();
        $execution_bulk_update_column1->receive_order_column_id = $params[Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME][0];
        $execution_bulk_update_column1->update_method_id = $params[Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME][5];
        $execution_bulk_update_column1->update_value = $params[Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME][5];

        $execution_bulk_update_column2 = new Model_Executionbulkupdatecolumn();
        $execution_bulk_update_column2->receive_order_column_id = $params[Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME][1];
        $execution_bulk_update_column2->update_method_id = $params[Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME][6];
        $execution_bulk_update_column2->update_value = $params[Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME][6];

        $execution_bulk_update_setting = new Model_Executionbulkupdatesetting();
        $execution_bulk_update_setting->request_key = self::DUMMY_REQUEST_KEY1;
        $execution_bulk_update_setting->user_id = self::DUMMY_USER_ID1;
        $execution_bulk_update_setting->company_id = self::DUMMY_COMPANY_ID1;
        $execution_bulk_update_setting->extension_execution_id = self::DUMMY_EXTENSION_EXCUTION_ID1;
        $execution_bulk_update_setting->name = $params['name'];
        $execution_bulk_update_setting->allow_update_shipment_confirmed = $params[Domain_Model_Updatesetting::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME];
        $execution_bulk_update_setting->allow_update_yahoo_cancel = $params[Domain_Model_Updatesetting::UPDATE_YAHOO_CANCEL_ELEMENT_NAME];
        $execution_bulk_update_setting->allow_optimistic_lock_update_retry = $params[Domain_Model_Updatesetting::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME];
        $execution_bulk_update_setting->allow_reflect_order_amount = $params[Domain_Model_Updatesetting::REFLECT_ORDER_AMOUNT];
        $execution_bulk_update_setting->execution_bulk_update_columns = [$execution_bulk_update_column1, $execution_bulk_update_column2];

        $mock = new class extends Domain_Model_Updatesetting {
            public static function get_task_id(string $company_id) : string {
                return Test_Domain_Model_Updatesetting::DUMMY_REQUEST_KEY1;
            }
        };

        $result = $this->invoke_method($mock, 'get_execution_bulk_update_setting',
            [self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1, self::DUMMY_EXTENSION_EXCUTION_ID1, $params, $receive_orders]);

        $this->assertEquals(self::DUMMY_REQUEST_KEY1, $result->request_key);
        $this->assertEquals(self::DUMMY_USER_ID1, $result->user_id);
        $this->assertEquals(self::DUMMY_COMPANY_ID1, $result->company_id);
        $this->assertEquals(self::DUMMY_EXTENSION_EXCUTION_ID1, $result->extension_execution_id);
        $this->assertEquals($params['name'], $result->name);
        $this->assertEquals($params[Domain_Model_Updatesetting::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME], $result->allow_update_shipment_confirmed);
        $this->assertEquals($params[Domain_Model_Updatesetting::UPDATE_YAHOO_CANCEL_ELEMENT_NAME], $result->allow_update_yahoo_cancel);
        $this->assertEquals($params[Domain_Model_Updatesetting::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME], $result->allow_optimistic_lock_update_retry);
        $this->assertEquals($params[Domain_Model_Updatesetting::REFLECT_ORDER_AMOUNT], $result->allow_reflect_order_amount);

        $this->assertEquals(2, count($result->execution_bulk_update_columns));
        $this->assertEquals($params[Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME][0], $result->execution_bulk_update_columns[0]->receive_order_column_id);
        $this->assertEquals($params[Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME][5], $result->execution_bulk_update_columns[0]->update_method_id);
        $this->assertEquals($params[Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME][5], $result->execution_bulk_update_columns[0]->update_value);
        $this->assertEquals($params[Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME][1], $result->execution_bulk_update_columns[1]->receive_order_column_id);
        $this->assertEquals($params[Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME][6], $result->execution_bulk_update_columns[1]->update_method_id);
        $this->assertEquals($params[Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME][6], $result->execution_bulk_update_columns[1]->update_value);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_get_execution_bulk_update_setting_対象の項目のselect_updateがなかった場合例外エラーが発生すること() {

        $receive_orders = ['DUMMY1'];
        $params = [
            'name' => 'TEST_NAME',
            Domain_Model_Updatesetting::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME => 1,
            Domain_Model_Updatesetting::UPDATE_YAHOO_CANCEL_ELEMENT_NAME => 2,
            Domain_Model_Updatesetting::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME => 3,
            Domain_Model_Updatesetting::REFLECT_ORDER_AMOUNT => 4,
            Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => [5,6],
            Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => [6 => 2],
            Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME => [5 => 1],
            Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME => [6 => 'VALUE'],
        ];

        $mock = new class extends Domain_Model_Updatesetting {
            public static function get_task_id(string $company_id) : string {
                return Test_Domain_Model_Updatesetting::DUMMY_REQUEST_KEY1;
            }
        };

        $this->invoke_method($mock, 'get_execution_bulk_update_setting',
            [self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1, self::DUMMY_EXTENSION_EXCUTION_ID1, $params, $receive_orders]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_get_execution_bulk_update_setting_対象の項目のselect_masterがなかった場合例外エラーが発生すること() {

        $receive_orders = ['DUMMY1'];
        $params = [
            'name' => 'TEST_NAME',
            Domain_Model_Updatesetting::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME => 1,
            Domain_Model_Updatesetting::UPDATE_YAHOO_CANCEL_ELEMENT_NAME => 2,
            Domain_Model_Updatesetting::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME => 3,
            Domain_Model_Updatesetting::REFLECT_ORDER_AMOUNT => 4,
            Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => [5,6],
            Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => [5 => 1, 6 => 2],
            Domain_Model_Updatesetting::UPDATE_VALUE_ELEMENT_NAME => [6 => 'VALUE'],
        ];

        $mock = new class extends Domain_Model_Updatesetting {
            public static function get_task_id(string $company_id) : string {
                return Test_Domain_Model_Updatesetting::DUMMY_REQUEST_KEY1;
            }
        };

        $this->invoke_method($mock, 'get_execution_bulk_update_setting',
            [self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1, self::DUMMY_EXTENSION_EXCUTION_ID1, $params, $receive_orders]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_get_execution_bulk_update_setting_対象の項目のupdate_valueがなかった場合例外エラーが発生すること() {

        $receive_orders = ['DUMMY1'];
        $params = [
            'name' => 'TEST_NAME',
            Domain_Model_Updatesetting::UPDATE_SHIPMENT_CONFIRMED_ELEMENT_NAME => 1,
            Domain_Model_Updatesetting::UPDATE_YAHOO_CANCEL_ELEMENT_NAME => 2,
            Domain_Model_Updatesetting::OPTIMISTIC_LOCK_UPDATE_ELEMENT_NAME => 3,
            Domain_Model_Updatesetting::REFLECT_ORDER_AMOUNT => 4,
            Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME => [5,6],
            Domain_Model_Updatesetting::SELECT_UPDATE_ELEMENT_NAME => [5 => 1, 6 => 2],
            Domain_Model_Updatesetting::SELECT_MASTER_ELEMENT_NAME => [5 => 1],
        ];

        $mock = new class extends Domain_Model_Updatesetting {
            public static function get_task_id(string $company_id) : string {
                return Test_Domain_Model_Updatesetting::DUMMY_REQUEST_KEY1;
            }
        };

        $this->invoke_method($mock, 'get_execution_bulk_update_setting',
            [self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1, self::DUMMY_EXTENSION_EXCUTION_ID1, $params, $receive_orders]);
    }

    /**
     * @dataProvider Domain_model_Updatesettingprovider::data_provider_convert_for_receive_order_gruoping_tag
     */
    public function test_convert_実行済みの受注分類タグについて(string $setting_model_name, string $setting_id, array $receive_orders, array $update_target_orders) {
        $setting = $setting_model_name::find($setting_id);
        $domain_model_updatesetting = new Domain_Model_Updatesetting();
        $domain_value_convertresult = $domain_model_updatesetting->convert($setting, $receive_orders);
        $expect = new Domain_Value_Convertresult($update_target_orders, []);
        $this->assertEquals($expect, $domain_value_convertresult);
    }

    /**
     * 引数のクラスのエイリアスを作成する
     *
     * @param $original_class_obj 元となるクラスのオブジェクト
     * @return string
     * @throws Exception
     */
    private static function _get_alias($original_class_obj) : string {
        $alias_name = __CLASS__ . '_' . bin2hex(random_bytes(64));
        class_alias(get_class($original_class_obj), $alias_name);
        return $alias_name;
    }
}