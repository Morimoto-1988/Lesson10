<?php
mb_internal_encoding("utf8");  //DBへ情報を格納する際に、文字化けしないようにするための決まり文句
session_start();

//パスワードのハッシュ化
//「ハッシュ化」とは、ハッシュ関数によって文字列を置換して元の文字を推測できなくすること
$password = password_hash($_POST["password"], PASSWORD_DEFAULT);

try {  //try部分。{}の中に例外処理を記述する。ここではPDOでデータベース接続の処理を記述。データベースに接続できない場合はcatch(){}の処理を行う

    //pdo = new PDO  DBと接続するための決まり文句
    //mysql:dbname=php_practice  MySQLに接続し、DB「php_practice」を利用するという意味
    //host=localhost  通常はDB用のサーバー名を記述するが、今回は自分のPC(=ローカル環境)のXAMPPを利用しているのでこのような記述になる
    //"root", ""  サーバー接続する際のIDとパスワードを記述。XAMPPの初期状態ではID:root、PW:なしなのでこのような記述になる
    $pdo = new PDO("mysql:dbname=php_practice;host=localhost;", "root", "");  //DBに接続

    //setAttribute()メソッドでエラーモードの設定ができる
    //PDO::ERRMODE_EXCEPTION(例外モード)  例外をスローしてくれる。PDOが生成する例外を処理しない限りプログラムは停止する。デバッグに適したモード
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  //エラーモードを「例外」に設定

    //プリペアードステートメント部分。
    //prepareの次にはSQL文を記述し、valueの次には疑問符型のプレースホルダーを使用する
    //また、全体を「$stmt」に代入する。$stmt部分の変数名は何でもOK
    $stmt = $pdo->prepare("INSERT INTO user(name, mail, age, password, comments) VALUES(?,?,?,?,?)");  //疑問符型のプレースホルダーなので(?,?,?,?)と記述する

    //execute()でSQLを実行する。引数としてプレースホルダーにバインドする値を記述
    //1つ目の?(=name)に$_POST["name"]をバインド、2つ目の?(=mail)に$_POST["mail"]をバインド
    //3つ目の?(=age)に$_POST["age"]をバインド、4つ目の?(=comments)に$_POST["comments"]をバインド、という意味
    $stmt->execute(array($_POST["name"], $_POST["mail"], $_POST["age"], $password, $_POST["comments"]));

    //catchの例外の種類を記述する部分には「PDOException(PDO関連の例外)」を記述し、その隣に「$e」を記述。ここには何らかの変数を記述する必要がある。変数名は何でもOK
} catch (PDOException $e) {
    $e->getMessage();  //例外発生時にエラーメッセージを出力
}

//DB切断
$pdo = null; //これを記述することでDBを切断できる。必要な処理を完了したら、セキュリティ上必ずDBを切断すること

//セッション変数を全て解除する
$_SESSION = array();

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), "", time() - 1800, "/");  //sessionIDの削除
}

session_destroy();  //セッションの破棄

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>フォームを作る</title>
    <link rel="stylesheet" type="text/css" href="style2.css">
</head>

<body>
    <h1>登録完了</h1>
    <div class="confirm">
        <p>登録有難うございました。</p>
        <form action="index.php">
            <input type="submit" class="button1" value="TOPに戻る">
        </form>
    </div>
</body>

</html>