<?php
/**
 * 全ての環境に適用するDBの定義
 * 各環境のディレクトリ配下でdb.phpを作ればそちらが優先されて参照されます
 * 特にローカルのDBに繋ぎたい場合にはdevelopment/db.phpを作成してください
 */
return array(
    'default' => array(
        'connection'  => array(
            'dsn'        => getenv('DB_DSN'),
            'username'   => getenv('DB_USER_NAME'),
            'password'   => getenv('DB_PASSWORD'),
        ),
    ),
);
