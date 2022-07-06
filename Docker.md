https://qiita.com/tomokei5634/items/ff0784e88b026b530e3e
# 目次
- [EC2にdockerインストール](#EC2にdockerインストール)
- [nginxオフィシャルDockerイメージを利用してみよう](#nginxオフィシャルDockerイメージを利用してみよう)
- [自分でDockerイメージを作ろう](#自分でDockerイメージを作ろう)
- [作成したDockerイメージをDockerHubへアップロード](#作成したDockerイメージをDockerHubへアップロード)
- [後始末](#後始末)


## EC2にdockerインストール
これはお決まりの手順です。
```bash
$ sudo yum install -y docker
$ sudo systemctl start docker
$ sudo systemctl status docker
$ sudo systemctl enable docker
$ sudo usermod -a -G docker ec2-user

# 現行ユーザをdockerグループに所属させる。これでdockerコマンドがsudoなしで実行できます。
$ sudo gpasswd -a $USER docker

# dockerデーモンを再起動する
$ sudo systemctl restart docker

# exitして再ログインすると反映される。
$ exit # このあと再度インスタンスにsshしてください。

$ docker -v
```

## [nginxオフィシャルDockerイメージ](https://hub.docker.com/_/nginx)を利用してみよう
※ ここで行う作業は[こちら](https://snowsystem.net/container/docker/nginx/)を参考にしています。
```bash
$ docker pull nginx # DockerHubで公開されているnginxをpullします。
$ docker image ls # pullしたimageを確認します
$ docker run -d --name nginx-test -p 8888:80 nginx # 8888でリクエストを受けて、コンテナがLISTENしているport 80に受け流す意味です
$ docker ps # コンテナの状態を確認します
```
- ブラウザから http://ec2-54-199-108-124.ap-northeast-1.compute.amazonaws.com:8888/ へアクセスします。アドレスは自分のインスタンスに置き換えてください。port開放してないないのでアクセスできないはずです。
- httpの8888ポートを開放
前回の演習資料: https://github.com/tmoritoki0227/cloudnative-hands-on/blob/main/Aws.md
- ブラウザから http://ec2-54-199-108-124.ap-northeast-1.compute.amazonaws.com:8888/ へアクセスできることを確認
![image](https://user-images.githubusercontent.com/20149115/177033144-7a9876f7-4b9a-4d4f-8706-926a763448fb.png)



```bash
$ docker container stop nginx-test # コンテナを停止します。
$ docker ps # コンテナの状態を確認します
```

## 自分でDockerイメージを作ろう
※ ここで使うアプリーケーションは[こちら](https://github.com/kichiram/golang/tree/main/http_server)です。
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
簡単にいうとOSはamazonlinuxを利用します。そして最低限のコマンドをインストールします。次にtest_httpserverをというwebアプリケーションをインストールします。このアプリケーションはport8080と8081を利用します。このアプリケーションは吉●さんのがプロメテウスの勉強会で作成したものです。

### Dockerイメージの作成
作成したDockerfileを利用してDockerイメージを作成します。
```bash
$ docker image build -t test_httpserver:latest .
```
ちょっと時間がかかります。Successfullyが出力されれば成功です。

### Dockerイメージの確認
```bash
$ docker image ls
```
`test_httpserver   latest           666b3e1df1b6   About a minute ago   1.31G`のようなものがあるはずです。

### コンテナ起動(バックグラウンドで起動）
```bash
$ docker run -d --name test_httpserver -p 8080:8080 -p 8081:8081 test_httpserver:latest
```

### port開放
- tcp 8080
- tcp 8081

### ブラウザから動作確認
- http://ec2-54-199-108-124.ap-northeast-1.compute.amazonaws.com:8080/hello
- http://ec2-54-199-108-124.ap-northeast-1.compute.amazonaws.com:8080/world
- http://ec2-54-199-108-124.ap-northeast-1.compute.amazonaws.com:8081/metrics


### 起動中のコンテナに入る
```bash
$ docker exec -it test_httpserver /bin/bash

$ ls # ここはコンテナ内で実行するコマンドです。任意のコマンド実行
$ exit # コンテナから抜ける

$ docker container stop test_httpserver
$ docker ps
$ docker ps -a # -aをつけると停止したコンテナも表示されます。
```

### 作ったコンテナを停止、削除
```bash
$ docker ps -a
$ docker container stop test_httpserver # これは間違いかも。２回実行している
$ docker rm test_httpserver
```

## 作成したDockerイメージをDockerHubへアップロード
※ ここで行う作業は[こちら](https://gray-code.com/blog/container-image-push-for-dockerhub/)を参考にしています。

### Docker hubへブラウザからログイン
https://hub.docker.com/

### コマンドでDocker hubへログイン
Docker hubのアカウントとパスワードを使ってログインします。
```bash
$ docker login
  Username: 入力
  Password: 入力
```
Login Succeededが表示されれば成功

### Dockerイメージアップロード
作成したtest_httpserverをアップロードします。これもアップロードするときのお決まりの手順です。
```bash
$ docker image ls # 現在の状態を確認
$ docker tag test_httpserver tmoritoki0227/test_httpserver:latest # 説明できませんがtag付けが必要です。
$ docker image ls # 現在の状態を確認
$ docker push tmoritoki0227/test_httpserver:latest # アップロードします。
```
- `tmoritoki0227`はdockerhubのアカウント名に合わせないとだめ
- コマンド成功後、https://hub.docker.com/ を確認しアップロードされたことを確認します。
-  AWSのネットワーク外に通信するのでおそらく課金されます。楽しみにしていてください。

### dockerイメージ(test_httpserver)を削除する
test_httpserverがローカルにあるとそれを使ってしまうため削除します
```bash
$ docker image rmi tmoritoki0227/test_httpserver
$ docker image rmi test_httpserver
$ docker image ls
```

### アップロードしたdockerイメージを使ってみる
```bash
$ docker pull tmoritoki0227/test_httpserver:latest
$ docker run -d --name test_httpserver -p 8080:8080 -p 8081:8081 tmoritoki0227/test_httpserver:latest
```
`docker pull`時に表示されるログにダウンロード状況の表示がない場合は、ローカルにあるイメージを使ってますので、注意してください。

### ブラウザからアクセスする
- http://ec2-54-199-108-124.ap-northeast-1.compute.amazonaws.com:8080/hello
- http://ec2-54-199-108-124.ap-northeast-1.compute.amazonaws.com:8080/world
- http://ec2-54-199-108-124.ap-northeast-1.compute.amazonaws.com:8081/metrics

## 後始末
### コンテナ停止、削除とイメージ全削除
この辺のコマンドを使う。作業をやり直すときなど楽。
```bash
$ docker stop $(docker ps -q) ;docker rmi $(docker images -q) -f;docker system prune -a
$ docker image ls;docker ps -a
```
※docker psで表示するものがないとエラーになりますが、問題はないです
