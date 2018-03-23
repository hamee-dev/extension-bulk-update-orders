<?php
/**
 * Amazon Simple Queue Service (SQS)の接続先の定義
 */
return [
    'region'   => getenv('SQS_REGION'),
    'que_name' => getenv('SQS_QUE_NAME'),
];