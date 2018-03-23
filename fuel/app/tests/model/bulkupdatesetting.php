<?php

class Test_Model_Bulkupdatesetting extends Testbase
{
    protected $fetch_init_yaml = false;

    protected $dataset_filenames = ['model/bulkupdatesetting.yml'];

    public function test_find_deleted_atに値が入っているレコードは取得しないこと(){
        $setting = Model_Bulkupdatesetting::find(self::DUMMY_BULK_UPDATE_SETTING_ID1);
        $setting->deleted_at = date('Y-m-d H:i:s');
        $setting->save();

        $result = Model_Bulkupdatesetting::find(self::DUMMY_BULK_UPDATE_SETTING_ID1);
        $this->assertNull($result);
    }

    public function test_find_optionsに他の条件が指定されていたらそれも合わせた条件になっていること(){
        $result1 = Model_Bulkupdatesetting::findOne(['id' => self::DUMMY_BULK_UPDATE_SETTING_ID1, 'temporary' => 0]);
        $this->assertEquals(self::DUMMY_BULK_UPDATE_SETTING_ID1, $result1->id);

        $result2 = Model_Bulkupdatesetting::findOne(['id' => self::DUMMY_BULK_UPDATE_SETTING_ID1, 'temporary' => 1]);
        $this->assertNull($result2);
    }

    public function test_delete_論理削除されること(){
        $setting = Model_Bulkupdatesetting::find(self::DUMMY_BULK_UPDATE_SETTING_ID1);
        $result = $setting->delete();
        $this->assertTrue($result);
        $this->assertNotNull($setting->deleted_at);
    }

    public function test_hard_delete_物理削除されること(){
        $before_count = Model_Bulkupdatesetting::count();
        $setting = Model_Bulkupdatesetting::find(self::DUMMY_BULK_UPDATE_SETTING_ID5);
        $setting->hard_delete();
        $after_count = Model_Bulkupdatesetting::count();
        $result = Model_Bulkupdatesetting::find(self::DUMMY_BULK_UPDATE_SETTING_ID5);

        $this->assertNull($result);
        $this->assertSame(1, $before_count - $after_count);
    }

    public function test_get_execution_user_id_対象レコードのcreated_user_idを返すこと(){
        $setting = Model_Bulkupdatesetting::findOne(['id' => self::DUMMY_BULK_UPDATE_SETTING_ID1]);
        $setting->created_user_id = '999';
        $this->assertSame('999', $setting->get_execution_user_id());
    }

    /**
     * @dataProvider Model_Bulkupdatesettingprovider::data_provider_get_setting
     */
    public function test_get_setting_指定した企業IDで検索されること($company_id, $bulk_update_setting_id, $column_order) {
        $bulk_update_setting = Model_Bulkupdatesetting::get_setting($company_id, $bulk_update_setting_id);
        // 指定した設定が取得されていること
        $this->assertEquals($bulk_update_setting_id, $bulk_update_setting->id);
        // 指定した企業になっていること
        $this->assertEquals($company_id, $bulk_update_setting->company_id);
        // 更新する項目がbulk_update_settingsのidの昇順になっていること
        $column_ids = [];
        foreach ($bulk_update_setting->bulk_update_columns as $bulk_update_column) {
            $column_ids[] = $bulk_update_column->id;
        }
        $this->assertEquals($column_order, $column_ids);

    }

    public function test_get_setting_optionを指定すると検索条件が追加されること() {
        $bulk_update_setting = Model_Bulkupdatesetting::get_setting(self::DUMMY_COMPANY_ID1, self::DUMMY_BULK_UPDATE_SETTING_ID1, ['temporary' => '1']);
        // 指定した設定が取得できないこと
        $this->assertNull($bulk_update_setting);

        $bulk_update_setting = Model_Bulkupdatesetting::get_setting(self::DUMMY_COMPANY_ID1, self::DUMMY_BULK_UPDATE_SETTING_ID1, ['temporary' => '0']);
        // 指定した設定が取得されていること
        $this->assertEquals(self::DUMMY_BULK_UPDATE_SETTING_ID1, $bulk_update_setting->id);
        // 指定した企業になっていること
        $this->assertEquals(self::DUMMY_COMPANY_ID1, $bulk_update_setting->company_id);
    }

