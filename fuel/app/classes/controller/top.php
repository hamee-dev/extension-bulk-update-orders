<?php
/**
 * アプリのトップページのコントローラー
 *
 * Class Controller_Top
 */
class Controller_Top extends Controller_Basic
{

    /**
     * アプリのトップページ
     */
    public function get_index() {

        // プレビュー画面から戻って遷移した場合に、プレビュー時に一時的に生成された一括更新設定を削除する
        $this->_delete_preview_setting();
        $this->display();
    }

    /**
     * プレビュー画面に遷移する
     * その際、バリデーションと保存を行う
     */
    public function post_execution() {

        if ($this->execution_method !== EXECUTION_METHOD_EXTENSION) {
            self::set_error_messages(__em('function_execute'));
            $this->redirect('/error/400');
        }

        if (is_null(\Input::post(BULK_UPDATE_SETTING_ID))) {
            self::set_error_messages(__em('bulk_update_setting_id_empty'));
            $this->redirect('/error/400');
        }

        // 保存している更新設定をバリデーションできる形で取得する
        $params = Model_Bulkupdatesetting::get_validation_params_by_bulk_update_setting_id($this->company_id, \Input::post(BULK_UPDATE_SETTING_ID));

        // バリデーションの実行
        $valid_response = Domain_Validator_Updatesetting::run_temporary($params, $this->company_id, $this->user_id);
        if (!$valid_response['result']) {
            // バリデーションエラー
            self::set_error_messages($valid_response['messages']);
            $this->redirect('/top');
        }else{
            // 登録処理
            $execute_id = Domain_Model_Updatesetting::save($this->company_id, $this->user_id, $params, true);

            $trasition_path = '/top?' . http_build_query([BULK_UPDATE_SETTING_ID => $execute_id]);
            $add_request_params = [
                BULK_UPDATE_SETTING_ID => $execute_id,
                TRANSITION_PATH => $trasition_path
            ];

            $this->redirect('/preview?' . http_build_query($add_request_params));

        }
    }

    /**
     * プレビュー画面から戻って遷移した場合に、プレビュー時に一時的に生成された一括更新設定を削除する
     *
     */
    private function _delete_preview_setting() {
        $bulk_update_setting_id = \Input::get(BULK_UPDATE_SETTING_ID);

        if (!is_null($bulk_update_setting_id)) {

            // 更新設定取得
            $setting = Model_Bulkupdatesetting::get_setting($this->company_id, $bulk_update_setting_id);

            if (!is_null($setting) && $setting->temporary) {
                // プレビュー時の一時的な一括更新設定の場合

                // プレビュー時の一時的な一括更新設定の削除
                // NOTE: このモデルは論理削除のモデルだがこのレコードに関しては物理削除する
                Domain_Model_Updatesetting::hard_delete($this->company_id, $setting->id);
            }
        }
    }

}