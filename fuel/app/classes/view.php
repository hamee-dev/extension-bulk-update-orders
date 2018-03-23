<?php

class View extends \Fuel\Core\View
{
    /**
     * キャッシュしたviewデータ
     *
     * @var array
     */
    private static $_view_cache = null;

    /**
     * キャッシュしたグローバル変数
     *
     * @var array
     */
    private static $_data_cache = [];

    /**
     * キャッシュしたサニタイズしたグローバル変数
     *
     * @var array
     */
    private static $_data_sanitize_cache = [];

    /**
     * viewファイルをレンダーする
     * ファイルをキャッシュするためrenderよりも高速
     * viewから$this->fast_renderで呼ぶことができます
     *
     * @param string $file_name viewファイルのパス(renderするときと同様)
     * @param array $data viewへ渡す値(renderするときと同様)
     * @param bool $filter サニタイズするかどうか(何も渡さない場合はconfigの設定を使用する。renderするときと同様)
     * @return string
     */
    public function fast_render(string $file_name, ?array $data = [], $filter = null) : string {

        // キャッシュがあればキャッシュからviewを取得する。なければファイルから取得しキャッシュする
        if (isset(self::$_view_cache[$file_name])) {
            $code = self::$_view_cache[$file_name];
        }else{
            $code = file_get_contents(APPPATH . 'views/' . $file_name . '.php');
            self::$_view_cache[$file_name] = $code;
        }

        $auto_filter = is_null($filter) ? $this->auto_filter : $filter;
        if (!empty($data)) {
            // viewに渡す値をサニタイズする(この値はあまりキャッシュの意味が無いためあえてキャッシュしない)
            $data = self::_sanitaize($data, [], $auto_filter, $this->filter_closures);
        }else{
            $data = [];
        }

        if (!empty(static::$global_data)) {
            // グローバル変数をサニタイズする(基本的に一度渡されたグローバル変数が変わることは無いためキャッシュを使う)
            $data = array_merge($data, self::_sanitaize(static::$global_data, static::$global_filter, $auto_filter, $this->filter_closures, true));
        }

        extract($data, EXTR_REFS);
        ob_start();
        eval( "?>" . $code );
        $result = ob_get_contents();
        ob_end_clean();

        return $result;
    }

    /**
     * 値をサニタイズする
     *
     * @param array $data サニタイズしたい値の連想配列
     * @param array $rules キーごとにサニタイズしたい場合のフラグ
     * @param bool $auto_filter 引数の値全体に対してサニタイズするかどうかのフラグ
     * @param bool $filter_closures クロージャだった場合に対するフラグ
     * @param bool $is_cache キャッシュ機能をつかうかどうか
     * @return array
     */
    private static function _sanitaize(array $data, array $rules, bool $auto_filter, bool $filter_closures, bool $is_cache = false) : array {
        $is_found_cache = false;
        if ($is_cache) {
            // キャッシュを探す
            foreach (self::$_data_cache as $key => $cache) {
                if ($data === $cache && isset(self::$_data_sanitize_cache[$key])) {
                    $cache_key = $key;
                    $is_found_cache = true;
                    break;
                }
            }
            if (!$is_found_cache) {
                // キャッシュが無い場合保存する
                $cache_key = count(self::$_data_cache);
                self::$_data_cache[$cache_key] = $data;
            }
        }

        $result = [];
        foreach ($data as $key => $value) {
            $filter = array_key_exists($key, $rules) ? $rules[$key] : null;
            $filter = is_null($filter) ? $auto_filter : $filter;

            if ($filter) {
                if ($is_found_cache && isset(self::$_data_sanitize_cache[$cache_key][$key])) {
                    // キャッシュしたした値(サニタイズ後の値)を取得する
                    $value = self::$_data_sanitize_cache[$cache_key][$key];
                } else {
                    if ($filter_closures and $value instanceOf \Closure) {
                        $value = $value();
                    }
                    $value = \Security::clean($value, null, 'security.output_filter');
                    if ($is_cache) {
                        // サニタイズ後の値をキャッシュする
                        self::$_data_sanitize_cache[$cache_key][$key] = $value;
                    }
                }
            }
            $result[$key] = $value;
        }
        return $result;
    }
}