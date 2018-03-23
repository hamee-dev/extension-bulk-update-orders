<?php
class ErrorhandlerBase extends \Errorhandler
{
    /**
     * 親クラスではerrors/production.phpを描画するようになっているが、それではなく本アプリの共通エラー画面を出したいのでその部分だけoverride
     * NOTE: 本当はparent::show_production_errorの処理→redirectとしたいが親処理内でexitしていてできないためこの対応とした
     *
     * 親クラス: エラーになったテンプレートの部分だけerrors/production.phpを表示する
     * 本クラス: 共通エラー画面にリダイレクトする
     */
    protected static function show_production_error($e)
    {
        // when we're on CLI, always show the php error
        if (\Fuel::$is_cli)
        {
            return static::show_php_error($e);
        }

        if ( ! headers_sent())
        {
            $protocol = \Input::server('SERVER_PROTOCOL') ? \Input::server('SERVER_PROTOCOL') : 'HTTP/1.1';
            header($protocol.' 500 Internal Server Error');
        }
        // この部分のみ上書き
        Response::redirect('error');
    }
}
