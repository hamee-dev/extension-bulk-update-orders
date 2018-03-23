<?php
/**
 * 拡張したDatabase_Query
 */
class Database_Query extends \Fuel\Core\Database_Query
{
    /**
     * SQLの実行
     * 実行したSQLをログ出力するためオーバーライド
     *
     * @param mixed $db DBインスタンス
     * @return object クエリ実行結果（詳しくは親クラス参照）
     * @throws FuelException
     */
    public function execute($db = null)
    {
        $result = parent::execute($db);
        Log::notice_ex(DB::last_query());
        return $result;
    }
}