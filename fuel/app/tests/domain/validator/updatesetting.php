<?php
/**
 * 同名のforgeを連続で使用するとエラーになってしまうためforgeする際には別名を指定すること
 * @see https://qiita.com/masahikoofjoyto/items/206e6f8d5b0aa7126678
 * OPTIMIZE: もしくはテストケース毎に$_instanceをリセットする
 */
class Test_Domain_Validator_Updatesetting extends Testbase
{
    protected $fetch_init_yaml = false;
    protected $dataset_filenames = ['model/bulkupdatesetting.yml'];

    protected function setUp()
    {
        parent::setUp();

        Closure::bind(function() {
            self::$_instances = [];
        }, $this, \Fuel\Core\Fieldset::class)->__invoke();
    }

    public function test_run_copy_重複したnameがあった場合バリデーションエラーとなること(){
        $name = 'TEST4';
        $result = Domain_Validator_Updatesetting::run_copy(self::DUMMY_COMPANY_ID2, $name);
        $this->assertFalse($result['result']);
        $this->assertEquals(\Lang::get('validation.setting_name', ['label' => '設定名']), $result['messages']['name']);
    }

    public function test_run_copy_更新設定の登録数が最大値を超えていない場合バリデーションエラーとならないこと(){
        $params = [];
        for($i = 1; $i < Model_Bulkupdatesetting::SETTING_COUNT_MAX; $i++){
            $params[] = [
                'company_id' => self::DUMMY_COMPANY_ID3,
                'name' => $i,
                'temporary' => 0,
                'allow_update_shipment_confirmed' => 0,
                'allow_update_yahoo_cancel' => 0,
                'allow_optimistic_lock_update_retry' => 0,
                'created_user_id' => self::DUMMY_USER_ID3,
                'last_updated_user_id' => self::DUMMY_USER_ID3,
            ];
        }
        Model_Bulkupdatesetting::bulk_insert($params);

        $name = '50';
        $result = Domain_Validator_Updatesetting::run_copy(self::DUMMY_COMPANY_ID3, $name);
        $this->assertTrue($result['result']);
        $this->assertEquals([], $result['messages']);
    }

    public function test_run_copy_更新設定の登録数が最大値を超えている場合バリデーションエラーとなること(){
        $params = [];
        for($i = 1; $i < Model_Bulkupdatesetting::SETTING_COUNT_MAX+1; $i++){
            $params[] = [
                'company_id' => self::DUMMY_COMPANY_ID3,
                'name' => $i,
                'temporary' => 0,
                'allow_update_shipment_confirmed' => 0,
                'allow_update_yahoo_cancel' => 0,
                'allow_optimistic_lock_update_retry' => 0,
                'created_user_id' => self::DUMMY_USER_ID3,
                'last_updated_user_id' => self::DUMMY_USER_ID3,
            ];
        }
        Model_Bulkupdatesetting::bulk_insert($params);

        $name = '51';
        $result = Domain_Validator_Updatesetting::run_copy(self::DUMMY_COMPANY_ID3, $name);
        $this->assertFalse($result['result']);
        $this->assertEquals(\Lang::get('validation.setting_count', ['label' => '更新設定数', 'param:1' => Model_Bulkupdatesetting::SETTING_COUNT_MAX]), $result['messages']['setting_count']);
    }

    public function test_run_copy_temporary1のレコードが登録上限数以上あってもバリデーションエラーとならないこと(){
        $params = [];
        for($i = 1; $i < Model_Bulkupdatesetting::SETTING_COUNT_MAX+1; $i++){
            $params[] = [
                'company_id' => self::DUMMY_COMPANY_ID3,
                'name' => $i,
                'temporary' => 1,
                'allow_update_shipment_confirmed' => 0,
                'allow_update_yahoo_cancel' => 0,
                'allow_optimistic_lock_update_retry' => 0,
                'created_user_id' => self::DUMMY_USER_ID3,
                'last_updated_user_id' => self::DUMMY_USER_ID3,
            ];
        }
        Model_Bulkupdatesetting::bulk_insert($params);

        $name = 'temporary0';
        $result = Domain_Validator_Updatesetting::run_copy(self::DUMMY_COMPANY_ID3, $name);
        $this->assertTrue($result['result']);
        $this->assertEquals([], $result['messages']);
    }

    public function test_run_updatename_重複したnameがあった場合バリデーションエラーとなること(){
        $name = 'TEST1';
        $result = Domain_Validator_Updatesetting::run_updatename(self::DUMMY_COMPANY_ID1, $name);
        $this->assertFalse($result['result']);
        $this->assertEquals(\Lang::get('validation.setting_name', ['label' => '設定名']), $result['messages']['name']);
    }

