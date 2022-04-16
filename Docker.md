# EC2にdockerインストール
```
sudo yum install -y docker
sudo systemctl start docker
sudo systemctl status docker
sudo systemctl enable docker
sudo usermod -a -G docker ec2-user

## dockerグループがなければ作る
sudo groupadd docker

## 現行ユーザをdockerグループに所属させる
sudo gpasswd -a $USER docker

## dockerデーモンを再起動する (CentOS7の場合)
sudo systemctl restart docker

## exitして再ログインすると反映される。
exit
```

## Dockerfile作成
```
mkdir ~/docker
vim ~/docker/Dockerfile
vim ~/docker/index.html
```


```ruby:sushi.rb
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

## index.html
hello
```

## docker buildを試す
https://scrapbox.io/llminatoll/docker_run%E3%81%AE%E3%82%AA%E3%83%97%E3%82%B7%E3%83%A7%E3%83%B3%E3%81%84%E3%82%8D%E3%81%84%E3%82%8D

```
sudo docker image build -t moritoki/sample:latest .
sudo docker image build -t test_httpserver:latest .
```

## イメージ確認
```
sudo docker image ls
```

## 起動中のコンテナに入る
```
docker exec -i -t CONTAINER_ID /bin/bash
docker exec -i -t CONTAINER_ID /bin/sh
```

## イメージの取得とコンテナに入る
```
sudo docker run -it --name test_dock moritoki/sample:latest /bin/bash
sudo docker run -it --name test_grok dalongrong/grok-exporter:latest /bin/bash
sudo docker run -it --name test_httpserver test_httpserver:latest /bin/bash
```


## コンテナ起動とバックグラウンド起動
なぜか10000超えると動かないように思える

```
### うごかん
sudo docker run -d --name test_dock -p 10080:80 moritoki/sample:latest  # 10080がダメなのか？

### コンテナを動かす（入らない）
sudo docker run -d --name test_dock -p 8000:80 tmoritoki0227/sample:latest 
sudo docker run -d --name test_dock -p 80:80 tmoritoki0227/sample:latest

sudo docker run -d --name test_httpserver -p 8080:8080 -p 8081:8081 test_httpserver:latest
```

疎通確認

```
# ec2
nc -vz localhost 8080
# local(Mac)
nc -vz ec2-35-77-196-144.ap-northeast-1.compute.amazonaws.com 8080
```
ブラウザから
http://ec2-35-77-196-144.ap-northeast-1.compute.amazonaws.com:8080/hello
http://ec2-35-77-196-144.ap-northeast-1.compute.amazonaws.com:8080/world
http://ec2-35-77-196-144.ap-northeast-1.compute.amazonaws.com:8081/metrics

## docker hub upload
https://gray-code.com/blog/container-image-push-for-dockerhub/

https://hub.docker.com/repository/docker/tmoritoki0227/study

https://hub.docker.com/r/tmoritoki0227/study

```
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

## コンテナ起動確認
```

sudo docker ps
sudo docker ps -a # 停止中のコンテナも表示
```

## コンテナに入る
```
sudo docker attach CONTAINER ID
```

## コンテナ停止
```
sudo docker container stop
```

## コンテナ削除
```
sudo docker rm [コンテナID]
```

## イメージ削除
```
sudo docker images で
sudo docker rmi イメージID
```

## Dockerコマンドをsudoなしで実行する方法
非推奨らしい

```
## dockerグループがなければ作る
sudo groupadd docker

## 現行ユーザをdockerグループに所属させる
sudo gpasswd -a $USER docker

## dockerデーモンを再起動する (CentOS7の場合)
sudo systemctl restart docker

## exitして再ログインすると反映される。
exit
```