    public function test_get_setting_指定した企業IDが存在しない場合nullが返ること() {
        $this->assertNull(Model_Bulkupdatesetting::get_setting(self::DUMMY_COMPANY_ID1, 'hoge'));
    }

    public function test_get_setting_他の企業のbulk_update_setting_idを指定した場合nullが返ること() {
        $this->assertNull(Model_Bulkupdatesetting::get_setting(self::DUMMY_COMPANY_ID1, self::DUMMY_BULK_UPDATE_SETTING_ID4));
    }

    /**
     * @dataProvider Model_Bulkupdatesettingprovider::data_provider_get_settings_for_top
     */
    public function test_get_settings_for_top_指定した企業IDで検索されること($company_id, $result_count, $setting_order, $column_order) {

        $bulk_update_settings = Model_Bulkupdatesetting::get_settings_for_top($company_id);

        // 件数が正しいこと
        $this->assertEquals($result_count, count($bulk_update_settings));
        $setting_ids = [];
        foreach ($bulk_update_settings as $bulk_update_setting) {
            // 指定した企業だけが取得されていること
            $this->assertEquals($company_id, $bulk_update_setting->company_id);
            // temporary=1の設定が取得されないこと
            $this->assertEquals(0, $bulk_update_setting->temporary);

            $setting_ids[] = $bulk_update_setting->id;
        }
        // 設定が作成日の昇順になっていること
        $this->assertEquals($setting_order, $setting_ids);
        // 更新する項目がbulk_update_settingsのidの昇順になっていること
        $column_ids = [];
        foreach (array_shift($bulk_update_settings)->bulk_update_columns as $bulk_update_column) {
            $column_ids[] = $bulk_update_column->id;
        }
        $this->assertEquals($column_order, $column_ids);
    }

    public function test_get_settings_for_top_0件だった場合は空の配列が返ること() {
        $this->assertEquals([], Model_Bulkupdatesetting::get_settings_for_top(self::DUMMY_COMPANY_ID3));
    }

    /**
     * @dataProvider Model_Bulkupdatesettingprovider::data_provider_get_validation_params_by_bulk_update_setting_id
     */
    public function test_get_validation_params_by_bulk_update_setting_id_指定した設定がバリデーションできる形の連想配列で返ること($company_id, $bulk_update_setting_id, $expected) {
        $this->assertEquals($expected, Model_Bulkupdatesetting::get_validation_params_by_bulk_update_setting_id($company_id, $bulk_update_setting_id));
    }

    public function test_get_validation_params_by_bulk_update_setting_id_指定した設定が他の企業の設定だった場合は空配列で返ること() {
        $this->assertEquals([], Model_Bulkupdatesetting::get_validation_params_by_bulk_update_setting_id(Test_Model_bulkupdatesetting::DUMMY_COMPANY_ID1, Test_Model_bulkupdatesetting::DUMMY_BULK_UPDATE_SETTING_ID4));
    }

    public function test_update_null_original_bulk_update_setting_id_指定したoriginal_bulk_update_setting_idを全てnullにしてtrueを返すこと() {
        $result = Model_Bulkupdatesetting::update_null_original_bulk_update_setting_id(self::DUMMY_COMPANY_ID1, self::DUMMY_BULK_UPDATE_SETTING_ID1);
        $setting2 = Model_Bulkupdatesetting::find(self::DUMMY_BULK_UPDATE_SETTING_ID2);
        $this->assertNull($setting2->original_bulk_update_setting_id);
        $setting3 = Model_Bulkupdatesetting::find(self::DUMMY_BULK_UPDATE_SETTING_ID3);
        $this->assertNull($setting3->original_bulk_update_setting_id);
        $this->assertTrue($result);
    }