    public function test_run_updatename_重複したnameがない場合バリデーションエラーとならないこと(){
        $name = 'not_exist_name';
        $result = Domain_Validator_Updatesetting::run_updatename(self::DUMMY_COMPANY_ID1, $name);
        $this->assertTrue($result['result']);
        $this->assertEquals([], $result['messages']);
    }

    public function test_run_updatename_ID値を指定するとそのIDは重複判定から除外されること(){
        $name = 'TEST2';
        $result = Domain_Validator_Updatesetting::run_updatename(self::DUMMY_COMPANY_ID1, $name, self::DUMMY_BULK_UPDATE_SETTING_ID2);
        $this->assertTrue($result['result']);
        $this->assertEquals([], $result['messages']);
    }

    /**
     * @dataProvider Domain_Validator_Updatesettingprovider::data_provider_run_name
     */
    public function test_run_設定名が正しくバリデーションされること(array $post_params, string $company_id, int $type, bool $valid_result) {
        $result = Domain_Validator_Updatesetting::run($post_params, $company_id, self::DUMMY_USER_ID1, $type);
        $this->assertEquals($valid_result, $result['result']);
    }

    /**
     * @dataProvider Domain_Validator_Updatesettingprovider::data_provider_run_option
     */
    public function test_run_伝票に関する高度な更新設定が正しくバリデーションされること(array $post_params, string $company_id, int $type, bool $valid_result) {
        $result = Domain_Validator_Updatesetting::run($post_params, $company_id, self::DUMMY_USER_ID1, $type);
        $this->assertEquals($valid_result, $result['result']);
    }

    /**
     * @dataProvider Domain_Validator_Updatesettingprovider::data_provider_run_select_column
     */
    public function test_run_更新する項目が正しくバリデーションされること(array $post_params, string $company_id, int $type, bool $valid_result) {
        // マスタデータを使うのでキャッシュを配置
        self::_create_master_data_cache('master_credit_approval.cache');
        self::_create_master_data_cache('master_deposit.cache');
        self::_create_master_data_cache('master_payment.cache');

        $result = Domain_Validator_Updatesetting::run($post_params, $company_id, self::DUMMY_USER_ID1, $type);
        $this->assertEquals($valid_result, $result['result']);
    }

    /**
     * @dataProvider Domain_Validator_Updatesettingprovider::data_provider_run_select_update
     */
    public function test_run_更新方法が正しくバリデーションされること(array $post_params, string $company_id, int $type, bool $valid_result) {
        $result = Domain_Validator_Updatesetting::run($post_params, $company_id, self::DUMMY_USER_ID1, $type);
        $this->assertEquals($valid_result, $result['result']);
    }

    /**
     * @dataProvider Domain_Validator_Updatesettingprovider::data_provider_run_forwarding_agent
     */
    public function test_run_発送関連項目が正しくバリデーションされること(array $post_params, string $company_id, int $type, bool $valid_result) {
        // マスタデータを使うのでキャッシュを配置
        self::_create_master_data_cache('master_delivery.cache');
        self::_create_master_data_cache('master_forwarding_agent.cache');

        $result = Domain_Validator_Updatesetting::run($post_params, $company_id, self::DUMMY_USER_ID1, $type);
        $this->assertEquals($valid_result, $result['result']);
    }

    /**
     * @dataProvider Domain_Validator_Updatesettingprovider::data_provider_run_master
     */
    public function test_run_マスタ型が正しくバリデーションされること(array $post_params, string $company_id, int $type, bool $valid_result) {
        // マスタデータを使うのでキャッシュを配置
        self::_create_master_data_cache('master_shop.cache');
        self::_create_master_data_cache('master_delivery.cache');
        self::_create_master_data_cache('master_forwarding_agent.cache');
        
        $result = Domain_Validator_Updatesetting::run($post_params, $company_id, self::DUMMY_USER_ID1, $type);
        $this->assertEquals($valid_result, $result['result']);
    }

    /**
     * @dataProvider Domain_Validator_Updatesettingprovider::data_provider_run_addwrite
     */
    public function test_run_空文字を追記しようとした場合バリデーションエラーになること(array $post_params, string $company_id, int $type, bool $valid_result) {
        $result = Domain_Validator_Updatesetting::run($post_params, $company_id, self::DUMMY_USER_ID1, $type);
        $this->assertEquals($valid_result, $result['result']);
    }

