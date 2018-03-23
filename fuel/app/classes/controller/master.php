<?php
/**
 * マスタデータをajaxで取得するためのコントローラー
 */
class Controller_Master extends Controller_Basic
{
    /**
     * マスタデータを取得する
     *
     * @return ajaxレスポンス
     */
    public function get_data() {
        $master = new Utility_Master($this->company_id, $this->user_id);
        $master_name = Input::get('name');
        $master_list = [];
        foreach ($master->get($master_name) as $index => $master) {
            $master_list[$index] = $master->to_array();
        }
        return $this->response([$master_name => $master_list]);
    }
}