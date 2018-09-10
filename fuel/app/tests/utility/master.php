<?php
/**
 *
 * Class Test_Utility_Master
 */
class Test_Utility_Master extends Testbase
{
    public function test_get_有効なマスタを取得できること() {
        $stub = $this->getMockBuilder(Utility_Master::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['get_shop'])
            ->getMock();
        $result = [
            '1' => ['name' => 'name1', 'disabled' => false],
            '3' => ['name' => 'name3', 'disabled' => true],
            '4' => ['name' => 'name4', 'disabled' => false],
        ];
        $enabled = true;
        $stub->expects($this->once())
            ->method('get_shop')
            ->with($this->equalTo($enabled))
            ->will($this->returnValue($result));
        $this->assertEquals($result, $stub->get(Utility_Master::MASTER_NAME_SHOP, $enabled));
    }

    public function test_get_すべてのマスタを取得できること() {
        $stub = $this->getMockBuilder(Utility_Master::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['get_shop'])
            ->getMock();
        $result = [
            '1' => ['name' => 'name1', 'disabled' => false],
            '2' => ['name' => 'name2', 'disabled' => true],
            '3' => ['name' => 'name3', 'disabled' => false],
            '4' => ['name' => 'name4', 'disabled' => false],
        ];
        $enabled = false;
        $stub->expects($this->once())
            ->method('get_shop')
            ->with($this->equalTo($enabled))
            ->will($this->returnValue($result));
        $this->assertEquals($result, $stub->get(Utility_Master::MASTER_NAME_SHOP, $enabled));
    }

    /**
     * @expectedException Exception
     */
    public function test_get_存在しないマスタの場合例外エラーが発生すること() {
        $master = new Utility_Master(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1);
        $master->get('none');
    }

    /**
     * 各マスタ取得処理のテスト
     *
     * @dataProvider Utility_Masterdataprovider::data_provider_master_get
     *
     * @param Masterprovider $params
     * @param array $result 期待する結果
     */
    public function test_各マスタ取得処理が正しく動作すること(Masterprovider $params, array $result) {
        // モックの作成
        $stub = $this->getMockBuilder(Utility_Master::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['get_master', 'get_data_formating'])
            ->getMock();

        // get_masterの引数が正しいか
        $stub->expects($this->once())
            ->method('get_master')
            ->with($this->equalTo($params->name), $this->equalTo($params->path), $this->equalTo($params->fields))
            ->will($this->returnValue($params->api_response));

        if (!empty($params->api_response)) {
            // get_data_formatingの引数が正しいか、並び替え後の配列が引数として渡されているか
            $stub->expects($this->once())
                ->method('get_data_formating')
                ->with($this->equalTo($params->sort_response), $this->equalTo($params->id_key), $this->equalTo($params->name_key), $this->equalTo($params->enabled), $this->equalTo($params->disabled_flags))
                ->will($this->returnValue($result));
        }

        $function_name = 'get_' . $params->name;
        $this->assertEquals($result, $stub->$function_name($params->enabled));
    }

    /**
     * @dataProvider Utility_Masterdataprovider::data_provider_get_forwarding_agent
     *
     * @param Masterprovider $params
     * @param string $delivery_id
     * @param string $name
     * @param array $result
     */
    public function test_get_forwarding_agent_発送方法別項目タイプマスタ取得処理が正しく動作すること(Masterprovider $params, string $delivery_id, $name, array $result) {
        // モックの作成
        $stub = $this->getMockBuilder(Utility_Master::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['get_master', 'get_data_formating'])
            ->getMock();

        // get_masterの引数が正しいか
        $stub->expects($this->once())
            ->method('get_master')
            ->with($this->equalTo($params->name), $this->equalTo($params->path), $this->equalTo($params->fields))
            ->will($this->returnValue($params->api_response));

        $this->assertEquals($result, $stub->get_forwarding_agent($params->enabled, $delivery_id, $name));
    }

    public function test_get_gift_ギフトマスタ取得処理が正しく動作すること() {
        $master = new Utility_Master(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1);
        $expected = [
            '0' => new Domain_Value_Master('0','無し'),
            '1' => new Domain_Value_Master('1','有り'),
        ];
        $this->assertEquals($expected, $master->get_gift());
    }

    public function test_is_forwarding_agent_発送方法別項目タイプマスタ名の場合はtrueが返ること() {
        $master = new Utility_Master(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1);
        $this->assertTrue($master->is_forwarding_agent('forwarding_agent_jikantaisitei'));
    }

