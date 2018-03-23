<?php
/**
 * エラー画面のコントローラー
 * ステータスコードごとにアクションを作成
 * デフォルトでは500番が使われる
 *
 * Class Controller_Error
 */
class Controller_Error extends Controller_Hybridbase
{
    /**
     * このコントローラ配下の画面で使用するテンプレートファイル名(.phpは除く)
     * @var string
     */
    public $template = 'template-error';

    /**
     * ステータスコードを変更したい場合は各アクションで上書きする
     * @var int
     */
    private $_status_code;

    /**
     * システムエラー等の画面
     */
    public function get_500()
    {
        $this->_status_code = 500;
        $this->template->title = __em('system_error');
        $this->template->error_messages = self::get_error_messages();
    }

    /**
     * 400エラー
     */
    public function get_400()
    {
        $this->_status_code = 400;
        $this->template->title = __em('system_error');
        $this->template->error_messages = self::get_error_messages();
    }

    /**
     * 404エラー
     */
    public function get_404()
    {
        $this->_status_code = 404;
        $this->template->title = __em('not_found_page');
    }

    /**
     * ステータスコードを変更する
     *
     * @param \Fuel\Core\Response $response
     * @return \Fuel\Core\Response
     */
    public function after($response)
    {
        $response = parent::after($response);
        $response->status = $this->_status_code;
        return $response;
    }
}