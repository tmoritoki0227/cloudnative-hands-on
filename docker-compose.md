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
$ git clone https://github.com/tmoritoki0227/cloudnative-hands-on.gitDockerfile.bk
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
    volumes: # 自分でカスタマイズしたphpの設定ファイルをコンテナ上にマウントする
      - ./cloudnative-hands-on/conf/html:/usr/share/nginx/html
      - ./php/php.ini:/usr/local/etc/php/conf.d/php.ini
    depends_on: ["db"]

  nginx:
    image: tmoritoki0227/my_nginx:latest # 自分で作成したnginxを指定する
    volumes: # 自分でカスタマイズしたphpの設定ファイルをコンテナ上にマウントする
      - ./cloudnative-hands-on/conf/default.conf:/etc/nginx/conf.d/default.conf
      - ./cloudnative-hands-on/conf/html:/usr/share/nginx/html
    restart: always
    ports: ["8080:80"]
    depends_on: ["php"]
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
$ docker exec -it my_nginx /bin/bash

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
Recreating docker-compose_db_1 ... done
Recreating docker-compose_phpmyadmin_1 ... done
Recreating docker-compose_php_1        ... done
Recreating docker-compose_nginx_1      ... done
Attaching to mysql, php, phpmyadmin, my_nginx
mysql         | 2022-07-06 13:53:55+00:00 [Note] [Entrypoint]: Entrypoint script for MySQL Server 5.7.38-1.el7 started.
mysql         | 2022-07-06 13:53:55+00:00 [Note] [Entrypoint]: Switching to dedicated user 'mysql'
mysql         | 2022-07-06 13:53:55+00:00 [Note] [Entrypoint]: Entrypoint script for MySQL Server 5.7.38-1.el7 started.
mysql         | '/var/lib/mysql/mysql.sock' -> '/var/run/mysqld/mysqld.sock'
php           | [06-Jul-2022 13:53:56] NOTICE: fpm is running, pid 1
php           | [06-Jul-2022 13:53:56] NOTICE: ready to handle connections
phpmyadmin    | AH00558: apache2: Could not reliably determine the server's fully qualified domain name, using 172.20.0.4. Set the 'ServerName' directive globally to suppress this message
phpmyadmin    | AH00558: apache2: Could not reliably determine the server's fully qualified domain name, using 172.20.0.4. Set the 'ServerName' directive globally to suppress this message
phpmyadmin    | [Wed Jul 06 13:53:57.083547 2022] [mpm_prefork:notice] [pid 1] AH00163: Apache/2.4.53 (Debian) PHP/8.0.19 configured -- resuming normal operations
phpmyadmin    | [Wed Jul 06 13:53:57.087347 2022] [core:notice] [pid 1] AH00094: Command line: 'apache2 -D FOREGROUND'
```

## （参考）nginx設定
- https://hub.docker.com/_/nginx
- https://solomaker.club/how-to-use-dokcer-compose-yml-file/
- https://amateur-engineer-blog.com/docker-compose-nginx/
