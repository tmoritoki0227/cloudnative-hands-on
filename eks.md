## AWSのEKSとは
- https://aws.amazon.com/jp/eks/
- １コマンドでkubernetesクラスタが作れることがわかった。UIを使うとしんどい。
- PC上でkubernetes操作したいだけならもっと簡単と思われる。https://birthday.play-with-docker.com/kubernetes-docker-desktop/

## EKSの構築手順
### awscliを利用可能にする
- https://github.com/tmoritoki0227/cloudnative-hands-on/blob/main/Aws.md#%E4%BB%BB%E6%84%8F%E8%A8%AD%E5%AE%9A%E4%BD%9C%E6%A5%AD
- これは一度実行すれば、次回skip可能です。

### INSTALLING CHOCOLATEY
- powershellを管理者で起動して、以下のコマンドを実行する。するとCHOCOLATEYがインストールされます。
```bash
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://community.chocolatey.org/install.ps1'))
```
- これは一度実行すれば、次回skip可能です。<br>
- [CHOCOLATEYとは](https://tomosta.jp/2019/06/chocolatey/)

###  EKSの構築
引き続きpowershellで以下を実行します。クラスタが作成されます。
```bash
eksctl create cluster --name moritoki --region ap-northeast-1  --node-type t2.micro --nodes 2 --nodes-min 2 --nodes-max 2 
```
- 完了後、https://ap-northeast-1.console.aws.amazon.com/eks/home?region=ap-northeast-1#/home eksをみるとクラスタができていることがわかる
- ３０分かかる 
- nameには自動で-clusterが付与されます
- クラスタへの接続設定もしてくれます`~/.kube/config`
- cloudformationをみると２つのスタックが動いてることがわかる

### kubectlをインストール
- 引き続きpowershellを管理者で起動し[kubectlをインストール](https://kubernetes.io/docs/tasks/tools/install-kubectl-windows/#install-on-windows-using-chocolatey-or-scoop)します。
    
- これは一度実行すれば、次回skip可能です。<br>
### 動作確認（基本編）
```bash
# nodeの確認
kubectl get node

# namespaceの確認
kubectl get namespace

# podの確認
kubectl get pod

# pod起動
kubectl run nginx --image=nginx:latest

# 外部に公開
kubectl expose pod nginx --port 80 --type LoadBalancer # この後EC２のロードバランサ画面をみるとロードバランサができている

# pod,service状況確認
kubectl get pod,service
NAME        READY   STATUS    RESTARTS   AGE
pod/nginx   1/1     Running   0          12m

NAME            TYPE           CLUSTER-IP     EXTERNAL-IP                                                                    PORT(S)        AGE
service/nginx   LoadBalancer   10.100.16.97   ac85d2e8341ae4629a2913ab9e6e8e44-1878198863.ap-northeast-1.elb.amazonaws.com   80:31761/TCP   18s
```

この後に http://ac85d2e8341ae4629a2913ab9e6e8e44-1878198863.ap-northeast-1.elb.amazonaws.com:80 でnginxの画面が表示される。外部からアクセスするのでEXTERNAL-IPを使う <br>
![image](https://user-images.githubusercontent.com/20149115/192526746-96abe7ba-db56-4ac6-aed2-934b7587efcc.png)

### 動作確認（応用編）
障害時のセルフヒーリングとロードバランシングを確認する。
```bash
# まずはこれまで作ったpodとサービス削除
kubectl delete pod,service nginx nginx

# 複数のpodを起動.replicaは2にしときましょう。3はスペックが足りません
kubectl create deployment hello-nginx --image=nginx:latest --replicas=2

# 外部に公開（ロードバランシング）
kubectl expose deployment hello-nginx --port 80 --type LoadBalancer

# deployment,pod,serviceの起動確認
kubectl get deployment,pod,service

# ブラウザで確認.以下は例です。
http://aab6b67dac06c4576b21c64d153d60b8-696062537.ap-northeast-1.elb.amazonaws.com:80

# podを1台削除します。
kubectl get pod
kubectl delete pod NAME
このあとブラウザで確認。１台は稼働継続中なのでまだ見れるはず。

# podを2台をすばやく削除します。
kubectl get pod
kubectl delete pod NAME NAME
このあとブラウザで確認。最初は見れませんが、自動で復旧し見れるようになります。（セルフヒーリング）

# serviceの削除（以降はクラスタ削除で代用可能
kubectl delete service hello-nginx

# deployment(pod)の削除
kubectl delete deployment hello-nginx

# deployment,pod,serviceの起動確認
kubectl get deployment,pod,service
```



### クラスタ削除
```bash
eksctl delete cluster --name moritoki --wait
```
- 10分ぐらいかかる
- 消し忘れると課金

8.  .kubeフォルダを削除した方がいいかもしれない。任意です。
```bash
C:\Users\user\.kube
```
## 参考
- https://docs.aws.amazon.com/ja_jp/eks/latest/userguide/eksctl.html
- https://dev.classmethod.jp/articles/getting-started-amazon-eks-with-eksctl/
- https://developer.mamezou-tech.com/containers/k8s/tutorial/infra/aws-eks-eksctl/#%E5%8B%95%E4%BD%9C%E7%A2%BA%E8%AA%8D

## おまけ
### EKS作成コマンドの実行ログ
30分かかります。
```
＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊
PS C:\Windows\system32> eksctl create cluster --name moritoki-cluster --region ap-northeast-1  --node-type t2.micro --nodes 2 --nodes-min 2 --nodes-max 2
2022-07-12 21:24:35 [ℹ]  eksctl version 0.105.0
2022-07-12 21:24:35 [ℹ]  using region ap-northeast-1
2022-07-12 21:24:35 [ℹ]  setting availability zones to [ap-northeast-1c ap-northeast-1a ap-northeast-1d]
2022-07-12 21:24:35 [ℹ]  subnets for ap-northeast-1c - public:192.168.0.0/19 private:192.168.96.0/19
2022-07-12 21:24:35 [ℹ]  subnets for ap-northeast-1a - public:192.168.32.0/19 private:192.168.128.0/19
2022-07-12 21:24:35 [ℹ]  subnets for ap-northeast-1d - public:192.168.64.0/19 private:192.168.160.0/19
2022-07-12 21:24:35 [ℹ]  nodegroup "ng-664bd91b" will use "" [AmazonLinux2/1.22]
2022-07-12 21:24:35 [ℹ]  using Kubernetes version 1.22
2022-07-12 21:24:35 [ℹ]  creating EKS cluster "moritoki-cluster" in "ap-northeast-1" region with managed nodes
2022-07-12 21:24:35 [ℹ]  will create 2 separate CloudFormation stacks for cluster itself and the initial managed nodegroup
2022-07-12 21:24:35 [ℹ]  if you encounter any issues, check CloudFormation console or try 'eksctl utils describe-stacks --region=ap-northeast-1 --cluster=moritoki-cluster'
2022-07-12 21:24:35 [ℹ]  Kubernetes API endpoint access will use default of {publicAccess=true, privateAccess=false} for cluster "moritoki-cluster" in "ap-northeast-1"
2022-07-12 21:24:35 [ℹ]  CloudWatch logging will not be enabled for cluster "moritoki-cluster" in "ap-northeast-1"
2022-07-12 21:24:35 [ℹ]  you can enable it with 'eksctl utils update-cluster-logging --enable-types={SPECIFY-YOUR-LOG-TYPES-HERE (e.g. all)} --region=ap-northeast-1 --cluster=moritoki-cluster'
2022-07-12 21:24:35 [ℹ]
2 sequential tasks: { create cluster control plane "moritoki-cluster",
    2 sequential sub-tasks: {
        wait for control plane to become ready,
        create managed nodegroup "ng-664bd91b",
    }
}
2022-07-12 21:24:35 [ℹ]  building cluster stack "eksctl-moritoki-cluster-cluster"
2022-07-12 21:24:35 [ℹ]  deploying stack "eksctl-moritoki-cluster-cluster"
2022-07-12 21:25:05 [ℹ]  waiting for CloudFormation stack "eksctl-moritoki-cluster-cluster"
2022-07-12 21:25:36 [ℹ]  waiting for CloudFormation stack "eksctl-moritoki-cluster-cluster"
2022-07-12 21:26:36 [ℹ]  waiting for CloudFormation stack "eksctl-moritoki-cluster-cluster"
2022-07-12 21:27:36 [ℹ]  waiting for CloudFormation stack "eksctl-moritoki-cluster-cluster"
2022-07-12 21:28:36 [ℹ]  waiting for CloudFormation stack "eksctl-moritoki-cluster-cluster"
2022-07-12 21:29:36 [ℹ]  waiting for CloudFormation stack "eksctl-moritoki-cluster-cluster"
2022-07-12 21:30:36 [ℹ]  waiting for CloudFormation stack "eksctl-moritoki-cluster-cluster"
2022-07-12 21:31:36 [ℹ]  waiting for CloudFormation stack "eksctl-moritoki-cluster-cluster"
2022-07-12 21:32:36 [ℹ]  waiting for CloudFormation stack "eksctl-moritoki-cluster-cluster"
2022-07-12 21:47:38 [ℹ]  building managed nodegroup stack "eksctl-moritoki-cluster-nodegroup-ng-664bd91b"
2022-07-12 21:48:30 [ℹ]  deploying stack "eksctl-moritoki-cluster-nodegroup-ng-664bd91b"
2022-07-12 21:48:31 [ℹ]  waiting for CloudFormation stack "eksctl-moritoki-cluster-nodegroup-ng-664bd91b"
2022-07-12 21:49:01 [ℹ]  waiting for CloudFormation stack "eksctl-moritoki-cluster-nodegroup-ng-664bd91b"
2022-07-12 21:49:58 [ℹ]  waiting for CloudFormation stack "eksctl-moritoki-cluster-nodegroup-ng-664bd91b"
2022-07-12 21:50:51 [ℹ]  waiting for CloudFormation stack "eksctl-moritoki-cluster-nodegroup-ng-664bd91b"
2022-07-12 21:51:26 [ℹ]  waiting for CloudFormation stack "eksctl-moritoki-cluster-nodegroup-ng-664bd91b"
2022-07-12 21:52:57 [ℹ]  waiting for CloudFormation stack "eksctl-moritoki-cluster-nodegroup-ng-664bd91b"
2022-07-12 21:52:57 [ℹ]  waiting for the control plane availability...
2022-07-12 21:52:59 [✔]  saved kubeconfig as "C:\\Users\\user\\.kube\\config"
2022-07-12 21:52:59 [ℹ]  no tasks
2022-07-12 21:52:59 [✔]  all EKS cluster resources for "moritoki-cluster" have been created
2022-07-12 21:52:59 [ℹ]  nodegroup "ng-664bd91b" has 2 node(s)
2022-07-12 21:52:59 [ℹ]  node "ip-192-168-3-44.ap-northeast-1.compute.internal" is ready
2022-07-12 21:52:59 [ℹ]  node "ip-192-168-76-242.ap-northeast-1.compute.internal" is ready
2022-07-12 21:52:59 [ℹ]  waiting for at least 2 node(s) to become ready in "ng-664bd91b"
2022-07-12 21:52:59 [ℹ]  nodegroup "ng-664bd91b" has 2 node(s)
2022-07-12 21:52:59 [ℹ]  node "ip-192-168-3-44.ap-northeast-1.compute.internal" is ready
2022-07-12 21:52:59 [ℹ]  node "ip-192-168-76-242.ap-northeast-1.compute.internal" is ready
2022-07-12 21:53:05 [ℹ]  kubectl command should work with "C:\\Users\\user\\.kube\\config", try 'kubectl get nodes'
2022-07-12 21:53:05 [✔]  EKS cluster "moritoki-cluster" in "ap-northeast-1" region is ready
PS C:\Windows\system32>
PS C:\Windows\system32>
PS C:\Windows\system32>
PS C:\Windows\system32>
PS C:\Windows\system32>
PS C:\Windows\system32>
PS C:\Windows\system32>

おそらくワーカーノードだけが提供されている。マネージドサービスなのでcontrolplaneは提供されない？
PS C:\Windows\system32> kubectl get node
NAME                                                STATUS   ROLES    AGE     VERSION
ip-192-168-3-44.ap-northeast-1.compute.internal     Ready    <none>   3m30s   v1.22.9-eks-810597c
ip-192-168-76-242.ap-northeast-1.compute.internal   Ready    <none>   3m24s   v1.22.9-eks-810597c
PS C:\Windows\system32> kubectl get namespace
NAME              STATUS   AGE
default           Active   23m
kube-node-lease   Active   23m
kube-public       Active   23m
kube-system       Active   23m
PS C:\Windows\system32> kubectl get pod
No resources found in default namespace.
＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊


PS C:\Windows\system32> kubectl run -i --tty busybox --image=busybox -- sh
If you don't see a command prompt, try pressing enter.
/ # wget -qO- 192.168.88.96:80
<!DOCTYPE html>
<html>
<head>
<title>Welcome to nginx!</title>
<style>
html { color-scheme: light dark; }
body { width: 35em; margin: 0 auto;
font-family: Tahoma, Verdana, Arial, sans-serif; }
</style>
</head>
<body>
<h1>Welcome to nginx!</h1>
<p>If you see this page, the nginx web server is successfully installed and
working. Further configuration is required.</p>

<p>For online documentation and support please refer to
<a href="http://nginx.org/">nginx.org</a>.<br/>
Commercial support is available at
<a href="http://nginx.com/">nginx.com</a>.</p>

<p><em>Thank you for using nginx.</em></p>
</body>
</html>
/ #

＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊
PS C:\Windows\system32> eksctl delete cluster --name moritoki-cluster --wait
2022-07-12 22:13:15 [ℹ]  deleting EKS cluster "moritoki-cluster"
2022-07-12 22:13:16 [ℹ]  will drain 0 unmanaged nodegroup(s) in cluster "moritoki-cluster"
2022-07-12 22:13:16 [ℹ]  starting parallel draining, max in-flight of 1
2022-07-12 22:13:16 [ℹ]  deleted 0 Fargate profile(s)
2022-07-12 22:13:16 [✔]  kubeconfig has been updated
2022-07-12 22:13:16 [ℹ]  cleaning up AWS load balancers created by Kubernetes objects of Kind Service or Ingress
2022-07-12 22:13:54 [ℹ]
2 sequential tasks: { delete nodegroup "ng-664bd91b", delete cluster control plane "moritoki-cluster"
}
2022-07-12 22:13:54 [ℹ]  will delete stack "eksctl-moritoki-cluster-nodegroup-ng-664bd91b"
2022-07-12 22:13:54 [ℹ]  waiting for stack "eksctl-moritoki-cluster-nodegroup-ng-664bd91b" to get deleted
2022-07-12 22:13:54 [ℹ]  waiting for CloudFormation stack "eksctl-moritoki-cluster-nodegroup-ng-664bd91b"
2022-07-12 22:14:24 [ℹ]  waiting for CloudFormation stack "eksctl-moritoki-cluster-nodegroup-ng-664bd91b"
2022-07-12 22:15:05 [ℹ]  waiting for CloudFormation stack "eksctl-moritoki-cluster-nodegroup-ng-664bd91b"
2022-07-12 22:16:58 [ℹ]  waiting for CloudFormation stack "eksctl-moritoki-cluster-nodegroup-ng-664bd91b"
2022-07-12 22:17:52 [ℹ]  waiting for CloudFormation stack "eksctl-moritoki-cluster-nodegroup-ng-664bd91b"
2022-07-12 22:19:10 [ℹ]  waiting for CloudFormation stack "eksctl-moritoki-cluster-nodegroup-ng-664bd91b"
2022-07-12 22:19:59 [ℹ]  waiting for CloudFormation stack "eksctl-moritoki-cluster-nodegroup-ng-664bd91b"
2022-07-12 22:20:58 [ℹ]  waiting for CloudFormation stack "eksctl-moritoki-cluster-nodegroup-ng-664bd91b"
2022-07-12 22:22:13 [ℹ]  waiting for CloudFormation stack "eksctl-moritoki-cluster-nodegroup-ng-664bd91b"
2022-07-12 22:22:13 [ℹ]  will delete stack "eksctl-moritoki-cluster-cluster"
2022-07-12 22:22:13 [ℹ]  waiting for stack "eksctl-moritoki-cluster-cluster" to get deleted
2022-07-12 22:22:13 [ℹ]  waiting for CloudFormation stack "eksctl-moritoki-cluster-cluster"
2022-07-12 22:22:43 [ℹ]  waiting for CloudFormation stack "eksctl-moritoki-cluster-cluster"
2022-07-12 22:23:20 [ℹ]  waiting for CloudFormation stack "eksctl-moritoki-cluster-cluster"
2022-07-12 22:24:39 [ℹ]  waiting for CloudFormation stack "eksctl-moritoki-cluster-cluster"
2022-07-12 22:24:40 [✔]  all cluster resources were deleted
PS C:\Windows\system32>
```

### 課金されるサービス
![image](https://user-images.githubusercontent.com/20149115/192515500-3874e407-198c-4161-b6d8-14dbc810f731.png)
１０円ぐらいかな。
