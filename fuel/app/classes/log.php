<?php

/**
 * 拡張したログクラス
 * ・logs配下にyyyymmdd.logのファイル名で出力
 * ・[ログレベル \t 日付 \t jsonエンコードしたログメッセージ] のフォーマットで出力するように修正
 *
 * Class Log
 */
class Log extends \Fuel\Core\Log {

    /**
     * noticeのログレベル
     */
    const L_NOTICE = 250;

    /**
     * 独自のファイル名及びフォーマットにしたいため、オーバーライドする
     *
     * @throws FuelException
     */
    public static function initialize()
    {
        // load the file config
        \Config::load('file', true);

        // make sure the log directories exist
        try
        {
            $filepath = APPPATH.'logs'.DS;
            $filename = $filepath.date('Ymd').'.log';

            // get the required folder permissions
            $permission = \Config::get('file.chmod.folders', 0777);
            if ( ! is_dir($filepath))
            {
                mkdir($filepath, 0777, true);
                chmod($filepath, $permission);
            }

            $handle = fopen($filename, 'a');
        }
        catch (\Exception $e)
        {
            \Config::set('log_threshold', \Fuel::L_NONE);
            throw new \FuelException('Unable to create or write to the log file. Please check the permissions on '.$filepath.'. ('.$e->getMessage().')');
        }

        if ( ! filesize($filename))
        {
            chmod($filename, \Config::get('file.chmod.files', 0666));
        }
        fclose($handle);

        // create the streamhandler, and activate the handler
        $stream = new \Monolog\Handler\StreamHandler($filename, \Monolog\Logger::DEBUG);
        $formatter = new \Monolog\Formatter\LineFormatter("%level_name%\t%datetime%\t%message%".PHP_EOL, "Y-m-d H:i:s.u");
        $stream->setFormatter($formatter);
        static::$monolog->pushHandler($stream);
    }

    /**
     * 拡張したdebug用ログ出力
     *
     * @param string $msg ログメッセージ
     * @param array $params パラメータ等がある場合、連想配列で渡す
     * @return bool 成功/失敗
     * @throws FuelException
     */
    public static function debug_ex(string $msg, array $params = null) : bool {
        return static::write(\Fuel::L_DEBUG, ['message' => $msg, 'params' => $params]);
    }

    /**
     * 拡張したinfo用ログ出力
     *
     * @param string $msg ログメッセージ
     * @param array $params パラメータ等がある場合、連想配列で渡す
     * @return bool 成功/失敗
     * @throws FuelException
     */
    public static function info_ex(string $msg, array $params = null) : bool {
        return static::write(\Fuel::L_INFO, ['message' => $msg, 'params' => $params]);
    }

    /**
     * 拡張したnotice用ログ出力
     *
     * @param string $msg ログメッセージ
     * @param array $params パラメータ等がある場合、連想配列で渡す
     * @return bool 成功/失敗
     * @throws FuelException
     */
    public static function notice_ex(string $msg, array $params = null) : bool {
        return static::write(self::L_NOTICE, ['message' => $msg, 'params' => $params]);
    }

    /**
     * 拡張したwarning用ログ出力
     *
     * @param string $msg ログメッセージ
     * @param array $params パラメータ等がある場合、連想配列で渡す
     * @return bool 成功/失敗
     * @throws FuelException
     */
    public static function warning_ex(string $msg, array $params = null) : bool {
        return static::write(\Fuel::L_WARNING, ['message' => $msg, 'params' => $params]);
    }

    /**
     * 拡張したerror用ログ出力
     *
     * @param string $msg ログメッセージ
     * @param array $params パラメータ等がある場合、連想配列で渡す
     * @return boo 成功/失敗
     * @throws FuelException
     */
    public static function error_ex(string $msg, array $params = null) : bool {
        return static::write(\Fuel::L_ERROR, ['message' => $msg, 'params' => $params]);
    }

    /**
     * 例外エラーをログ出力する
     *
     * @param Exception $exception 例外オブジェクト
     * @return bool 成功/失敗
     * @throws FuelException
     */
    public static function exception(Exception $exception) : bool {
        return static::write(\Fuel::L_ERROR,[
            'message' => $exception->getMessage(),
            'params' => [
                'code'    => $exception->getCode(),
                'file'    => $exception->getFile(),
                'line'    => $exception->getLine(),
                'trace'   => $exception->getTraceAsString()
            ]
        ]);
    }

    /**
     * ログメッセージにプレフィックスを追加する
     *
     * 注意:
     * はjsonエンコードした文字列は渡さないでください
     * メソッドの中でjsonエンコードしていますので、配列をそのまま渡してください
     *
     *  {
     *      "class": "Client_Neapi ",      // ログが出力されたクラス名
     *      "function": "apiExecute",   // ログが出力されたメソッド名
     *      "line": 95,                 // ログが出力された行数
     *      "pid": 23,                  // プロセスID
     *      "message": "update access_token and refresh_token.",    // ログメッセージ
     *      "params": {                 // 各種パラメータ等の連想配列
     *          "user_id": "1"
     *      }
     *  }
     *
     * @param int|string $level ログレベル
     * @param string|array $msg ログメッセージ ['message' => 文字列, 'params' => 連想配列] の想定
     * @param string $method メソッド名（ログにはメソッド名が自動的に出るので基本的には渡す必要なし）
     * @return bool 成功/失敗
     * @throws FuelException
     */
    public static function write($level, $msg, $method = null) : bool {

        // ヘルスチェックだった場合はログを出力しない
        if (self::_is_health()) {
            return false;
        }

        $trace = debug_backtrace();
        $class_name    = isset($trace[2]['class'])    ? $trace[2]['class']    : 'no_class';
        $function_name = isset($trace[2]['function']) ? $trace[2]['function'] : 'no_function';
        $line          = isset($trace[1]['line'])     ? $trace[1]['line']     : 'no_line';

        $new_msg = [
            'class' => $class_name,
            'function' => $function_name,
            'line' => $line,
            'pid' => getmypid(),
        ];

        $message = null;
        if (is_string($msg)) {
            $message = $msg;
        }else if (is_array($msg) && isset($msg['message'])) {
            $message = $msg['message'];
        }
        if (!is_null($message)) {
            $new_msg['message'] = $message;
        }
        if (is_array($msg) && isset($msg['params'])) {
            $new_msg['params'] = $msg['params'];
        }
        if (!is_null($method)) {
            $new_msg['method'] = $method;
        }

        // 日本語文字列は変換しないようにする
        // @see https://qiita.com/munaita_/items/f68dde0d1fe7c07b8939
        $json_msg = json_encode($new_msg, JSON_UNESCAPED_UNICODE);
        // バイナリデータなどjson_encodeできないものに関してはmessage,paramsをunsetしたものを表示する
        // NOTE: 必ずしもmessage,paramsが原因とは限らないが可能性が高いのがこの2つなので対応
        if($json_msg === false){
            unset($new_msg['message']);
            unset($new_msg['params']);
            $json_msg = json_encode($new_msg, JSON_UNESCAPED_UNICODE);
        }

        return parent::write($level, $json_msg);
    }

    /**
     * ヘルスチェックURLかどうか
     * Uri::string() でも $_SERVER["REQUEST_URI"] と同じ結果を得ることができるが
     * タイミングによっては Uri::string() がnullになることがあるので $_SERVER["REQUEST_URI"] で取得する
     *
     * @return bool true:ヘルスチェック/false:ヘルスチェックではない
     */
    private static function _is_health() {
        return isset($_SERVER["REQUEST_URI"]) && $_SERVER["REQUEST_URI"] === \Config::get('health_check_uri');
    }
}