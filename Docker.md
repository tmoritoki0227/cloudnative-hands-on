## EC2にdockerインストール
```bash
$ sudo yum install -y docker
$ sudo systemctl start docker
$ sudo systemctl status docker
$ sudo systemctl enable docker
$ sudo usermod -a -G docker ec2-user

## 現行ユーザをdockerグループに所属させる
$ sudo gpasswd -a $USER docker

## dockerデーモンを再起動する (CentOS7の場合)
$ sudo systemctl restart docker

## exitして再ログインすると反映される。
$ exit

$ docker -v
```

## nginxコンテナを起動してブラウザから確認しよう

```
$ docker pull nginx
$ docker image ls
$ docker run -d -p 8888:80 nginx
```
- ブラウザから
http://ec2-54-199-108-124.ap-northeast-1.compute.amazonaws.com:8888/
アクセスできないことを確認
- httpの8888ポートを解放
- ブラウザから
http://ec2-54-199-108-124.ap-northeast-1.compute.amazonaws.com:8888/
アクセスできることを確認
- この作業の詳細は以下を参考にしてください。
https://snowsystem.net/container/docker/nginx/

## 自分でDockerイメージを作ろう
### Dockerfile作成
```bash
$ mkdir ~/docker
$ vim ~/docker/Dockerfile
```

```bash
FROM amazonlinux:2.0.20211223.0

# yum update & install
RUN yum update -y \
    && yum install \
        systemd \
        tar \
        unzip \
        sudo \
        golang \
        httpd \
        vim \
        wget \
        hostname \
        -y

# setup golang test_httpserver
RUN wget https://raw.githubusercontent.com/kichiram/golang/main/testgo/test_httpserver.go \
  && go get github.com/prometheus/client_golang/prometheus \
  && go build test_httpserver.go \
  && mv test_httpserver /usr/local/bin/ 

# init
EXPOSE 8080
EXPOSE 8081
CMD ["/usr/local/bin/test_httpserver", "-D", "FOREGROUND"]
```

### Dockerイメージの作成
```
$ docker image build -t test_httpserver:latest .
```

### Dockerイメージの確認
```bash
$ docker image ls
```

### コンテナ起動とバックグラウンド起動
```bash
docker run -d --name test_httpserver -p 8080:8080 -p 8081:8081 test_httpserver:latest
```

### 起動中のコンテナに入る
```bash
$ docker exec -i -t CONTAINER_ID /bin/bash
$ 任意のコマンド実行
$ exit
```

### dockerイメージへの疎通確認、コマンド
```bash
# ec2
$ nc -vz localhost 8080
# local(Mac or Windows)
$ nc -vz ec2-35-77-196-144.ap-northeast-1.compute.amazonaws.com 8080
```

### port解放

### dockerイメージへの疎通確認、ブラウザから
urlは自分のec2のDNSにする
- http://ec2-54-199-108-124.ap-northeast-1.compute.amazonaws.com:8080/hello
- http://ec2-54-199-108-124.ap-northeast-1.compute.amazonaws.com:8080/world
- http://ec2-54-199-108-124.ap-northeast-1.compute.amazonaws.com:8081/metrics

### docker hubへ作成したDockerイメージをアップロード

#### 参考Doc
- https://gray-code.com/blog/container-image-push-for-dockerhub/
- https://hub.docker.com/r/tmoritoki0227/

#### docker hub ブラウザからログイン
https://hub.docker.com/

#### アップロードコマンド
```bash
docker login
  Username: 入力汁
  Password:　　入力汁

sudo docker image build -t test_httpserver:latest .
docker image ls
docker tag test_httpserver tmoritoki0227/test_httpserver:latest
docker image ls
docker push tmoritoki0227/test_httpserver:latest
```
tmoritoki0227 はdockerhubのリポジトリ名に合わせないとだめ
https://hub.docker.com/repository/docker/tmoritoki0227/test_httpserver

#### アップロードしたdockerイメージを使ってみる

```bash
docker run -it --name test_httpserver test_httpserver:latest /bin/bash
```

## おまけ
## イメージの取得とコンテナに入る
```bash
docker run -it --name test_httpserver test_httpserver:latest /bin/bash
# portいらない？
docker run -d --name test_httpserver -p 8080:8080 -p 8081:8081 test_httpserver:latest
```

### コンテナ起動確認
```bash
docker ps
docker ps -a # 停止中のコンテナも表示
```

### コンテナに入る
```bash
docker attach CONTAINER ID
```

### コンテナ停止
```bash
docker container stop
```

### コンテナ削除
```bash
docker rm [コンテナID]
```

### イメージ削除
```bash
docker images で
docker rmi イメージID
```
