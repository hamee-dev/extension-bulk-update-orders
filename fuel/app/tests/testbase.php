<?php
/**
 * テストのベースクラス
 * 必要な処理があれば追加する
 */

use PHPUnit\DbUnit\TestCase;
use PHPUnit\DbUnit\DataSet\YamlDataSet;

abstract class Testbase extends TestCase
{
    const DUMMY_COMPANY_ID1 = 1;
    const DUMMY_COMPANY_ID2 = 2;
    const DUMMY_COMPANY_ID3 = 3;
    const DUMMY_USER_ID1    = 1;
    const DUMMY_USER_ID2    = 2;
    const DUMMY_USER_ID3    = 3;
    const DUMMY_BULK_UPDATE_SETTING_ID1 = 1;
    const DUMMY_BULK_UPDATE_SETTING_ID2 = 2;
    const DUMMY_BULK_UPDATE_SETTING_ID3 = 3;
    const DUMMY_BULK_UPDATE_SETTING_ID4 = 4;
    const DUMMY_BULK_UPDATE_SETTING_ID5 = 5;
    const DUMMY_BULK_UPDATE_COLUMN_ID1 = 1;
    const DUMMY_BULK_UPDATE_COLUMN_ID2 = 2;
    const DUMMY_BULK_UPDATE_COLUMN_ID3 = 3;
    const DUMMY_BULK_UPDATE_COLUMN_ID4 = 4;
    const DUMMY_BULK_UPDATE_COLUMN_ID5 = 5;
    const DUMMY_BULK_UPDATE_COLUMN_ID6 = 6;
    const DUMMY_BULK_UPDATE_COLUMN_ID7 = 7;
    const DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID1 = 1;
    const DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID2 = 2;
    const DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID3 = 3;
    const DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID4 = 4;
    const DUMMY_EXECUTION_BULK_UPDATE_SETTING_ID5 = 5;
    const DUMMY_EXTENSION_EXCUTION_ID1 = 1;
    const DUMMY_EXTENSION_EXCUTION_ID2 = 2;
    const DUMMY_EXTENSION_EXCUTION_ID3 = 3;
    const DUMMY_EXTENSION_EXCUTION_ID4 = 4;
    const DUMMY_EXTENSION_EXCUTION_ID5 = 5;

    /**
     * DBの初期データセットを読み込むかどうか
     * 同じテーブルを複数のyamlで読み込ませようとエラーが発生するためそれを回避する為に追加した
     *
     * @var bool
     */
    protected $fetch_init_yaml = true;

    /**
     * テストごとの読み込みたいデータセット名（dataset/から後ろのパスの配列）
     *
     * @var array
     */
    protected $dataset_filenames = [];

    /**
     * PDO のインスタンス生成は、クリーンアップおよびフィクスチャ読み込みのときに一度だけ
     *
     * @var PDO
     */
    static private $pdo = null;

    /**
     * PHPUnit\DbUnit\Database\Connection のインスタンス生成は、テストごとに一度だけ
     *
     * @var DefaultConnection
     */
    private $conn = null;

    protected function setUp()
    {
        parent::setUp();

        // キャッシュファイルを削除する
        foreach(glob(APPPATH . 'cache/test/*') as $file_name){
            unlink($file_name);
        }

        // マスタデータのメモリキャッシュを削除する
        $master = new Utility_Master(self::DUMMY_USER_ID1);
        $reflectionClass = new ReflectionClass(get_class($master));
        $property = $reflectionClass->getProperty('cache');
        $property->setAccessible(true);
        $property->setValue($master, null);
    }

    /**
     * DBの接続情報
     * @see http://phpunit.readthedocs.io/ja/latest/database.html
     *
     * @return null|\PHPUnit\DbUnit\Database\Connection|\PHPUnit\DbUnit\Database\DefaultConnection
     */
    protected function getConnection()
    {
        if ($this->conn === null) {
            if (self::$pdo == null) {
                \Config::load('db', true);
                self::$pdo = new PDO(
                    \Config::get('db.default.connection.dsn'),
                    \Config::get('db.default.connection.username'),
                    \Config::get('db.default.connection.password'));
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, \Config::get('db.default.connection.db_name'));
        }

        return $this->conn;
    }

    /**
     * テストで追加されたレコードをTRUNCATEする
     *
     * @return \PHPUnit\DbUnit\Operation\Operation
     */
    public function getTearDownOperation()
    {
        return \PHPUnit\DbUnit\Operation\Factory::TRUNCATE();
    }