    /**
     * @dataProvider Domain_Validator_Updatesettingprovider::data_provider_run_text
     */
    public function test_run_文字列型が正しくバリデーションされること(array $post_params, string $company_id, int $type, bool $valid_result) {
        $result = Domain_Validator_Updatesetting::run($post_params, $company_id, self::DUMMY_USER_ID1, $type);
        $this->assertEquals($valid_result, $result['result']);
    }

    /**
     * @dataProvider Domain_Validator_Updatesettingprovider::data_provider_run_number
     */
    public function test_run_数値型が正しくバリデーションされること(array $post_params, string $company_id, int $type, bool $valid_result) {
        $result = Domain_Validator_Updatesetting::run($post_params, $company_id, self::DUMMY_USER_ID1, $type);
        $this->assertEquals($valid_result, $result['result']);
    }

    /**
     * @dataProvider Domain_Validator_Updatesettingprovider::data_provider_run_email
     */
    public function test_run_Eメール型が正しくバリデーションされること(array $post_params, string $company_id, int $type, bool $valid_result) {
        $result = Domain_Validator_Updatesetting::run($post_params, $company_id, self::DUMMY_USER_ID1, $type);
        $this->assertEquals($valid_result, $result['result']);
    }

    /**
     * @dataProvider Domain_Validator_Updatesettingprovider::data_provider_run_bool
     */
    public function test_run_ブール型が正しくバリデーションされること(array $post_params, string $company_id, int $type, bool $valid_result) {
        $result = Domain_Validator_Updatesetting::run($post_params, $company_id, self::DUMMY_USER_ID1, $type);
        $this->assertEquals($valid_result, $result['result']);
    }

    /**
     * @dataProvider Domain_Validator_Updatesettingprovider::data_provider_run_tag
     */
    public function test_run_タグ型が正しくバリデーションされること(array $post_params, string $company_id, int $type, bool $valid_result) {
        $result = Domain_Validator_Updatesetting::run($post_params, $company_id, self::DUMMY_USER_ID1, $type);
        $this->assertEquals($valid_result, $result['result']);
    }

    /**
     * @dataProvider Domain_Validator_Updatesettingprovider::data_provider_run_telephone
     */
    public function test_run_電話番号型が正しくバリデーションされること(array $post_params, string $company_id, int $type, bool $valid_result) {
        $result = Domain_Validator_Updatesetting::run($post_params, $company_id, self::DUMMY_USER_ID1, $type);
        $this->assertEquals($valid_result, $result['result']);
    }

    /**
     * @dataProvider Domain_Validator_Updatesettingprovider::data_provider_run_zip
     */
    public function test_run_郵便番号型が正しくバリデーションされること(array $post_params, string $company_id, int $type, bool $valid_result) {
        $result = Domain_Validator_Updatesetting::run($post_params, $company_id, self::DUMMY_USER_ID1, $type);
        $this->assertEquals($valid_result, $result['result']);
    }

    /**
     * @dataProvider Domain_Validator_Updatesettingprovider::data_provider_run_date
     */
    public function test_run_日付型が正しくバリデーションされること(array $post_params, string $company_id, int $type, bool $valid_result) {
        $result = Domain_Validator_Updatesetting::run($post_params, $company_id, self::DUMMY_USER_ID1, $type);
        $this->assertEquals($valid_result, $result['result']);
    }

    /**
     * @dataProvider Domain_Validator_Updatesettingprovider::data_provider_run_forwarding_agent_seal
     */
    public function test_run_発送関連項目のシールの重複チェックが正しくバリデーションされること(array $post_params, string $company_id, int $type, bool $valid_result) {
        // マスタデータを使うのでキャッシュを配置
        self::_create_master_data_cache('master_delivery.cache');
        self::_create_master_data_cache('master_forwarding_agent.cache');

        $result = Domain_Validator_Updatesetting::run($post_params, $company_id, self::DUMMY_USER_ID1, $type);
        $this->assertEquals($valid_result, $result['result']);
    }

    /**
     * @dataProvider Domain_Validator_Updatesettingprovider::data_provider_run_max_column_count
     */
    public function test_run_更新設定項目の登録数の上限チェックが正しくバリデーションされること(array $post_params, string $company_id, int $type, bool $valid_result){
        $result = Domain_Validator_Updatesetting::run($post_params, $company_id, self::DUMMY_USER_ID1, $type);
        $this->assertEquals($valid_result, $result['result']);
    }
}