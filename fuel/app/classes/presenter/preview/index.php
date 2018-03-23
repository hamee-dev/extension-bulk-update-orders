<?php
/**
 * プレビュー画面のviewモデル
 *
 * Class Presenter_Preview_Index
 */
class Presenter_Preview_Index extends Presenter_Base
{
    /**
     * 2018/08 現在は伝票1000件 × 設定項目数20件までを担保しています
     *
     * @throws UnexpectedValueException
     */
    public function view() {
        $setting = Model_Bulkupdatesetting::get_setting($this->company_id, $this->{BULK_UPDATE_SETTING_ID}, ['temporary' => '1']);

        // 高度なオプションの警告表示文言
        $this->warning_messages = [];
        if($setting->allow_reflect_order_amount){
            $this->warning_messages[] = __p('warning.setting_option') . __p('warning.allow_reflect_order_amount');
        }
        if($setting->allow_update_shipment_confirmed){
            $this->warning_messages[] = __p('warning.setting_option') . __p('warning.allow_update_shipment_confirmed');
        }
        if($setting->allow_update_yahoo_cancel){
            $this->warning_messages[] = __p('warning.setting_option') . __p('warning.allow_update_yahoo_cancel');
        }
        if($setting->allow_optimistic_lock_update_retry){
            $this->warning_messages[] = __p('warning.setting_option') . __p('warning.allow_optimistic_lock_update_retry');
        }

        // 現在の伝票の状態を取得する
        $client_neapi = new \Client_Neapi($this->user_id);
        $search_result = Domain_Model_Updatesetting::request_receiveorder_search($client_neapi, $this->extension_execution_id);
        if ($search_result['result'] !== \Client_Neapi::RESULT_SUCCESS){
            throw new UnexpectedValueException(__em('receive_order_search_error') . __em('please_redo'));
        }
        if ($search_result['count'] <= 0){
            throw new UnexpectedValueException(__em('receive_order_search_empty') . __em('please_redo'));
        }
        // 先頭100件を取り出す
        $receive_order_list_initial = array_slice($search_result['data'], 0, Domain_Value_Receiveordercolumn::PREVIEW_DISPLAY_INITIAL_MAX_ORDER_COUNT);
        // 100件以降はjsonに仕込み「さらに表示」ボタンで動的に描画する
        $receive_order_list_other   = array_slice($search_result['data'], Domain_Value_Receiveordercolumn::PREVIEW_DISPLAY_INITIAL_MAX_ORDER_COUNT);
        // 残りxxx件に表示する値をviewに渡す
        $other_count = count($receive_order_list_other);
        $this->get_view()->set_global('other_count', $other_count);

        // マスタ取得用オブジェクト
        $master = new Utility_Master($this->company_id, $this->user_id);
        // 描画用配列を取得しviewに渡す
        $domain_model_update_setting = new Domain_Model_Updatesetting();
        $domain_value_convert_result = $domain_model_update_setting->convert($setting, $receive_order_list_initial);

        // viewに渡す更新設定はシステム更新値も含めた更新設定にする
        // 例えば手数料を更新して総合計に反映していれば総合計も表示するため
        $bulk_update_columns = Domain_Model_Preview::get_bulk_update_columns($domain_value_convert_result, $setting);
        $receive_order_columns = [];
        foreach($bulk_update_columns as $bulk_update_column){
            $receive_order_columns[] = $bulk_update_column->receive_order_column->to_array();
        }
        $this->get_view()->set_global('receive_order_columns', $receive_order_columns);

        $display_values = Domain_Model_Preview::get_display_value($domain_value_convert_result, $receive_order_list_initial, $bulk_update_columns, $master);
        // 内部的にサニタイズ処理を行って意図したタグは画面に出したいのでここはフィルタをoffで画面に渡す
        $this->get_view()->set_global('display_values', $display_values, false);

        // 「さらに表示」用の情報を画面に渡しておく
        $domain_value_convert_result_other = $domain_model_update_setting->convert($setting, $receive_order_list_other);
        $display_values_other = Domain_Model_Preview::get_display_value($domain_value_convert_result_other, $receive_order_list_other, $bulk_update_columns, $master);
        $this->get_view()->set_global('display_values_other', $display_values_other, false);

        // 実行時の注意事項
        $execute_cautions = [];
        if($setting->is_selected_order_amount()) {
            // 更新項目に受注金額関連が含む場合に注意事項を設定する
            $execute_cautions[] = __p('execute_caution.order_amount');
        }
        $this->get_view()->set_global('execute_cautions', json_encode($execute_cautions));
    }
}