    public function test_update_null_original_bulk_update_setting_id_指定したoriginal_bulk_update_setting_idのnull更新に1件でも失敗した場合falseを返すこと() {
        $this->markTestSkipped('staticメソッドのためスタブ化が難しいためスキップします');
    }

    public function test_update_null_original_bulk_update_setting_id_指定したoriginal_bulk_update_setting_idが存在しない場合はtrueを返すこと() {
        $result = Model_Bulkupdatesetting::update_null_original_bulk_update_setting_id(self::DUMMY_COMPANY_ID1, self::DUMMY_BULK_UPDATE_SETTING_ID5);
        $this->assertTrue($result);
    }

    public function test_get_exclude_comparison_columns_比較対象から除外するカラム名とリレーション定義のプロパティ名が取得できること() {
        $model_mock = self::get('Model_Bulkupdatesetting');

        $method = $this->getMethod('Model_Bulkupdatesetting', 'get_exclude_comparison_columns');
        $result = $method->invokeArgs(null, []);
        $expect = array_merge(
            [
                'id',
                'company_id',
                'temporary',
                'original_bulk_update_setting_id',
                'created_user_id',
                'last_updated_user_id',
                'created_at',
                'updated_at',
                'deleted_at',
            ],
            array_keys($model_mock::getStatic('_belongs_to')),
            array_keys($model_mock::getStatic('_has_many'))
        );

        $this->assertEquals($expect, $result);
    }

    public function test_get_comparison_columns_比較対象となるカラム配列のみが取得できること() {
        $model = Model_Bulkupdatesetting::find(self::DUMMY_BULK_UPDATE_SETTING_ID1);

        $result = $model->get_comparison_columns();
        $expect = [
            'name' => $model->name,
            'allow_update_shipment_confirmed' => $model->allow_update_shipment_confirmed,
            'allow_update_yahoo_cancel' => $model->allow_update_yahoo_cancel,
            'allow_optimistic_lock_update_retry' => $model->allow_optimistic_lock_update_retry,
            'allow_reflect_order_amount' => $model->allow_reflect_order_amount,
        ];
        $this->assertEquals($expect, $result);
    }

    public function test_is_different_original_未保存の場合にtrueとなること() {
        $model = new Model_Bulkupdatesetting();
        $result = $model->is_different_original();
        $this->assertTrue($result);
    }

    public function test_is_different_original_カラムの内容が変更されている場合にtrueとなること() {
        $model = Model_Bulkupdatesetting::find(self::DUMMY_BULK_UPDATE_SETTING_ID1);
        $model->name = 'changed_name';
        $result = $model->is_different_original();
        $this->assertTrue($result);
    }

    public function test_is_different_original_一時的な一括更新設定ではない場合にfalseとなること() {
        $model = Model_Bulkupdatesetting::find(self::DUMMY_BULK_UPDATE_SETTING_ID1);
        $model->temporary = '0';
        $model->save();
        $result = $model->is_different_original();
        $this->assertFalse($result);
    }

    public function test_is_different_original_新規作成の場合にtrueとなること() {
        $model = Model_Bulkupdatesetting::find(self::DUMMY_BULK_UPDATE_SETTING_ID1);
        $model->temporary = '1';
        $model->original_bulk_update_setting_id = null;
        $model->save();
        $result = $model->is_different_original();
        $this->assertTrue($result);
    }

    public function test_is_different_original_登録されているオリジナルの一括更新設定が存在しない場合にtrueとなること() {
        // phpunitだけでは、staticメソッドはモック化できないため、無名クラスを用いて擬似的にモック化
        // 登録されているオリジナルの一括更新設定が存在しない想定のモックを生成
        $model_mock_class = new class extends Model_Bulkupdatesetting {
            public $company_id = '1';
            public $temporary = '1';
            public $original_bulk_update_setting_id = '-1';
            public function is_new() {return false;}
            public function is_changed($property = null) {return false;}
        };

        $result = $model_mock_class->is_different_original();
        $this->assertTrue($result);
    }

