## EC2の基本的な操作
[資料](https://github.com/kichiram/aws)を参考に以下の操作に慣れてください。リージョンは東京を使います。
- インスタンスの作成
  - osはamazonlinuxで。
  - 最近インスタンス作成のUIがかなり変わりましたので、資料と画面が異なります。
- インスタンス起動・停止
  - インスタンスの停止起動すると変わります。再起動では変更されません。有料で固定IPを使うことはできます。
- ポート解放
  - 自宅のグローバルIPを許可しないとインスタンスにsshできません。0.0.0.0/0　は全てのIPを許可です。
- インスタンスへのsshログイン
  - ec2-userはsudoパスワードは無しになってます。
- インスタンス削除（終了）
- 費用の確認方法

## 試しにnginx導入してみる
- nginxインストール
```
$ sudo amazon-linux-extras install nginx1 -y # awsではこのコマンドです。
$ sudo systemctl enable nginx # 自動起動設定
$ sudo systemctl start nginx　 # nginx起動
$ sudo systemctl status nginx # Active: active (running)であること
```
- ブラウザでアクセスしてみる。エラーになります。IPは自分のIPに読み替えてください。
http://ec2-18-179-29-96.ap-northeast-1.compute.amazonaws.com
- セキュリティグループのインバウンドのルールの編集でport:80を解放
- 再度ブラウザでアクセス.画面が表示されます

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

