<?php
class Test_Domain_Model_Sqs extends Testbase {
    public function test___construct_configを引数で渡したらその情報でsqs_clientが作られること(){
        $config = [
            'credentials' => ['key' => 'test_key', 'secret' => 'test_secret'],
            'region' => 'test_region',
            'endpoint' => 'http://localhost_test/',
            'version' => '2012-11-05'
        ];

        $reflection = new ReflectionClass('Domain_Model_Sqs');
        $property = $reflection->getProperty('_sqs_client');
        $property->setAccessible(true);
        $sqs_client = $property->getValue(new Domain_Model_Sqs($config));

        $this->assertEquals('test_key',    $sqs_client->getCredentials()->wait()->getAccessKeyId());
        $this->assertEquals('test_secret', $sqs_client->getCredentials()->wait()->getSecretKey());
        $this->assertEquals('test_region', $sqs_client->getRegion());
        $this->assertEquals('http://localhost_test/', (string)$sqs_client->getEndpoint());
        $this->assertEquals('2012-11-05', $sqs_client->getApi()->getApiVersion());
    }

    public function test___construct_configを渡さなければ各環境のconfigファイルの情報を参照したsqs_clientが作られること(){
        $reflection = new ReflectionClass('Domain_Model_Sqs');
        $property = $reflection->getProperty('_sqs_client');
        $property->setAccessible(true);
        $sqs_client = $property->getValue(new Domain_Model_Sqs());

        $this->assertEquals('test_config_key',    $sqs_client->getCredentials()->wait()->getAccessKeyId());
        $this->assertEquals('test_config_secret', $sqs_client->getCredentials()->wait()->getSecretKey());
        $this->assertEquals('test_config_region', $sqs_client->getRegion());
        $this->assertEquals('http://localhost:9324/', (string)$sqs_client->getEndpoint());
        $this->assertEquals('2012-11-05', $sqs_client->getApi()->getApiVersion());
    }

    public function test_enque_内部的にsendMessageを呼んでいること(){
        $queue_url = 'dummy_url';
        $message_body = 'dummy_message_body';
        $message_group_id = 'dummy_message_group_id';
        $queue_name = Config::get('sqs.que_name');
        $expect = new Aws\Result();

        // NOTE: Aws\Sqs\SqsClientのモックを作ろうとすると継承とクラス名依存の関係でエラーとなってしまうため存在しないダミークラスのモックを使うことにする
        $stub = $this->getMockBuilder(Dummy_SQS::class)
            ->setMethods(['sendMessage', 'getQueueUrl'])
            ->getMock();
        $stub->expects($this->once())
            ->method('sendMessage')
            ->with($this->equalTo([
                'QueueUrl'       => $queue_url,
                'MessageBody'    => $message_body,
                'MessageGroupId' => $message_group_id,
            ]))
            ->will($this->returnValue($expect));
        $stub->expects($this->once())
            ->method('getQueueUrl')
            ->with($this->equalTo(['QueueName'=>$queue_name]))
            ->will($this->returnValue(['QueueUrl' => $queue_url]));

        // _sqs_clientのプロパティを上記で作成したスタブオブジェクトに置き換える
        $domain_model_sqs = new Domain_Model_Sqs();
        $class = new ReflectionClass($domain_model_sqs);
        $property = $class->getProperty('_sqs_client');
        $property->setAccessible(true);
        $property->setValue($domain_model_sqs, $stub);
        $result = $domain_model_sqs->enque($message_body, $message_group_id);

        $this->assertEquals($expect, $result);
    }

    /**
     * @expectedException Aws\Exception\AwsException
     */
    public function test_enque_キューの登録処理に失敗したら例外が発生すること(){
        // elasticmqを起動していない状態で実行すると接続に失敗の例外が起きる
        // elasticmqを起動している状態で実行すると存在しないエンドポイントの例外が起きる
        $domain_model_sqs = new Domain_Model_Sqs(['endpoint' => 'http://no_exist/']);
        $result = $domain_model_sqs->enque('test_body', 'test_group_id');
    }

