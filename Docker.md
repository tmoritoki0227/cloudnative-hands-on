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
$ docker run -d --name nginx-test -p 8080:80 nginx # 8080でリクエストを受けて、コンテナがLISTENしているport 80に受け流す意味です
$ docker ps # コンテナの状態を確認します
```
- ブラウザから http://ec2-54-199-108-124.ap-northeast-1.compute.amazonaws.com:8080/ へアクセスします。アドレスは自分のインスタンスに置き換えてください。port開放してないないのでアクセスできないはずです。
- httpの8080ポートを開放
前回の演習資料: https://github.com/tmoritoki0227/cloudnative-hands-on/blob/main/Aws.md
- ブラウザから http://ec2-54-199-108-124.ap-northeast-1.compute.amazonaws.com:8080/ へアクセスできることを確認
![image](https://user-images.githubusercontent.com/20149115/177033144-7a9876f7-4b9a-4d4f-8706-926a763448fb.png)



```bash
$ docker container stop nginx-test # コンテナを停止します。
$ docker ps # コンテナの状態を確認します
```

## 自分でDockerイメージを作ろう

### Dockerfile作成
```bash
$ mkdir ~/docker
$ cd ~/docker
$ git clone https://github.com/tmoritoki0227/cloudnative-hands-on.git
$ vim Dockerfile
```

```bash
FROM amazonlinux:2

LABEL version="1.0"
LABEL description="amazonlinux2にnginxを入れて立ち上げ"

RUN amazon-linux-extras install -y nginx1 # nginxインストール

# 自分用にindex.htmlをカスタマイズしたものを配置
COPY ./cloudnative-hands-on/conf/html/index.html /usr/share/nginx/html/
COPY ./cloudnative-hands-on/conf/html/yoshi.jpeg /usr/share/nginx/html/

CMD ["nginx", "-g", "daemon off;"] # nginxの起動。
```
※`CMD ["nginx", "-g", "daemon off;"] `の意味をもう少し知りたい人は[こちら](https://tottoto-toto.hatenablog.com/)

### Dockerイメージの作成
作成したDockerfileを利用してDockerイメージを作成します。
```bash
$ docker image build -t my_nginx:latest .
```
ちょっと時間がかかります。以下の出力があれば成功です。
```
Successfully built 027dfbdbb347
Successfully tagged my_nginx:latest
```

### Dockerイメージの確認
imageが作られたことを確認します。
```bash
$ docker image ls
```

以下のような出力があればOKです。
```
REPOSITORY    TAG       IMAGE ID       CREATED         SIZE
my_nginx      latest    027dfbdbb347   2 minutes ago   535MB
amazonlinux   2         ef0e8aec8ddc   2 weeks ago     164MB
```

### nginxのコンテナ起動
```bash
$ docker run -d --name my_nginx -p 8080:80  my_nginx:latest
$ docker ps # コンテナ状態確認
```
- `-d` デーモンで起動（バックグラウンドで起動）
- `--name my_nginx` コンテナ名
- `-p 8080:80` listenするポート。8080でリクエストを受けて80に流す
- `my_nginx:latest` 作成したイメージ名

以下がコンテナが起動した状態です。
```
CONTAINER ID   IMAGE             COMMAND                  CREATED         STATUS         PORT                                    NAMES
e0228103ccbb   my_nginx:latest   "nginx -g 'daemon of…"   2 minutes ago   Up 2 minutes   0.0.0.0:8080->80/tcp, :::8080->80/tcp   my_nginx
```

### port開放
- tcp 8080

### ブラウザから動作確認
- http://ec2-54-199-108-124.ap-northeast-1.compute.amazonaws.com:8080 をブラウザで開きます。my_nginxの画面が表示されればOKです。


### 起動中のコンテナに入る
```bash
$ docker exec -it my_nginx /bin/bash

$ ls # ここはコンテナ内で実行するコマンドです。任意のコマンド実行してください。ただし実行できるコマンドは少ないです。
$ exit # コンテナから抜ける
```

### 作ったコンテナを停止、削除
```bash
$ docker container stop my_nginx
$ docker rm my_nginx
$ docker ps # コンテナ状態確認
```

以下の状態でコンテナが停止した状態です。
```
CONTAINER ID   IMAGE     COMMAND   CREATED   STATUS    PORTS     NAMES
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
作成したmy_nginxをアップロードします。これもアップロードするときのお決まりの手順です。
```bash
$ docker image ls # 現在の状態を確認
$ docker tag my_nginx tmoritoki0227/my_nginx:latest # 説明できませんがtag付けが必要です。
$ docker image ls # 現在の状態を確認.tag名のついたimageが追加されてます。
$ docker push tmoritoki0227/my_nginx:latest # アップロードします。
```
- `tmoritoki0227`はdockerhubのアカウント名に合わせないとだめ
- コマンド成功後、https://hub.docker.com/ を確認しアップロードされたことを確認します。
-  AWSのネットワーク外に通信するのでおそらく課金されます。楽しみにしていてください。

### dockerイメージ(my_nginx)を削除する
my_nginxがローカルにあるとそれを使ってしまうため削除します
```bash
$ docker image rmi tmoritoki0227/my_nginx
$ docker image rmi my_nginx
$ docker image ls
```

### アップロードしたdockerイメージを使ってみる
```bash
$ docker pull tmoritoki0227/my_nginx:latest
$ docker run -d --name my_nginx -p 8080:80 tmoritoki0227/my_nginx:latest
```
`docker pull`時に表示されるログにダウンロード状況の表示がない場合は、ローカルにあるイメージを使ってますので、注意してください。

### ブラウザからアクセスする
- http://ec2-54-199-108-124.ap-northeast-1.compute.amazonaws.com:8080

## 後始末
コンテナ停止、削除とイメージ全削除します。インスタンスのディスク空き容量は６GBぐらいしかないので、イメージが増えるとすぐ一杯になります。不要イメージはすぐ削除しましょう。
```bash
$ docker stop $(docker ps -q) ;docker rmi $(docker images -q) -f;docker system prune -a
$ docker image ls;docker ps -a
```
`Are you sure you want to continue? [y/N]`が出てきたら`y`を入力してください。<br>
※docker psで表示するものがないとエラーになりますが、問題はないです