    public function test_is_different_original_一括更新設定の更新項目以外の設定内容が異なる場合にtrueとなること() {
        // phpunitだけでは、staticメソッドはモック化できないため、無名クラスを用いて擬似的にモック化
        // 登録されているオリジナルの一括更新設定の更新項目以外の設定内容が異なる想定のモックを生成
        // get_comparison_columnsメソッドが異なる値となる想定
        $model_mock_class = new class extends Model_Bulkupdatesetting {
            public $temporary = '1';
            public function is_new() {return false;}
            public function is_changed($property = null) {return false;}
        };

        $model_mock_class->company_id = self::DUMMY_COMPANY_ID1;
        $model_mock_class->original_bulk_update_setting_id = self::DUMMY_BULK_UPDATE_SETTING_ID1;

        $result = $model_mock_class->is_different_original();
        $this->assertTrue($result);
    }

    public function test_is_different_original_一括更新設定の更新項目の設定内容が異なる場合にtrueとなること() {
        // phpunitだけでは、staticメソッドはモック化できないため、無名クラスを用いて擬似的にモック化
        // 登録されているオリジナルの一括更新設定の更新項目の設定内容が異なる想定のモックを生成
        // get_comparison_bulk_update_columnsメソッドが異なる値となる想定
        $model_mock_class = new class extends Model_Bulkupdatesetting {
            public $temporary = '1';
            public function is_new() {return false;}
            public function is_changed($property = null) {return false;}
            // 登録されているオリジナルの一括更新設定の更新項目以外は同じとなるように設定
            // （オリジナルの項目が返るように設定）
            public function get_comparison_columns() : array {return [];}
            // public function get_comparison_bulk_update_columns() : array {return [];}
        };

        $model_mock_class->company_id = self::DUMMY_COMPANY_ID1;
        $model_mock_class->original_bulk_update_setting_id = self::DUMMY_BULK_UPDATE_SETTING_ID1;

        $result = $model_mock_class->is_different_original();
        $this->assertTrue($result);
    }

    public function test_is_different_original_一括更新設定の設定内容が同じ場合にfalseとなること() {
        // phpunitだけでは、staticメソッドはモック化できないため、無名クラスを用いて擬似的にモック化
        // 登録されているオリジナルの一括更新設定の更新項目の設定内容が異なる想定のモックを生成
        // get_comparison_bulk_update_columnsメソッドが異なる値となる想定
        $model_mock_class = new class extends Model_Bulkupdatesetting {
            public $temporary = '1';
            public function is_new() {return false;}
            public function is_changed($property = null) {return false;}
            // 登録されているオリジナルの一括更新設定の更新項目以外は同じとなるように設定
            // （オリジナルの項目が返るように設定）
            public function get_comparison_columns() : array {return [];}
            public function get_comparison_bulk_update_columns() : array {return [];}
        };

        $model_mock_class->company_id = self::DUMMY_COMPANY_ID1;
        $model_mock_class->original_bulk_update_setting_id = self::DUMMY_BULK_UPDATE_SETTING_ID1;

        $result = $model_mock_class->is_different_original();
        $this->assertFalse($result);
    }

    /**
     * @dataProvider Model_Bulkupdatesettingprovider::data_provider_is_selected_option
     */
    public function test_is_selected_option_高度な設定のいずれかが選択されている場合はtrueが返ること(
        string $allow_update_shipment_confirmed,
        string $allow_update_yahoo_cancel,
        string $allow_optimistic_lock_update_retry,
        string $allow_reflect_order_amount,
        bool $is_selected_option
    ) {
        $setting = new Model_Bulkupdatesetting();
        $setting->allow_update_shipment_confirmed = $allow_update_shipment_confirmed;
        $setting->allow_update_yahoo_cancel = $allow_update_yahoo_cancel;
        $setting->allow_optimistic_lock_update_retry = $allow_optimistic_lock_update_retry;
        $setting->allow_reflect_order_amount = $allow_reflect_order_amount;
        $result = $setting->is_selected_option();
        $this->assertEquals($is_selected_option, $result);
    }

