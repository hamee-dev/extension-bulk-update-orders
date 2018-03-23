<?php
// coreよりも先に読み込みたい場合ここに定義する
require APPPATH.'base.php' ;

// Bootstrap the framework DO NOT edit this
require COREPATH.'bootstrap.php';

\Autoloader::add_classes(array(
    // coreのクラスを置き換えしたいのでここにcoreのクラスと同じ名前で定義する
    'Log' => APPPATH.'classes/log.php',
    'Database_Query' => APPPATH.'classes/database/query.php',
    'Security' => APPPATH.'classes/security.php',
    'View' => APPPATH.'classes/view.php',
    // テストのベースクラス（サブクラスよりも後にロードされることがあるのでここに定義する）
    'Testbase' => APPPATH.'tests/testbase.php',
));

// Register the autoloader
\Autoloader::register();

/**
 * Your environment.  Can be set to any of the following:
 *
 * Fuel::DEVELOPMENT
 * Fuel::TEST
 * Fuel::STAGING
 * Fuel::PRODUCTION
 */
\Fuel::$env = \Arr::get($_SERVER, 'FUEL_ENV', \Arr::get($_ENV, 'FUEL_ENV', \Fuel::DEVELOPMENT));

require APPPATH.'config/constants.php' ;

// Initialize the framework with the config file.
\Fuel::init('config.php');

\Config::load('nextengine', true);
\Config::load('host', true);
\Config::load('sqs', true);
\Config::load('gtm', true);
\Lang::load('common.yml', true);
\Lang::load('page.yml', true);
\Lang::load('validation.yml', true);
\Lang::load('message.yml', true);

// PHP7とFuelPHP1.8とPHPUnit6.5は噛み合わせがよくないようで以下モンキーパッチをあてる
// 名前空間の差異なので下記対応による影響はさほどない判断
// https://qiita.com/Prof/items/eccc8a99b7eb982de817
// https://qiita.com/h_narita/items/fac219d567444c8e99e2
if(\Fuel::$env === Fuel::DEVELOPMENT || \Fuel::$env === Fuel::TEST){
    class PHPUnit_Framework_TestCase extends \PHPUnit\Framework\TestCase{}
}
