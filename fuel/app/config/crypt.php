<?php
/**
 * cryptの生成ロジックは以下
 * @see fuel/core/classes/crypt.php
 * @see https://qiita.com/takakiku/items/c6f508330070b06f8e81
 */
return array(
  'crypto_key' => getenv('FUEL_CRYPTO_KEY'),
  'crypto_iv' => getenv('FUEL_CRYPTO_IV'),
  'crypto_hmac' => getenv('FUEL_CRYPTO_HMAC'),
);
