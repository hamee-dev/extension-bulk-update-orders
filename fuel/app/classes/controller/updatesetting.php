<?php
/**
 * 更新設定画面のコントローラー
 *
 * Class Controller_Updatesetting
 */
class Controller_Updatesetting extends Controller_Basic
{
    /**
     * indexにアクセスした場合はTOPに遷移する
     */
    public function get_index() {
        $this->redirect('/top');
    }

    /**
     * 更新設定の新規作成画面
     */
    public function get_new() {

        $bulk_update_setting_id = \Input::get(BULK_UPDATE_SETTING_ID);

        if (is_null($bulk_update_setting_id)) {
            // 画面表示用の共通情報設定
            $this->_set_template_common_vars($bulk_update_setting_id, new Model_Bulkupdatesetting());
        } else {
            // 画面に表示する一括更新設定の情報を設定
            $this->_set_bulk_update_setting($bulk_update_setting_id);
        }

        $this->display();
    }

    /**
     * 更新設定の編集画面
     */
    public function get_edit() {

        $bulk_update_setting_id = \Input::get(BULK_UPDATE_SETTING_ID);
        if (is_null($bulk_update_setting_id)) {
            self::set_error_messages(__em('bulk_update_setting_id_empty'));
            $this->redirect('/error/400');
        }

        // 画面に表示する一括更新設定の情報を設定
        $this->_set_bulk_update_setting($bulk_update_setting_id);
        $this->display();
    }

    /**
     * 更新設定の保存
     */
    public function post_save() {
        $execute_id = $this->_validation_and_save(\Input::post(), false);
        if ($execute_id !== '0') {
            self::set_success_messages(__sm('save'));
            $this->redirect('/updatesetting/edit?' . BULK_UPDATE_SETTING_ID . '=' . $execute_id);
        }
    }

    /**
     * 実行画面へ遷移するためのバリデーションと一時保存を行う
     */
    public function post_execution() {

        if ($this->execution_method !== EXECUTION_METHOD_EXTENSION) {
            self::set_error_messages(__em('function_execute'));
            $this->redirect('/error/400');
        }

        $execute_id = $this->_validation_and_save(\Input::post(), true);
        if ($execute_id !== '0') {

            $bulk_update_setting = Model_Bulkupdatesetting::get_setting($this->company_id, $execute_id);

            $trasition_path = '/updatesetting';
            if(is_null($bulk_update_setting->original_bulk_update_setting_id)) {
                // 新規作成の場合
                $trasition_path .= '/new';
            } else {
                // 登録済みの場合
                $trasition_path .= '/edit';
            }
            $trasition_path .= '?' . http_build_query([BULK_UPDATE_SETTING_ID => $execute_id]);

            $add_request_params = [
                BULK_UPDATE_SETTING_ID => $execute_id,
                TRANSITION_PATH => $trasition_path
            ];

            $this->redirect('/preview?' . http_build_query($add_request_params));
        }
    }

    /**
     * 設定のバリデーションの実行と保存を行う
     *
     * @param array $post_params POSTされたパラメータ
     * @param bool $is_temporary 一時保存かどうか(保存時のtemporaryを0にするか1にするか) true:一時保存(temporary=1)/false:一時保存ではない(temporary=0)
     * @return string 保存した設定のid バリデーションエラーがあった場合は0が返る
     * @throws FuelException
     */
    private function _validation_and_save(array $post_params, bool $is_temporary = false) : string {

        $execute_id = '0';

        // 初期状態（項目を選択）の項目もしくは、発送関連の項目がある場合は取り除く
        foreach ($post_params[Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME] as $index => $column_id) {
            if (empty($column_id) || $column_id === Domain_Model_Updatesetting::SELECT_COLUMN_DELIVERY_VALUE) {
                unset($post_params[Domain_Model_Updatesetting::SELECT_COLUMN_ELEMENT_NAME][$index]);
            }
        }

        // バリデーションの実行
        if ($is_temporary) {
            $valid_response = Domain_Validator_Updatesetting::run_temporary($post_params, $this->company_id, $this->user_id);
        }else{
            $valid_response = Domain_Validator_Updatesetting::run($post_params, $this->company_id, $this->user_id);
        }

        if (!$valid_response['result']) {
            // バリデーションエラー

            // バリデーションエラーがあった場合の画面再描画用のオブジェクトを取得する
            $setting = Domain_Model_Updatesetting::get_setting_for_validation_error($this->company_id, $this->user_id, $post_params);

            // エラー画面表示
            $this->_error_display($setting, $valid_response['messages']);

        }else{
            // 登録処理
            $execute_id = Domain_Model_Updatesetting::save($this->company_id, $this->user_id, $post_params, $is_temporary);
        }

        return $execute_id;
    }