    public function test_is_forwarding_agent_発送方法別項目タイプマスタ名ではない場合はfalseが返ること() {
        $master = new Utility_Master(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1);
        $this->assertFalse($master->is_forwarding_agent('none'));
    }

    public function test_get_master_キャッシュがあればキャッシュが返ること() {
        // キャッシュ作成
        $name = Utility_Master::MASTER_NAME_SHOP;
        $path = Client_Neapi::PATH_SHOP_SEARCH;
        $fields = 'shop_id,shop_name,shop_deleted_flag';
        $cache_name = self::DUMMY_COMPANY_ID1 . Utility_Master::CACHE_FILE_NAME_SEPARATOR . Utility_Master::CACHE_KEYWORD . Utility_Master::CACHE_FILE_NAME_SEPARATOR . $name;
        $cache_data = [
            ['shop_id' => '1', 'shop_name' => 'name1', 'shop_deleted_flag' => '0'],
            ['shop_id' => '2', 'shop_name' => 'name2', 'shop_deleted_flag' => '1'],
            ['shop_id' => '3', 'shop_name' => 'name3', 'shop_deleted_flag' => '0'],
            ['shop_id' => '4', 'shop_name' => 'name4', 'shop_deleted_flag' => '0'],
        ];
        Cache::set($cache_name, $cache_data, Utility_Master::CACHE_EXPIRATION);
        $method = $this->getMethod(Utility_Master::class, 'get_master');
        $this->assertEquals($cache_data, $method->invoke(new Utility_Master(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1), $name, $path, $fields));
        Cache::delete($cache_name);
    }

    public function test_get_master_キャッシュがなければAPIリクエストを実行しキャッシュされていること() {
        $name = Utility_Master::MASTER_NAME_SHOP;
        $path = Client_Neapi::PATH_SHOP_SEARCH;
        $fields = 'shop_id,shop_name,shop_deleted_flag';
        $api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'data' =>[
                ['shop_id' => '1', 'shop_name' => 'name1', 'shop_deleted_flag' => '0'],
                ['shop_id' => '2', 'shop_name' => 'name2', 'shop_deleted_flag' => '1'],
                ['shop_id' => '3', 'shop_name' => 'name3', 'shop_deleted_flag' => '0'],
                ['shop_id' => '4', 'shop_name' => 'name4', 'shop_deleted_flag' => '0'],
            ]
        ];
        $result = $api_response['data'];

        $cache_name = self::DUMMY_COMPANY_ID1 . Utility_Master::CACHE_FILE_NAME_SEPARATOR . Utility_Master::CACHE_KEYWORD . Utility_Master::CACHE_FILE_NAME_SEPARATOR . $name;

