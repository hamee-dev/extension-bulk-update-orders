<?php
/**
 * キュー登録結果
 *
 * Class Domain_Value_Enqueresult
 */
class Domain_Value_Enqueresult
{
    /**
     * キュー登録の結果
     *
     * @var bool
     */
    private $_result;

    /**
     * キュー登録した際のModel_Executionbulkupdatesettingオブジェクト
     *
     * @var null|Model_Executionbulkupdatesetting
     */
    private $_execution_bulkupdate_setting;

    /**
     * キュー登録時のメッセージ
     *
     * @var null|string
     */
    private $_message;

    /**
     * Domain_Value_Enqueresult constructor.
     * @param bool $result キュー登録の結果
     * @param null|Model_Executionbulkupdatesetting $execution_bulkupdate_setting キュー登録した際のModel_Executionbulkupdatesettingオブジェクト
     * @param null|string $message キュー登録時のメッセージ
     */
    public function __construct(bool $result, ?Model_Executionbulkupdatesetting $execution_bulkupdate_setting, ?string $message = null) {
        $this->_result = $result;
        $this->_execution_bulkupdate_setting = $execution_bulkupdate_setting;
        $this->_message = $message;
    }

    /**
     * キュー登録の結果
     *
     * @return bool
     */
    public function get_result() : bool {
        return $this->_result;
    }

    /**
     * キュー登録した際のModel_Executionbulkupdatesettingオブジェクト
     *
     * @return null|Model_Executionbulkupdatesetting
     */
    public function get_execution_bulkupdate_setting() : ?Model_Executionbulkupdatesetting {
        return $this->_execution_bulkupdate_setting;
    }

    /**
     * キュー登録時のメッセージ
     *
     * @return null|string
     */
    public function get_message() : ?string {
        return $this->_message;
    }

    /**
     * 画面表示用のキュー登録完了メッセージを取得する
     *
     * @return array
     */
    public function get_done_message() : array {
        return [
            '<h1 id="message_title"><span class="glyphicon glyphicon-ok icon"></span>実行を開始しました</h1>',
            '<div class="font_bold message_contents">※更新の実行には約5~10分ほどかかる場合があります</div>',
            '<div class="message_contents">タスクID: ' . $this->_execution_bulkupdate_setting->request_key . '</div>',
            '<div class="message_contents">対象伝票数: ' . $this->_execution_bulkupdate_setting->target_order_count . '件</div>',
        ];
    }
}