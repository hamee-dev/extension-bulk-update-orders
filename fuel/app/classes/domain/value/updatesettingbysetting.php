<?php

/**
 * 設定情報から取得した更新設定画面生成用のデータ
 *
 * Class Domain_Value_Updatesettingbysetting
 */
class Domain_Value_Updatesettingbysetting
{
    /**
     * テンプレートファイル情報の配列
     *
     * @var array
     */
    private $_template_file_list;

    /**
     * 更新項目の注意文言テンプレートファイル情報の配列
     *
     * @var array
     */
    private $_caution_template_file_list;

    /**
     * 設定しているマスタデータの配列
     *
     * @var array
     */
    private $_master_options_list;

    /**
     * 発送方法タイプ別区分の配列
     *
     * @var array
     */
    private $_forwarding_agent_options;

    /**
     * 受注分類タグの配列
     *
     * @var array
     */
    private $_tag_list;

    /**
     * 伝票に関する高度な更新設定が初期表示で開いているか
     *
     * @var bool
     */
    private $_is_open_option;

    /**
     * Domain_Value_Updatesettingbysetting constructor.
     * @param array $template_file_list テンプレートファイル情報の配列
     * @param array $caution_template_file_list 更新項目の注意文言テンプレートファイル情報の配列
     * @param array $master_options_list 設定しているマスタデータの配列
     * @param array $forwarding_agent_options 発送方法タイプ別区分の配列
     * @param array $tag_list 受注分類タグの配列
     * @param bool $is_open_option 伝票に関する高度な更新設定が初期表示で開いているか
     */
    public function __construct(
        array $template_file_list,
        array $caution_template_file_list,
        array $master_options_list,
        array $forwarding_agent_options,
        array $tag_list,
        bool $is_open_option
    ) {
        $this->_template_file_list = $template_file_list;
        $this->_caution_template_file_list = $caution_template_file_list;
        $this->_master_options_list = $master_options_list;
        $this->_forwarding_agent_options = $forwarding_agent_options;
        $this->_tag_list = $tag_list;
        $this->_is_open_option = $is_open_option;
    }

    /**
     * テンプレートファイル情報の配列
     *
     * @return array
     */
    public function get_template_file_list() : array {
        return $this->_template_file_list;
    }

    /**
     * 更新項目の注意文言テンプレートファイル情報の配列
     *
     * @return array
     */
    public function get_caution_template_file_list() : array {
        return $this->_caution_template_file_list;
    }

    /**
     * 設定しているマスタデータの配列
     *
     * @return array
     */
    public function get_master_options_list() : array {
        return $this->_master_options_list;
    }

    /**
     * 発送方法タイプ別区分の配列
     *
     * @return array
     */
    public function get_forwarding_agent_options() : array {
        return $this->_forwarding_agent_options;
    }

    /**
     * 受注分類タグの配列
     *
     * @return array
     */
    public function get_tag_list() : array {
        return $this->_tag_list;
    }

    /**
     * 伝票に関する高度な更新設定が初期表示で開いているか
     *
     * @return bool
     */
    public function is_open_option() : bool {
        return $this->_is_open_option;
    }
}