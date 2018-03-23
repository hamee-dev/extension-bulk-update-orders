#!/bin/sh
service php-fpm start
service supervisord start
cd /var/www/html
# composerの処理を速くする
# @see http://yuzurus.hatenablog.jp/entry/composer-hayai
php composer.phar config -g repositories.packagist composer https://packagist.jp
# NOTE: 環境構築時や明示的にcomposer.pharを更新したい場合にはここのコメントを解除してdockerを起動してください
# php composer.phar self-update
php composer.phar update
# 今はたまたまDBコンテナが先に立ち上がっているのでマイグレーションが実行できているだけ
#OPTIMIZE: DBコンテナの立ち上がりを待つ機構が必要
php oil refine migrate
# テスト用DBにマイグレーション実行
export FUEL_ENV=test
php oil refine migrate
/usr/sbin/nginx -g 'daemon off;'