## EC2の基本的な操作
[資料](https://github.com/kichiram/aws)を参考に以下の操作に慣れてください。リージョンは東京を使います。！！始める前にPCのOSを教えてください。Windows or Macbook！！
###  `ec2`の画面を開く。

- 画面上部の検索窓に`ec2`と入力

![image](https://user-images.githubusercontent.com/20149115/214306979-bdd9c24d-20f2-4b84-8f9e-9c6505316fdd.png)

### キーペアを作ってない人は先に作成してください。

- 左メニューの`ネットワーク & セキュリティ`→`キーペア`をクリック。
- 名前: 任意。例`id_rsa` この名前が多いです。
- キーペアのタイプ: `RSA`
- プライベートキーファイル形式: `.pem`　（puttyユーザは.ppkでよいです）
- 保存場所: 任意。一般的にはホームディレクトリの下の.sshディレクトリに保存します。(Windowsの例`C:\Users\自分の名前\.ssh\id_rsa`)
- macの場合はchmod 400

![image](https://user-images.githubusercontent.com/20149115/214307317-a10d7c71-f9ea-4a0b-892a-24255b2f818e.png)

### インスタンスの起動(インスタンスの作成）

- インスタンスを起動ボタンをクリック。インスタンス起動とはインスタンスを作成する意味です。初心者は間違えやすいポイントです。 インスタンスとはサーバのことです。サーバがわからなければ業務用PCだと思ってください。
- OSは`amazonlinux`、作成したキーペアの名前を指定する。あとはデフォルトのままでOKです。
- Awsの操作画面はよく変更が入るので資料と画面が異なる可能性があります。

![image](https://user-images.githubusercontent.com/20149115/214305329-a022fdb7-911b-4e1f-8cf1-f054641027d4.png)

### インスタンス停止

- `インスタンスを停止`をクリック。これはインスタンスを停止する操作です。よくわからなければPCの電源スイッチをOFFにすると思ってください。
- インスタンスを停止すると次回開始するときにIPは変わります。再起動では変更されません。有料で固定IPを使うことはできます。

![image](https://user-images.githubusercontent.com/20149115/214306010-bda04dde-dfcf-43d9-a2c4-b221848a2b47.png)



### インスタンス開始

- `インスタンスを開始`をクリック。インスタンスを開始する操作です。よくわからなければPCの電源スイッチをONにすると思ってください。`インスタンスを起動`と間違えやすいので注意。

![image](https://user-images.githubusercontent.com/20149115/214309159-dc899a10-41e5-4644-9c33-6970b037e110.png)


### ポート開放のやり方を覚える

- ポート開放とは外部（インターネット）からのアクセスを受け付けられるようにする行為です。詳細はググってみましょう。
- たとえば`port 22`のポート開放をしないと、インスタンスにsshできません。この設定はデフォルトで入っているので確認してみましょう。
- 0.0.0.0/0　は全てのIPを意味します。
- インスタンスを選択し、インスタンス詳細画面の`セキュリティタブ`→`セキュリティグループ`→`インバウンドのルールの編集` 

![image](https://user-images.githubusercontent.com/20149115/214309643-1395e09f-140d-444a-846e-aee13bfbeb84.png)
---
![image](https://user-images.githubusercontent.com/20149115/214309762-eb31a8c0-ed2d-4567-bdc0-ce087a67d965.png)
---
![image](https://user-images.githubusercontent.com/20149115/214309868-49b7ecad-93ed-4369-966b-241f8115cc33.png)





### インスタンスへのsshログイン。ログイン方法はいろいろありますが、ここではsshログインを紹介します。

- [powershell](https://mimimopu.com/powershell-ssh-client/)等のsshクライアントを用意してください。
- インスタンスを選択し、画面上部の`接続`ボタン→ `SSHクライアント`でsshコマンドが表示されます。コピーしてください。
- インスタンスにsshログインしてください。-iオプションでは保存した秘密鍵のファイル名を指定してください。
  ```
  $ cd 秘密鍵を保存したディレクトリ
  $ ssh -i "id_rsa.pem" ec2-user@ec2-3-113-4-139.ap-northeast-1.compute.amazonaws.com
  ```
- ちょっと幸せになれる~/.ssh/configの設定。設定すると`ssh ec2-3-113-4-139.ap-northeast-1.compute.amazonaws.com`でログイン可能になるはずです。
  ```
  User ec2-user
  IdentityFile ~/.ssh/id_rsa
  StrictHostKeyChecking no
  ```
  
![image](https://user-images.githubusercontent.com/20149115/214310548-633dd07e-0524-4389-a5de-c6ac9b04648a.png)
---

![image](https://user-images.githubusercontent.com/20149115/214310702-b9a9b136-5cbe-4e20-b021-ad1ca0dc24b7.png)



### インスタンス終了
- `インスタンスを終了`をクリック。インスタンスを削除する操作です。終了したインスタンスは停止、開始はできません。

![image](https://user-images.githubusercontent.com/20149115/214314453-3dc44f05-47bf-4407-b739-197234919905.png)


### 費用の確認方法
  - 画面右上の`自分のアカウント名`→`請求ダッシュボード`  

![image](https://user-images.githubusercontent.com/20149115/214311066-c375425a-71ff-4539-a94f-494500265327.png)


## 適当にセットアップしてみよう
※ これからsudoコマンドを使いますが、ec2-userのデフォルトのsudoパスワードは設定されていませんのでパスワードの入力は求められません。
### nginxインストール
[nginxとは...](https://www.kagoya.jp/howto/it-glossary/web/nginx/)

```bash
$ sudo amazon-linux-extras install nginx1 -y 
$ sudo systemctl enable nginx  # 自動起動設定
$ sudo systemctl start nginx　 # nginx起動
$ sudo systemctl status nginx  # Active: active (running)であること
```
- ブラウザでアクセスしてみる。プロトコルはhttpで、IPは利用しているインスタンスのIP（パブリックDNS)に読み替えてください。アクセスするとポート開放されてないのでエラーになります。
  - http://ec2-18-179-29-96.ap-northeast-1.compute.amazonaws.com
- セキュリティグループのインバウンドのルールの編集でport:80を開放
- 再度ブラウザでアクセスするとnginxのwelcome画面が表示されます

### gitインストール
```
$ sudo yum install git
$ git clone https://github.com/tmoritoki0227/cloudnative-hands-on.git
```
他にも試してみてください。
### 課金について
- インスタンスが起動している時間分だけ課金されます。

## 他のサービスを試してみよう。
よく利用されているAWS Lambdaをやってみます。Lambdaとは簡単にいうとサーバなしでプログラムを実行する方法です。ここではブラウザにアクセスすると"Hello, from Lambda"を表示するプログラムを作ります。そしてAPI GatewayでそのLambdaプログラムにブラウザ等からアクセスできるように設定をします。画像付きの説明を見たい方は[資料](https://predora005.hatenablog.com/entry/2021/05/08/190000)を参考にしてください

### `Lambda`の画面を開いて（画面上部の検索窓に`Lambda`と入力）
- `関数の作成`ボタンをクリック
- "一から作成"を選択
- 関数名: `myfunction`
- ランタイム: `Python 3.9`   (何でもいいと思います）
- アーキテクチャ：　`x86_64`
- 関数の作成ボタン押下。"Hello, from Lambda"を返すだけの関数（プログラム）が自動作成されます。
### `API Gateway`の画面で（画面上部の検索窓に`API Gateway`と入力）
- `HTTP API`の`構築`ボタンをクリック
- 統合： Lambdaを選択、
- Lambda 関数: `myfunction`を選択
- api名: `my-http-api`
- あとはデフォルトのままで`次へ`ボタンをクリックし進める。
- `URLを呼び出す`のURLをコピーする
### ブラウザからアクセス
- https://[コピーしたAPIのURL]/myfunction
  - 例) https://p73v0e26mf.execute-api.ap-northeast-1.amazonaws.com/myfunction
- "Hello from Lambda!"が表示されればOK
- httpでアクセスすると動きません。httpsでアクセスすること。
### 課金について
- プログラムにアクセスすると課金が発生する仕組みになっています。利用されない間は課金されません。

## 演習はここで終了です。課金が不安な人は作ったものを削除しましょう
- インスタンス
- Lambda
- API Gateway

## インスタンスを作っただけのときのシステム構成図の紹介
![image](https://user-images.githubusercontent.com/20149115/163699566-6b8a83c3-ca91-4e92-bd6f-be10d0d5bb13.png)
- vpc、サブネットは自動で作成してくれています。

## awsが推奨しているシステム構成図の紹介
![image](https://user-images.githubusercontent.com/20149115/163699639-9ffaef8b-3363-42e3-832b-9e92907ae501.png)
- こんな感じで障害耐性が取られています。この演習では覚える必要はありません。


## 任意設定作業
興味があれば実施してください。
- インスタンス自動停止設定（費用が心配な人は設定しましょう）
  - [EC2 インスタンスの起動と停止を自動化することは出来ますか？](https://dev.classmethod.jp/articles/tsnote-ec2-ssm-automation/)
- AwsCLI
  - [AWS CLIを利用するメリットと導入方法](https://www.cloudsolution.tokai-com.co.jp/white-paper/2021/0617-239.html)
  - [AWS CLIとは？インストール手順や使い方を初心者向けに解説！](https://udemy.benesse.co.jp/development/system/aws-cli.html)
- [AWSアカウント作成後に絶対にやるべき初期設定5項目：前半](https://kacfg.com/aws-first-config_1/)
  - ルートアカウントにMFAを設定する、はおすすめ
