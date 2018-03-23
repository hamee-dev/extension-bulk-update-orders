<?php
class Test_Domain_Value_Receiveordercolumn extends Testbase {
    protected $dataset_filenames = ['model/bulkupdatecolumn.yml'];

    public function test_get_display_value_発送方法別項目タイプ以外のマスタ型が正常に取得できた場合値が取得できること() {
        // 受注伝票カラムデータ作成
        $receive_order_column_id = '4';
        $receive_order_column = Model_Receiveordercolumn::findOne(['id' => $receive_order_column_id]);

        // 項目情報データ作成
        $list = [];
        $model = new Model_Bulkupdatecolumn();
        $model->bulk_update_setting_id = '1';
        $model->receive_order_column_id = $receive_order_column_id;
        $model->update_method_id = '1';
        $model->update_value = 'BV';
        $list[] = $model;

        // マスタのスタブ作成
        $master = Model_Receiveordercolumn::findOne([['id' => $list[0]->receive_order_column_id]]);
        $master_stub = $this->getMockBuilder(Utility_Master::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['get'])
            ->getMock();
        $master_value = 'TEST';
        $value_master = new Domain_Value_Master($list[0]->update_value, $master_value);
        $master_stub->expects($this->once())
            ->method('get')
            ->with($this->equalTo($master->master_name))
            ->will($this->returnValue([$list[0]->update_value => $value_master]));

        // 結果
        $original_value = $list[0]->update_value;
        $expect = $original_value . ' : ' . $master_value;
        $this->assertEquals($expect, Domain_Value_Receiveordercolumn::get_display_value($receive_order_column, $master_stub, $list, $original_value));
    }

    public function test_get_display_value_発送方法別項目タイプ以外のマスタ型で正常に取得できなかった場合「不正な値です」と返ること() {
        // 受注伝票カラムデータ作成
        $receive_order_column_id = '4';
        $receive_order_column = Model_Receiveordercolumn::findOne(['id' => $receive_order_column_id]);

        // 項目情報データ作成
        $list = [];
        $model = new Model_Bulkupdatecolumn();
        $model->bulk_update_setting_id = '1';
        $model->receive_order_column_id = $receive_order_column_id;
        $model->update_method_id = '1';
        $model->update_value = 'BV';
        $list[] = $model;

        // マスタのスタブ作成
        $master = Model_Receiveordercolumn::findOne([['id' => $list[0]->receive_order_column_id]]);
        $master_stub = $this->getMockBuilder(Utility_Master::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['get'])
            ->getMock();
        $master_value = 'TEST';
        $value_master = new Domain_Value_Master('AA', $master_value);
        $master_stub->expects($this->once())
            ->method('get')
            ->with($this->equalTo($master->master_name))
            ->will($this->returnValue(['AA' => $value_master]));

        // 結果
        $original_value = $list[0]->update_value;
        $this->assertEquals(
            \Lang::get('message.error.invalid_value'),
            Domain_Value_Receiveordercolumn::get_display_value($receive_order_column, $master_stub, $list, $original_value));
    }

    public function test_get_display_value_発送方法別項目タイプのマスタ型が正常に取得できた場合値が取得できること() {
        // 受注伝票カラムデータ作成
        $receive_order_column = Model_Receiveordercolumn::findOne(['id' => '27']);

        // 項目情報データ作成
        $list = [];
        $model = new Model_Bulkupdatecolumn();
        $model->bulk_update_setting_id = '1';
        $model->receive_order_column_id = '8';
        $model->update_method_id = '1';
        $model->update_value = '10';
        $list[] = $model;

        $model = new Model_Bulkupdatecolumn();
        $model->bulk_update_setting_id = '1';
        $model->receive_order_column_id = '27';
        $model->update_method_id = '1';
        $model->update_value = '001';
        $list[] = $model;


        // マスタのスタブ作成
        $master = Model_Receiveordercolumn::findOne([['id' => $list[1]->receive_order_column_id]]);
        $master_stub = $this->getMockBuilder(Utility_Master::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['get_forwarding_agent'])
            ->getMock();
        $master_value = 'TEST';
        $value_master = new Domain_Value_Master($model->update_value, $master_value);
        $master_stub->expects($this->once())
            ->method('get_forwarding_agent')
            ->with($this->equalTo(true), $this->equalTo($list[0]->update_value), $master->master_name)
            ->will($this->returnValue([$model->update_value => $value_master]));

        // 結果
        $original_value = $list[1]->update_value;
        $expect = $original_value . ' : ' . $master_value;
        $this->assertEquals($expect, Domain_Value_Receiveordercolumn::get_display_value($receive_order_column, $master_stub, $list, $original_value));
    }

