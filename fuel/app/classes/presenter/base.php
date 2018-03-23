<?php
/**
 * プレゼンタークラスの共通親クラス
 */
abstract class Presenter_Base extends Presenter
{
    /**
     * 親クラスの例外処理を上書きしたいのでoverride
     * presenterで例外が発生した場合共通エラー画面を表示する
     *
     * __toStringで例外を投げることはできないのでエラーハンドリング処理内でリダイレクトさせる
     * @see http://php.net/manual/ja/language.oop5.magic.php#object.tostring
     */
    public function __toString()
    {
        try{
            return $this->render();
        } catch(Exception $e) {
            \Log::info_ex('presenterで例外が発生しました');
            // エラーハンドラーの中でエラーログを吐いているためここではエラーログ不要
            ErrorhandlerBase::exception_handler($e);

            return '';
        }
    }
}