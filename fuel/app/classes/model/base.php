<?php

/**
 * DB操作するモデル全ての親クラス
 * クエリの根っことなる部分
 */
class Model_Base extends Orm\Model{
    // 以下モデル毎に拡張してください
    protected static $_primary_key = ['id'];
    protected static $_table_name = '';
    protected static $_properties = ['id'];

    /**
     * 比較対象から除外するカラム名の配列
     *
     * モデルとモデルを比較する（同じ値かを比較）際に、
     * モデルのインスタンスが保有するカラムの値（プロパティ値）を比較対象外にするカラム名を定義する。
     * モデル毎に拡張（オーバーライド）してください。
     *
     * 書き方はコメントを参照。※下記コメントはサンプルとして意図的に残しているので消さないように。
     * @var array カラム名
     */
    protected static $exclude_comparison_columns = [
        // 'id',
        // 'created_at',
        // 'updated_at',
    ];

    // created_atとupdated_atが勝手につくように設定
    // 子クラスで不要な場合は空配列宣言してください
    protected static $_observers = [
        'Orm\Observer_CreatedAt' => [
            'events' => ['before_insert'],
            'mysql_timestamp' => true,
        ],
        'Orm\Observer_UpdatedAt' => [
            'events' => ['before_save'],
            'mysql_timestamp' => true,
        ],
        'Orm\Observer_Validation' => [
            'events' => ['before_save'],
        ],
    ];

    /**
     * fuel/coreのfindをoverrideし、勝手にキャッシュが使われてしまうのを避ける
     *
     * @param int|null $id
     * @param array $options
     * @return  Model|Model[]
     */
    public static function find($id = null, array $options = []) {
        $options['from_cache'] = false;
        return parent::find($id, $options);
    }

    /**
     * 条件を渡して1件だけ取得する
     *
     * @param array $where where句の連想配列
     * @param array $options where句以外の条件がある場合の連想配列
     * @return mixed|null|\Orm\Model
     * @throws FuelException
     */
    public static function findOne(array $where, array $options = []) {
        $options['where'] = $where;
        return static::find('first', $options);
    }

    /**
     * 条件にあったレコードを取得する
     *
     * @param array $where where句の連想配列
     * @param array $options where句以外の条件がある場合の連想配列
     * @return array \Orm\Modelの配列
     * @throws FuelException
     */
    public static function findAll(array $where, array $options = []) : array {
        $options['where'] = $where;
        return static::find('all', $options);
    }

    /**
     * 引数で渡したカラムの情報のみを配列で返す
     * @param string $column_name
     * @param array $option where句以外の条件がある場合の連想配列
     * @return array Arr::pluckの結果配列
     */
    public static function pluck(string $column_name, array $option =[]) : array {
        $query = static::query($option)->select($column_name);
        $result = $query->get();
        return Arr::pluck($result, $column_name);
    }

    /**
     * queryメソッドにキャッシュを利用しないためのオーバライド
     *
     * FuelPHPのOrmモデルではデフォルトでキャッシュを取得するようになっている
     * 本プロダクトではレコードオブジェクトを取得する際にselectを利用する
     * 上記の理由より、queryメソッドをオーバライドする
     * from_cache(false)がデフォルトでつくようにし、キャッシュを利用せず
     * 毎回レコードオブジェクトを取得するようにした
     *
     * @param array $option クエリの条件の連想配列
     * @return Orm\Query from_cache(false)セットしたOrm\Queryオブジェクト
     */
    public static function query($option = []) : Orm\Query {
        return parent::query($option)
            ->from_cache(false);
    }

    /**
     * 比較対象から除外するカラム名の配列の取得
     *
     * $exclude_comparison_columnsに定義された除外対象カラムに加えて、
     * リレーション定義されたプロパティも除外する
     *
     * @return array 比較対象から除外するカラム名の配列
     */
    protected static function get_exclude_comparison_columns() : array {
        $class = get_called_class();
        return array_merge(
            // 除外対象カラムに定義されたプロパティ
            property_exists($class, 'exclude_comparison_columns') && is_array(static::$exclude_comparison_columns) ? static::$exclude_comparison_columns : [],

            // リレーション定義されたプロパティ
            property_exists($class, '_has_one')    && is_array(static::$_has_one)    ? array_keys(static::$_has_one)    : [],
            property_exists($class, '_belongs_to') && is_array(static::$_belongs_to) ? array_keys(static::$_belongs_to) : [],
            property_exists($class, '_has_many')   && is_array(static::$_has_many)   ? array_keys(static::$_has_many)   : [],
            property_exists($class, '_many_many')  && is_array(static::$_many_many)  ? array_keys(static::$_many_many)  : [],
            property_exists($class, '_eav')        && is_array(static::$_eav)        ? array_keys(static::$_eav)        : []
        );
    }

    /**
     * 比較対象のカラム値の取得
     *
     * モデルとモデルを比較する（同じ値かを比較）際に、比較対象となるカラム値を取得する
     *
     * @return array 比較対象のカラム値の配列（[カラム名=>値]の配列）
     */
    public function get_comparison_columns() : array {
        return array_diff_key($this->to_array(), array_flip(static::get_exclude_comparison_columns()));
    }

    /**
     * バルクインサートを実行
     *
     * @param  array $params バルクインサートを行うパラメータ一覧（連想配列を複数格納した配列）
     *                       先頭の連想配列のキー一覧をインサート対象のカラムとして設定する
     * 例: [
     *   [
     *     'column_name1' => 'value1',
     *     'column_name2' => 'value2',
     *     'column_name3' => 'value3',
     *   ],
     *   [
     *     'column_name1' => 'value1',
     *     'column_name2' => 'value2',
     *     'column_name3' => 'value3',
     *   ],
     *  ...
     * ]
     * @return bool  インサートに成功したかどうか
     */
    public static function bulk_insert(array $params): bool {
        if(!isset($params[0])) return false;
        $columns = array_keys($params[0]);

        $query = \DB::insert(static::$_table_name)
            ->columns($columns);
        foreach ($params as $param) {
            $values = array_values($param);
            $query->values($values);
        }

        $ret = $query->execute();
        return ($ret[1] >= 0);
    }
}