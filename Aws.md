## Awsを使ってEC2に関して以下の練習を行います。
これらの操作を覚えてください。参考資料は[こちら](https://github.com/kichiram/aws)
- インスタンス（サーバ）の作成、osはamazonlinuxで。
- インスタンス（サーバ）のポート解放（これは正確にはセキュリティグループの設定です）
- インスタンス（サーバ）起動・停止
- インスタンス（サーバ）へのsshログイン
- インスタンス（サーバ）削除

## ec2を作っただけのときのシステム構成図
![image](https://user-images.githubusercontent.com/20149115/163699566-6b8a83c3-ca91-4e92-bd6f-be10d0d5bb13.png)
- vpc、サブネットは自動で作成してくれています。

## awsが推奨しているシステム構成図
![image](https://user-images.githubusercontent.com/20149115/163699639-9ffaef8b-3363-42e3-832b-9e92907ae501.png)
- こんな感じです。
