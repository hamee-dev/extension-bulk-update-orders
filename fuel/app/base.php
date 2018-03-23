<?php

/**
 * coreのlogger関数を使いたくないため、こちらで先に定義する
 */
if (!function_exists('logger')) {
    /**
     * ログを出力する
     *
     * @param string|int $level ログレベル
     * @param string|array $msg ログメッセージ
     * @param string $method メソッド名（ログにはメソッド名が自動的に出るので基本的には渡す必要なし）
     * @return bool 成功/失敗
     * @throws FuelException
     */
    function logger($level, string $msg, string $method = null) : bool {
        return \Log::write($level, $msg, $method);
    }
}

if (!function_exists('trim_length')) {
    /**
     * 指定した文字数が指定した文字数以上だった場合、指定した文字数に省略し最後にサフィックスをつける
     * @param $str 対象とする文字列
     * @param $length 文字数
     * @param $suffix 省略した場合につけるサフィックス
     * @return string
     */
    function trim_length(string $str, string $length, string $suffix = '...') : string {
        if (mb_strlen($str) > $length) {
            $str = mb_substr($str, 0, $length) . $suffix;
        }
        return $str;
    }
}

if ( ! function_exists('__p'))
{
    /**
     * ページごとの文言を取得する
     * 本来$lineは 'page.updatesetting.no_select_value' のように渡す必要があるが、
     * 表示するコントローラーが一致している場合は 'no_select_value' に省略させることができる関数
     * 引数についてはLang::getと同じためそちらを参照
     *
     * @param string $line
     * @param array $params
     * @param mixed $default
     * @param string|null $language
     * @return mixed
     */
    function __p(string $line, array $params = [], $default = null, $language = null)
    {
        $line = 'page.' . Request::main()->uri->segment(1) . '.' . $line;
        return \Lang::get($line, $params, $default, $language);
    }
}

if ( ! function_exists('__c'))
{
    /**
     * 共通の文言を取得する
     * $lineを 'common.created_at' から 'created_at' と省略することができる関数
     * 引数についてはLang::getと同じためそちらを参照
     *
     * @param string $line
     * @param array $params
     * @param mixed $default
     * @param string|null $language
     * @return mixed
     */
    function __c(string $line, array $params = [], $default = null, $language = null)
    {
        $line = 'common.' .  $line;
        return \Lang::get($line, $params, $default, $language);
    }
}

if ( ! function_exists('__em'))
{
    /**
     * エラーメッセージの文言を取得する
     * $lineを 'message.error.params' から 'params' と省略することができる関数
     * 引数についてはLang::getと同じためそちらを参照
     *
     * @param string $line
     * @param array $params
     * @param mixed $default
     * @param string|null $language
     * @return mixed
     */
    function __em(string $line, array $params = [], $default = null, $language = null)
    {
        $line = 'message.error.' .  $line;
        return \Lang::get($line, $params, $default, $language);
    }
}

if ( ! function_exists('__sm'))
{
    /**
     * 成功メッセージの文言を取得する
     * $linenを 'message.success.params' から 'params' と省略することができる関数
     * 引数についてはLang::getと同じためそちらを参照
     *
     * @param string $line
     * @param array $params
     * @param mixed $default
     * @param string|null $language
     * @return mixed
     */
    function __sm(string $line, array $params = [], $default = null, $language = null)
    {
        $line = 'message.success.' .  $line;
        return \Lang::get($line, $params, $default, $language);
    }
}