    public function test_get_display_value_発送方法別項目タイプのマスタ型で正常に取得できなかった場合は「空欄にする」の要素が返ること() {
        // 受注伝票カラムデータ作成
        $receive_order_column = Model_Receiveordercolumn::findOne(['id' => '27']);

        // 項目情報データ作成
        $list = [];
        $model = new Model_Bulkupdatecolumn();
        $model->bulk_update_setting_id = '1';
        $model->receive_order_column_id = '8';
        $model->update_method_id = '1';
        $model->update_value = '10';
        $list[] = $model;

        $model = new Model_Bulkupdatecolumn();
        $model->bulk_update_setting_id = '1';
        $model->receive_order_column_id = '27';
        $model->update_method_id = '1';
        $model->update_value = 'hoge';
        $list[] = $model;


        // マスタのスタブ作成
        $master = Model_Receiveordercolumn::findOne([['id' => $list[1]->receive_order_column_id]]);
        $master_stub = $this->getMockBuilder(Utility_Master::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['get_forwarding_agent'])
            ->getMock();
        $master_value = 'TEST';
        $value_master = new Domain_Value_Master('001', $master_value);
        $master_stub->expects($this->once())
            ->method('get_forwarding_agent')
            ->with($this->equalTo(true), $this->equalTo($list[0]->update_value), $master->master_name)
            ->will($this->returnValue(['001' => $value_master]));

        // 結果
        $original_value = $list[1]->update_value;
        $this->assertEquals(
            \Lang::get('common.empty_update_dom'),
            Domain_Value_Receiveordercolumn::get_display_value($receive_order_column, $master_stub, $list, $original_value));
    }

    public function test_get_display_value_発送方法別項目タイプのマスタ型で発送方法がなかった場合「不正な値です」と返ること() {
        // 受注伝票カラムデータ作成
        $receive_order_column = Model_Receiveordercolumn::findOne(['id' => '27']);

        // 項目情報データ作成
        $list = [];
        $model = new Model_Bulkupdatecolumn();
        $model->bulk_update_setting_id = '1';
        $model->receive_order_column_id = '27';
        $model->update_method_id = '1';
        $model->update_value = '001';
        $list[] = $model;

        // 結果
        $original_value = $list[0]->update_value;
        $this->assertEquals(
            \Lang::get('message.error.invalid_value'),
            Domain_Value_Receiveordercolumn::get_display_value($receive_order_column, new Utility_Master(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1), $list, $original_value));
    }

    public function test_get_display_value_タグ型で空欄にする場合「空欄にする」の要素が返ること() {
        // 受注伝票カラムデータ作成
        $receive_order_column = Model_Receiveordercolumn::findOne(['id' => '6']);

        // 項目情報データ作成
        $list = [];
        $model = new Model_Bulkupdatecolumn();
        $model->bulk_update_setting_id = '1';
        $model->receive_order_column_id = '6';
        $model->update_method_id = '1';
        $model->update_value = '';
        $list[] = $model;

        // 結果
        $original_value = $list[0]->update_value;
        $this->assertEquals(
            \Lang::get('common.empty_update_dom'),
            Domain_Value_Receiveordercolumn::get_display_value($receive_order_column, new Utility_Master(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1), $list, $original_value));
    }

    public function test_get_display_value_タグ型で空欄にする場合is_order_valueがtrueの場合は空文字で返すこと() {
        // 受注伝票カラムデータ作成
        $receive_order_column = Model_Receiveordercolumn::findOne(['id' => '6']);

        // 項目情報データ作成
        $list = [];
        $model = new Model_Bulkupdatecolumn();
        $model->bulk_update_setting_id = self::DUMMY_BULK_UPDATE_SETTING_ID1;
        $model->receive_order_column_id = '6';
        $model->update_method_id = '1';
        $model->update_value = '';
        $list[] = $model;

        // 結果
        $original_value = $list[0]->update_value;
        $is_order_value = true;
        $setting_name = 'TEST';
        $this->assertEquals(
            '',
            Domain_Value_Receiveordercolumn::get_display_value($receive_order_column, new Utility_Master(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1), $list, $original_value, $is_order_value, $setting_name));
    }