    public function test_deque_内部的にreceiveMessageを呼んでいること(){
        $queue_url = 'dummy_url';
        $queue_name = Config::get('sqs.que_name');
        $expect = new Aws\Result();

        // NOTE: Aws\Sqs\SqsClientのモックを作ろうとすると継承とクラス名依存の関係でエラーとなってしまうため存在しないダミークラスのモックを使うことにする
        $stub = $this->getMockBuilder(Dummy_SQS::class)
            ->setMethods(['receiveMessage', 'getQueueUrl'])
            ->getMock();
        $stub->expects($this->once())
            ->method('receiveMessage')
            ->with($this->equalTo([
                'QueueUrl'              => $queue_url,
                'AttributeNames'        => ['All'],
                'MaxNumberOfMessages'   => Domain_Model_Sqs::MAX_NUMBER_OF_MESSAGES,
                'MessageAttributeNames' => ['All'],
            ]))
            ->will($this->returnValue($expect));
        $stub->expects($this->once())
            ->method('getQueueUrl')
            ->with($this->equalTo(['QueueName'=>$queue_name]))
            ->will($this->returnValue(['QueueUrl' => $queue_url]));

        // _sqs_clientのプロパティを上記で作成したスタブオブジェクトに置き換える
        $domain_model_sqs = new Domain_Model_Sqs();
        $class = new ReflectionClass($domain_model_sqs);
        $property = $class->getProperty('_sqs_client');
        $property->setAccessible(true);
        $property->setValue($domain_model_sqs, $stub);
        $result = $domain_model_sqs->deque();

        $this->assertEquals($expect, $result);
    }

    /**
     * @expectedException Aws\Exception\AwsException
     */
    public function test_deque_キューの取得処理に失敗したら例外が発生すること(){
        // elasticmqを起動していない状態で実行すると接続に失敗の例外が起きる
        // elasticmqを起動している状態で実行すると存在しないエンドポイントの例外が起きる
        $domain_model_sqs = new Domain_Model_Sqs(['endpoint' => 'http://no_exist/']);
        $result = $domain_model_sqs->deque();
    }

    public function test_delete_message_内部的にdeleteMessageを呼んでいること(){
        $queue_url = 'dummy_url';
        $receipt_handle = 'dummy_receipt_handle';
        $queue_name = Config::get('sqs.que_name');
        $expect = new Aws\Result();

        // NOTE: Aws\Sqs\SqsClientのモックを作ろうとすると継承とクラス名依存の関係でエラーとなってしまうため存在しないダミークラスのモックを使うことにする
        $stub = $this->getMockBuilder(Dummy_SQS::class)
            ->setMethods(['deleteMessage', 'getQueueUrl'])
            ->getMock();
        $stub->expects($this->once())
            ->method('deleteMessage')
            ->with($this->equalTo([
                'QueueUrl'      => $queue_url,
                'ReceiptHandle' => $receipt_handle,
            ]))
            ->will($this->returnValue($expect));
        $stub->expects($this->once())
            ->method('getQueueUrl')
            ->with($this->equalTo(['QueueName'=>$queue_name]))
            ->will($this->returnValue(['QueueUrl' => $queue_url]));

        // _sqs_clientのプロパティを上記で作成したスタブオブジェクトに置き換える
        $domain_model_sqs = new Domain_Model_Sqs();
        $class = new ReflectionClass($domain_model_sqs);
        $property = $class->getProperty('_sqs_client');
        $property->setAccessible(true);
        $property->setValue($domain_model_sqs, $stub);
        $result = $domain_model_sqs->delete_message($receipt_handle);

        $this->assertEquals($expect, $result);
    }

