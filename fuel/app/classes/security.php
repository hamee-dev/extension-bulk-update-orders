<?php

class Security extends \Fuel\Core\Security {

    /**
     * getの場合、csrfトークンのチェックをしないuri
     *
     * @var array
     */
    private static $_check_token_exclusion_uri_for_get_method = ['/', '/auth', '/auth/health', '/error'];


    /**
     * csrfトークンエラーが発生した場合はエラー画面にリダイレクトする
     *
     * @throws Exception
     * @throws FuelException
     */
    public static function _init()
    {
        try {
            parent::_init();
        }catch (SecurityException | HttpBadRequestException $e) {
            // csrfトークンエラーの場合、エラー画面にリダイレクトする
            Log::notice_ex($e->getMessage(), ['url' => Uri::current(), 'request_params' =>  \Input::all()]);
            \Lang::load('message.yml', true); // このタイミングではロードされていない為、ymlを明示的にロードする
            \Session::set_flash(\Config::get('session.flash_keys.ERROR_MESSAGE'), [__em('session_timeout')]);
            Response::redirect('/error/400');
        }catch (Exception $e) {
            \Log::exception($e);
            throw $e;
        }
    }

    /**
     * error画面へのuriかを判定する
     * ex.) true: '/error', '/error/500'
     * ex.) false: '/error/4000'
     *
     * @param string $uri
     * @return bool
     */
    private static function _is_error_uri(string $uri): bool
    {
        return preg_match('/^\/error\/\d{3}\/?|\/error\/?$/', $uri) === 1;
    }

    /**
     * getの場合のcsrfトークンのチェック時に除外uriを考慮する
     *
     * @param null $value
     * @return bool
     */
    public static function check_token($value = null)
    {
        if ((\Input::method() === 'get' || \Input::method() === 'GET')) {
            // csrfトークンのチェック除外uriでなければ親メソッドでチェックを行い、除外uriであればtrueを返す
            if (strpos(Input::uri(), '/') === 0 && // 「/」で始まらないuri(UnitTest)の場合はtrueを返す
                !self::_is_error_uri(Input::uri()) &&
                !in_array(Input::uri(), self::$_check_token_exclusion_uri_for_get_method)) {
                return parent::check_token($value);
            }
            return true;
        }else{
            return parent::check_token($value);
        }
    }

    /**
     * csrfトークンを生成して返す
     * 親クラスとの違いは、セッションが取得できソルトがある場合はそれを使用してセッションでユニークな固定トークンを生成する
     * 無い場合は親メソッドを呼ぶ
     *
     * @return string
     */
    public static function generate_token()
    {
        if (!is_null(\Session::key()) && \Session::get(\Config::get('session.keys.CSRF_TOKEN_SALT'))) {
            $token_base = \Session::get(\Config::get('session.keys.CSRF_TOKEN_SALT')) . \Config::get('security.token_salt');

            if (function_exists('hash_algos'))
            {
                foreach (array('sha512', 'sha384', 'sha256', 'sha224', 'sha1', 'md5') as $hash)
                {
                    if (in_array($hash, hash_algos()))
                    {
                        return hash($hash, $token_base);
                    }
                }
            }

            return md5($token_base);
        }else{
            return parent::generate_token();
        }
    }
}