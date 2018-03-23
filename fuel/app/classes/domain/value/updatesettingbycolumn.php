<?php
/**
 * カラム情報から取得した画面生成用のデータ
 *
 * Class Domain_Value_Updatesettingbycolumn
 */
class Domain_Value_Updatesettingbycolumn
{
    /**
     * 全カラム情報の配列
     *
     * @var array
     */
    private $_columns;

    /**
     * 各カラムごとの更新方法の配列
     *
     * @var array
     */
    private $_update_mehod_options_list;

    /**
     * 発送関連情報のタイプ情報の配列
     *
     * @var array
     */
    private $_delivery_column_ids;

    /**
     * 支払関連情報のタイプ情報の配列
     *
     * @var array
     */
    private $_payment_column_ids;

    /**
     * 受注金額関連情報のタイプ情報の配列
     *
     * @var array
     */
    private $_order_amount_column_ids;

    /**
     * 発送方法別タイプの配列
     *
     * @var array
     */
    private $_forwarding_agent_types;

    /**
     * 更新する項目一覧
     *
     * @var array
     */
    private $_target_list;

    /**
     * 発送方法関連の連想配列
     *
     * @var array
     */
    private $_forwarding_agent_column_list;

    /**
     * Domain_Value_Updatesettingbycolumn constructor.
     * @param array $columns 全カラム情報の配列
     * @param array $update_mehod_options_list 各カラムごとの更新方法の配列
     * @param array $delivery_column_ids 発送関連情報のタイプ情報の配列
     * @param array $payment_column_ids 支払関連情報のタイプ情報の配列
     * @param array $order_amount_column_ids 受注金額関連情報のタイプ情報の配列
     * @param array $forwarding_agent_types 発送方法別タイプの配列
     * @param array $target_list 更新する項目一覧
     * @param array $forwarding_agent_column_list 発送方法関連の連想配列
     */
    public function __construct(
        array $columns,
        array $update_mehod_options_list,
        array $delivery_column_ids,
        array $payment_column_ids,
        array $order_amount_column_ids,
        array $forwarding_agent_types,
        array $target_list,
        array $forwarding_agent_column_list
    ) {
        $this->_columns = $columns;
        $this->_update_mehod_options_list = $update_mehod_options_list;
        $this->_delivery_column_ids = $delivery_column_ids;
        $this->_payment_column_ids = $payment_column_ids;
        $this->_order_amount_column_ids = $order_amount_column_ids;
        $this->_forwarding_agent_types = $forwarding_agent_types;
        $this->_target_list = $target_list;
        $this->_forwarding_agent_column_list = $forwarding_agent_column_list;
    }

    /**
     * 全カラム情報の配列
     *
     * @return array
     */
    public function get_columns() : array {
        return $this->_columns;
    }

    /**
     * 各カラムごとの更新方法の配列
     *
     * @return array
     */
    public function get_update_mehod_options_list() : array {
        return $this->_update_mehod_options_list;
    }

    /**
     * 発送関連情報のタイプ情報の配列
     *
     * @return array
     */
    public function get_delivery_column_ids() : array {
        return $this->_delivery_column_ids;
    }

    /**
     * 支払関連情報のタイプ情報の配列
     *
     * @return array
     */
    public function get_payment_column_ids() : array {
        return $this->_payment_column_ids;
    }

    /**
     * 受注金額関連情報のタイプ情報の配列
     *
     * @return array
     */
    public function get_order_amount_column_ids() : array {
        return $this->_order_amount_column_ids;
    }

    /**
     * 発送方法別タイプの配列
     *
     * @return array
     */
    public function get_forwarding_agent_types() : array {
        return $this->_forwarding_agent_types;
    }

    /**
     * 更新する項目一覧
     *
     * @return array
     */
    public function get_target_list() : array {
        return $this->_target_list;
    }

    /**
     * 発送方法関連の連想配列
     *
     * @return array
     */
    public function get_forwarding_agent_column_list() : array {
        return $this->_forwarding_agent_column_list;
    }
}