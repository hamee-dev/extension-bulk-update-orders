<?php

class Test_Presenter_Updatesetting_Presenter extends Testbase
{
    protected $fetch_init_yaml = false;

    protected $dataset_filenames = ['model/bulkupdatesetting.yml'];

    /**
     * @dataProvider Presenter_Updatesettingdataprovider::data_provider_get_view_info_by_setting
     */
    public function test__get_view_info_by_setting_画面生成用のデータをDomain_Value_Updatesettingbysetting型で取得できること(string $company_id, string $user_id, Model_Bulkupdatesetting $bulk_update_setting, Domain_Value_Updatesettingbysetting $updatesetting_by_setting) {
        $mock = new class extends Presenter_Updatesetting_Presenter {

            public function __construct()
            {
                parent::__construct('view', null, 'updatesetting/new');
            }

            protected static function _get_view_path(Model_Bulkupdatecolumn $bulk_update_column) : string {
                return 'PATH';
            }

            protected static function _get_caution_view_path(Model_Receiveordercolumn $receive_order_column) : ?string {
                return 'CAUTION_PATH';
            }

            protected static function _get_forwarding_agent_options(string $delivery_id, array $forwarding_agent_names, Utility_Master $master) : array {
                return ['OPTION1', 'OPTION2'];
            }

            protected static function _get_tag_list(Utility_Master $master) : array {
                return ['TAG1', 'TAG2'];
            }

            protected static function _get_master_for_options($master, $master_name) : array {
                return ['MASTER1', 'MASTER2'];
            }
        };

        $result = self::invoke_method($mock, '_get_view_info_by_setting', [$company_id, $user_id, $bulk_update_setting]);
        $this->assertEquals($updatesetting_by_setting, $result);
    }

    /**
     * @dataProvider Presenter_Updatesettingdataprovider::data_provider_get_view_info_by_columns
     */
    public function test__get_view_info_by_columns_画面生成用のデータをDomain_Value_Updatesettingbycolumn型で取得できること(string $company_id, Model_Bulkupdatesetting $bulk_update_setting, Domain_Value_Updatesettingbycolumn $updatesetting_by_column) {
        $mock = new class extends Presenter_Updatesetting_Presenter {

            public function __construct()
            {
                parent::__construct('view', null, 'updatesetting/edit');
            }

            protected static function _get_receive_order_column_for_array(Model_Receiveordercolumn $receive_order_column) : array {
                return ['COLUMN'];
            }

            protected static function _get_update_mehod_options(array $column_types_update_methods) : array {
                return ['UPDATE_METHOD'];
            }

            protected static function _get_target_list(array $columns, Model_Bulkupdatesetting $setting) : array {
                return ['TARGET_LIST'];
            }

            protected static function _get_forwarding_agent_column_list(array $receive_order_columns, Model_Bulkupdatesetting $setting) : array {
                return ['FORWARD_AGENT_COLUMN'];
            }
        };

        $result = self::invoke_method(
            $mock,
            '_get_view_info_by_columns',
            [
                $bulk_update_setting
            ]
        );
        $this->assertEquals($updatesetting_by_column, $result);
    }

    /**
     * @dataProvider Presenter_Updatesettingdataprovider::data_provider_get_forwarding_agent_options
     */
    public function test__get_forwarding_agent_options_発送方法別タイプのセレクトボックス表示用に連想配列が取得できること(string $delivery_id, array $forwarding_agent_names, array $get_forwarding_agent_returns, array $expected) {

        $with_consecutive = [];
        $will_return_on_consecutive_calls = [];
        foreach ($forwarding_agent_names as $forwarding_agent_name) {
            $with_consecutive[] = [$this->equalTo(true), $this->equalTo($delivery_id), $this->equalTo($forwarding_agent_name)];
            $will_return_on_consecutive_calls[] = $get_forwarding_agent_returns[$forwarding_agent_name];

        }

        $master_stub = $this->getMockBuilder(Utility_Master::class)
            ->setConstructorArgs([self::DUMMY_COMPANY_ID1])
            ->setMethods(['get_forwarding_agent'])
            ->getMock();
        $invocation_mocker = $master_stub->expects($this->exactly(count($forwarding_agent_names)));
        $invocation_mocker->method('get_forwarding_agent');
        call_user_func_array([$invocation_mocker, 'withConsecutive'], $with_consecutive);
        call_user_func_array([$invocation_mocker, 'willReturnOnConsecutiveCalls'], $will_return_on_consecutive_calls);

        $mock = Presenter_Updatesetting_Presenter::forge('updatesetting/new', 'view');
        $result = self::invoke_method($mock, '_get_forwarding_agent_options', [$delivery_id, $forwarding_agent_names, $master_stub]);
        $this->assertEquals($expected, $result);
    }

