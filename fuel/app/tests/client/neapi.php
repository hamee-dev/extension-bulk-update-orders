<?php
class Test_Client_Neapi extends Testbase {
    public function test_get_shop_ids_店舗の検索に成功した場合は店舗idを配列で返すこと(){
        $path = Client_Neapi::PATH_SHOP_SEARCH;
        $shop_search_params = ['fields' => 'shop_id'];
        $api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'count' => '4',
            'data' => [
                ['shop_id' => '2'],
                ['shop_id' => '4'],
                ['shop_id' => '6'],
                ['shop_id' => '8'],
            ],
            'access_token' => 'dummy_access_token',
            'access_token_end_date' => 'dummy_access_token_end_date',
            'refresh_token' => 'dummy_refresh_token',
            'refresh_token_end_date' => 'dummy_refresh_token_end_date',
        ];

        $stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['apiExecute'])
            ->getMock();
        $stub->expects($this->once())
            ->method('apiExecute')
            ->with($this->equalTo($path), $this->equalTo($shop_search_params))
            ->will($this->returnValue($api_response));

        $expect = ['2', '4', '6', '8'];
        $result = $stub->get_shop_ids();
        $this->assertEquals($expect, $result);
    }

    public function test_get_shop_ids_引数にmall_codeでの検索条件を指定した場合API実行時のshop_mall_id_eqにそのmall_codeを指定していること(){
        $path = Client_Neapi::PATH_SHOP_SEARCH;
        $mall_code = '9';
        $shop_search_params = ['fields' => 'shop_id', 'shop_mall_id-eq' => $mall_code];
        $api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'count' => '4',
            'data' => [
                ['shop_id' => '2'],
                ['shop_id' => '4'],
                ['shop_id' => '6'],
                ['shop_id' => '8'],
            ],
            'access_token' => 'dummy_access_token',
            'access_token_end_date' => 'dummy_access_token_end_date',
            'refresh_token' => 'dummy_refresh_token',
            'refresh_token_end_date' => 'dummy_refresh_token_end_date',
        ];

        $stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['apiExecute'])
            ->getMock();
        $stub->expects($this->once())
            ->method('apiExecute')
            ->with($this->equalTo($path), $this->equalTo($shop_search_params))
            ->will($this->returnValue($api_response));

        $expect = ['2', '4', '6', '8'];
        $result = $stub->get_shop_ids(['shop_mall_id-eq' => $mall_code]);
        $this->assertEquals($expect, $result);
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function test_get_shop_ids_店舗の検索に失敗した場合はUnexpectedValueExceptionの例外が発生すること(){
        $path = Client_Neapi::PATH_SHOP_SEARCH;
        $shop_search_params = ['fields' => 'shop_id'];
        $api_response = [
            'result' => Client_Neapi::RESULT_ERROR,
            'code' => Client_Neapi::ERROR_CODE_EXCEPTION,
            'message' => '原因不明のエラーが発生しました',
            'access_token' => 'dummy_access_token',
            'access_token_end_date' => 'dummy_access_token_end_date',
            'refresh_token' => 'dummy_refresh_token',
            'refresh_token_end_date' => 'dummy_refresh_token_end_date',
        ];

        $stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1])
            ->setMethods(['apiExecute'])
            ->getMock();
        $stub->expects($this->once())
            ->method('apiExecute')
            ->with($this->equalTo($path), $this->equalTo($shop_search_params))
            ->will($this->returnValue($api_response));
        $result = $stub->get_shop_ids();
    }

    public function test_getApiServerHost_configのホスト名が取得できること() {
        $neapi = new Client_Neapi(Test_Client_Neapi::DUMMY_USER_ID1);
        $result = self::invoke_method($neapi, 'getApiServerHost');
        $this->assertEquals(Config::get('host.api_server'), $result);
    }

    public function test_getNeServerHost_configのホスト名が取得できること() {
        $neapi = new Client_Neapi(Test_Client_Neapi::DUMMY_USER_ID1);
        $result = self::invoke_method($neapi, 'getNeServerHost');
        $this->assertEquals(Config::get('host.ne_server'), $result);
    }

    /**
     * @dataProvider Client_Neapidataprovider::data_provider_apiExecute
     */
    public function test_apiExecute_正常にレスポンスを返すこと(bool $is_retry, array $api_response_list, int $count) {
        $stub = $this->getMockBuilder(Client_Neapimock::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1, $is_retry, 0.1])
            ->setMethods(['post', 'set_access_token', 'update_user_access_token'])
            ->getMock();

        $path = '/test';
        $request_path = \Config::get('host.api_server') . $path;
        $api_params = ['data1' => 'value1', 'data2' => 'value2'];
        $request_api_params = $api_params;
        $request_api_params['access_token'] = 'dummy_access_token';
        $request_api_params['refresh_token'] = 'dummy_refresh_token';
        $stub->_access_token = 'dummy_access_token';
        $stub->_refresh_token = 'dummy_refresh_token';

        $will_return_on_consecutive_calls = [];
        $with_consecutive = [];
        foreach ($api_response_list as $api_response) {
            $will_return_on_consecutive_calls[] = $api_response;
            $with_consecutive[] = $this->equalTo($api_response);
        }
        $invocation_mocker = $stub->expects($this->exactly($count));
        $invocation_mocker->method('post');
        $invocation_mocker->with($this->equalTo($request_path), $this->equalTo($request_api_params));
        call_user_func_array([$invocation_mocker, 'willReturnOnConsecutiveCalls'], $will_return_on_consecutive_calls);

        $stub->expects($this->exactly($count - 1))
            ->method('set_access_token');

        $invocation_mocker = $stub->expects($this->exactly($count));
        $invocation_mocker->method('update_user_access_token');
        call_user_func_array([$invocation_mocker, 'withConsecutive'], $with_consecutive);

        $redirect_uri = '/redirect_uri';
        $this->assertEquals(end($api_response_list), $stub->apiExecute($path, $api_params, $redirect_uri));
        $this->assertEquals($redirect_uri, $stub->_redirect_uri);
    }

    /**
     * @dataProvider Client_Neapidataprovider::data_provider_apiExecute_token
     */
    public function test_apiExecute_トークンが更新されること(array $request_api_params_list, array $api_response_list, bool $is_update, array $user_token_data, int $count) {
        $stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1, true, 0.1])
            ->setMethods(['post'])
            ->getMock();
        $stub->_access_token = 'dummy_access_token';
        $stub->_refresh_token = 'dummy_refresh_token';

        $path = '/test';
        $request_path = \Config::get('host.api_server').$path;

        $post_with_list = [];
        foreach ($request_api_params_list as $request_api_params) {
            $post_with_list[] = [$this->equalTo($request_path), $this->equalTo($request_api_params)];
        }

        $invocation_mocker = $stub->expects($this->exactly($count));
        $invocation_mocker->method('post');
        call_user_func_array([$invocation_mocker, 'withConsecutive'], $post_with_list);
        call_user_func_array([$invocation_mocker, 'willReturnOnConsecutiveCalls'], $api_response_list);

        $before_user_data = Model_User::findOne(['id' => self::DUMMY_USER_ID1]);
        $this->assertEquals(end($api_response_list), $stub->apiExecute($path));
        $after_user_data = Model_User::findOne(['id' => self::DUMMY_USER_ID1]);

        if ($is_update) {
            // 更新された場合
            $this->assertEquals($user_token_data['access_token'], $after_user_data->access_token);
            $this->assertEquals($user_token_data['access_token_end_date'], $after_user_data->access_token_end_date);
            $this->assertEquals($user_token_data['refresh_token'], $after_user_data->refresh_token);
            $this->assertEquals($user_token_data['refresh_token_end_date'], $after_user_data->refresh_token_end_date);
        }else{
            // 更新されていない場合
            $this->assertEquals($before_user_data->access_token, $after_user_data->access_token);
            $this->assertEquals($before_user_data->access_token_end_date, $after_user_data->access_token_end_date);
            $this->assertEquals($before_user_data->refresh_token, $after_user_data->refresh_token);
            $this->assertEquals($before_user_data->refresh_token_end_date, $after_user_data->refresh_token_end_date);
        }
    }

    /**
     * @dataProvider Client_Neapidataprovider::data_provider_apiExecuteNoRequiredLogin
     */
    public function test_apiExecuteNoRequiredLogin_正常にレスポンスを返すこと(bool $is_retry, array $api_params, array $api_response_list, int $count) {
        $stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([self::DUMMY_USER_ID1, $is_retry, 0.1])
            ->setMethods(['post'])
            ->getMock();

        $path = '/test';
        $request_path = \Config::get('host.api_server').$path;
        $request_api_params = $api_params;
        $request_api_params['client_id'] = \Config::get('nextengine.client_id');
        $request_api_params['client_secret'] = \Config::get('nextengine.client_secret');

        $invocation_mocker = $stub->expects($this->exactly($count));
        $invocation_mocker->method('post');
        $invocation_mocker->with($this->equalTo($request_path), $this->equalTo($request_api_params));
        call_user_func_array([$invocation_mocker, 'willReturnOnConsecutiveCalls'], $api_response_list);

        $this->assertEquals(end($api_response_list), $stub->apiExecuteNoRequiredLogin($path, $api_params));
    }

    /**
     * @dataProvider Client_Neapidataprovider::data_provider_is_execute_retry
     */
    public function test_is_execute_retry_リトライ可能な場合はtrueを返すこと(bool $is_retry, array $response, bool $is_execute_retry) {
        $neapi = new Client_Neapi(Test_Client_Neapi::DUMMY_USER_ID1, $is_retry);
        $result = self::invoke_method($neapi, 'is_execute_retry', [$response]);
        $this->assertEquals($is_execute_retry, $result);

    }

    /**
     * @dataProvider Client_Neapidataprovider::data_provider_update_user_access_token
     */
    public function test_update_user_access_token_APIレスポンスのアクセストークンの最終更新日が新しくなっていた場合はDBのアクセストークンとリフレッシュトークンが更新されること(array $response, bool $is_update) {
        $user_id = self::DUMMY_USER_ID1;
        $before_user_data = Model_User::findOne(['id' => $user_id]);
        $neapi = new Client_Neapi($user_id);
        $neapi->_access_token = $response['access_token'];
        $neapi->_refresh_token = $response['refresh_token'];
        self::invoke_method($neapi, 'update_user_access_token', [$response]);
        $after_user_data = Model_User::findOne(['id' => $user_id]);

        if ($is_update) {
            // 更新された場合
            $this->assertEquals($response['access_token'], $after_user_data->access_token);
            $this->assertEquals($response['access_token_end_date'], $after_user_data->access_token_end_date);
            $this->assertEquals($response['refresh_token'], $after_user_data->refresh_token);
            $this->assertEquals($response['refresh_token_end_date'], $after_user_data->refresh_token_end_date);
        }else{
            // 更新されていない場合
            $this->assertEquals($before_user_data->access_token, $after_user_data->access_token);
            $this->assertEquals($before_user_data->access_token_end_date, $after_user_data->access_token_end_date);
            $this->assertEquals($before_user_data->refresh_token, $after_user_data->refresh_token);
            $this->assertEquals($before_user_data->refresh_token_end_date, $after_user_data->refresh_token_end_date);
        }
    }

    public function test_update_user_access_token_PIレスポンスのアクセストークンの最終更新日が同じだった場合アクセストークンとリフレッシュトークンは更新されないこと() {
        $user_id = self::DUMMY_USER_ID1;
        $before_user_data = Model_User::findOne(['id' => $user_id]);
        $neapi = new Client_Neapi($user_id);
        $response = [
            'access_token' => 'new_access_token',
            'access_token_end_date' => $before_user_data->access_token_end_date,
            'refresh_token' => 'new_refresh_token',
            'refresh_token_end_date' => $before_user_data->refresh_token_end_date
        ];
        $neapi->_access_token = $response['access_token'];
        $neapi->_refresh_token = $response['refresh_token'];
        self::invoke_method($neapi, 'update_user_access_token', [$response]);

        $after_user_data = Model_User::findOne(['id' => $user_id]);
        $this->assertEquals($before_user_data->access_token, $after_user_data->access_token);
        $this->assertEquals($before_user_data->access_token_end_date, $after_user_data->access_token_end_date);
        $this->assertEquals($before_user_data->refresh_token, $after_user_data->refresh_token);
        $this->assertEquals($before_user_data->refresh_token_end_date, $after_user_data->refresh_token_end_date);
    }

    /**
     * @dataProvider Client_Neapidataprovider::data_provider_set_access_token
     */
    public function test_set_access_token_正しくアクセストークンが設定されること(?string $neapi_user_id, ?string $set_access_token_user_id, ?string $access_token, ?string $refresh_token) {
        $neapi = new Client_Neapi($neapi_user_id);
        self::invoke_method($neapi, 'set_access_token', [$set_access_token_user_id]);
        $this->assertEquals($access_token, $neapi->_access_token);
        $this->assertEquals($refresh_token, $neapi->_refresh_token);
    }
}

class Client_Neapimock extends Client_Neapi {
    public $_redirect_uri = null;
}