<?php
session_start();
mb_internal_encoding("utf8");

if (!empty($_SESSION["name"])) {
    $input["name"] = htmlentities($_SESSION["name"], ENT_QUOTES);
    $input["mail"] = htmlentities($_SESSION["mail"], ENT_QUOTES);
    $input["age"] = htmlentities($_SESSION["age"], ENT_QUOTES);
    $input["password"] = htmlentities($_SESSION["password"], ENT_QUOTES);
    $input["comments"] = htmlentities($_SESSION["comments"], ENT_QUOTES);
} else {
    header("Location: index.php");
} //それぞれ入力された値がSESSIONにセットされていなければ、入力画面にリダイレクトの処理を行う

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>フォームを作る</title>
    <link rel="stylesheet" type="text/css" href="style2.css">
</head>

<bady>
    <h1>内容確認</h1>
    <div class="confirm">
        <p>
            内容はこちらで宜しいでしょうか?<br>
            宜しければ「登録する」ボタンを押してください。
        </p>
        <p>名前<br><?php echo $input["name"]; ?></p>
        <p>メールアドレス<br><?php echo $input["mail"]; ?></p>
        <p>年齢<br><?php echo $input["age"] . "歳"; ?></p>
        <p>パスワード<br>**********</p>
        <p>コメント<br><?php echo $input["comments"]; ?></p>
        <form action="index.php">
            <input type="submit" class="button1" value="戻って修正する">
        </form>
        <!--「index.php」から引き渡された値をここでサイド箱の中に格納し、「insert.php」へ引き渡す-->
        <!--type="hidden"にすることで、代入した内容を隠す(ブラウザ上に表示しない)ことができる-->
        <form action="insert.php" method="POST">
            <input type="hidden" value="<?php echo $input["name"]; ?>" name="name">
            <input type="hidden" value="<?php echo $input["mail"]; ?>" name="mail">
            <input type="hidden" value="<?php echo $input["age"]; ?>" name="age">
            <input type="hidden" value="<?php echo $input["password"]; ?>" name="password">
            <input type="hidden" value="<?php echo $input["comments"]; ?>" name="comments">
            <input type="submit" class="button2" value="登録する">
        </form>
    </div>
</bady>

</html>