    public function test_is_selected_type_tag_タグ型が含まれている場合はtrueとなること() {

        $bulk_update_columns = [];

        // タグ型ではないのカラムタイプのスタブ
        $column_type_not_tag_stub = $this->getMockBuilder(Model_Columntype::class)
            ->setMethods(['is_tag'])
            ->getMock();
        $column_type_not_tag_stub->expects($this->once())
            ->method('is_tag')
            ->will($this->returnValue(false));

        $receive_order_column = new Model_Receiveordercolumn();
        $receive_order_column->column_type = $column_type_not_tag_stub;
        $bulk_update_column = new Model_Bulkupdatecolumn();
        $bulk_update_column->receive_order_column = $receive_order_column;
        $bulk_update_columns[] = $bulk_update_column;


        // タグ型のカラムタイプのスタブ
        $column_type_tag_stub = $this->getMockBuilder(Model_Columntype::class)
            ->setMethods(['is_tag'])
            ->getMock();
        $column_type_tag_stub->expects($this->once())
            ->method('is_tag')
            ->will($this->returnValue(true));

        $receive_order_column = new Model_Receiveordercolumn();
        $receive_order_column->column_type = $column_type_tag_stub;
        $bulk_update_column = new Model_Bulkupdatecolumn();
        $bulk_update_column->receive_order_column = $receive_order_column;
        $bulk_update_columns[] = $bulk_update_column;


        $setting = new Model_Bulkupdatesetting();
        $setting->bulk_update_columns = $bulk_update_columns;

        $this->assertTrue($setting->is_selected_type_tag());
    }

    public function test_is_selected_type_tag_タグ型が含まれていない場合はfalseとなること() {

        $bulk_update_columns = [];

        // タグ型ではないのカラムタイプのスタブ
        $column_type_not_tag_stub = $this->getMockBuilder(Model_Columntype::class)
            ->setMethods(['is_tag'])
            ->getMock();
        $column_type_not_tag_stub->expects($this->exactly(2))
            ->method('is_tag')
            ->will($this->returnValue(false));

        $receive_order_column = new Model_Receiveordercolumn();
        $receive_order_column->column_type = $column_type_not_tag_stub;
        $bulk_update_column = new Model_Bulkupdatecolumn();
        $bulk_update_column->receive_order_column = $receive_order_column;
        $bulk_update_columns[] = $bulk_update_column;

        $receive_order_column = new Model_Receiveordercolumn();
        $receive_order_column->column_type = $column_type_not_tag_stub;
        $bulk_update_column = new Model_Bulkupdatecolumn();
        $bulk_update_column->receive_order_column = $receive_order_column;
        $bulk_update_columns[] = $bulk_update_column;


        $setting = new Model_Bulkupdatesetting();
        $setting->bulk_update_columns = $bulk_update_columns;

        $this->assertFalse($setting->is_selected_type_tag());

    }

    public function test_is_selected_delivery_発送関連項目が含まれている場合はtrueとなること() {

        $bulk_update_columns = [];

        // 発送関連項目ではないの受注伝票項目のスタブ
        $receive_order_column_not_delivery_stub = $this->getMockBuilder(Model_Receiveordercolumn::class)
            ->setMethods(['is_delivery'])
            ->getMock();
        $receive_order_column_not_delivery_stub->expects($this->once())
            ->method('is_delivery')
            ->will($this->returnValue(false));

        $bulk_update_column = new Model_Bulkupdatecolumn();
        $bulk_update_column->receive_order_column = $receive_order_column_not_delivery_stub;
        $bulk_update_columns[] = $bulk_update_column;


        // 発送関連項目の受注伝票項目のスタブ
        $receive_order_column_delivery_stub = $this->getMockBuilder(Model_Receiveordercolumn::class)
            ->setMethods(['is_delivery'])
            ->getMock();
        $receive_order_column_delivery_stub->expects($this->once())
            ->method('is_delivery')
            ->will($this->returnValue(true));

        $bulk_update_column = new Model_Bulkupdatecolumn();
        $bulk_update_column->receive_order_column = $receive_order_column_delivery_stub;
        $bulk_update_columns[] = $bulk_update_column;


        $setting = new Model_Bulkupdatesetting();
        $setting->bulk_update_columns = $bulk_update_columns;

        $this->assertTrue($setting->is_selected_delivery());
    }

