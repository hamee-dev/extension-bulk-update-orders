<?php

/**
 * アプリ起動時に実行されるロジッククラス
 *
 * Class Domain_Model_Auth
 */
class Domain_Model_Auth
{
    /**
     * APIクライアントオブジェクト
     * @var Client_Neapi
     */
    private $_client_neapi = null;

    /**
     * Domain_Model_Auth constructor.
     */
    public function __construct()
    {
        $this->_client_neapi = new Client_Neapi();
    }

    /**
     * ログイン情報（企業情報とユーザー情報）を設定する
     *
     * @throws Exception
     * @throws FuelException
     */
    public function set_login_info() {
        // 企業情報とユーザー情報をAPIから取得する
        $company_info = $this->_fetch_company_info();
        $user_info = $this->_fetch_user_info();

        try {
            \DB::start_transaction();

            // 企業情報の保存（見つかれば上書きし、なければ作成する）
            $company = $this->_create_company($company_info);

            // ユーザー情報の保存（見つかれば上書きし、なければ作成する）
            $user = $this->_create_user($user_info, $company->id, $user_info);

            \DB::commit_transaction();
        } catch (Exception $e) {
            \Log::exception($e);
            \DB::rollback_transaction();
            throw $e;
        }

        // セッションに保存する
        \Session::set(\Config::get('session.keys.ACCOUNT_COMPANY'), $company->id);
        \Session::set(\Config::get('session.keys.ACCOUNT_USER'), $user->id);
    }

    /**
     * 企業情報の保存し、そのオブジェクトを返す
     * 見つかれば上書きし、なければ作成する
     *
     * @param array $company_info
     * @return Model_Company
     * @throws Exception
     * @throws FuelException
     */
    private function _create_company(array $company_info) : Model_Company {
        $company = \Model_Company::findOne([['main_function_id', $company_info['company_id']]]) ?: new \Model_Company();
        $company->main_function_id  = $company_info['company_id'];
        $company->company_ne_id     = $company_info['company_ne_id'];
        $company->name              = $company_info['company_name'];
        $company->name_kana         = $company_info['company_kana'];
        // 新規作成時は停止日時null
        // 更新時ここの処理を通るということはAPIの認証を通っている=契約状態なので停止日時をnullにする
        $company->stoped_at         = null;
        $company->save();

        return $company;
    }

    /**
     * ユーザー情報の保存し、そのオブジェクトを返す
     * 見つかれば上書きし、なければ作成する
     *
     * @param array $user_info
     * @param string $company_id
     * @return Model_User
     * @throws Exception
     * @throws FuelException
     */
    private function _create_user(array $user_info, string $company_id) : Model_User{
        $access_token_end_date = $user_info['access_token_end_date'];
        $refresh_token_end_date = $user_info['refresh_token_end_date'];
        $user_info = $user_info['data'][0];

        $user = \Model_User::findOne([['uid', $user_info['uid']]]) ?: new \Model_User();
        $user->company_id               = $company_id;
        $user->uid                      = $user_info['uid'];
        $user->pic_id                   = $user_info['pic_id'];
        $user->pic_ne_id                = $user_info['pic_ne_id'];
        $user->pic_name                 = $user_info['pic_name'];
        $user->pic_kana                 = $user_info['pic_kana'];
        $user->access_token             = $this->_client_neapi->_access_token;
        $user->access_token_end_date    = $access_token_end_date;
        $user->refresh_token            = $this->_client_neapi->_refresh_token;
        $user->refresh_token_end_date   = $refresh_token_end_date;
        $user->save();

        return $user;
    }

    /**
     * NE APIからユーザ情報を取得するユーティリティ
     *
     * @return array 企業情報の連想配列。参照：https://developer.next-engine.com/api/api_v1_login_company/info
     * @throws UnexpectedValueException
     */
    private function _fetch_company_info() : array {
        $company_info = $this->_client_neapi->apiExecute(Client_Neapi::PATH_LOGIN_COMPANY_INFO);
        if (!isset($company_info['data'][0])) {
            throw new UnexpectedValueException('企業情報の取得に失敗しました');
        }
        return $company_info['data'][0];
    }

    /**
     * NE APIからユーザ情報を取得するユーティリティ
     *
     * @return array ユーザ情報の連想配列。参照：https://developer.next-engine.com/api/api_v1_login_user/info
     * @throws UnexpectedValueException
     */
    private function _fetch_user_info() : array
    {
        $user_info = $this->_client_neapi->apiExecute(Client_Neapi::PATH_LOGIN_USER_INFO);
        if (!isset($user_info['data'][0])) {
            throw new UnexpectedValueException('ユーザー情報の取得に失敗しました');
        }
        return $user_info;
    }
}