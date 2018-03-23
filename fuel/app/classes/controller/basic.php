<?php
/**
 * アプリログイン後の画面の基本コントローラー
 *
 * Class Controller_Basic
 */
abstract class Controller_Basic extends Controller_Hybridbase
{
    /**
     * 画面ヘッダーに表示するアプリアイコンのファイル名
     */
    const HEADER_APP_ICON_NAME = 'header_app_icon.png';

    /**
     * この画面を実行、設定どちらの場合に表示するか
     * 実行の場合だけ、設定の場合だけにしたい場合はオーバーライドしてください
     *
     * @var array
     */
    protected $permissions = [EXECUTION_METHOD_EXTENSION, EXECUTION_METHOD_CONFIG];

    /**
     * 現在ログインしている企業IDを保持する
     *
     * @var int
     */
    protected $company_id = null;

    /**
     * 現在ログインしているユーザIDを保持する
     *
     * @var int
     */
    protected $user_id = null;

    /**
     * アプリを表示できるかチェックし、各種プロパティをセットする
     */
    public function before(){

        parent::before();

        // 企業ID、ユーザーIDがセッションにあるか、親のプロパティexecution_method、extension_execution_idに値がセットされているかをチェックする
        $this->company_id = \Session::get(\Config::get('session.keys.ACCOUNT_COMPANY'));
        $this->user_id    = \Session::get(\Config::get('session.keys.ACCOUNT_USER'));

        $is_error = false;
        if (is_null($this->company_id) || is_null($this->user_id)) {
            // セッション切れ
            self::set_error_messages(__em('session_timeout'));
            $is_error = true;
        }else if (is_null($this->execution_method) ||
            ($this->execution_method === EXECUTION_METHOD_EXTENSION && is_null($this->extension_execution_id))) {
            // パラメータ不足
            self::set_error_messages(__em('params'));
            $is_error = true;
        }else if (!in_array($this->execution_method, $this->permissions)) {
            // 実行できない機能
            self::set_error_messages(__em('function_execute'));
            $is_error = true;
        }

        if ($is_error) {
            Response::redirect('error/400');
        }

        // フラッシュメッセージがある場合はviewに渡す
        $success_messages = self::get_success_messages();
        if ($success_messages) {
            // エスケープせずHTMLタグをそのままviewに渡したいケースがあるためsetメソッドの第三引数をfalseにする
            // ただし危険なHTMLタグは取り除く
            $this->template->set('success_messages', Security::xss_clean($success_messages), false);
        }
        $error_messages = self::get_error_messages();
        if ($error_messages) {
            $this->template->set('error_messages', Security::xss_clean($error_messages), false);
        }
    }

    /**
     * viewを表示する
     *
     * @param string $action_name 画面を表示するアクション名を指定したい場合（指定しなければ呼び出し元と同じアクション名になる）
     * @param string $path viewファイルのパスを指定したい場合（指定しなければ[コントローラー/アクション]のviewが使われる）
     * @param string $footer_path フッターのパスを指定したい場合（指定しなければ[_コントローラー_アクション]のフッターがあればそれが使われる）
     * @param array $css cssファイル名の配列
     * @param array $js jsファイル名の配列
     */
    protected function display(string $action_name = null ,string $path = null, string $footer_path = null, array $css = [], array $js = []) {

        $controller_name = Request::main()->uri->segment(1);
        $action_name = is_null($action_name) ? Request::main()->action : $action_name;

        // アプリアイコン
        $this->template->app_icon = self::HEADER_APP_ICON_NAME;
        
        // ページタイトル(アクションごとのタイトルがある場合はそれを使い、無ければコントローラー共通のタイトルを表示する)
        $title = __p('title.' . $action_name . '.' . $this->execution_method);
        if (is_null($title)) {
            $title = __p('title.' . $this->execution_method);
        }
        $this->template->title = $title;
        
        // ページの説明(アクションごとのページの説明がある場合はそれを使い、無ければコントローラー共通のページの説明を表示する)
        $description = __p('description.' . $action_name . '.' . $this->execution_method);
        if (is_null($description)) {
            $description = __p('description.' . $this->execution_method);
        }
        $this->template->description = $description;

        // csrfトークン
        $this->template->token_key = Config::get('security.csrf_token_key');
        $this->template->token = Security::fetch_token();

        // viewからrenderでさらにviewを読み込んだ場合でも利用できるようにset_globalに設定する
        $this->template->set_global('execution_method', $this->execution_method);
        $this->template->set_global('extension_execution_id', $this->extension_execution_id);
        $this->template->set_global('company_id', $this->company_id);
        $this->template->set_global('user_id', $this->user_id);

        // プレゼンターを利用する
        if (is_null($path)) {
            // パスが指定されていない場合は、ファイル名とアクション名からパスを作成する
            $path = $controller_name . '/' . $action_name;
        }
        $this->template->content = \Presenter::forge($path);

        // footerを設定する
        if (is_null($footer_path)) {
            // パスが指定されていない場合は、ファイル名とアクション名からパスを作成する
            $footer_path = '_' . $controller_name . '_' . $action_name;
        }
        if (file_exists(APPPATH . 'views/footer/' . $footer_path . '.php') !== false) {
            $this->template->footer_path = $footer_path;
        }

        // css
        if (file_exists(APPPATH . '../../public/assets/css/' . $controller_name . '.css') !== false) {
            $css[] = $controller_name . '.css';
        }
        if (!empty($css)) {
            \Asset::css($css, [], 'css');
        }

        // js
        if (file_exists(APPPATH . '../../public/assets/js/' . $controller_name . '.js') !== false) {
            $js[] = $controller_name . '.js';
        }
        if (!empty($js)) {
            \Asset::js($js, [], 'js');
        }
    }
}