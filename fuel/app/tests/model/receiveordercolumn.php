<?php
class Test_Model_Receiveordercolumn extends Testbase {
    protected $dataset_filenames = ['model/bulkupdatecolumn.yml'];

    /**
     * @dataProvider Model_Receiveordercolumndataprovider::data_provider_all_records
     */
    public function test_get_physical_names_引数未指定の場合テーブルの全てのレコード情報を取得すること($all_records){
        $result = Model_Receiveordercolumn::get_physical_names();
        $this->assertEquals($all_records, $result);
    }

    /**
     * @dataProvider Model_Receiveordercolumndataprovider::data_provider_can_update_records
     */
    public function test_get_physical_names_with_disabledにfalseを指定すると更新できるカラムだけに絞った配列を返すこと($can_update_records){
        $result = Model_Receiveordercolumn::get_physical_names(false);
        $this->assertEquals($can_update_records, $result);
    }

    /**
     * @dataProvider Model_Receiveordercolumndataprovider::data_provider_section_base_records
     */
    public function test_get_physical_names_section_idにbaseを指定するとsectionがbaseのものだけ取れること($section_base_records){
        $result = Model_Receiveordercolumn::get_physical_names(true, Model_Receiveordersection::RECEIVE_ORDER_BASE);
        $this->assertEquals($section_base_records, $result);
    }

    /**
     * @dataProvider Model_Receiveordercolumndataprovider::data_provider_section_option_records
     */
    public function test_get_physical_names_section_idにoptionを指定するとsectionがoptionのものだけ取れること($section_option_records){
        $result = Model_Receiveordercolumn::get_physical_names(true, Model_Receiveordersection::RECEIVE_ORDER_OPTION);
        $this->assertEquals($section_option_records, $result);
    }

    public function test_get_additional_columns_ユーザーが任意で更新可能な項目以外で必要なカラムが取得できること() {
        $expect = [
            'receive_order_id' => '伝票番号',
            'receive_order_last_modified_date' => '最終更新日',
            'receive_order_order_status_id' => '受注状態区分',
            'receive_order_confirm_ids' => '受注確認内容',
        ];
        $result = Model_Receiveordercolumn::get_additional_columns();
        $this->assertEquals($expect, $result);
    }

    public function test_get_all_columns_引数を指定しない場合は更新できないカラムも取得できること() {
        $is_found = false;
        $display_order = null;
        foreach (Model_Receiveordercolumn::get_all_columns() as $column) {
            if ($column->disabled === '1') {
                $is_found = true;
            }

            // カラムタイプが設定されていること（この値についてはModel_Columntype::get_all()でテストされているのでここでは省略する）
            $this->assertNotEmpty($column->column_type);

            // receive_order_sectionがあること
            $this->assertNotEmpty($column->receive_order_section);

            // display_orderの昇順になっていること
            if ($display_order && $display_order > $column->display_order){
                $this->fail('display_orderの昇順になっていません');
            }
            $display_order = $column->display_order;
        }
        // disabled=1のカラムも含まれていること
        $this->assertTrue($is_found);
    }

    public function test_get_all_columns_引数がfalseの場合は更新できないカラムは取得できないこと() {
        $is_found = false;
        foreach (Model_Receiveordercolumn::get_all_columns(false) as $column) {
            if ($column->disabled === '1') {
                $is_found = true;
            }
        }
        // disabled=1のカラムも含まれていないこと
        $this->assertFalse($is_found);
    }

    public function test_get_all_columns_引数がtrueの場合は更新できないカラムも取得できること() {
        $is_found = false;
        foreach (Model_Receiveordercolumn::get_all_columns(true) as $column) {
            if ($column->disabled === '1') {
                $is_found = true;
            }
        }
        // disabled=1のカラムも含まれていること
        $this->assertTrue($is_found);
    }

    public function test_is_payment_支払関連項目の場合trueが返ること(){
        $receive_order_column = new Model_Receiveordercolumn();
        $receive_order_column->payment = '1';
        $this->assertTrue($receive_order_column->is_payment());
    }

    public function test_is_payment_支払関連項目ではない場合falseが返ること(){
        $receive_order_column = new Model_Receiveordercolumn();
        $receive_order_column->payment = '0';
        $this->assertFalse($receive_order_column->is_payment());
    }

    public function test_is_delivery_発送関連項目の場合trueが返ること(){
        $receive_order_column = new Model_Receiveordercolumn();
        $receive_order_column->delivery = '1';
        $this->assertTrue($receive_order_column->is_delivery());
    }

    public function test_is_delivery_発送関連項目ではない場合falseが返ること(){
        $receive_order_column = new Model_Receiveordercolumn();
        $receive_order_column->delivery = '0';
        $this->assertFalse($receive_order_column->is_delivery());
    }

    public function test_is_order_amount_受注金額関連項目の場合trueが返ること(){
        $receive_order_column = new Model_Receiveordercolumn();
        $receive_order_column->order_amount = '1';
        $this->assertTrue($receive_order_column->is_order_amount());
    }

