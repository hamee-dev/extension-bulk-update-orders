<?php
/**
 * Controller_Templateを拡張した基本コントローラー
 */
abstract class Controller_Hybridbase extends Controller_Hybrid
{
    /**
     * 実行か設定か
     *
     * @var string
     */
    protected $execution_method = null;

    /**
     * 実行ID
     *
     * @var int
     */
    protected $extension_execution_id = null;

    /**
     * get/postパラメータのexecution_methodとextension_execution_idをプロパティに設定する
     */
    public function before(){
        parent::before();

        $this->execution_method = \Input::param(PARAM_EXECUTION_METHOD);
        $this->extension_execution_id = \Input::param(PARAM_EXTENSION_EXECUTION_ID);
    }

    /**
     * 成功メッセージをフラッシュセッションとして設定する
     *
     * @param string|array $message
     */
    protected static function set_success_messages($message) {
        if (!is_array($message)) {
            $message = [$message];
        }
        \Session::set_flash(\Config::get('session.flash_keys.SUCCESS_MESSAGE'), $message);
    }

    /**
     * 失敗メッセージをフラッシュセッションとして設定する
     *
     * @param string|array $message
     */
    protected static function set_error_messages($message) {
        if (!is_array($message)) {
            $message = [$message];
        }
        \Session::set_flash(\Config::get('session.flash_keys.ERROR_MESSAGE'), $message);
    }

    /**
     * フラッシュセッションから成功メッセージを取得する
     *
     * @return string|array
     */
    protected static function get_success_messages() {
        return \Session::get_flash(\Config::get('session.flash_keys.SUCCESS_MESSAGE'));
    }

    /**
     * フラッシュセッションから失敗メッセージを取得する
     *
     * @return string|array
     */
    protected static function get_error_messages() {
        return \Session::get_flash(\Config::get('session.flash_keys.ERROR_MESSAGE'));
    }

    /**
     * リダイレクトを実行する
     * urlに自動的にexecution_methodとextension_execution_idを追加する
     *
     * @param string $path
     */
    protected function redirect(string $path) {

        if (!is_null($this->execution_method)) {
            $add_request_params = [
                PARAM_EXECUTION_METHOD => $this->execution_method,
                \Config::get('security.csrf_token_key') => \Security::fetch_token(),
            ];

            if (!is_null($this->extension_execution_id) && $this->execution_method === EXECUTION_METHOD_EXTENSION) {
                $add_request_params[PARAM_EXTENSION_EXECUTION_ID] = $this->extension_execution_id;
            }
            if (strpos($path, '?') === false) {
                $path .= '?';
            }else{
                $path .= '&';
            }
            $path .= http_build_query($add_request_params);
        }

        Log::notice_ex('start redirect.',
            ['original_url' => Uri::current(), 'original_request_params' =>  \Input::all(), 'redirect_url' => $path]);

        \Response::redirect($path);
    }
}