<?php
/**
 * Domain_Model_Updatesetting::executionの結果のオブジェクト
 */
class Domain_Value_Executionresult {
    // 一括更新後のresponse
    private $_response;
    // 実際に一括更新に送った件数（除外設定適用後の値、実行通知の件数表示などで使用）
    private $_sent_count;
    // 除外された伝票番号とその理由（実行通知の表示などで使用）
    private $_excluded_id_and_reason;

    /**
     * @param array $response
     * @param int $sent_count
     * @param array $excluded_id_and_reason
     */
    public function __construct(array $response, int $sent_count, array $excluded_id_and_reason){
        $this->_response               = $response;
        $this->_sent_count             = $sent_count;
        $this->_excluded_id_and_reason = $excluded_id_and_reason;
    }

    /**
     * @return array
     */
    public function get_response() : array {
        return $this->_response;
    }

    /**
     * @return int
     */
    public function get_sent_count() : int {
        return $this->_sent_count;
    }

    /**
     * @return array
     */
    public function get_excluded_id_and_reason() : array {
        return $this->_excluded_id_and_reason;
    }
}