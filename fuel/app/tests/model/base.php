<?php
class Test_Model_Base extends Testbase {
    public function test_pluck_引数で渡したカラムの情報のみを配列で返すこと(){
        // テストとしてupdate_methodsに対して実行する
        $result = Model_Updatemethod::pluck('name', ['order_by' => 'id']);
        $expect = ['次の値で上書き', '次の値を追記', '次の値を加算[+]', '次の値で減算[-]', '次の値を乗算[x]', '次の値で除算[÷]'];
        $this->assertEquals($expect, $result);
    }

    public function test_pluck_第二引数で条件を渡すと渡した条件で絞った配列を返すこと(){
        // テストとしてupdate_methodsに対して実行する
        $result = Model_Updatemethod::pluck('name', ['where' => ['id' => '2']]);
        $expect = ['次の値を追記'];
        $this->assertEquals($expect, $result);
    }

    /**
     * @dataProvider Model_Basedataprovider::data_provider_exclude_comparison_columns
     */
    public function test_get_exclude_comparison_columns_定義された除外カラムとリレーション定義の情報が取得できること(
        $exclude_comparison_columns,
        $has_one,
        $belongs_to,
        $has_many,
        $many_many,
        $eav,
        $expected
    ) {

        // テスト対象のクラスを継承して無名クラスとして定義
        // 無名クラスとして定義する理由は、
        // テスト対象のクラスのprivateやprotectedのstaticプロパティを設定すると
        // テスト実行時のプロセスで動作している間は、
        // そのクラスのstaticプロパティが書き換わったままとなり、他のテストケースに影響が生じるため、
        // 無名クラスとした。
        // また、@backupStaticAttributesを指定すると、
        // 「Exception: Serialization of 'Closure' is not allowed」が発生し、使用できなかった。
        $model_base_dummy = new class extends Model_Base {
            protected static $_has_one;
            protected static $_belongs_to;
            protected static $_has_many;
            protected static $_many_many;
            protected static $_eav;

            protected static $exclude_comparison_columns;
        };

        $model_base_mock = self::get(get_class($model_base_dummy));

        $model_base_mock::setStatic('exclude_comparison_columns', $exclude_comparison_columns);
        $model_base_mock::setStatic('_has_one'   , $has_one);
        $model_base_mock::setStatic('_belongs_to', $belongs_to);
        $model_base_mock::setStatic('_has_many'  , $has_many);
        $model_base_mock::setStatic('_many_many' , $many_many);
        $model_base_mock::setStatic('_eav'       , $eav);

        $result = $model_base_mock::get_exclude_comparison_columns();
        $this->assertEquals($expected, $result);
    }

    public function test_get_exclude_comparison_columns_除外しているカラムはないこと() {
        $method = $this->getMethod('Model_Base', 'get_exclude_comparison_columns');
        $result = $method->invokeArgs(null, []);
        $expect = [];
        $this->assertEquals($expect, $result);
    }

    public function test_get_comparison_columns_プロパティに定義しているカラム配列が取得できること() {
        $model = new Model_Base();
        $result = $model->get_comparison_columns();
        $expect = ['id' => null];
        $this->assertEquals($expect, $result);
    }

    public function test_bulk_insert_バルクインサートできること(){
        // テストとしてcompanyに対して実行する
        $before_count = Model_Company::query()->count();
        $params = [
            ['main_function_id' => '1', 'company_ne_id' => '1'],
            ['main_function_id' => '2', 'company_ne_id' => '2'],
            ['main_function_id' => '3', 'company_ne_id' => '3'],
        ];
        $result = Model_Company::bulk_insert($params);
        $after_count = Model_Company::query()->count();
        // 3件レコードが増えていること
        $this->assertEquals($before_count+3, $after_count);
        $this->assertTrue($result);
    }

    public function test_bulk_insert_パラメータが空の場合はfalseを返すこと(){
        // テストとしてcompanyに対して実行する
        $params = [];
        $result = Model_Company::bulk_insert($params);
        $this->assertFalse($result);
    }

    /**
     * @expectedException Fuel\Core\Database_Exception
     */
    public function test_bulk_insert_パラメータが意図した形でない（存在しないカラム名）場合は例外が発生すること(){
        // テストとしてcompanyに対して実行する
        $params = [
            ['hoge' => '1', 'company_ne_id' => '1'],
            ['hoge' => '2', 'company_ne_id' => '2'],
            ['hoge' => '3', 'company_ne_id' => '3'],
        ];
        $result = Model_Company::bulk_insert($params);
        $this->assertFalse($result);
    }

    /**
     * @expectedException Fuel\Core\Database_Exception
     */
    public function test_bulk_insert_パラメータが意図した形でない（配列の要素数の不一致）場合は例外が発生すること(){
        // テストとしてcompanyに対して実行する
        $params = [
            ['main_function_id' => '1', 'company_ne_id' => '1'],
            ['company_ne_id' => '2'],
            ['main_function_id' => '3'],
        ];
        $result = Model_Company::bulk_insert($params);
        $this->assertFalse($result);
    }
}