    public function test_is_selected_delivery_発送関連項目が含まれていない場合はfalseとなること() {

        $bulk_update_columns = [];

        // 発送関連項目ではないの受注伝票項目のスタブ
        $receive_order_column_not_delivery_stub = $this->getMockBuilder(Model_Receiveordercolumn::class)
            ->setMethods(['is_delivery'])
            ->getMock();
        $receive_order_column_not_delivery_stub->expects($this->exactly(2))
            ->method('is_delivery')
            ->will($this->returnValue(false));

        $bulk_update_column = new Model_Bulkupdatecolumn();
        $bulk_update_column->receive_order_column = $receive_order_column_not_delivery_stub;
        $bulk_update_columns[] = $bulk_update_column;


        $bulk_update_column = new Model_Bulkupdatecolumn();
        $bulk_update_column->receive_order_column = $receive_order_column_not_delivery_stub;
        $bulk_update_columns[] = $bulk_update_column;


        $setting = new Model_Bulkupdatesetting();
        $setting->bulk_update_columns = $bulk_update_columns;

        $this->assertFalse($setting->is_selected_delivery());
    }

    public function test_is_selected_payment_支払関連項目が含まれている場合はtrueとなること() {

        $bulk_update_columns = [];

        // 支払関連項目ではないの受注伝票項目のスタブ
        $receive_order_column_not_payment_stub = $this->getMockBuilder(Model_Receiveordercolumn::class)
            ->setMethods(['is_payment'])
            ->getMock();
        $receive_order_column_not_payment_stub->expects($this->once())
            ->method('is_payment')
            ->will($this->returnValue(false));

        $bulk_update_column = new Model_Bulkupdatecolumn();
        $bulk_update_column->receive_order_column = $receive_order_column_not_payment_stub;
        $bulk_update_columns[] = $bulk_update_column;


        // 支払関連項目の受注伝票項目のスタブ
        $receive_order_column_payment_stub = $this->getMockBuilder(Model_Receiveordercolumn::class)
            ->setMethods(['is_payment'])
            ->getMock();
        $receive_order_column_payment_stub->expects($this->once())
            ->method('is_payment')
            ->will($this->returnValue(true));

        $bulk_update_column = new Model_Bulkupdatecolumn();
        $bulk_update_column->receive_order_column = $receive_order_column_payment_stub;
        $bulk_update_columns[] = $bulk_update_column;


        $setting = new Model_Bulkupdatesetting();
        $setting->bulk_update_columns = $bulk_update_columns;

        $this->assertTrue($setting->is_selected_payment());
    }

    public function test_is_selected_payment_支払関連項目が含まれていない場合はfalseとなること() {

        $bulk_update_columns = [];

        // 支払関連項目ではないの受注伝票項目のスタブ
        $receive_order_column_not_payment_stub = $this->getMockBuilder(Model_Receiveordercolumn::class)
            ->setMethods(['is_payment'])
            ->getMock();
        $receive_order_column_not_payment_stub->expects($this->exactly(2))
            ->method('is_payment')
            ->will($this->returnValue(false));

        $bulk_update_column = new Model_Bulkupdatecolumn();
        $bulk_update_column->receive_order_column = $receive_order_column_not_payment_stub;
        $bulk_update_columns[] = $bulk_update_column;


        $bulk_update_column = new Model_Bulkupdatecolumn();
        $bulk_update_column->receive_order_column = $receive_order_column_not_payment_stub;
        $bulk_update_columns[] = $bulk_update_column;


        $setting = new Model_Bulkupdatesetting();
        $setting->bulk_update_columns = $bulk_update_columns;

        $this->assertFalse($setting->is_selected_payment());
    }

