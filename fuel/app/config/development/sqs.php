<?php
return [
    'credentials' => [
        'key'    => 'elasticmq_dummy',
        'secret' => 'elasticmq_dummy',
    ],
    'endpoint' => 'http://localhost:9324/',
    'region'   => 'elasticmq',
    // NOTE: .fifoのサフィックスをつけてしまうとElasticMQの方でサポートしていないためエラーになってしまう
    // @see https://github.com/adamw/elasticmq/issues/125
    'que_name' => 'ne-ext-buo-req-local',
];