    public function test_get_display_value_タグ型で空欄以外にする場合タグのDOM要素を生成して返すこと() {
        // 受注伝票カラムデータ作成
        $receive_order_column = Model_Receiveordercolumn::findOne(['id' => '6']);

        // 項目情報データ作成
        $list = [];
        $model = new Model_Bulkupdatecolumn();
        $model->bulk_update_setting_id = '1';
        $model->receive_order_column_id = '6';
        $model->update_method_id = '1';
        $model->update_value = '[hoge1][hoge2][hoge3]';
        $list[] = $model;

        // 結果
        $original_value = $list[0]->update_value;
        $this->assertEquals(
            '<div class="font_bold tag-list"><span>hoge1</span><span>hoge2</span><span>hoge3</span></div>',
            Domain_Value_Receiveordercolumn::get_display_value($receive_order_column, new Utility_Master(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1), $list, $original_value));
    }

    public function test_get_display_value_タグ型で括弧区切りになってなかった場合「不正な値です」と返ること() {
        // 受注伝票カラムデータ作成
        $receive_order_column = Model_Receiveordercolumn::findOne(['id' => '6']);

        // 項目情報データ作成
        $list = [];
        $model = new Model_Bulkupdatecolumn();
        $model->bulk_update_setting_id = '1';
        $model->receive_order_column_id = '6';
        $model->update_method_id = '1';
        $model->update_value = 'hoge1,hoge2,hoge3';
        $list[] = $model;

        // 結果
        $original_value = $list[0]->update_value;
        $this->assertEquals(
            \Lang::get('message.error.invalid_value'),
            Domain_Value_Receiveordercolumn::get_display_value($receive_order_column, new Utility_Master(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1), $list, $original_value));
    }

    /**
     * @dataProvider Domain_Value_Receiveordercolumndataprovider::data_provider_get_display_value_for_bool
     */
    public function test_get_display_value_bool型の場合パラメータによって「あり」「なし」の文言を返すこと($receive_order_column_id, $original_value, $is_order_value, $expect) {
        // 受注伝票カラムデータ作成
        $receive_order_column = Model_Receiveordercolumn::findOne(['id' => $receive_order_column_id]);

        // 項目情報データ作成
        $list = [];
        $model = new Model_Bulkupdatecolumn();
        $model->bulk_update_setting_id = '1';
        $model->receive_order_column_id = $receive_order_column_id;
        $model->update_method_id = '2';
        $model->update_value = 'TEST';
        $list[] = $model;

        // 結果
        $master = new Utility_Master(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1);
        $this->assertEquals($expect, Domain_Value_Receiveordercolumn::get_display_value($receive_order_column, $master, $list, $original_value, $is_order_value));
    }

    /**
     * @dataProvider Domain_Value_Receiveordercolumndataprovider::data_provider_get_display_value_for_date
     */
    public function test_get_display_value_日付型の時、正しい日付ならYmdの形式・不正な形式なら「空欄にする」の要素が返ること($update_value, $expect) {
        // 受注伝票カラムデータ作成
        $receive_order_column = Model_Receiveordercolumn::findOne(['id' => '3']);

        // 項目情報データ作成
        $list = [];
        $model = new Model_Bulkupdatecolumn();
        $model->bulk_update_setting_id = '1';
        $model->receive_order_column_id = '3';
        $model->update_method_id = '1';
        $model->update_value = $update_value;
        $list[] = $model;

        // 結果
        $original_value = $list[0]->update_value;
        $this->assertSame(
            $expect,
            Domain_Value_Receiveordercolumn::get_display_value($receive_order_column, new Utility_Master(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1), $list, $original_value));
    }

    /**
     * @dataProvider Domain_Value_Receiveordercolumndataprovider::data_provider_get_display_value_for_number
     */
    public function test_get_display_value_数値型で小数点があるが0の場合小数点が消えること($value, $expected) {
        // 数値型のカラムデータ作成
        $receive_order_column = Model_Receiveordercolumn::findOne(['id' => '10']);

        // 項目情報データ作成
        $list = [];
        $model = new Model_Bulkupdatecolumn();
        $model->bulk_update_setting_id = '1';
        $model->receive_order_column_id = $receive_order_column->id;
        $model->update_method_id = '1';
        $model->update_value = $value;
        $list[] = $model;

        $this->assertEquals(
            $expected,
            Domain_Value_Receiveordercolumn::get_display_value($receive_order_column, new Utility_Master(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1), $list, $value));
    }

