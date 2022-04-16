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

## nginxオフィシャルDockerイメージを利用してみよう
https://hub.docker.com/_/nginx
```
$ docker pull nginx
$ docker image ls
$ docker run -d --name nginx-test -p 8888:80 nginx
$ docker ps
$ docker container stop nginx-test
$ docker ps
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
https://github.com/kichiram/golang/tree/main/http_server 吉●さんのテスト用webサーバをパクリます。
### Dockerfile作成
```bash
$ mkdir ~/docker
$ cd ~/docker/
$ vim Dockerfile
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
```bash
$ docker image build -t test_httpserver:latest .
```
ちょっと時間がかかります。Successfullyが出力されれば成功です。

### Dockerイメージの確認
```bash
$ docker image ls
```
`test_httpserver   latest           666b3e1df1b6   About a minute ago   1.31G`のようなものがあるはずです。

### コンテナ起動とバックグラウンド起動
```bash
docker run -d --name test_httpserver -p 8080:8080 -p 8081:8081 test_httpserver:latest
```

### port解放
- 8080
- 8081

### ブラウザから動作確認
- http://ec2-54-199-108-124.ap-northeast-1.compute.amazonaws.com:8080/hello
- http://ec2-54-199-108-124.ap-northeast-1.compute.amazonaws.com:8080/world
- http://ec2-54-199-108-124.ap-northeast-1.compute.amazonaws.com:8081/metrics


### 起動中のコンテナに入る
```bash
$ docker ps
$ docker exec -it test_httpserver /bin/bash
$ ls # 任意のコマンド実行
$ exit
$ docker container stop test_httpserver
```

### これまで作ったコンテナを削除
```bash
$ docker ps -a
$ docker container stop test_httpserver
$ docker container stop nginx-test
```

### docker hubへ作成したDockerイメージをアップロード

#### 参考Doc
- https://gray-code.com/blog/container-image-push-for-dockerhub/
- https://hub.docker.com/r/tmoritoki0227/

#### docker hub ブラウザからログイン
https://hub.docker.com/

#### コマンドでdocker hub ログイン
```bash
docker login
  Username: 入力汁
  Password:　　入力汁
```
Login Succeededが表示されれば成功

#### Dockerイメージアップロード
作成したtest_httpserverをアップロードします。コマンド成功後、https://hub.docker.com/を確認します。
```
docker image ls
docker tag test_httpserver tmoritoki0227/test_httpserver:latest
docker image ls
docker push tmoritoki0227/test_httpserver:latest
```
- `tmoritoki0227`はdockerhubのアカウント名に合わせないとだめ
- https://hub.docker.com/repository/docker/tmoritoki0227/test_httpserver
- 実行例
```
[ec2-user@ip-172-31-3-57 docker]$ docker push tmoritoki0227/test_httpserver:latest
The push refers to repository [docker.io/tmoritoki0227/test_httpserver]
cc0659cca492: Pushed
3f78366f85d9: Pushed
4f765311acac: Layer already exists
latest: digest: sha256:5af8f982846291287743f08c757f7ba4ac7c4d82af82ae9d0c0256c186261939 size: 954
```

#### dockerイメージ(test_httpserver)を削除する
ローカルにあるとそれを使ってしまうため。
```
$ docker image rmi test_httpserver
$ docker image rmi tmoritoki0227/test_httpserver
```

#### アップロードしたdockerイメージを使ってみる
```bash
docker pull tmoritoki0227/test_httpserver:latest
docker run -d --name test_httpserver -p 8080:8080 -p 8081:8081 tmoritoki0227/test_httpserver:latest
```

#### ブラウザからアクセスする
- http://ec2-54-199-108-124.ap-northeast-1.compute.amazonaws.com:8080/hello
- http://ec2-54-199-108-124.ap-northeast-1.compute.amazonaws.com:8080/world
- http://ec2-54-199-108-124.ap-northeast-1.compute.amazonaws.com:8081/metrics

## 後始末
### コンテナとイメージ全削除
```bash
$ docker stop $(docker ps -q) && docker rmi $(docker images -q) -f
```
