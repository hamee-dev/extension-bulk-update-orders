<?php

return [
    // セッションのドライバとして何を使用するか（cookie, file, db, memcached, redis）
    'driver' => 'cookie',
    'enable_cookie' => true,
    'cookie_http_only' => true,
    'expire_on_close' => false,
    'expiration_time' => 7200,
    'rotation_time' => 300,

    // セッションのキーを設定値として持つ
    'keys' => [
        'ACCOUNT_USER' => 'account.user_id',
        'ACCOUNT_COMPANY' => 'account.company_id',
        'CSRF_TOKEN_SALT' => 'csrf_token_salt',
    ],

    'flash_keys' => [
        'SUCCESS_MESSAGE' => 'success_message',
        'ERROR_MESSAGE' => 'error_message',
    ],
];