    public function test_is_selected_payment_method_id_支払方法の項目が含まれている場合はtrueとなること() {

        $bulk_update_columns = [];

        // 支払方法の項目ではないの受注伝票項目のスタブ
        $receive_order_column_not_payment_method_id_stub = $this->getMockBuilder(Model_Receiveordercolumn::class)
            ->setMethods(['is_payment_method_id'])
            ->getMock();
        $receive_order_column_not_payment_method_id_stub->expects($this->once())
            ->method('is_payment_method_id')
            ->will($this->returnValue(false));

        $bulk_update_column = new Model_Bulkupdatecolumn();
        $bulk_update_column->receive_order_column = $receive_order_column_not_payment_method_id_stub;
        $bulk_update_columns[] = $bulk_update_column;


        // 支払方法の項目の受注伝票項目のスタブ
        $receive_order_column_payment_method_id_stub = $this->getMockBuilder(Model_Receiveordercolumn::class)
            ->setMethods(['is_payment_method_id'])
            ->getMock();
        $receive_order_column_payment_method_id_stub->expects($this->once())
            ->method('is_payment_method_id')
            ->will($this->returnValue(true));

        $bulk_update_column = new Model_Bulkupdatecolumn();
        $bulk_update_column->receive_order_column = $receive_order_column_payment_method_id_stub;
        $bulk_update_columns[] = $bulk_update_column;


        $setting = new Model_Bulkupdatesetting();
        $setting->bulk_update_columns = $bulk_update_columns;

        $this->assertTrue($setting->is_selected_payment_method_id());
    }

    public function test_is_selected_payment_method_id_支払方法の項目が含まれていない場合はfalseとなること() {

        $bulk_update_columns = [];

        // 支払方法の項目ではないの受注伝票項目のスタブ
        $receive_order_column_not_payment_method_id_stub = $this->getMockBuilder(Model_Receiveordercolumn::class)
            ->setMethods(['is_payment_method_id'])
            ->getMock();
        $receive_order_column_not_payment_method_id_stub->expects($this->exactly(2))
            ->method('is_payment_method_id')
            ->will($this->returnValue(false));

        $bulk_update_column = new Model_Bulkupdatecolumn();
        $bulk_update_column->receive_order_column = $receive_order_column_not_payment_method_id_stub;
        $bulk_update_columns[] = $bulk_update_column;


        $bulk_update_column = new Model_Bulkupdatecolumn();
        $bulk_update_column->receive_order_column = $receive_order_column_not_payment_method_id_stub;
        $bulk_update_columns[] = $bulk_update_column;


        $setting = new Model_Bulkupdatesetting();
        $setting->bulk_update_columns = $bulk_update_columns;

        $this->assertFalse($setting->is_selected_payment_method_id());
    }

    public function test_is_selected_order_amount_受注金額関連の項目が含まれている場合はtrueとなること() {

        $bulk_update_columns = [];

        // 受注金額関連の項目ではないの受注伝票項目のスタブ
        $receive_order_column_not_order_amount_stub = $this->getMockBuilder(Model_Receiveordercolumn::class)
            ->setMethods(['is_order_amount'])
            ->getMock();
        $receive_order_column_not_order_amount_stub->expects($this->once())
            ->method('is_order_amount')
            ->will($this->returnValue(false));

        $bulk_update_column = new Model_Bulkupdatecolumn();
        $bulk_update_column->receive_order_column = $receive_order_column_not_order_amount_stub;
        $bulk_update_columns[] = $bulk_update_column;


        // 受注金額関連の項目の受注伝票項目のスタブ
        $receive_order_column_order_amount_stub = $this->getMockBuilder(Model_Receiveordercolumn::class)
            ->setMethods(['is_order_amount'])
            ->getMock();
        $receive_order_column_order_amount_stub->expects($this->once())
            ->method('is_order_amount')
            ->will($this->returnValue(true));

        $bulk_update_column = new Model_Bulkupdatecolumn();
        $bulk_update_column->receive_order_column = $receive_order_column_order_amount_stub;
        $bulk_update_columns[] = $bulk_update_column;


        $setting = new Model_Bulkupdatesetting();
        $setting->bulk_update_columns = $bulk_update_columns;

        $this->assertTrue($setting->is_selected_order_amount());
    }

