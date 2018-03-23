<?php
/**
 * 利用するサーバーのURLのスキーム＋ホスト名の定義
 */
return [
    'api_server' => getenv('NE_API_SERVER'),
    'ne_server'  => getenv('NE_SERVER'),
];