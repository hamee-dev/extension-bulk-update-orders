<?php
return array(
    'fuelphp' => array(
        'app_created' => function()
        {
            // リクエスト開始ログ(urlは本来 Uri::current() で取得できるが、このタイミングではまだ取得できないためInputクラスから生成する)
            Log::notice_ex('request start.', ['url' => \Input::protocol() . '://' .  \Input::server('HTTP_HOST') . \Input::uri(), 'request_params' => \Input::all()]);
        },
        'shutdown' => function()
        {
            // リクエスト終了ログ
            Log::notice_ex('request finish.', ['url' => \Input::protocol() . '://' .  \Input::server('HTTP_HOST') . \Input::uri(), 'request_params' => \Input::all()]);
        },
    ),
);