<?php
/**
 * プレビュー画面
 *
 * Class Controller_Preview
 */
class Controller_Preview extends Controller_Basic
{
    // プレビュー画面は実行する場合のみ表示することができる
    protected $permissions = [EXECUTION_METHOD_EXTENSION];

    public function before()
    {
        parent::before();
        $request_params = \Input::all();
        if (!isset($request_params[BULK_UPDATE_SETTING_ID])) {
            self::set_error_messages(__em('bulk_update_setting_id_empty'));
            $this->redirect('/error/400');
        }
    }

    /**
     * プレビュー画面
     */
    public function get_index() {
        $this->_set_view_params(
            \Input::get(BULK_UPDATE_SETTING_ID),
            \Input::get(TRANSITION_PATH)
        );
        $this->display();
    }

    /**
     * キュー登録
     */
    public function post_execution() {
        $bulk_update_setting_id = \Input::post(BULK_UPDATE_SETTING_ID);
        $transition_path = \Input::post(TRANSITION_PATH);
        $exclude_orders = \Input::post('exclude_orders', []);

        // バリデーションできる形で取得する
        $params = Model_Bulkupdatesetting::get_validation_params_by_bulk_update_setting_id($this->company_id, $bulk_update_setting_id);

        // バリデーションの実行
        $valid_response = Domain_Validator_Updatesetting::run_execute($params, $this->company_id, $this->user_id);

        if ($valid_response['result'] === true) {
            // 登録処理
            $domain_model_updatesetting = new Domain_Model_Updatesetting();
            $domain_value_enqueresult = $domain_model_updatesetting->execution_enque($this->extension_execution_id, $this->company_id, $this->user_id, $params, $exclude_orders);
            if ($domain_value_enqueresult->get_result()) {
                self::set_success_messages($domain_value_enqueresult->get_done_message());
                $this->redirect('/tasklist');
            }else{
                $this->_error_display(
                    $bulk_update_setting_id,
                    $transition_path,
                    $domain_value_enqueresult->get_message()
                );
            }
        }else{
            // バリデーションエラー
            $this->_error_display(
                $bulk_update_setting_id,
                $transition_path,
                $valid_response['messages']
            );
        }
    }

    /**
     * 画面表示用の情報をセットする
     *
     * @param string $bulk_update_setting_id 更新設定ID
     * @param string $transition_path 遷移パス
     * @throws Exception
     * @throws FuelException
     */
    private function _set_view_params(string $bulk_update_setting_id, string $transition_path) {
        $this->template->set_global(BULK_UPDATE_SETTING_ID, $bulk_update_setting_id);
        $this->template->set_global(TRANSITION_PATH, $transition_path);
    }

    /**
     * エラーがあった場合の画面表示
     *
     * @param string $bulk_update_setting_id 更新設定ID
     * @param string $transition_path 遷移パス
     * @param array|string $message エラーメッセージ
     */
    private function _error_display(string $bulk_update_setting_id, string $transition_path, $message) {
        $this->_set_view_params($bulk_update_setting_id, $transition_path);
        $this->template->error_messages = is_array($message) ? $message : [$message];
        $this->display('index');
    }
}