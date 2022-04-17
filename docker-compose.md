## docker-composeのセットアップ
```bash
$ sudo curl -L "https://github.com/docker/compose/releases/download/1.29.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
$ sudo chmod +x /usr/local/bin/docker-compose
$ docker-compose --version
```

## 設定ファイルの作成
### docker-compose.yml
```bash
$ cd ~/docker
$ vi docker-compose.yml
```

```bash
version: '3.5'
services:
  nginx:
    image: nginx:latest
    container_name: nginx00
    ports:
      - "8888:80"
    volumes:
      - ./default.conf:/etc/nginx/conf.d/default.conf

  test_httpserver:
    image: tmoritoki0227/test_httpserver:latest # 先の演習で作成したイメージです。
    container_name: test_httpserver
    hostname: test_httpserver
    ports:
      - 8080:8080
      - 8081:8081
    restart: always
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

# docker-compose停止
$ docker-compose down
```

## nginx動作確認
http://ec2-35-76-109-31.ap-northeast-1.compute.amazonaws.com:8888/

## test_httpserver動作確認
http://ec2-35-76-109-31.ap-northeast-1.compute.amazonaws.com:8080/hello
http://ec2-35-76-109-31.ap-northeast-1.compute.amazonaws.com:8080/world
http://ec2-35-76-109-31.ap-northeast-1.compute.amazonaws.com:8081/metrics

## （参考）nginx設定
- https://hub.docker.com/_/nginx
- https://solomaker.club/how-to-use-dokcer-compose-yml-file/
- https://amateur-engineer-blog.com/docker-compose-nginx/