    /**
     * @expectedException Aws\Exception\AwsException
     */
    public function test_delete_message_キューの削除処理に失敗したら例外が発生すること(){
        // elasticmqを起動していない状態で実行すると接続に失敗の例外が起きる
        // elasticmqを起動している状態で実行すると存在しないエンドポイントの例外が起きる
        $domain_model_sqs = new Domain_Model_Sqs(['endpoint' => 'http://no_exist/']);
        $result = $domain_model_sqs->delete_message('no_exist');
    }

    public function test_change_message_visibility_内部的にchangeMessageVisibilityを呼んでいること(){
        $queue_url = 'dummy_url';
        $receipt_handle = 'dummy_receipt_handle';
        $queue_name = Config::get('sqs.que_name');
        $expect = new Aws\Result();

        // NOTE: Aws\Sqs\SqsClientのモックを作ろうとすると継承とクラス名依存の関係でエラーとなってしまうため存在しないダミークラスのモックを使うことにする
        $stub = $this->getMockBuilder(Dummy_SQS::class)
            ->setMethods(['changeMessageVisibility', 'getQueueUrl'])
            ->getMock();
        $stub->expects($this->once())
            ->method('changeMessageVisibility')
            ->with($this->equalTo([
                'QueueUrl'          => $queue_url,
                'ReceiptHandle'     => $receipt_handle,
                'VisibilityTimeout' => Domain_Model_Sqs::LONG_VISIBILITY_TIMEOUT,
            ]))
            ->will($this->returnValue($expect));
        $stub->expects($this->once())
            ->method('getQueueUrl')
            ->with($this->equalTo(['QueueName'=>$queue_name]))
            ->will($this->returnValue(['QueueUrl' => $queue_url]));

        // _sqs_clientのプロパティを上記で作成したスタブオブジェクトに置き換える
        $domain_model_sqs = new Domain_Model_Sqs();
        $class = new ReflectionClass($domain_model_sqs);
        $property = $class->getProperty('_sqs_client');
        $property->setAccessible(true);
        $property->setValue($domain_model_sqs, $stub);
        $result = $domain_model_sqs->change_message_visibility($receipt_handle);

        $this->assertEquals($expect, $result);
    }

    /**
     * @expectedException Aws\Exception\AwsException
     */
    public function test_change_message_visibility_キューの可視性タイムアウトを変更する処理に失敗したら例外が発生すること(){
        // elasticmqを起動していない状態で実行すると接続に失敗の例外が起きる
        // elasticmqを起動している状態で実行すると存在しないエンドポイントの例外が起きる
        $domain_model_sqs = new Domain_Model_Sqs(['endpoint' => 'http://no_exist/']);
        $result = $domain_model_sqs->change_message_visibility('no_exist');
    }

    /********** privateメソッドのテスト *********/

    public function test__get_queue_url_内部的にgetQueueUrlを呼んでいること(){
        $queue_url = 'dummy_url';
        $queue_name = Config::get('sqs.que_name');
        $receipt_handle = 'dummy_receipt_handle';

        // NOTE: Aws\Sqs\SqsClientのモックを作ろうとすると継承とクラス名依存の関係でエラーとなってしまうため存在しないダミークラスのモックを使うことにする
        $stub = $this->getMockBuilder(Dummy_SQS::class)
            ->setMethods(['getQueueUrl'])
            ->getMock();
        $stub->expects($this->once())
            ->method('getQueueUrl')
            ->with($this->equalTo(['QueueName'=>$queue_name]))
            ->will($this->returnValue(['QueueUrl' => $queue_url]));

        // _sqs_clientのプロパティを上記で作成したスタブオブジェクトに置き換える
        $domain_model_sqs = new Domain_Model_Sqs();
        $class = new ReflectionClass($domain_model_sqs);
        $property = $class->getProperty('_sqs_client');
        $property->setAccessible(true);
        $property->setValue($domain_model_sqs, $stub);

        $method = $this->getMethod(get_class($domain_model_sqs), '_get_queue_url');
        $result = $method->invoke($domain_model_sqs);
        $this->assertEquals($queue_url, $result);
    }
}