        // モックの作成
        $stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['apiExecute'])
            ->getMock();
        $stub->expects($this->once())
            ->method('apiExecute')
            ->with($this->equalTo($path), $this->equalTo(['fields' => $fields]))
            ->will($this->returnValue($api_response));

        $master = new Utility_Master(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1);
        $class = new ReflectionClass($master);
        $property = $class->getProperty('client_neapi');
        $property->setAccessible(true);
        $property->setValue($master, $stub);
        $method = $class->getMethod('get_master');
        $method->setAccessible(true);

        $this->assertEquals($result, $method->invoke($master, $name, $path, $fields));
        $this->assertEquals($result, Cache::get($cache_name));

        Cache::delete($cache_name);
    }

    /**
     * キャッシュがなかった場合は例外エラーが発生する
     * @expectedException UnexpectedValueException
     */
    public function test_get_master_APIレスポンスがエラーだった場合はUnexpectedValueExceptionの例外が発生すること() {
        $name = Utility_Master::MASTER_NAME_FORWARDINGAGENT;
        $path = Client_Neapi::PATH_RECEIVEORDER_FORWARDINGAGENT_SEARCH;
        $fields = 'forwarding_agent_id,forwarding_agent_type,forwarding_agent_type_id,forwarding_agent_type_name,forwarding_agent_display_order,forwarding_agent_deleted_flag';
        $api_response = ['result' => Client_Neapi::RESULT_ERROR,];

        $cache_name = self::DUMMY_COMPANY_ID1 . Utility_Master::CACHE_FILE_NAME_SEPARATOR . Utility_Master::CACHE_KEYWORD . Utility_Master::CACHE_FILE_NAME_SEPARATOR . $name;

        // モックの作成
        $stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['apiExecute'])
            ->getMock();
        $stub->expects($this->once())
            ->method('apiExecute')
            ->with($this->equalTo($path), $this->equalTo(['fields' => $fields]))
            ->will($this->returnValue($api_response));

        $master = new Utility_Master(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1);
        $class = new ReflectionClass($master);
        $property = $class->getProperty('client_neapi');
        $property->setAccessible(true);
        $property->setValue($master, $stub);
        $method = $class->getMethod('get_master');
        $method->setAccessible(true);
        $result = $method->invoke($master, $name, $path, $fields);
    }

    public function test_get_master_is_cacheがfalseだった場合、キャッシュがあってもキャッシュを返さないしキャッシュもしないこと() {
        // キャッシュ作成
        $name = Utility_Master::MASTER_NAME_SHOP;
        $path = Client_Neapi::PATH_SHOP_SEARCH;
        $fields = 'shop_id,shop_name,shop_deleted_flag';
        $cache_name = self::DUMMY_COMPANY_ID1 . Utility_Master::CACHE_FILE_NAME_SEPARATOR . Utility_Master::CACHE_KEYWORD . Utility_Master::CACHE_FILE_NAME_SEPARATOR . $name;
        $cache_data = [
            ['shop_id' => '1', 'shop_name' => 'name1', 'shop_deleted_flag' => '0'],
            ['shop_id' => '2', 'shop_name' => 'name2', 'shop_deleted_flag' => '1'],
            ['shop_id' => '3', 'shop_name' => 'name3', 'shop_deleted_flag' => '0'],
            ['shop_id' => '4', 'shop_name' => 'name4', 'shop_deleted_flag' => '0'],
        ];
        Cache::set($cache_name, $cache_data, Utility_Master::CACHE_EXPIRATION);

        // モックの作成
        $api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'data' =>[
                ['shop_id' => '1', 'shop_name' => 'name1', 'shop_deleted_flag' => '0'],
            ]
        ];
        $result = $api_response['data'];
        $stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['apiExecute'])
            ->getMock();
        $stub->expects($this->once())
            ->method('apiExecute')
            ->with($this->equalTo($path), $this->equalTo(['fields' => $fields]))
            ->will($this->returnValue($api_response));

        $master = new Utility_Master(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1);
        $class = new ReflectionClass($master);
        $property = $class->getProperty('client_neapi');
        $property->setAccessible(true);
        $property->setValue($master, $stub);
        $method = $class->getMethod('get_master');
        $method->setAccessible(true);

        $this->assertEquals($result, $method->invoke($master, $name, $path, $fields, false));
        $this->assertEquals($cache_data, Cache::get($cache_name));
    }

    public function test_get_data_formating_有効なデータのみ整形されること() {
        $list = [
            ['shop_id' => '1', 'shop_name' => 'name1', 'shop_deleted_flag' => '0'],
            ['shop_id' => '2', 'shop_name' => 'name2', 'shop_deleted_flag' => '1'],
            ['shop_id' => '3', 'shop_name' => 'name3', 'shop_deleted_flag' => '0'],
            ['shop_id' => '4', 'shop_name' => 'name4', 'shop_deleted_flag' => '0'],
        ];
        $is_key = 'shop_id';
        $name_key = 'shop_name';
        $enabled = true;
        $disabled_flags = ['shop_deleted_flag' => '1'];
        $result = [
            '1' => new Domain_Value_Master('1', 'name1', false, ['shop_deleted_flag' => '0']),
            '3' => new Domain_Value_Master('3', 'name3', false, ['shop_deleted_flag' => '0']),
            '4' => new Domain_Value_Master('4', 'name4', false, ['shop_deleted_flag' => '0']),
        ];

        $method = $this->getMethod(Utility_Master::class, 'get_data_formating');
        $this->assertEquals($result, $method->invoke(new Utility_Master(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1), $list, $is_key, $name_key, $enabled, $disabled_flags));
    }

    public function test_get_data_formating_すべてのデータが整形されること() {
        $list = [
            ['shop_id' => '1', 'shop_name' => 'name1', 'shop_deleted_flag' => '0'],
            ['shop_id' => '2', 'shop_name' => 'name2', 'shop_deleted_flag' => '1'],
            ['shop_id' => '3', 'shop_name' => 'name3', 'shop_deleted_flag' => '0'],
            ['shop_id' => '4', 'shop_name' => 'name4', 'shop_deleted_flag' => '0'],
        ];
        $is_key = 'shop_id';
        $name_key = 'shop_name';
        $enabled = false;
        $disabled_flags = ['shop_deleted_flag' => '1'];
        $result = [
            '1' => new Domain_Value_Master('1', 'name1', false, ['shop_deleted_flag' => '0']),
            '2' => new Domain_Value_Master('2', 'name2', true, ['shop_deleted_flag' => '1']),
            '3' => new Domain_Value_Master('3', 'name3', false, ['shop_deleted_flag' => '0']),
            '4' => new Domain_Value_Master('4', 'name4', false, ['shop_deleted_flag' => '0']),
        ];

        $method = $this->getMethod(Utility_Master::class, 'get_data_formating');
        $this->assertEquals($result, $method->invoke(new Utility_Master(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1), $list, $is_key, $name_key, $enabled, $disabled_flags));
    }

    public function test_get_data_formating_disabled_flagsが複数ある場合でも正しく整形されること() {
        $list = [
            ['confirm_id' => '2', 'confirm_name' => 'name2', 'confirm_display_order' => '1', 'confirm_valid_flag' => '1', 'confirm_deleted_flag' => '1'],
            ['confirm_id' => '1', 'confirm_name' => 'name1', 'confirm_display_order' => '2', 'confirm_valid_flag' => '1', 'confirm_deleted_flag' => '0'],
            ['confirm_id' => '5', 'confirm_name' => 'name5', 'confirm_display_order' => '3', 'confirm_valid_flag' => '0', 'confirm_deleted_flag' => '1'],
            ['confirm_id' => '3', 'confirm_name' => 'name3', 'confirm_display_order' => '4', 'confirm_valid_flag' => '0', 'confirm_deleted_flag' => '0'],
            ['confirm_id' => '4', 'confirm_name' => 'name4', 'confirm_display_order' => '5', 'confirm_valid_flag' => '1', 'confirm_deleted_flag' => '0'],
        ];
        $is_key = 'confirm_id';
        $name_key = 'confirm_name';
        $enabled = false;
        $disabled_flags = ['confirm_valid_flag' => '0', 'confirm_deleted_flag' => '1'];
        $result = [
            '2' => new Domain_Value_Master('2', 'name2', true, ['confirm_display_order' => '1', 'confirm_valid_flag' => '1', 'confirm_deleted_flag' => '1']),
            '1' => new Domain_Value_Master('1', 'name1', false, ['confirm_display_order' => '2', 'confirm_valid_flag' => '1', 'confirm_deleted_flag' => '0']),
            '5' => new Domain_Value_Master('5', 'name5', true, ['confirm_display_order' => '3', 'confirm_valid_flag' => '0', 'confirm_deleted_flag' => '1']),
            '3' => new Domain_Value_Master('3', 'name3', true, ['confirm_display_order' => '4', 'confirm_valid_flag' => '0', 'confirm_deleted_flag' => '0']),
            '4' => new Domain_Value_Master('4', 'name4', false, ['confirm_display_order' => '5', 'confirm_valid_flag' => '1', 'confirm_deleted_flag' => '0']),
        ];

        $method = $this->getMethod(Utility_Master::class, 'get_data_formating');
        $this->assertEquals($result, $method->invoke(new Utility_Master(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1), $list, $is_key, $name_key, $enabled, $disabled_flags));
    }

    public function test_get_data_formating_id_keyもしくはname_keyがnullだった場合そのデータは無視されること() {
        $list = [
            ['shop_id' => '1', 'shop_name' => null, 'shop_deleted_flag' => '0'],
            ['shop_id' => '2', 'shop_name' => 'name2', 'shop_deleted_flag' => '1'],
            ['shop_id' => '3', 'shop_name' => 'name3', 'shop_deleted_flag' => '0'],
            ['shop_id' => null, 'shop_name' => 'name4', 'shop_deleted_flag' => '0'],
        ];
        $is_key = 'shop_id';
        $name_key = 'shop_name';
        $enabled = true;
        $disabled_flags = ['shop_deleted_flag' => '1'];
        $result = [
            '3' => new Domain_Value_Master('3', 'name3', false, ['shop_deleted_flag' => '0']),
        ];

        $method = $this->getMethod(Utility_Master::class, 'get_data_formating');
        $this->assertEquals($result, $method->invoke(new Utility_Master(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1), $list, $is_key, $name_key, $enabled, $disabled_flags));
    }

    public function test_get_data_formating_id_keyもしくはname_key以外がnullだった場合そのデータは無視されないこと() {
        $list = [
            ['shop_id' => '1', 'shop_name' => 'name1', 'dummy' => null, 'shop_deleted_flag' => '0'],
            ['shop_id' => '2', 'shop_name' => 'name2', 'dummy' => null, 'shop_deleted_flag' => '1'],
            ['shop_id' => '3', 'shop_name' => 'name3', 'dummy' => null, 'shop_deleted_flag' => '0'],
            ['shop_id' => '4', 'shop_name' => 'name4', 'dummy' => null, 'shop_deleted_flag' => '0'],
        ];
        $is_key = 'shop_id';
        $name_key = 'shop_name';
        $enabled = true;
        $disabled_flags = ['shop_deleted_flag' => '1'];
        $result = [
            '1' => new Domain_Value_Master('1', 'name1', false, ['dummy' => null, 'shop_deleted_flag' => '0']),
            '3' => new Domain_Value_Master('3', 'name3', false, ['dummy' => null, 'shop_deleted_flag' => '0']),
            '4' => new Domain_Value_Master('4', 'name4', false, ['dummy' => null, 'shop_deleted_flag' => '0']),
        ];

        $method = $this->getMethod(Utility_Master::class, 'get_data_formating');
        $this->assertEquals($result, $method->invoke(new Utility_Master(self::DUMMY_COMPANY_ID1, self::DUMMY_USER_ID1), $list, $is_key, $name_key, $enabled, $disabled_flags));
    }

    public function test_get_キャッシュがある場合キャッシュを取得すること() {
        $stub = $this->getMockBuilder(Utility_Master::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['get_shop'])
            ->getMock();
        $result = [
            '1' => ['name' => 'name1', 'disabled' => false],
            '3' => ['name' => 'name3', 'disabled' => true],
            '4' => ['name' => 'name4', 'disabled' => false],
        ];

        $stub->expects($this->once())
            ->method('get_shop')
            ->will($this->returnValue($result));
        $stub->get(Utility_Master::MASTER_NAME_SHOP, true);

        // get_shopが呼ばれないこと
        $stub = $this->getMockBuilder(Utility_Master::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['get_shop'])
            ->getMock();
        $stub->expects($this->exactly(0))
            ->method('get_shop');
        $this->assertEquals($result, $stub->get(Utility_Master::MASTER_NAME_SHOP, true));
    }

    public function test_get_キャッシュがない場合キャッシュを取得しないこと() {
        $stub = $this->getMockBuilder(Utility_Master::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['get_shop'])
            ->getMock();
        $result = [
            '1' => ['name' => 'name1', 'disabled' => false],
            '2' => ['name' => 'name2', 'disabled' => true],
            '3' => ['name' => 'name3', 'disabled' => false],
        ];

        $stub->expects($this->once())
            ->method('get_shop')
            ->will($this->returnValue($result));
        $stub->get(Utility_Master::MASTER_NAME_SHOP, true);

        // get_confirmが呼ばれること
        $stub = $this->getMockBuilder(Utility_Master::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['get_confirm'])
            ->getMock();
        $result = [
            '4' => ['name' => 'name4', 'disabled' => false],
            '5' => ['name' => 'name5', 'disabled' => true],
            '6' => ['name' => 'name6', 'disabled' => false],
        ];
        $stub->expects($this->once())
            ->method('get_confirm')
            ->will($this->returnValue($result));
        $this->assertEquals($result, $stub->get(Utility_Master::MASTER_NAME_CONFIRM, true));
    }

    public function test_get_forwarding_agent_キャッシュがある場合キャッシュを取得すること() {

        $api_response = [
            ["forwarding_agent_id" => "1", "forwarding_agent_type" => "binsyu_kbn", "forwarding_agent_type_id" => "1", "forwarding_agent_type_name" => "name1", "forwarding_agent_display_order" => "5", "forwarding_agent_deleted_flag" => "0"],
            ["forwarding_agent_id" => "1", "forwarding_agent_type" => "binsyu_kbn", "forwarding_agent_type_id" => "2", "forwarding_agent_type_name" => "name2", "forwarding_agent_display_order" => "1", "forwarding_agent_deleted_flag" => "0"],
            ["forwarding_agent_id" => "1", "forwarding_agent_type" => "binsyu_kbn", "forwarding_agent_type_id" => "3", "forwarding_agent_type_name" => "name3", "forwarding_agent_display_order" => "3", "forwarding_agent_deleted_flag" => "1"],
            ["forwarding_agent_id" => "1", "forwarding_agent_type" => "eigyosyo_dome_kbn", "forwarding_agent_type_id" => "4", "forwarding_agent_type_name" => "name4", "forwarding_agent_display_order" => "2", "forwarding_agent_deleted_flag" => "0"],
            ["forwarding_agent_id" => "2", "forwarding_agent_type" => "binsyu_kbn", "forwarding_agent_type_id" => "5", "forwarding_agent_type_name" => "name5", "forwarding_agent_display_order" => "4", "forwarding_agent_deleted_flag" => "1"],
        ];
        $result = [
            Utility_Masterdataprovider::get_forwarding_agent_unique_key($api_response[1]) => new Domain_Value_Master('2', 'name2', false, ["forwarding_agent_id" => "1", "forwarding_agent_type" => "binsyu_kbn", "forwarding_agent_display_order" => "1", "forwarding_agent_deleted_flag" => "0"]),
            Utility_Masterdataprovider::get_forwarding_agent_unique_key($api_response[0]) => new Domain_Value_Master('1', 'name1', false, ["forwarding_agent_id" => "1", "forwarding_agent_type" => "binsyu_kbn", "forwarding_agent_display_order" => "5", "forwarding_agent_deleted_flag" => "0"]),
        ];

        // モックの作成
        $stub = $this->getMockBuilder(Utility_Master::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['get_master', 'get_data_formating'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_master')
            ->will($this->returnValue($api_response));
        $stub->get_forwarding_agent(true, 1, 'forwarding_agent_binsyu');

        // get_masterが呼ばれないこと
        $stub = $this->getMockBuilder(Utility_Master::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['get_master'])
            ->getMock();
        $stub->expects($this->exactly(0))
            ->method('get_master');
        $this->assertEquals($result, $stub->get_forwarding_agent(true, 1, 'forwarding_agent_binsyu'));
    }

    public function test_get_forwarding_agent_キャッシュがない場合キャッシュを取得しないこと() {

        $api_response = [
            ["forwarding_agent_id" => "1", "forwarding_agent_type" => "binsyu_kbn", "forwarding_agent_type_id" => "1", "forwarding_agent_type_name" => "name1", "forwarding_agent_display_order" => "5", "forwarding_agent_deleted_flag" => "0"],
            ["forwarding_agent_id" => "1", "forwarding_agent_type" => "binsyu_kbn", "forwarding_agent_type_id" => "2", "forwarding_agent_type_name" => "name2", "forwarding_agent_display_order" => "1", "forwarding_agent_deleted_flag" => "0"],
            ["forwarding_agent_id" => "1", "forwarding_agent_type" => "binsyu_kbn", "forwarding_agent_type_id" => "3", "forwarding_agent_type_name" => "name3", "forwarding_agent_display_order" => "3", "forwarding_agent_deleted_flag" => "1"],
            ["forwarding_agent_id" => "1", "forwarding_agent_type" => "eigyosyo_dome_kbn", "forwarding_agent_type_id" => "4", "forwarding_agent_type_name" => "name4", "forwarding_agent_display_order" => "2", "forwarding_agent_deleted_flag" => "0"],
            ["forwarding_agent_id" => "2", "forwarding_agent_type" => "binsyu_kbn", "forwarding_agent_type_id" => "5", "forwarding_agent_type_name" => "name5", "forwarding_agent_display_order" => "4", "forwarding_agent_deleted_flag" => "1"],
        ];
        $result = [
            Utility_Masterdataprovider::get_forwarding_agent_unique_key($api_response[3]) => new Domain_Value_Master('4', 'name4', false, ["forwarding_agent_id" => "1", "forwarding_agent_type" => "eigyosyo_dome_kbn", "forwarding_agent_display_order" => "2", "forwarding_agent_deleted_flag" => "0"]),
        ];

        // モックの作成
        $stub = $this->getMockBuilder(Utility_Master::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['get_master', 'get_data_formating'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_master')
            ->will($this->returnValue($api_response));
        $stub->get_forwarding_agent(true, 1, 'forwarding_agent_binsyu');

        // get_masterが呼ばれること
        $stub = $this->getMockBuilder(Utility_Master::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['get_master'])
            ->getMock();
        $stub->expects($this->exactly(1))
            ->method('get_master')
            ->will($this->returnValue($api_response));
        $this->assertEquals($result, $stub->get_forwarding_agent(true, 1, 'forwarding_agent_eigyosyo_dome'));
    }
}