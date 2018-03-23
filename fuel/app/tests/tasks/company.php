<?php
require_once 'fuel/app/tasks/company.php';

class Test_Fuel_Tasks_Company extends Testbase
{
    public function test_disable_canceled_company_解約企業がいた場合、その企業のstoped_atに現在の日時を入れること(){
        // 利用企業が0なので今登録されているDUMMY_COMPANY_ID1の企業は解約企業となる
        $api_response = [
            'count' => '0',
            'data' => [],
            'result' => 'success',
        ];
        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([null, true])
            ->setMethods(['apiExecuteNoRequiredLogin'])
            ->getMock();
        $ne_api_stub->expects($this->once())
            ->method('apiExecuteNoRequiredLogin')
            ->with($this->equalTo(Client_Neapi::PATH_CONTRACTED_COMPANIES_GET))
            ->will($this->returnValue($api_response));

        // 現在時刻を固定
        $now = date("Y-m-d H:i:s");

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        // 現在時刻を固定値を返すようにする
        $stub = $this->getMockBuilder(Fuel\Tasks\Company::class)
            ->setMethods(['get_client_neapi', 'get_date_now'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->will($this->returnValue($ne_api_stub));
        $stub->expects($this->once())
            ->method('get_date_now')
            ->will($this->returnValue($now));

        $stub->disable_canceled_company();
        $company = Model_Company::find(self::DUMMY_COMPANY_ID1);
        $this->assertEquals($now, $company->stoped_at);
    }

    public function test_disable_canceled_company_DUMMY_COMPANY_ID1が解約されてない場合、stoped_atはnullのままであること(){
        $api_response = [
            'count' => '1',
            'data' => [
                ['company_id' => 'dummy_main_function_id', 'company_ne_id' => 'dummy_company_ne_id'],
            ],
            'result' => 'success',
        ];
        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([null, true])
            ->setMethods(['apiExecuteNoRequiredLogin'])
            ->getMock();
        $ne_api_stub->expects($this->once())
            ->method('apiExecuteNoRequiredLogin')
            ->with($this->equalTo(Client_Neapi::PATH_CONTRACTED_COMPANIES_GET))
            ->will($this->returnValue($api_response));

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        // 現在時刻を固定値を返すようにする
        $stub = $this->getMockBuilder(Fuel\Tasks\Company::class)
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->will($this->returnValue($ne_api_stub));

        $stub->disable_canceled_company();
        $company = Model_Company::find(self::DUMMY_COMPANY_ID1);
        $this->assertEquals(null, $company->stoped_at);
    }

    public function test__get_canceled_companies_解約した企業がいた場合は解約企業を返すこと(){
        // 利用企業が0なので今登録されているDUMMY_COMPANY_ID1の企業は解約企業となる
        $api_response = [
            'count' => '0',
            'data' => [],
            'result' => 'success',
        ];
        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([null, true])
            ->setMethods(['apiExecuteNoRequiredLogin'])
            ->getMock();
        $ne_api_stub->expects($this->once())
            ->method('apiExecuteNoRequiredLogin')
            ->with($this->equalTo(Client_Neapi::PATH_CONTRACTED_COMPANIES_GET))
            ->will($this->returnValue($api_response));

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        $stub = $this->getMockBuilder(Fuel\Tasks\Company::class)
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->will($this->returnValue($ne_api_stub));

        $method = $this->getMethod(get_class($stub), '_get_canceled_companies');
        $method->setAccessible(true);
        $result = $method->invoke($stub);
        $company = Model_Company::find(self::DUMMY_COMPANY_ID1);
        $expect[$company->id] = $company;
        $this->assertEquals($expect, $result);
    }

    public function test__get_canceled_companies_解約した企業がいなかった場合は空配列を返すこと(){
        $api_response = [
            'count' => '1',
            'data' => [
                ['company_id' => 'dummy_main_function_id', 'company_ne_id' => 'dummy_company_ne_id'],
            ],
            'result' => 'success',
        ];
        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([null, true])
            ->setMethods(['apiExecuteNoRequiredLogin'])
            ->getMock();
        $ne_api_stub->expects($this->once())
            ->method('apiExecuteNoRequiredLogin')
            ->with($this->equalTo(Client_Neapi::PATH_CONTRACTED_COMPANIES_GET))
            ->will($this->returnValue($api_response));

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        $stub = $this->getMockBuilder(Fuel\Tasks\Company::class)
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->will($this->returnValue($ne_api_stub));

        $method = $this->getMethod(get_class($stub), '_get_canceled_companies');
        $method->setAccessible(true);
        $result = $method->invoke($stub);
        $this->assertEquals([], $result);
    }

    public function test__get_contracted_main_function_ids_アプリを利用している企業のcompany_idの配列を返すこと(){
        $api_response = [
            'count' => '2',
            'data' => [
                ['company_id' => 'AAA', 'company_ne_id' => 'BBB'],
                ['company_id' => 'CCC', 'company_ne_id' => 'DDD'],
            ],
            'result' => 'success',
        ];
        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([null, true])
            ->setMethods(['apiExecuteNoRequiredLogin'])
            ->getMock();
        $ne_api_stub->expects($this->once())
            ->method('apiExecuteNoRequiredLogin')
            ->with($this->equalTo(Client_Neapi::PATH_CONTRACTED_COMPANIES_GET))
            ->will($this->returnValue($api_response));

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        $stub = $this->getMockBuilder(Fuel\Tasks\Company::class)
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->will($this->returnValue($ne_api_stub));

        $method = $this->getMethod(get_class($stub), '_get_contracted_main_function_ids');
        $method->setAccessible(true);
        $result = $method->invoke($stub);
        $this->assertEquals(['AAA', 'CCC'], $result);
    }

    public function test__get_contracted_main_function_ids_アプリを利用している企業がいない場合は空配列を返すこと(){
        $api_response = [
            'count' => '0',
            'data' => [],
            'result' => 'success',
        ];
        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([null, true])
            ->setMethods(['apiExecuteNoRequiredLogin'])
            ->getMock();
        $ne_api_stub->expects($this->once())
            ->method('apiExecuteNoRequiredLogin')
            ->with($this->equalTo(Client_Neapi::PATH_CONTRACTED_COMPANIES_GET))
            ->will($this->returnValue($api_response));

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        $stub = $this->getMockBuilder(Fuel\Tasks\Company::class)
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->will($this->returnValue($ne_api_stub));

        $method = $this->getMethod(get_class($stub), '_get_contracted_main_function_ids');
        $method->setAccessible(true);
        $result = $method->invoke($stub);
        $this->assertEquals([], $result);
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function test__get_contracted_main_function_ids_アプリ利用企業一覧の取得に失敗した場合例外が発生すること(){
        $api_response = [
            'result' => \Client_Neapi::RESULT_ERROR,
            'code' => \Client_Neapi::ERROR_CODE_EXCEPTION,
            'message' => '原因不明のエラー',
        ];
        // Client_Neapiのスタブを作成する
        $ne_api_stub = $this->getMockBuilder(Client_Neapi::class)
            ->setConstructorArgs([null, true])
            ->setMethods(['apiExecuteNoRequiredLogin'])
            ->getMock();
        $ne_api_stub->expects($this->once())
            ->method('apiExecuteNoRequiredLogin')
            ->with($this->equalTo(Client_Neapi::PATH_CONTRACTED_COMPANIES_GET))
            ->will($this->returnValue($api_response));

        // get_client_neapiの戻り値を上記で作成したスタブオブジェクトに置き換える
        $stub = $this->getMockBuilder(Fuel\Tasks\Company::class)
            ->setMethods(['get_client_neapi'])
            ->getMock();
        $stub->expects($this->once())
            ->method('get_client_neapi')
            ->will($this->returnValue($ne_api_stub));

        $method = $this->getMethod(get_class($stub), '_get_contracted_main_function_ids');
        $method->setAccessible(true);
        $method->invoke($stub);
    }
}