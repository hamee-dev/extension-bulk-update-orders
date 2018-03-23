<?php
/**
 * トップページのviewモデル
 *
 * Class Presenter_Top_Index
 */
class Presenter_Top_Index extends Presenter_Base
{
    public function view()
    {
        // 更新設定一覧を取得する
        $this->settings = Model_Bulkupdatesetting::get_settings_for_top($this->company_id);
        $this->settings_count = count($this->settings);

        // 画面に表示する値を取得する
        $master = new Utility_Master($this->company_id, $this->user_id);
        $display_values = [];
        $show_update_methods = [];
        foreach ($this->settings as $setting) {
            $bulk_update_columns = $setting->bulk_update_columns;
            foreach ($bulk_update_columns as $bulk_update_column) {
                $value = Domain_Value_Receiveordercolumn::get_display_value($bulk_update_column->receive_order_column, $master, $bulk_update_columns, $bulk_update_column->update_value);
                $display_values[$bulk_update_column->id] = $value;
                $show_update_methods[$bulk_update_column->id] = Domain_Value_Receiveordercolumn::is_show_update_method($bulk_update_column->receive_order_column->column_type, $value);
            }
        }
        // 内部的にサニタイズ処理を行って意図したタグは画面に出したいのでここはフィルタをoffで画面に渡す
        $this->get_view()->set_global('display_values', $display_values, false);

        // 画面に更新方法を表示するか
        $this->get_view()->set_global('show_update_methods', $show_update_methods);
    }
}