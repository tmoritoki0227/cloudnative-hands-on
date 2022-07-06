## docker-composeのセットアップ
```bash
$ sudo curl -L "https://github.com/docker/compose/releases/download/1.29.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
$ sudo chmod +x /usr/local/bin/docker-compose
$ docker-compose --version
```

## 設定ファイルの作成
### docker-compose.yml
２つのコンテナnginxとtest_httpserverを取り扱います。今回は複数のコンテナを一度の操作で起動することが目的のため、この２つのコンテナには関係性はありません。<br>
一般的には関係性のあるコンテナを組み合わせて利用します。例えばウェブアプリケーションであればnginx（webサーバ）,mysql（データベース）のコンテナで構成します。
```bash
$ mkdir ~/docker-compose
$ cd ~/docker-compose
$ git clone https://github.com/tmoritoki0227/cloudnative-hands-on
$ vi docker-compose.yml
```

```bash
version: "3"
services:
  db:
    image: mysql:5.7
    volumes:
      - ./db/mysql:/var/lib/mysql
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: root_pass_fB3uWvTS

  phpmyadmin:
    image: phpmyadmin/phpmyadmin:latest
    restart: always
    ports: ["8888:80"]
    depends_on: ["db"]

  php:
    image: php:7.4-fpm
    volumes:
      - ./nginx/html:/usr/share/nginx/html
      - ./php/php.ini:/usr/local/etc/php/conf.d/php.ini
    depends_on: ["db"]

  nginx:
    image: tmoritoki0227/my_nginx:latest # 自分で作成したnginxを指定する
    volumes: # 自分でカスタマイズしたphpの設定ファイルをコンテナ上にマウントする
      - ./cloudnative-hands-on/conf/default.conf:/etc/nginx/conf.d/default.conf
      - ./cloudnative-hands-on/conf/index.php:/usr/share/nginx/html/index.php
    restart: always
    ports: ["8080:80"]
    depends_on: ["php"]
```

### default.conf（nginxイメージ用）

```bash
$ vi default.conf
```

```bash
server {
    listen       80;
    listen  [::]:80;
    server_name  localhost;

    #access_log  /var/log/nginx/host.access.log  main;

    location / {
        root   /usr/share/nginx/html;
        index  index.html index.htm;
    }

    #error_page  404              /404.html;

    # redirect server error pages to the static page /50x.html
    #
    error_page   500 502 503 504  /50x.html;
    location = /50x.html {
        root   /usr/share/nginx/html;
    }

    # proxy the PHP scripts to Apache listening on 127.0.0.1:80
    #
    #location ~ \.php$ {
    #    proxy_pass   http://127.0.0.1;
    #}

    # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
    #
    #location ~ \.php$ {
    #    root           html;
    #    fastcgi_pass   127.0.0.1:9000;
    #    fastcgi_index  index.php;
    #    fastcgi_param  SCRIPT_FILENAME  /scripts$fastcgi_script_name;
    #    include        fastcgi_params;
    #}

    # deny access to .htaccess files, if Apache's document root
    # concurs with nginx's one
    #
    #location ~ /\.ht {
    #    deny  all;
    #}
}
```

## docker-compose起動・停止
```bash
# docker-compose起動(このあとctrl + cで停止可能）
$ docker-compose up

# docker-composeバックグラウンドで起動
$ docker-compose up -d

# 状態確認
$ docker-compose ps

# コンテナ(nginx)に入りたい場合
$ docker exec -it docker-compose_nginx_1 /bin/bas

# docker-compose停止
$ docker-compose down
```

## port解放
- 8080
- 8888

## nginx動作確認
- http://ec2-35-76-109-31.ap-northeast-1.compute.amazonaws.com:8080

## phpadmin/mysql動作確認
- http://ec2-35-76-109-31.ap-northeast-1.compute.amazonaws.com:8888
  - phpMyAdmin は PHP で実装された MySQL の管理ツールです。 MySQL のデータベースやテーブルの作成を行ったり、データの追加や参照などをブラウザから行うことができま
  - ユーザー名root、パスワードroot_pass_fB3uWvTSでログインできるはずです。
  - 適当に触ってみましょう。

