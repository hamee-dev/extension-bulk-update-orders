<?php
/**
 * Domain_Model_Updatesetting::convertの結果のオブジェクト
 */
class Domain_Value_Convertresult {
    // 変換処理を適用した結果の更新対象伝票一覧
    private $_update_target_orders;
    // 除外処理を適用した結果の除外伝票一覧
    private $_excluded_id_and_reason;

    /**
     * @param array $update_target_orders
     * @param array $excluded_id_and_reason
     */
    public function __construct(array $update_target_orders, array $excluded_id_and_reason){
        $this->_update_target_orders   = $update_target_orders;
        $this->_excluded_id_and_reason = $excluded_id_and_reason;
    }

    /**
     * @return array
     */
    public function get_update_target_orders() : array {
        return $this->_update_target_orders;
    }

    /**
     * @return array
     */
    public function get_excluded_id_and_reason() : array {
        return $this->_excluded_id_and_reason;
    }
}