    /**
     * エラー時の画面表示
     *
     * @param Model_Bulkupdatesetting $bulk_update_setting 一括更新設定オブジェクト
     * @param array|string $error_messages エラーメッセージ
     */
    private function _error_display(Model_Bulkupdatesetting $bulk_update_setting, $error_messages) {
        $bulk_update_setting_id = \Input::post(BULK_UPDATE_SETTING_ID);
        // 画面表示用の共通情報設定
        $this->_set_template_common_vars($bulk_update_setting_id, $bulk_update_setting);
        // バリデーションエラーメッセージの設定
        $this->template->error_messages = $error_messages;
        // $bulk_update_setting_idがあれば編集画面、なければ新規作成画面を表示する
        $this->display(!empty($bulk_update_setting_id) ? 'edit' : 'new');
    }

    /**
     * 画面に表示する一括更新設定の情報を設定する
     *
     * @param string $bulk_update_setting_id 一括更新設定ID
     */
    private function _set_bulk_update_setting(string $bulk_update_setting_id) {
        // 更新設定取得
        $setting = Model_Bulkupdatesetting::get_setting($this->company_id, $bulk_update_setting_id);

        if (is_null($setting)) {
            self::set_error_messages(__em('bulk_update_setting_empty'));
            $this->redirect('/error/400');
        }

        if($setting->temporary) {
            // プレビュー時の一時的な一括更新設定の場合

            // 登録済みの一括更新設定IDを画面に渡す
            $bulk_update_setting_id = $setting->original_bulk_update_setting_id;
            // プレビュー時の一時的な一括更新設定の削除
            // NOTE: このモデルは論理削除のモデルだがこのレコードに関しては物理削除する
            Domain_Model_Updatesetting::hard_delete($this->company_id, $setting->id);
        }

        // 画面表示用の共通情報設定
        $this->_set_template_common_vars($bulk_update_setting_id, $setting);
    }

    /**
     * 画面に表示する共通情報を設定する
     *
     * @param string $bulk_update_setting_id 一括更新設定ID
     * @param Model_Bulkupdatesetting $bulk_update_setting 一括更新設定オブジェクト
     */
    private function _set_template_common_vars(?string $bulk_update_setting_id, Model_Bulkupdatesetting $bulk_update_setting) {
        // 画面表示用の情報設定
        $this->template->set_global(BULK_UPDATE_SETTING_ID, $bulk_update_setting_id);
        $this->template->set_global('setting', $bulk_update_setting);
        // 「登録されている一括更新設定の内容」と「プレビュー時の一時的な一括更新設定の内容」が異なるかを判定情報
        // 設定オブジェクトがviewに渡る前に（サニタイズされるとmodelクラスのis_changedがtrueになってしまうから）情報取得して設定
        $this->template->set_global('is_different_original', $bulk_update_setting->is_different_original());
    }

    /**
     * 更新設定削除
     */
    public function post_delete(){
        try {
            $bulk_update_setting_id = \Input::post(BULK_UPDATE_SETTING_ID);
            Domain_Model_Updatesetting::delete($this->company_id, $bulk_update_setting_id);
            self::set_success_messages(__sm('delete'));
            $this->redirect('/top');
        } catch(Exception $e){
            // 削除処理に失敗した場合画面にはエラーメッセージを出して例外を握りつぶす
            \Log::exception($e);
            self::set_error_messages(__em('delete'));
            $this->redirect('/top');
        }
    }

    /**
     * 更新設定複製
     */
    public function post_copy() {
        $bulk_update_setting_id = \Input::post(BULK_UPDATE_SETTING_ID);
        $name = \Input::post('modal_text');

        $valid_response = Domain_Validator_Updatesetting::run_copy($this->company_id, $name);
        if ($valid_response['result']) {
            // 複製処理
            [$result, $message] = Domain_Model_Updatesetting::copy($this->company_id, $this->user_id, $bulk_update_setting_id, $name);
            if($result){
                self::set_success_messages($message);
            } else {
                self::set_error_messages($message);
            }

        } else {
            // バリデーションエラー
            if(isset($valid_response['messages'])){
                self::set_error_messages($valid_response['messages']);
            } else {
                // バリデーションエラーメッセージが入ってない場合は汎用メッセージを出す
                self::set_error_messages(__em('copy'));
            }
        }

        $this->redirect('/top');
    }

    /**
     * 設定名称変更
     */
    public function post_updatename(){
        $bulk_update_setting_id = \Input::post(BULK_UPDATE_SETTING_ID);
        $name = \Input::post('modal_text');

        $valid_response = Domain_Validator_Updatesetting::run_updatename($this->company_id, $name, $bulk_update_setting_id);
        if ($valid_response['result']) {
            // 設定名称変更処理
            [$result, $message] = Domain_Model_Updatesetting::update_name($this->company_id, $this->user_id, $bulk_update_setting_id, $name);
            if($result){
                self::set_success_messages($message);
            } else {
                self::set_error_messages($message);
            }

        } else {
            // バリデーションエラー
            if(isset($valid_response['messages']['name'])){
                self::set_error_messages($valid_response['messages']['name']);
            } else {
                // nameのバリデーションしかかけていないはずだがもしnameに対するバリデーションエラーメッセージが入ってない場合は汎用メッセージを出す
                self::set_error_messages(__em('name_update'));
            }
        }

        $this->redirect('/top');
    }
}