## docker-compose実行例
```
[ec2-user@ip-172-31-4-17 docker-compose]$ docker-compose up
Starting docker-compose_db_1 ... done
Starting docker-compose_php_1        ... done
Starting docker-compose_phpmyadmin_1 ... done
Starting docker-compose_nginx_1      ... done
Attaching to docker-compose_db_1, docker-compose_php_1, docker-compose_phpmyadmin_1, docker-compose_nginx_1
db_1          | 2022-07-06 13:31:43+00:00 [Note] [Entrypoint]: Entrypoint script for MySQL Server 5.7.38-1.el7 started.
db_1          | 2022-07-06 13:31:43+00:00 [Note] [Entrypoint]: Switching to dedicated user 'mysql'
db_1          | 2022-07-06 13:31:43+00:00 [Note] [Entrypoint]: Entrypoint script for MySQL Server 5.7.38-1.el7 started.
db_1          | 2022-07-06 13:31:43+00:00 [Note] [Entrypoint]: Initializing database files
php_1         | [06-Jul-2022 13:31:44] NOTICE: fpm is running, pid 1
php_1         | [06-Jul-2022 13:31:44] NOTICE: ready to handle connections
phpmyadmin_1  | AH00558: apache2: Could not reliably determine the server's fully qualified domain name, using 172.20.0.4. Set the 'ServerName' directive globally to suppress this message
phpmyadmin_1  | AH00558: apache2: Could not reliably determine the server's fully qualified domain name, using 172.20.0.4. Set the 'ServerName' directive globally to suppress this message
phpmyadmin_1  | [Wed Jul 06 13:31:45.299990 2022] [mpm_prefork:notice] [pid 1] AH00163: Apache/2.4.53 (Debian) PHP/8.0.19 configured -- resuming normal operations
phpmyadmin_1  | [Wed Jul 06 13:31:45.300393 2022] [core:notice] [pid 1] AH00094: Command line: 'apache2 -D FOREGROUND'
db_1          | 2022-07-06 13:31:48+00:00 [Note] [Entrypoint]: Database files initialized
db_1          | 2022-07-06 13:31:48+00:00 [Note] [Entrypoint]: Starting temporary server
db_1          | 2022-07-06 13:31:48+00:00 [Note] [Entrypoint]: Waiting for server startup
db_1          | 2022-07-06 13:31:49+00:00 [Note] [Entrypoint]: Temporary server started.
db_1          | '/var/lib/mysql/mysql.sock' -> '/var/run/mysqld/mysqld.sock'
db_1          | Warning: Unable to load '/usr/share/zoneinfo/iso3166.tab' as time zone. Skipping it.
db_1          | Warning: Unable to load '/usr/share/zoneinfo/leapseconds' as time zone. Skipping it.
db_1          | Warning: Unable to load '/usr/share/zoneinfo/tzdata.zi' as time zone. Skipping it.
db_1          | Warning: Unable to load '/usr/share/zoneinfo/zone.tab' as time zone. Skipping it.
db_1          | Warning: Unable to load '/usr/share/zoneinfo/zone1970.tab' as time zone. Skipping it.
db_1          |
db_1          | 2022-07-06 13:31:51+00:00 [Note] [Entrypoint]: Stopping temporary server
db_1          | 2022-07-06 13:31:53+00:00 [Note] [Entrypoint]: Temporary server stopped
db_1          |
db_1          | 2022-07-06 13:31:53+00:00 [Note] [Entrypoint]: MySQL init process done. Ready for start up.
db_1          |
```

## （参考）nginx設定
- https://hub.docker.com/_/nginx
- https://solomaker.club/how-to-use-dokcer-compose-yml-file/
- https://amateur-engineer-blog.com/docker-compose-nginx/