    public function test__get_receive_order_column_for_array_Model_Receiveordercolumnが配列に変換されtemplate_nameとcolumn_type_nameが追加されていること() {

        $mock = new class extends Presenter_Updatesetting_Presenter {

            public function __construct()
            {
                parent::__construct('view', null, 'updatesetting/edit');
            }

            protected static function _get_template_name(Model_Receiveordercolumn $receive_order_column) : string {
                return 'TEMPLATE_NAME';
            }

            protected static function _get_caution_template_name(Model_Receiveordercolumn $receive_order_column) : ?string {
                return 'CAUTION_TEMPLATE_NAME';
            }

            protected static function _get_column_type_name(Model_Receiveordercolumn $receive_order_column) : string {
                return 'COLUMN_TYPE_NAME';
            }
        };

        $receive_order_column = new Model_Receiveordercolumn();
        $receive_order_column->id = 1;
        $receive_order_column->receive_order_section_id = 1;
        $receive_order_column->column_type_id = 7;
        $receive_order_column->physical_name = 'receive_order_shop_id';
        $receive_order_column->logical_name = '店舗';
        $result = self::invoke_method($mock, '_get_receive_order_column_for_array', [$receive_order_column]);
        $expected = [
            'id' => 1,
            'receive_order_section_id' => 1,
            'column_type_id' => 7,
            'physical_name' => 'receive_order_shop_id',
            'logical_name' => '店舗',
            'template_name' => 'TEMPLATE_NAME',
            'caution_template_name' => 'CAUTION_TEMPLATE_NAME',
            'column_type_name' => 'COLUMN_TYPE_NAME'
        ];
        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider Presenter_Updatesettingdataprovider::data_provider_get_template_names
     */
    public function test__get_template_name_テンプレート名が取得できること(Model_Receiveordercolumn $receive_order_column, string $template_name) {
        $mock = Presenter_Updatesetting_Presenter::forge('updatesetting/new', 'view');
        $result = self::invoke_method($mock, '_get_template_name', [$receive_order_column]);
        $this->assertEquals($template_name, $result);
    }

    /**
     * @dataProvider Presenter_Updatesettingdataprovider::data_provider_get_caution_template_names
     */
    public function test__get_caution_template_name_テンプレート名が取得できること(Model_Receiveordercolumn $receive_order_column, $template_name) {
        $mock = Presenter_Updatesetting_Presenter::forge('updatesetting/new', 'view');
        $result = self::invoke_method($mock, '_get_caution_template_name', [$receive_order_column]);
        $this->assertEquals($template_name, $result);
    }

    public function test__get_update_mehod_options_更新方法のidとnameの連想配列で返ること() {

        $column_types_update_methods = [];

        $update_method = new Model_Updatemethod();
        $update_method->id = Model_Updatemethod::OVERWRITE;
        $update_method->name = '上書き';

        $columntypes_updatemethod = new Model_Columntypesupdatemethod();
        $columntypes_updatemethod->update_method = $update_method;

        $column_types_update_methods[] = $columntypes_updatemethod;

        $update_method = new Model_Updatemethod();
        $update_method->id = Model_Updatemethod::ADDWRITE;
        $update_method->name = '追記';

        $columntypes_updatemethod = new Model_Columntypesupdatemethod();
        $columntypes_updatemethod->update_method = $update_method;

        $column_types_update_methods[] = $columntypes_updatemethod;

        $update_method = new Model_Updatemethod();
        $update_method->id = Model_Updatemethod::ADDITION;
        $update_method->name = '加算';

        $columntypes_updatemethod = new Model_Columntypesupdatemethod();
        $columntypes_updatemethod->update_method = $update_method;

        $column_types_update_methods[] = $columntypes_updatemethod;

        $mock = Presenter_Updatesetting_Presenter::forge('updatesetting/new', 'view');
        $result = self::invoke_method($mock, '_get_update_mehod_options', [$column_types_update_methods]);
        $expected = [
            Model_Updatemethod::OVERWRITE => '上書き',
            Model_Updatemethod::ADDWRITE => '追記',
            Model_Updatemethod::ADDITION => '加算',
        ];
        $this->assertEquals($expected, $result);
    }

    public function test__get_update_mehod_options_column_types_update_methodsが空配列だった場合、空配列が返ること() {
        $mock = Presenter_Updatesetting_Presenter::forge('updatesetting/new', 'view');
        $result = self::invoke_method($mock, '_get_update_mehod_options', [[]]);
        $this->assertEquals([], $result);
    }

    public function test__is_forwarding_agent_column_発送方法関連ではない場合falseが返ること() {
        $receiveorder_column = new Model_Receiveordercolumn();
        $mock = Presenter_Updatesetting_Presenter::forge('updatesetting/new', 'view');
        $result = self::invoke_method($mock, '_is_forwarding_agent_column', [$receiveorder_column]);
        $this->assertFalse($result);
    }

    public function test__is_forwarding_agent_column_発送方法だがタイプ別区分の項目ではない場合falseが返ること() {
        $receiveorder_column = new Model_Receiveordercolumn();
        $receiveorder_column->delivery = '1';
        $receiveorder_column->master_name = 'TEST';
        $mock = Presenter_Updatesetting_Presenter::forge('updatesetting/new', 'view');
        $result = self::invoke_method($mock, '_is_forwarding_agent_column', [$receiveorder_column]);
        $this->assertFalse($result);
    }

    public function test__is_forwarding_agent_column_タイプ別区分の項目の場合trueが返ること() {
        $receiveorder_column = new Model_Receiveordercolumn();
        $receiveorder_column->delivery = '1';
        $receiveorder_column->master_name = Utility_Master::MASTER_NAME_FORWARDINGAGENT . 'TEST';
        $mock = Presenter_Updatesetting_Presenter::forge('updatesetting/new', 'view');
        $result = self::invoke_method($mock, '_is_forwarding_agent_column', [$receiveorder_column]);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider Presenter_Updatesettingdataprovider::data_provider_get_view_path
     */
    public function test__get_view_path_テンプレートのファイルパスを取得できること(Model_Bulkupdatecolumn $bulk_update_column, string $view_path) {
        $mock = Presenter_Updatesetting_Presenter::forge('updatesetting/new', 'view');
        $result = self::invoke_method($mock, '_get_view_path', [$bulk_update_column]);
        $this->assertEquals($view_path, $result);
    }

    public function test__get_caution_view_path_テンプレート名が存在しない場合テンプレートのファイルパスがnullであること() {
        $mock = new class extends Presenter_Updatesetting_Presenter {

            public function __construct()
            {
                parent::__construct('view', null, 'updatesetting/edit');
            }

            protected static function _get_caution_template_name(Model_Receiveordercolumn $receive_order_column) : ?string {
                return null;
            }

        };

        $receiveorder_column = new Model_Receiveordercolumn();
        $result = self::invoke_method($mock, '_get_caution_view_path', [$receiveorder_column]);
        $this->assertEquals(null, $result);
    }

    public function test__get_caution_view_path_テンプレート名が存在する場合テンプレートのファイルパスを取得できること() {
        $mock = new class extends Presenter_Updatesetting_Presenter {

            public function __construct()
            {
                parent::__construct('view', null, 'updatesetting/edit');
            }

            protected static function _get_caution_template_name(Model_Receiveordercolumn $receive_order_column) : ?string {
                return 'CAUTION_TEMPLATE_NAME';
            }

        };

        $receiveorder_column = new Model_Receiveordercolumn();
        $result = self::invoke_method($mock, '_get_caution_view_path', [$receiveorder_column]);
        $this->assertEquals('updatesetting/templates/_CAUTION_TEMPLATE_NAME', $result);
    }

    public function test__get_master_for_options_マスタデータのidとマスタ名の連想配列が返ること() {
        $master_name = 'MASTER_NAME';
        $master_data = [
            1 => new Domain_Value_Master(1, 'MASTER1'),
            2 => new Domain_Value_Master(2, 'MASTER2'),
            3 => new Domain_Value_Master(3, 'MASTER3'),
        ];
        $master_stub = $this->getMockBuilder(Utility_Master::class)
            ->setConstructorArgs([self::DUMMY_COMPANY_ID1])
            ->setMethods(['get'])
            ->getMock();
        $master_stub->expects($this->once())
            ->method('get')
            ->with($master_name)
            ->willReturn($master_data);

        $mock = Presenter_Updatesetting_Presenter::forge('updatesetting/new', 'view');
        $result = self::invoke_method($mock, '_get_master_for_options', [$master_stub, $master_name]);
        $expected = [
            1 => 'MASTER1',
            2 => 'MASTER2',
            3 => 'MASTER3',
        ];
        $this->assertEquals($expected, $result);
    }

    public function test__get_master_for_options_マスタデータを取得できなかった場合、空配列が返ること() {
        $master_name = 'MASTER_NAME';
        $master_stub = $this->getMockBuilder(Utility_Master::class)
            ->setConstructorArgs([self::DUMMY_COMPANY_ID1])
            ->setMethods(['get'])
            ->getMock();
        $master_stub->expects($this->once())
            ->method('get')
            ->with($master_name)
            ->willReturn([]);

        $mock = Presenter_Updatesetting_Presenter::forge('updatesetting/new', 'view');
        $result = self::invoke_method($mock, '_get_master_for_options', [$master_stub, $master_name]);
        $this->assertEquals([], $result);
    }

    public function test__get_tag_list_タグ名と表示スタイルが連想配列で返ること() {
        $master_data = [
            1 => new Domain_Value_Master(1, 'MASTER1', false, ['grouping_tag_color' => 'TAG_COLOR1', 'grouping_tag_str_color' => 'TAG_STR_COLOR1']),
            2 => new Domain_Value_Master(2, 'MASTER2', false, ['grouping_tag_color' => 'TAG_COLOR2', 'grouping_tag_str_color' => 'TAG_STR_COLOR2']),
            3 => new Domain_Value_Master(3, 'MASTER3', false, ['grouping_tag_color' => 'TAG_COLOR3', 'grouping_tag_str_color' => 'TAG_STR_COLOR3']),
        ];
        $master_stub = $this->getMockBuilder(Utility_Master::class)
            ->setConstructorArgs([self::DUMMY_COMPANY_ID1])
            ->setMethods(['get'])
            ->getMock();
        $master_stub->expects($this->once())
            ->method('get')
            ->with(Utility_Master::MASTER_NAME_TAG)
            ->willReturn($master_data);

        $mock = Presenter_Updatesetting_Presenter::forge('updatesetting/new', 'view');
        $result = self::invoke_method($mock, '_get_tag_list', [$master_stub]);
        $expected = [
            ['name' => 'MASTER1', 'style' => 'background-color: TAG_COLOR1; color: TAG_STR_COLOR1; border: 1px solid TAG_COLOR1; '],
            ['name' => 'MASTER2', 'style' => 'background-color: TAG_COLOR2; color: TAG_STR_COLOR2; border: 1px solid TAG_COLOR2; '],
            ['name' => 'MASTER3', 'style' => 'background-color: TAG_COLOR3; color: TAG_STR_COLOR3; border: 1px solid TAG_COLOR3; '],
        ];
        $this->assertEquals($expected, $result);
    }

    public function test__get_tag_list_タグが１つもなかった場合、空配列で返ること() {
        $master_stub = $this->getMockBuilder(Utility_Master::class)
            ->setConstructorArgs([self::DUMMY_COMPANY_ID1])
            ->setMethods(['get'])
            ->getMock();
        $master_stub->expects($this->once())
            ->method('get')
            ->with(Utility_Master::MASTER_NAME_TAG)
            ->willReturn([]);

        $mock = Presenter_Updatesetting_Presenter::forge('updatesetting/new', 'view');
        $result = self::invoke_method($mock, '_get_tag_list', [$master_stub]);
        $this->assertEquals([], $result);
    }

    public function test__get_cache_master_list_マスタデータのキャッシュがある場合、それを返すこと() {

        $file_name = 'master_shop.cache';
        copy(APPPATH . 'tests/file/' . $file_name, APPPATH . 'cache/test/' . self::DUMMY_COMPANY_ID1 . '_' . $file_name);
        $mock = Presenter_Updatesetting_Presenter::forge('updatesetting/new', 'view');
        $result = self::invoke_method($mock, '_get_cache_master_list', [self::DUMMY_COMPANY_ID1]);
        $expected = [
            'shop' => [
                [
                    'id' => '1',
                    'name' => '店舗1',
                    'disabled' => false,
                    'params' => ['shop_deleted_flag' => '0'],
                ],
                [
                    'id' => '2',
                    'name' => '店舗2',
                    'disabled' => false,
                    'params' => ['shop_deleted_flag' => '0'],
                ],
                [
                    'id' => '3',
                    'name' => '店舗3',
                    'disabled' => false,
                    'params' => ['shop_deleted_flag' => '0'],
                ],
            ],
            'gift' => [
                [
                    'id' => '0',
                    'name' => '無し',
                    'disabled' => false,
                    'params' => [],
                ],
                [
                    'id' => '1',
                    'name' => '有り',
                    'disabled' => false,
                    'params' => [],
                ],
            ],
        ];
        $this->assertEquals($expected, $result);
    }

    public function test__get_cache_master_list_マスタデータのキャッシュが無い場合、ギフトのマスタデータだけ返ること() {

        $cache_file = APPPATH . 'cache/test/' . self::DUMMY_COMPANY_ID1 . '_master_shop.cache';
        if (file_exists($cache_file)) {
            unlink($cache_file);
        }
        $mock = Presenter_Updatesetting_Presenter::forge('updatesetting/new', 'view');
        $result = self::invoke_method($mock, '_get_cache_master_list', [self::DUMMY_COMPANY_ID1]);
        $expected = [
            'gift' => [
                [
                    'id' => '0',
                    'name' => '無し',
                    'disabled' => false,
                    'params' => [],
                ],
                [
                    'id' => '1',
                    'name' => '有り',
                    'disabled' => false,
                    'params' => [],
                ],
            ],
        ];
        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider Presenter_Updatesettingdataprovider::data_provider_get_forwarding_agent_column_list
     */
    public function test__get_forwarding_agent_column_list_発送方法別タイプのカラムの配列を取得できること(array $receive_order_columns, Model_Bulkupdatesetting $setting, array $forwarding_agent_column_list) {
        $mock = Presenter_Updatesetting_Presenter::forge('updatesetting/new', 'view');
        $result = self::invoke_method($mock, '_get_forwarding_agent_column_list', [$receive_order_columns, $setting]);
        $this->assertEquals($forwarding_agent_column_list, $result);
    }

    /**
     * @dataProvider Presenter_Updatesettingdataprovider::data_provider_get_target_list
     */
    public function test__get_target_list_更新する項目一覧を連想配列で取得できること(
        array $columns,
        Model_Bulkupdatesetting $setting,
        array $target_list
    ) {

        $mock = Presenter_Updatesetting_Presenter::forge('updatesetting/new', 'view');
        $result = self::invoke_method(
            $mock,
            '_get_target_list',
            [$columns, $setting]
        );
        $this->assertEquals($target_list, $result);
    }
}