    public function test_is_selected_order_amount_受注金額関連の項目が含まれていない場合はfalseとなること() {

        $bulk_update_columns = [];

        // 受注金額関連の項目ではないの受注伝票項目のスタブ
        $receive_order_column_not_order_amount_stub = $this->getMockBuilder(Model_Receiveordercolumn::class)
            ->setMethods(['is_order_amount'])
            ->getMock();
        $receive_order_column_not_order_amount_stub->expects($this->exactly(2))
            ->method('is_order_amount')
            ->will($this->returnValue(false));

        $bulk_update_column = new Model_Bulkupdatecolumn();
        $bulk_update_column->receive_order_column = $receive_order_column_not_order_amount_stub;
        $bulk_update_columns[] = $bulk_update_column;


        $bulk_update_column = new Model_Bulkupdatecolumn();
        $bulk_update_column->receive_order_column = $receive_order_column_not_order_amount_stub;
        $bulk_update_columns[] = $bulk_update_column;


        $setting = new Model_Bulkupdatesetting();
        $setting->bulk_update_columns = $bulk_update_columns;

        $this->assertFalse($setting->is_selected_order_amount());
    }

    public function test_is_selected_total_amount_総合計の項目が含まれている場合はtrueとなること() {

        $bulk_update_columns = [];

        // 総合計の項目ではないの受注伝票項目のスタブ
        $receive_order_column_not_total_amount_stub = $this->getMockBuilder(Model_Receiveordercolumn::class)
            ->setMethods(['is_total_amount'])
            ->getMock();
        $receive_order_column_not_total_amount_stub->expects($this->once())
            ->method('is_total_amount')
            ->will($this->returnValue(false));

        $bulk_update_column = new Model_Bulkupdatecolumn();
        $bulk_update_column->receive_order_column = $receive_order_column_not_total_amount_stub;
        $bulk_update_columns[] = $bulk_update_column;


        // 総合計の項目の受注伝票項目のスタブ
        $receive_order_column_total_amount_stub = $this->getMockBuilder(Model_Receiveordercolumn::class)
            ->setMethods(['is_total_amount'])
            ->getMock();
        $receive_order_column_total_amount_stub->expects($this->once())
            ->method('is_total_amount')
            ->will($this->returnValue(true));

        $bulk_update_column = new Model_Bulkupdatecolumn();
        $bulk_update_column->receive_order_column = $receive_order_column_total_amount_stub;
        $bulk_update_columns[] = $bulk_update_column;


        $setting = new Model_Bulkupdatesetting();
        $setting->bulk_update_columns = $bulk_update_columns;

        $this->assertTrue($setting->is_selected_total_amount());
    }

    public function test_is_selected_total_amount_総合計の項目が含まれていない場合はfalseとなること() {

        $bulk_update_columns = [];

        // 総合計の項目ではないの受注伝票項目のスタブ
        $receive_order_column_not_total_amount_stub = $this->getMockBuilder(Model_Receiveordercolumn::class)
            ->setMethods(['is_total_amount'])
            ->getMock();
        $receive_order_column_not_total_amount_stub->expects($this->exactly(2))
            ->method('is_total_amount')
            ->will($this->returnValue(false));

        $bulk_update_column = new Model_Bulkupdatecolumn();
        $bulk_update_column->receive_order_column = $receive_order_column_not_total_amount_stub;
        $bulk_update_columns[] = $bulk_update_column;


        $bulk_update_column = new Model_Bulkupdatecolumn();
        $bulk_update_column->receive_order_column = $receive_order_column_not_total_amount_stub;
        $bulk_update_columns[] = $bulk_update_column;


        $setting = new Model_Bulkupdatesetting();
        $setting->bulk_update_columns = $bulk_update_columns;

        $this->assertFalse($setting->is_selected_total_amount());
    }

}