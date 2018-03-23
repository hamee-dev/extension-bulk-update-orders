<?php
// NOTE: 本番環境用のエラーページ。
//       Fuelの仕様上、コアを書き換える以外に本番環境のエラーページを上書きするにはこのファイルを作成するしか無い。
//       どちらにせよ共通エラー画面を利用するのでそれを呼び出すだけ。

\Lang::load('message.yml', true);
$message = \Lang::get('message.error.system_error');
\Log::error_ex($message, ['url' => Uri::current(), 'request_params' =>  \Input::all()]);

// エラー画面を描画
$view = View::forge('template-error');
$view->title = $message;
$response = Response::forge($view);
$response->send(true);
