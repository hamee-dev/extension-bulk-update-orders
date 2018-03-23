<?php
namespace Fuel\Tasks;

class Company{
    /**
     * 解約した企業を検索し無効化する
     * 実行方法: php oil r company:disable_canceled_company
     */
    public function disable_canceled_company(){
        try {
            \Log::info_ex('企業解約処理を開始します');

            $canceled_companies = $this->_get_canceled_companies();
            if(empty($canceled_companies)) {
                \Log::info_ex('解約した企業は0社です。企業解約処理を終了します。');
                return;
            }

            $stoped_at = $this->get_date_now();
            foreach($canceled_companies as $company) {
                \Log::info_ex('解約されているためこの企業を無効な企業にします', ['company_id' => $company->id]);
                // 停止日時に現在の日時を入れる
                $company->stoped_at = $stoped_at;
                $company->save();
            }

            \Log::info_ex('企業解約処理を終了します', ['canceled_companies_count' => count($canceled_companies)]);
        } catch (\Exception $e) {
            \Log::info_ex('企業解約処理にて異常が発生しました');
            \Log::exception($e);
        }
    }

    /**
     * @return Client_Neapi
     */
    protected function get_client_neapi() : \Client_Neapi {
        return new \Client_Neapi(null, true);
    }

    /**
     * @return string
     */
    protected function get_date_now() : string {
        return date("Y-m-d H:i:s");
    }

    /**
     * アプリを解約した企業を取得する
     *
     * @return array
     */
    private function _get_canceled_companies() : array {
        $contracted_main_function_ids = $this->_get_contracted_main_function_ids();
        $companies = \Model_Company::findAll(['stoped_at' => null]);
        $main_function_ids = \Arr::pluck($companies, 'main_function_id');

        // 解約した企業を探す(第2引数の配列にない企業IDを探す)
        $canceled_company_ids = array_diff($main_function_ids, $contracted_main_function_ids);
        \Log::info_ex('解約企業ID一覧', ['canceled_company_ids' => $canceled_company_ids]);

        $canceled_companies = [];
        if (count($canceled_company_ids) > 0) {
            $canceled_companies = \Model_Company::query()
                ->where('main_function_id', 'in', $canceled_company_ids)
                ->get();
        }

        return $canceled_companies;
    }

    /**
     * アプリを利用している企業IDを取得する
     *
     * @return array NEAPIのフィールドのcompany_idの配列、アプリ側ではmain_function_id
     * @throws UnexpectedValueException
     */
    private function _get_contracted_main_function_ids() : array {
        $client_neapi = $this->get_client_neapi();
        // アプリ利用企業一覧を取得
        $response = $client_neapi->apiExecuteNoRequiredLogin(\Client_Neapi::PATH_CONTRACTED_COMPANIES_GET);
        // APIの実行に失敗した場合は例外を投げる
        if($response['result'] !== \Client_Neapi::RESULT_SUCCESS || !isset($response['data'])){
            \Log::info_ex('アプリ利用企業一覧の取得に失敗しました', ['API_response' => $response]);
            throw new \UnexpectedValueException('アプリ利用企業一覧の取得に失敗しました');
        }

        // 解約はメイン機能単位で判定
        return array_column($response['data'], 'company_id');
    }
}
