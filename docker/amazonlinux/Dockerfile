FROM amazonlinux:2017.09

RUN yum -y update

RUN set -x &&\
    yum -y install  ntp\
                    vim-enhanced\
                    wget\
                    epel-release\
                    gcc\
                    sudo\
                    sendmail\
                    nginx

## php
RUN yum -y install  php71\
                    php71-mbstring\
                    php7-pear\
                    php71-fpm\
                    php71-mcrypt\
                    php71-pdo\
                    php71-mysqlnd\
                    php71-bcmath

## php-fpm
RUN set -x && \
    sed -i -e 's/user = apache/user = nginx/g' /etc/php-fpm.d/www.conf && \
    sed -i -e 's/group = apache/group = nginx/g' /etc/php-fpm.d/www.conf && \
    sed -i -e 's/listen = \/var\/run\/php-fpm\/www.sock/listen = 127.0.0.1:9000/g' /etc/php-fpm.d/www.conf

## supervisor
# yumで入れると古いバージョンが入ってしまうのでeasy_installで新しいバージョンを入れる
# @see https://qiita.com/yushin/items/15f4f90c5663710dbd56
# easy_install
# @see https://dev.classmethod.jp/cloud/aws/preparing-ec2-python-environment/
RUN curl -o /tmp/ez_setup.py https://bootstrap.pypa.io/ez_setup.py
RUN /usr/bin/python27 /tmp/ez_setup.py
# @see http://komaken.me/blog/2017/01/25/aws-playframeworkamazonlinux
RUN easy_install pip
RUN pip install supervisor
# 設定ファイルを配置
RUN touch /etc/supervisord.conf
RUN mkdir /etc/supervisord.d
RUN mkdir /var/log/supervisor/
# サービスに設定(コンテナ起動時にプロセスが起動するようにする)
RUN curl -o /etc/rc.d/init.d/supervisord https://raw.githubusercontent.com/Supervisor/initscripts/master/redhat-init-equeffelec
# 持ってきたinitファイル内のパスをこの環境のsupervisordのパスに置換する
RUN sed -i -e 's/\/usr\/bin\/supervisorctl/\/usr\/local\/bin\/supervisorctl/g' /etc/rc.d/init.d/supervisord
RUN sed -i -e 's/\/usr\/bin\/supervisord/\/usr\/local\/bin\/supervisord/g' /etc/rc.d/init.d/supervisord
RUN chmod 755 /etc/rc.d/init.d/supervisord
RUN chkconfig --add /etc/init.d/supervisord

## timezone
RUN ln -sf  /usr/share/zoneinfo/Asia/Tokyo /etc/localtime

# entrykit
ENV  ENTRYKIT_VERSION '0.4.0'
RUN set -x && \
  wget https://github.com/progrium/entrykit/releases/download/v${ENTRYKIT_VERSION}/entrykit_${ENTRYKIT_VERSION}_Linux_x86_64.tgz && \
  tar -xvzf entrykit_${ENTRYKIT_VERSION}_Linux_x86_64.tgz && \
  rm entrykit_${ENTRYKIT_VERSION}_Linux_x86_64.tgz && \
  mv entrykit /bin/entrykit && \
  chmod +x /bin/entrykit && \
  entrykit --symlink

## sslkey gen
RUN set -x  &&\
    mkdir /etc/nginx/certs && \
    cd /etc/nginx/certs && \
    openssl genrsa -out server.key 2048 && \
    openssl req -new -key server.key -out server.csr -subj '/C=JP/ST=Tokyo/L=Tokyo/O=Example Ltd./OU=Web/CN=example.com' && \
    openssl x509 -in server.csr -days 3650 -req -signkey server.key > server.crt

## setting file
ADD ./network /etc/sysconfig/network
ADD ./nginx.conf /etc/nginx/conf.d/app.conf
COPY ./php.ini /usr/local/etc/php/
ADD ./supervisord.conf /etc/supervisord.conf
ADD ./elasticmq.ini /etc/supervisord.d/elasticmq.ini
ADD ./updateexecution.ini /etc/supervisord.d/updateexecution.ini

## language
RUN yum reinstall -y glibc-common
ENV LANG ja_JP.utf8

## composer
RUN curl -sS https://getcomposer.org/installer | php

## ElasticMQ用にjavaを入れておく
RUN yum install -y java-1.8.0-openjdk

# 起動時の動きをstartup.shにまとめた
# 本当はEntrykitを利用していい感じにしようと思ったがうまくいかなかったためシェルを作成
# @see https://qiita.com/futoase/items/681550f466c97f0f62bc
RUN adduser -m app
RUN mkdir -p /var/app && chown app:app /var/app
ADD ./startup.sh /var/app/startup.sh
RUN chown app:app /var/app/startup.sh
RUN chmod 777 /var/app/startup.sh
CMD ["/var/app/startup.sh"]