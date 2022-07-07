## docker-composeのインストール
```bash
$ sudo curl -L "https://github.com/docker/compose/releases/download/1.29.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
$ sudo chmod +x /usr/local/bin/docker-compose
$ docker-compose --version
```

## docker-compose.ymlの作成
今回は4つのコンテナmysql、phpadmin、php、nginxを扱います。このdocker-compose.ymlは[こちら](https://qiita.com/tomokei5634/items/ff0784e88b026b530e3e)を参考にしています。<br>
nginxのイメージは自分で作成したものを指定してください。

```bash
$ mkdir ~/docker-compose
$ cd ~/docker-compose
$ git clone https://github.com/tmoritoki0227/cloudnative-hands-on.git
$ vi docker-compose.yml
```

```yaml:docker-compose.yml
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
簡単に素早く４つのコンテナを起動できることを体験しましょう。

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

## 動作確認

### nginx動作確認
- http://ec2-35-76-109-31.ap-northeast-1.compute.amazonaws.com:8080 をブラウザで開きます。
  - php用の画面が表示されればOKです。 表示している画面のコードは[こちら](https://github.com/tmoritoki0227/cloudnative-hands-on/blob/main/conf/html/index.php)
  - nginxの設定ファイルは[こちら](https://github.com/tmoritoki0227/cloudnative-hands-on/blob/main/conf/default.conf)

### phpadmin/mysql動作確認
- http://ec2-35-76-109-31.ap-northeast-1.compute.amazonaws.com:8888 をブラウザで開きます。
  - phpMyAdmin は PHP で実装された MySQL の管理ツールです。 MySQL のデータベースやテーブルの作成を行ったり、データの追加や参照などをブラウザから行うことができます。
  - ユーザー名`root`、パスワード`root_pass_fB3uWvTS`でログインできます。
  - 適当に触ってみましょう。SQLも実行可能です

## これでハンズオン演習は終了です
- インスタンス停止またはインスタンスを削除しましょう。

