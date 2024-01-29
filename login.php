<?php
session_start();
mb_internal_encoding("utf8");

//ログイン状態であれば、マイページにリダイレクト
if (isset($_SESSION['id'])) { //ログイン状態か把握するために、isset関数とsession配列のid(idでなくともOK)を使用する
    header("Location:mypage.php");
}

//変数の初期値
$errors = "";

//POST処理
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //エスケープ処理
    $input["mail"] = htmlentities($_POST["mail"] ?? "", ENT_QUOTES);
    $input["password"] = htmlentities($_POST["password"] ?? "", ENT_QUOTES);

    //1.バリデーションのチェック
    if (!filter_input(INPUT_POST, "mail", FILTER_VALIDATE_EMAIL)) { //メールの形式確認
        $errors = "メールアドレスとパスワードを正しく入力してください。";
    }
    if (strlen(trim($_POST["password"] ?? "")) == 0) { //入力されているかの確認
        $errors = "メールアドレスとパスワードを正しく入力してください。";
    }
    //2.ログイン認証
    if (empty($errors)) {
        //DBに接続
        try {
            $pdo = new PDO("mysql:dbname=php_practice;host=localhost;", "root", ""); //DBに接続
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //エラーモードを「例外」に設定
            //入力されたメールアドレスをもとにユーザー情報を取り出す
            $stmt = $pdo->prepare("SELECT * FROM user WHERE mail = ?");
            $stmt->execute(array($input["mail"]));
            $user = $stmt->fetch(PDO::FETCH_ASSOC); //文字列キーによる配列としてテーブル取得
        } catch (PDOException $e) {
            echo mb_convert_encoding($e->getMessage(), 'UTF-8', 'sjis'); //例外発生時にエラーメッセージを出力
        }

        //DBを切断
        $pdo = NULL;

        //ユーザー情報が取り出せた　かつ　パスワードが一致すれば、セッションに値を代入しマイページへ遷移
        /*password_verify()とは、パスワードがハッシュ値に適合するかどうかを調査する関数
        第1引数に平文のパスワード、第2引数にハッシュ値を指定し、パスワードとハッシュが適合する場合にTRUE、それ以外の場合にFALSEを返す*/
        if ($user && password_verify($input["password"], $user["password"])) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['mail'] = $user['mail'];
            $_SESSION['age'] = $user['age'];
            $_SESSION['password'] = $input['password'];
            $_SESSION['comments'] = $user['comments'];

            //「ログイン情報を保持する」にチェックがあれば、セッションにセットする
            if ($_POST['login_keep'] == 1) {
                $_SESSION['login_keep'] = $_POST['login_keep'];
            }

            //「ログイン情報を保持する」にチェックがあればクッキーをセット、無ければ削除する
            /*ログインに成功している、かつ$_SESSION[login_keep]が空でない場合(チェックを入れている場合)、cookieにデータを保存する
            time()+60*60*24*7で現在日時から7日後の日時を計算して、有効期限を7日後としている*/
            if (!empty($_SESSION['id']) && !empty($_SESSION['login_keep'])) {
                setcookie('mail', $_SESSION['mail'], time() + 60 * 60 * 24 * 7);
                setcookie('password', $_SESSION['password'], time() + 60 * 60 * 24 * 7);
                setcookie('login_keep', $_SESSION['login_keep'], time() + 60 * 60 * 24 * 7);
                /*$_SESSION[login_keep]が空の場合(チェックを入れていない場合)、cookieのデータを削除する
            time()-1で過去の時間を指定した事となり、cookieからデータを削除できる*/
            } else if (empty($_SESSION['login_keep'])) {
                setcookie('mail', '', time() - 1);
                setcookie('password', '', time() - 1);
                setcookie('login_keep', '', time() - 1);
            }
            header("Location:mypage.php");
        } else {
            $errors = "メールアドレスとパスワードを正しく入力してください。";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ログインページ</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <h1 class="form_title">ログインページ</h1>
    <form method="POST" action="">
        <div class="item">
            <label>メールアドレス</label>
            <input type="text" class="text" size="35" name="mail" value="<?php //「ログイン状態を保持する」にチェックを入れていた場合、メールアドレスとパスワードの入力項目に、cookieからの値を呼び出して表示させる
                                                                            if ($_COOKIE['login_keep'] ?? '') {
                                                                                echo $_COOKIE['mail'];
                                                                            }
                                                                            ?>">
        </div>
        <div class="item">
            <label>パスワード</label>
            <input type="password" class="text" size="35" name="password" value="<?php
                                                                                    if ($_COOKIE['login_keep'] ?? '') {
                                                                                        echo $_COOKIE['password'];
                                                                                    }
                                                                                    ?>">
            <?php if (!empty($errors)) : ?>
                <p class="err_message"><?php echo $errors; ?></p>
            <?php endif; ?>
        </div>
        <div class="item">
            <label>
                <input type="checkbox" name="login_keep" value="1" <?php
                                                                    if ($_COOKIE['login_keep'] ?? '') {
                                                                        echo "checked = 'checked'"; //前回チェックを入れた場合、自動的にチェックが入るようにする
                                                                    }
                                                                    ?>>ログイン状態を保持する
            </label>
        </div>

        <div class="item">
            <input type="submit" class="submit" value="ログイン">
        </div>
    </form>
</body>

</html>