    public function test_is_order_amount_受注金額関連項目ではない場合falseが返ること(){
        $receive_order_column = new Model_Receiveordercolumn();
        $receive_order_column->order_amount = '0';
        $this->assertFalse($receive_order_column->is_order_amount());
    }

    public function test_is_payment_method_id_支払方法の場合trueが返ること(){
        $receive_order_column = new Model_Receiveordercolumn();
        $receive_order_column->id = Model_Receiveordercolumn::COLUMN_ID_PAYMENT;
        $this->assertTrue($receive_order_column->is_payment_method_id());
    }

    public function test_is_payment_method_id_支払方法ではない場合falseが返ること(){
        $receive_order_column = new Model_Receiveordercolumn();
        $receive_order_column->id = '1';
        $this->assertFalse($receive_order_column->is_payment_method_id());
    }

    public function test_is_point_amount_ポイント数の場合trueが返ること(){
        $receive_order_column = new Model_Receiveordercolumn();
        $receive_order_column->id = Model_Receiveordercolumn::COLUMN_ID_POINT_AMOUNT;
        $this->assertTrue($receive_order_column->is_point_amount());
    }

    public function test_is_point_amount_ポイント数ではない場合falseが返ること(){
        $receive_order_column = new Model_Receiveordercolumn();
        $receive_order_column->id = '1';
        $this->assertFalse($receive_order_column->is_point_amount());
    }

    public function test_is_total_amount_総合計の場合trueが返ること(){
        $receive_order_column = new Model_Receiveordercolumn();
        $receive_order_column->id = Model_Receiveordercolumn::COLUMN_ID_TOTAL_AMOUNT;
        $this->assertTrue($receive_order_column->is_total_amount());
    }

    public function test_is_total_amount_総合計ではない場合falseが返ること(){
        $receive_order_column = new Model_Receiveordercolumn();
        $receive_order_column->id = '1';
        $this->assertFalse($receive_order_column->is_total_amount());
    }

    public function test_is_order_date_受注日の場合trueが返ること(){
        $receive_order_column = new Model_Receiveordercolumn();
        $receive_order_column->id = Model_Receiveordercolumn::COLUMN_ID_ORDER_DATE;
        $this->assertTrue($receive_order_column->is_order_date());
    }

    public function test_is_order_date_受注日ではない場合falseが返ること(){
        $receive_order_column = new Model_Receiveordercolumn();
        $receive_order_column->id = '1';
        $this->assertFalse($receive_order_column->is_order_date());
    }

    /**
     * @dataProvider Model_Receiveordercolumndataprovider::data_provider_is_seal
     */
    public function test_is_seal_シールの場合trueが返り、そうで無い場合はfalseが返ること(string $receive_order_column_id, bool $expected){
        $receive_order_column = new Model_Receiveordercolumn();
        $receive_order_column->id = $receive_order_column_id;
        $this->assertEquals($expected, $receive_order_column->is_seal());
    }

    public function test_get_date_select_types_日付の入力方法のリストが取得できること() {
        $expect = [
            Model_Receiveordercolumn::DATE_SELECT_TYPE_INPUT => '日付を入力',
            Model_Receiveordercolumn::DATE_SELECT_TYPE_TODAY => '今日',
            Model_Receiveordercolumn::DATE_SELECT_TYPE_TOMORROW => '明日',
            Model_Receiveordercolumn::DATE_SELECT_TYPE_PLUS_TWO_DAYS => '明後日'
        ];
        $result = Model_Receiveordercolumn::get_date_select_types();
        $this->assertEquals($expect, $result);
    }

    public function test_get_relative_date_list_相対的な日付指定のリストが取得できること() {
        $expect = [
            Model_Receiveordercolumn::DATE_SELECT_TYPE_TODAY => '今日',
            Model_Receiveordercolumn::DATE_SELECT_TYPE_TOMORROW => '明日',
            Model_Receiveordercolumn::DATE_SELECT_TYPE_PLUS_TWO_DAYS => '明後日'
        ];
        $result = Model_Receiveordercolumn::get_relative_date_list();
        $this->assertEquals($expect, $result);
    }

    public function test_is_date_select_relative_date_引数の値が相対的な日付指定の値の場合trueを返すこと() {
        $result = Model_Receiveordercolumn::is_date_select_relative_date('today');
        $this->assertTrue($result);

        $result = Model_Receiveordercolumn::is_date_select_relative_date('tomorrow');
        $this->assertTrue($result);

        $result = Model_Receiveordercolumn::is_date_select_relative_date('+2 day');
        $this->assertTrue($result);
    }

    public function test_is_date_select_relative_date_引数の値が相対的な日付指定の値ではない場合falseを返すこと() {
        $result = Model_Receiveordercolumn::is_date_select_relative_date('2018/08/28');
        $this->assertFalse($result);

        $result = Model_Receiveordercolumn::is_date_select_relative_date('TEST');
        $this->assertFalse($result);
    }

}