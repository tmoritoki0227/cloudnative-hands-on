## EC2の基本的な操作
[資料](https://github.com/kichiram/aws)を参考に以下の操作に慣れてください。リージョンは東京を使います。
- `ec2`の画面を開く。画面上部の検索窓に`ec2`と入力
- キーペアを作ってない人は先に作成してください。左メニューの`ネットワーク & セキュリティ`→`キーペア`をクリック。
  - キーペアのタイプ: `RSA`
  - プライベートキーファイル形式: `.pem`　（puttyユーザは.ppkでよいです）
  - 秘密鍵は任意の場所に保存してください。一般的にはホームディレクトリの下の.sshディレクトリに配置します。
- インスタンスの作成（起動）
  - OSは`amazonlinux`で。
  - 最近インスタンス作成のUIがかなり変わりましたので、資料と画面が異なります。
- インスタンス開始・停止
  - インスタンスの停止起動するとIPは変わります。再起動では変更されません。有料で固定IPを使うことはできます。
  - 開始と起動が間違えやすい。起動はインスタンス作成です。
- ポート解放
  - 自宅のグローバルIPを許可しないとインスタンスにsshできません。0.0.0.0/0　は全てのIPを許可です。
- インスタンスへのsshログイン
  - インスタンスを選択し、画面上部の`接続`ボタン→ `SSHクライアント`でsshコマンドが表示されます。-iオプションで保存した秘密鍵の場所を指定してください。
- インスタンス削除（終了）
- 費用の確認方法

## 適当にセットアップしてみよう
※ これからsudoコマンドを使いますが、ec2-userのデフォルトのsudoパスワードは設定されていませんのでパスワードの入力は求められません。
### nginxインストール
```
$ sudo amazon-linux-extras install nginx1 -y 
$ sudo systemctl enable nginx # 自動起動設定
$ sudo systemctl start nginx　 # nginx起動
$ sudo systemctl status nginx # Active: active (running)であること
```
- ブラウザでアクセスしてみる。プロトコルはhttpで、IPは利用しているインスタンスのIP（パブリックDNS)に読み替えてください。アクセスするとポート開放されてないのでエラーになります。
  - http://ec2-18-179-29-96.ap-northeast-1.compute.amazonaws.com
- セキュリティグループのインバウンドのルールの編集でport:80を解放
- 再度ブラウザでアクセス.画面が表示されます

### gitインストール
```
$ sudo yum install git
$ git clone https://github.com/tmoritoki0227/cloudnative-hands-on.git
```
他にも試してみてください。
### 課金について
- インスタンスが起動している時間分だけ課金されます。

## 他のサービスを試してみよう。
よく耳にするAWS Lambdaをやってみます。Lambdaとは簡単にいうとサーバなしでプログラムを実行する方法です。ここではブラウザにアクセスすると"Hello, from Lambda"を表示するプログラムを作ります。そしてAPI GatewayでそのLambdaプログラムにブラウザ等からアクセスできるように設定をします。画像付きの説明を見たい方は[資料](https://predora005.hatenablog.com/entry/2021/05/08/190000)を参考にしてください

### `Lambda`の画面を開いて（画面上部の検索窓に`Lambda`と入力）
  - "一から作成"を選択
  - 関数名: myfunction
  - ランタイム: Python 3.9   (何でもいいと思います）
  - アーキテクチャ：　x８６_６４
  - 関数の作成ボタン押下。"Hello, from Lambda"を返すだけの関数（プログラム）が作成されます。
### `API Gateway`の画面で（画面上部の検索窓に`API Gateway`と入力）
  - HTTP APIの構築ボタン押下
  - 統合： Lambdaを選択、
  - api名: my-http-api
  - あとはデフォルトのままで進める。URLを呼び出すのURLをコピーする
### ブラウザからアクセス
  - https://[コピーしたAPIのURL]/myfunction
    - 例) https://p73v0e26mf.execute-api.ap-northeast-1.amazonaws.com/myfunction
  - "Hello from Lambda!"が表示されればOK
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