    public function test_get_display_value_その他の型だった場合update_valueがそのまま返ること() {
        // 受注伝票カラムデータ作成
        $receive_order_column = Model_Receiveordercolumn::findOne(['id' => '2']);

        // 項目情報データ作成
        $list = [];
        $model = new Model_Bulkupdatecolumn();
        $model->bulk_update_setting_id = '1';
        $model->receive_order_column_id = '2';
        $model->update_method_id = '2';
        $model->update_value = 'TEST';
        $list[] = $model;

        // 結果
        $original_value = $list[0]->update_value;
        $this->assertEquals(
            $model->update_value,
            Domain_Value_Receiveordercolumn::get_display_value($receive_order_column, new Utility_Master(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1), $list, $original_value));
    }

    /**
     * @dataProvider Domain_Value_Receiveordercolumndataprovider::data_provider_get_display_value_for_trim
     */
    public function test_get_display_value_文字数が上限を超えている場合trimされること($is_preview, $update_value, $expect) {
        // 受注伝票カラムデータ作成
        $receive_order_column = Model_Receiveordercolumn::findOne(['id' => '2']);

        // 項目情報データ作成
        $list = [];
        $model = new Model_Bulkupdatecolumn();
        $model->bulk_update_setting_id = '1';
        $model->receive_order_column_id = '2';
        $model->update_method_id = '2';
        $model->update_value = $update_value;
        $list[] = $model;

        // 結果
        $original_value = $list[0]->update_value;
        $this->assertEquals(
            $expect,
            Domain_Value_Receiveordercolumn::get_display_value($receive_order_column, new Utility_Master(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1), $list, $original_value, false, $is_preview));
    }

    /**
     * @dataProvider Domain_Value_Receiveordercolumndataprovider::data_provider_get_display_value_for_tag_trim
     */
    public function test_get_display_value_タグ型の文字数が上限を超えている場合trimされること($is_preview, $update_value, $expect) {
        // 受注伝票カラムデータ作成
        $receive_order_column = Model_Receiveordercolumn::findOne(['id' => '6']);

        // 項目情報データ作成
        $list = [];
        $model = new Model_Bulkupdatecolumn();
        $model->bulk_update_setting_id = '1';
        $model->receive_order_column_id = '6';
        $model->update_method_id = '1';
        $model->update_value = $update_value;
        $list[] = $model;

        // 結果
        $original_value = $list[0]->update_value;
        $this->assertEquals(
            $expect,
            Domain_Value_Receiveordercolumn::get_display_value($receive_order_column, new Utility_Master(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1), $list, $original_value, false, $is_preview));
    }

    public function test_get_display_value_渡された値の改行がbrタグに置換されていること() {
        // 受注伝票カラムデータ作成
        $receive_order_column = Model_Receiveordercolumn::findOne(['id' => '2']);

        // 項目情報データ作成
        $list = [];
        $model = new Model_Bulkupdatecolumn();
        $model->bulk_update_setting_id = '1';
        $model->receive_order_column_id = '2';
        $model->update_method_id = '2';
        $model->update_value = "a\nb\nc";
        $list[] = $model;

        // 結果
        $original_value = $list[0]->update_value;
        $this->assertEquals(
            "a<br />\nb<br />\nc",
            Domain_Value_Receiveordercolumn::get_display_value($receive_order_column, new Utility_Master(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1), $list, $original_value));
    }

    public function test_get_display_value_渡された値がサニタイズされていること() {
        // 受注伝票カラムデータ作成
        $receive_order_column = Model_Receiveordercolumn::findOne(['id' => '2']);

        // 項目情報データ作成
        $list = [];
        $model = new Model_Bulkupdatecolumn();
        $model->bulk_update_setting_id = '1';
        $model->receive_order_column_id = '2';
        $model->update_method_id = '2';
        $model->update_value = '<div>hoge</div>';
        $list[] = $model;

        // 結果
        $original_value = $list[0]->update_value;
        $this->assertEquals(
            '&lt;div&gt;hoge&lt;/div&gt;',
            Domain_Value_Receiveordercolumn::get_display_value($receive_order_column, new Utility_Master(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1), $list, $original_value));
    }


    public function test__get_master_value(){
        $this->markTestSkipped('get_display_valueでテストが行われているためスキップします');
    }

    /**
     * @dataProvider Domain_Value_Receiveordercolumndataprovider::data_provider_is_show_update_method
     */
    public function test_is_show_update_method_更新方法を表示するかどうか正しく返ること(string $column_type_id, string $value, bool $expected) {
        $column_type = Model_Columntype::findOne(['id' => $column_type_id]);
        $result = Domain_Value_Receiveordercolumn::is_show_update_method($column_type, $value);
        $this->assertEquals($expected, $result);
    }
}