    /**
     * データセットを読み込む
     *
     * @return PHPUnit\DbUnit\DataSet\CompositeDataSet
     * @throws ReflectionException
     */
    protected function getDataSet()
    {
        $compositeDs = new PHPUnit\DbUnit\DataSet\CompositeDataSet();
        if ($this->fetch_init_yaml) {
            // 初期データのデータセット
            $compositeDs->addDataSet(self::_get_yaml_dataset('init.yml'));
        }
        // 各テスト別のデータセット
        foreach ($this->dataset_filenames as $dataset_filename) {
            $dataset = self::_get_yaml_dataset($dataset_filename);
            if ($dataset) {
                $compositeDs->addDataSet($dataset);
            }
        }
        return $compositeDs;
    }


    /**
     * yamlファイルからデータセットを取得する
     * データセットの値を「##DUMMY_◯◯##」とすると##で囲まれたTestbaseにある同じ名前の定数で置換する
     *
     * @param $file_name
     * @return PHPUnit\DbUnit\DataSet\ReplacementDataSet
     * @throws ReflectionException
     */
    private static function _get_yaml_dataset($file_name) {
        $file_path = dirname(__FILE__)."/dataset/". $file_name;
        if (!file_exists($file_path)) {
            return null;
        }
        $dataset = new YamlDataSet($file_path);
        // yamlの ##DUMMY_◯◯## となっている値を同じ名前の定数で置換する
        $dataset = new PHPUnit\DbUnit\DataSet\ReplacementDataSet($dataset);
        $reflect = new ReflectionClass(self::class);
        foreach ($reflect->getConstants() as $name => $value) {
            if (strpos($name, 'DUMMY_') === 0) {
                $dataset->addFullReplacement('##' . $name . '##', $value);
            }
        }
        return $dataset;
    }

