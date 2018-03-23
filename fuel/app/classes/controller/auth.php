<?php

/**
 * アプリの起動チェックを行うコントローラー
 *
 * Class Controller_Auth
 */
class Controller_Auth extends Controller_Hybridbase
{

    /**
     * アプリの起動URL
     * GETパラメータをチェックして、ログイン情報の保存、TOPへのリダイレクトを行う
     *
     * @throws Exception
     * @throws FuelException
     */
    public function get_index() {
        // パラメータが正しいかチェック
        if(is_null(\Input::get('uid')) ||
            is_null(\Input::get('state')) ||
            is_null($this->execution_method) ||
            ($this->execution_method === EXECUTION_METHOD_EXTENSION && is_null($this->extension_execution_id))) {
            self::set_error_messages(__em('params'));
            $this->redirect('/error/400');
        }

        $before_company_id = \Session::get(\Config::get('session.keys.ACCOUNT_COMPANY'));
        $before_user_id = \Session::get(\Config::get('session.keys.ACCOUNT_USER'));

        // ログイン情報（企業情報とユーザー情報）を保存する
        $auth_model = new Domain_Model_Auth();
        $auth_model->set_login_info();

        // csrfトークンをセッションで固定にする
        if ($before_company_id !== \Session::get(\Config::get('session.keys.ACCOUNT_COMPANY')) ||
            $before_user_id !== \Session::get(\Config::get('session.keys.ACCOUNT_USER'))) {
            // 企業IDとユーザーIDが変わった場合は、セッションでユニークなトークン生成用のソルトをセッションに保存する
            $salt = \Session::get(\Config::get('session.keys.ACCOUNT_COMPANY')) . '_' . \Session::get(\Config::get('session.keys.ACCOUNT_USER')) . '_' . random_bytes(64);
            \Session::set(\Config::get('session.keys.CSRF_TOKEN_SALT'), $salt);
        }

        $this->redirect('/top');
    }

    /**
     * ヘルスチェック用URL
     *
     * @throws FuelException
     */
    public function get_health() {
        Model_User::find('first', ['select' => 'id']);
        $this->template = null;
        echo 'OK';
    }
}