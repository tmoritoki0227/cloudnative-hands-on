<?php
//変数に文字列を代入
$text_1 = "帰りたい。<span>あぁ帰りたい。
やっぱ帰りたい。</span>さりとて帰りたい。";
?>

<!-- HTML5の文書型宣言 -->
<!DOCTYPE html>
<!-- 「lang属性」でコードが何語か設定宣言 -->
<html lang="ja">
<head>

<!-- 文字エンコーディングの設定宣言 -->
<meta charset="UTF-8">

<!-- title宣言 -->
<title>空の画像を表示しよう</title>

<!-- スタイルシートCSS設定 -->
<style>
div{
/* 文字の色 */
color:#ff0000;
/* ブラウザで表示される幅 */
width:50%;
/* 上の余白の設定 */
margin-top:20px;
/* 下記2行でコンテンツを中央寄せにする */
margin-right:auto;
margin-left:auto;
}
span {
/* 文字の色 */
color:#009900;
}
h1 {
/* 文字の色 */
color:#660000;
/* 文字の大きさ */
font-size:30px;
}
</style>

</head>
<body>
<div>
<h1>PHPプログラミング</h1>
<!-- 画像を表示 -->
<img src="yoshi.jpeg" width="600px" height="400px" alt="yoshi">
<p>
<?php echo $text_1; ?>
</p>
<?php phpinfo(); ?>
</div>
</body>
</html>