    /**
     * privateメソッドをテストする時に使用
     *
     * @param string $class クラス名
     * @param string $method メソッド名
     * @return ReflectionMethod
     */
    protected function getMethod(string $class,string $method) : ReflectionMethod {
        $class = new ReflectionClass($class);
        $method = $class->getMethod($method);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * Create anonymous proxy class of your class.
     * @param  string $classname
     * @return class@anonymous
     * @see https://qiita.com/mpyw/items/494b9737a6212fea7f0b
     * @see https://github.com/mpyw/privator
     */
    public static function get(string $classname)
    {
        return new class($classname)
        {
            private static $rc;

            public function __construct(string $classname)
            {
                self::$rc = new \ReflectionClass($classname);
            }

            /**
             * Call static method of your class.
             * @param  string $name
             * @param  array $args
             * @return mixed
             */
            public static function __callStatic(string $name, array $args)
            {
                $rc = self::$rc;
                if (method_exists($rc->name, $name)) {
                    $rm = $rc->getMethod($name);
                    if (!$rm->isStatic()) {
                        throw new LogicException(
                            "Non-static method called statically: " .
                            "$rc->name::$name()");
                    }
                    $rm->setAccessible(true);
                    return $rm->invokeArgs(null, $args);
                }
                if (method_exists($rc->name, '__callStatic')) {
                    return $rc->name::$name(...$args);
                }
                throw new LogicException(
                    "Call to undefined method: $rc->name::$name()");
            }

            private static function getStaticReflectionProperty(string $name): \ReflectionProperty
            {
                $rc = self::$rc;
                if (property_exists($rc->name, $name)) {
                    $rp = $rc->getProperty($name);
                    if (!$rp->isStatic()) {
                        throw new LogicException(
                            "Access to undeclared static property: " .
                            "$rc->name::\$$name");
                    }
                    $rp->setAccessible(true);
                    return $rp;
                }
                throw new LogicException(
                    "Access to undeclared static property: " .
                    "$rc->name::\$$name");
            }

            /**
             * Get static property of your class.
             * If you want to call your own "static function getStatic()":
             *   $proxy->__callStatic('getStatic', $args)
             * @param  string $name
             * @return mixed
             */
            public static function getStatic(string $name)
            {
                return self::getStaticReflectionProperty($name)->getValue();
            }

            /**
             * Set static property of your class.
             * If you want to call your own "static function setStatic()":
             *   $proxy->__callStatic('setStatic', $args)
             * @param string $name
             * @param mixed $value
             */
            public static function setStatic(string $name, $value)
            {
                self::getStaticReflectionProperty($name)->setValue($name, $value);
            }

            /**
             * Create anonymous proxy object of your class.
             * If you want to call your own "static function new()":
             *   $proxy->__callStatic('new', $args)
             * @param  mixed ...$args
             * @return class@anonymous
             */
            public static function new(...$args)
            {
                return self::newInstance($args);
            }

            /**
             * Create anonymous proxy object of your class without constructor.
             * If you want to call your own "static function newWithoutConstructor()":
             *   $proxy->__callStatic('newWithoutConstructor', $args)
             * @return class@anonymous
             */
            public static function newWithoutConstructor()
            {
                return self::newInstance();
            }

            private static function newInstance(array $args = null)
            {
                return new class(self::$rc, $args)
                {
                    private $ro;
                    private $ins;

                    public function __construct(\ReflectionClass $rc, array $args = null)
                    {
                        $this->ins = $rc->newInstanceWithoutConstructor();
                        if ($args !== null && $con = $rc->getConstructor()) {
                            $con->setAccessible(true);
                            $con->invokeArgs($this->ins, $args);
                        }
                        $this->ro = new \ReflectionObject($this->ins);
                    }

                    /**
                     * Call instance method of your class.
                     * @param  string $name
                     * @param  array $args
                     * @return mixed
                     */
                    public function __call(string $name, array $args)
                    {
                        if (method_exists($this->ro->name, $name)) {
                            $rm = $this->ro->getMethod($name);
                            $rm->setAccessible(true);
                            return $rm->invokeArgs($this->ins, $args);
                        }
                        if (method_exists($this->ro->name, '__call')) {
                            return $this->ins->$name(...$args);
                        }
                        throw new LogicException(
                            "Call to undefined method: " .
                            "{$this->ro->name}::$name()");
                    }

                    private function getReflectionProperty(string $name)
                    {
                        if (property_exists($this->ins, $name)) {
                            $rp = $this->ro->getProperty($name);
                            $rp->setAccessible(true);
                            return $rp;
                        }
                        throw new LogicException(
                            "Undefined property: {$this->ro->name}::\$$name");
                    }

                    /**
                     * Get property of your object.
                     * @param  string $name
                     * @return mixed
                     */
                    public function __get(string $name)
                    {
                        try {
                            return $this->getReflectionProperty($name)
                                ->getValue($this->ins);
                        } catch (LogicException $e) {
                            try {
                                return $this->__call('__get', [$name]);
                            } catch (LogicException $_) {
                                throw $e;
                            }
                        }
                    }

                    /**
                     * Set property of your object.
                     * @param  string $name
                     * @param  mixed $value
                     */
                    public function __set(string $name, $value)
                    {
                        try {
                            $property = $this->getReflectionProperty($name);
                            $property->setValue($this->ins, $value);
                        } catch (LogicException $e) {
                            try {
                                $this->__call('__set', [$name, $value]);
                                return;
                            } catch (LogicException $_) {
                            } // If __set() is undefined,
                            // fallback to the actual property.
                            if (isset($property)) {
                                throw $e; // Static property exists,
                                // so you cannot create a new field.
                            } else {
                                $this->ins->$name = $value; // Property does not exists
                                // so you can create a new field.
                            }
                        }
                    }
                };
            }
        };
    }

    /**
     * privateメソッドを実行する
     * private staticメソッドの実行も可能
     *
     * @param $class_obj 実行するクラスのオブジェクト
     * @param string $method_name メソッド名
     * @param array $params メソッドの引数
     * @return mixed 実行結果
     * @throws ReflectionException
     */
    protected static function invoke_method($class_obj, string $method_name, array $params = []) {
        $method = new ReflectionMethod(get_class($class_obj), $method_name);
        $method->setAccessible(true);
        return call_user_func_array([$method, 'invoke'], array_merge([$class_obj], $params));
    }

    /**
     * マスタデータ用のキャッシュファイルを配置する
     * tests/file配下にあるファイル名を指定する
     *
     * @param string $file_name tests/file配下にあるキャッシュファイル名
     */
    protected static function _create_master_data_cache(string $file_name) {
        copy(APPPATH . 'tests/file/' . $file_name, APPPATH . 'cache/test/' . self::DUMMY_COMPANY_ID1 . '_' . $file_name);
    }
}