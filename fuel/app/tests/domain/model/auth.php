<?php
class Test_Domain_Model_Auth extends Testbase {
    public function test__fetch_company_info_企業情報が取得できた場合はその企業情報の連想配列を返すこと(){
        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setMethods(['apiExecute'])
            ->getMock();

        $expect = ['company_name' => 'test_company', 'company_pic_name' => 'test_user'];
        $api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'data' => [$expect],
        ];
        $ne_api_stub->expects($this->once())
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_LOGIN_COMPANY_INFO))
            ->will($this->returnValue($api_response));

        // _client_neapiのプロパティを上記で作成したスタブオブジェクトに置き換える
        $domain_model_auth = new Domain_Model_Auth();
        $class = new ReflectionClass($domain_model_auth);
        $property = $class->getProperty('_client_neapi');
        $property->setAccessible(true);
        $property->setValue($domain_model_auth, $ne_api_stub);

        $method = $this->getMethod(get_class($domain_model_auth), '_fetch_company_info');
        $result = $method->invoke($domain_model_auth);
        $this->assertEquals($expect, $result);
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function test__fetch_company_info_企業情報が取得できなかった場合は例外が発生すること(){
        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setMethods(['apiExecute'])
            ->getMock();

        $api_response = [
            'result' => Client_Neapi::RESULT_ERROR,
            'code' => Client_Neapi::ERROR_CODE_EXCEPTION,
            'message' => '原因不明のエラー',
        ];
        $ne_api_stub->expects($this->once())
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_LOGIN_COMPANY_INFO))
            ->will($this->returnValue($api_response));

        // _client_neapiのプロパティを上記で作成したスタブオブジェクトに置き換える
        $domain_model_auth = new Domain_Model_Auth();
        $class = new ReflectionClass($domain_model_auth);
        $property = $class->getProperty('_client_neapi');
        $property->setAccessible(true);
        $property->setValue($domain_model_auth, $ne_api_stub);

        $method = $this->getMethod(get_class($domain_model_auth), '_fetch_company_info');
        $result = $method->invoke($domain_model_auth);
    }

    public function test__fetch_user_info_ユーザー情報が取得できた場合はそのユーザー情報の連想配列を返すこと(){
        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setMethods(['apiExecute'])
            ->getMock();

        $api_response = [
            'result' => Client_Neapi::RESULT_SUCCESS,
            'data' => [
                ['pic_name' => 'test_user', 'pic_kana' => 'test_user'],
            ],
        ];
        $ne_api_stub->expects($this->once())
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_LOGIN_USER_INFO))
            ->will($this->returnValue($api_response));

        // _client_neapiのプロパティを上記で作成したスタブオブジェクトに置き換える
        $domain_model_auth = new Domain_Model_Auth();
        $class = new ReflectionClass($domain_model_auth);
        $property = $class->getProperty('_client_neapi');
        $property->setAccessible(true);
        $property->setValue($domain_model_auth, $ne_api_stub);

        $method = $this->getMethod(get_class($domain_model_auth), '_fetch_user_info');
        $result = $method->invoke($domain_model_auth);
        $this->assertEquals($api_response, $result);
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function test__fetch_user_info_ユーザー情報が取得できなかった場合は例外が発生すること(){
        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setMethods(['apiExecute'])
            ->getMock();

        $api_response = [
            'result' => Client_Neapi::RESULT_ERROR,
            'code' => Client_Neapi::ERROR_CODE_EXCEPTION,
            'message' => '原因不明のエラー',
        ];
        $ne_api_stub->expects($this->once())
            ->method('apiExecute')
            ->with($this->equalTo(Client_Neapi::PATH_LOGIN_USER_INFO))
            ->will($this->returnValue($api_response));

        // _client_neapiのプロパティを上記で作成したスタブオブジェクトに置き換える
        $domain_model_auth = new Domain_Model_Auth();
        $class = new ReflectionClass($domain_model_auth);
        $property = $class->getProperty('_client_neapi');
        $property->setAccessible(true);
        $property->setValue($domain_model_auth, $ne_api_stub);

        $method = $this->getMethod(get_class($domain_model_auth), '_fetch_user_info');
        $result = $method->invoke($domain_model_auth);
    }
}