## スペック

* PHP 7.1
* FuelPHP 1.8
* Amazon Linux (2017.09)
* nginx 1.12.1
* mysql 5.7
* elasticmq 0.13.9
* supervisord 3.3.4
* PHPUnit 6.5

## 開発環境

Dockerで構築しています
以下のコマンドでコンテナを起動してください  
起動時にcomposer updateやmigrateを実行しているので起動に少し時間がかかる場合があります
```
$ docker-compose build
$ docker-compose up -d

# 上記で上手くいかない場合にはDBを先に立ち上げ、完全に立ち上がりきった後にwebを立ち上げるとうまくいく可能性があります
$ docker-compose up -d db
$ docker-compose up -d web
```

PCのhostsに以下を追加
```
127.0.0.1 ext-buo-local
```

ネクストエンジンでアプリを作成し、リダイレクトURLに以下を入力

[https://ext-buo-local:10443/auth](https://ext-buo-local:10443/auth)

ネクストエンジンでアプリを作成するとクライアントIDとクライアントシークレットが発行されるので、それらを使って以下のファイルを作成
/fuel/app/config/development/nextengine.php
```
<?php
return [
    'client_id' => アプリのclient_id,
    'client_secret' => アプリのclient_secret,
];
```

### 起動時スクリプト

`docker/amazonlinux/startup.sh`に起動時スクリプトを記載した  
起動時に動かしたいものはこちらに列挙すること

### ElasticMQ

開発環境ではSQS疎通確認用にElasticMQが入っている  
supervisorでデーモン化してある  
コンテナ内で以下実行し該当のプロセスがあれば起動している
```
ps aux | grep elasticmq
```

### supervisor

キュー監視にsupervisorが入っている  
コンテナ内で以下実行し該当のプロセスがあれば起動している
```
ps aux | grep supervisor
```
コンテナ内で`supervisorctl`コマンドでスーパーバイザーの管理ツールを起動することができる  
このツール内でstatus, start, stop, restartなどでプロセスを管理することができます  
また各種設定は`/etc/supervisord.conf`と`/etc/supervisord.d/`あたりにあります

## マイグレーションの実行

コンテナに入る
```
$ docker exec -it ext-buo_web_1 bash
```

以下のコマンドでcomposerのアップデート後にマイグレーションを実行してください  
起動時のスクリプトに入っているので明示的に行いたい場合のみ必要
```
$ cd /var/www/html
$ php composer.phar self-update
$ php composer.phar update
$ php oil refine migrate
```

## データベースの接続

|||
|:---|:---|
|host|127.0.0.1|
|port|13306|
|user|root|
|pass|ext_buo|
|database_name|ext_buo|

## ユニットテスト
```
php /var/www/html/oil test
```

手動でtest用DB(ext_buo_test)にマイグレーションを実行したい場合
```
FUEL_ENV=test php oil refine migrate
```