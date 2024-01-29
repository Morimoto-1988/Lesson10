<?php
session_start();  //SESSIONを使用するときは宣言が必要
mb_internal_encoding("utf8");

//変数の初期値
$errors = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {  //アクセスの仕方がPOST通信かGET通信かを判別している
    //POST処理
    //エスケープ処理  不正なスクリプトが埋め込まれていないかXSS対策を行う
    $_SESSION["name"] = htmlentities($_POST["name"] ?? "", ENT_QUOTES);
    $_SESSION["mail"] = htmlentities($_POST["mail"] ?? "", ENT_QUOTES);
    $_SESSION["age"] = htmlentities($_POST["age"] ?? "", ENT_QUOTES);
    $_SESSION["password"] = htmlentities($_POST["password"] ?? "", ENT_QUOTES);
    $_SESSION["comments"] = htmlentities($_POST["comments"] ?? "", ENT_QUOTES);
    //バリデーションチェック
    $errors = validate_form();
    if (empty($errors)) {  //empty()関数は変数が空かどうかを調べる。ここではエラーがあるかどうかを判断している
        //エラーがなければ、完了ページに遷移
        header("Location:confirm.php");  //header()関数は引数に指定したURLにリダイレクト(遷移)する関数
    }
}

//バリデーションチェックを行う関数
function validate_form()
{
    //エラーメッセージを初期化
    $form_errors = array();

    //1.氏名のバリデーション
    //半角スペースを取り除く
    $input["name"] = trim($_POST["name"] ?? "");  //$_POSTが設定されていない時に備えて、null合体演算子で対応
    //半角スペースを取り除いた後の要素の長さを調べる
    if (strlen($input["name"]) == 0) {  //入力されているかの確認
        //要素の長さが0ならエラーを代入
        $form_errors["name"] = "氏名を入力してください";
    }

    //2.メールのバリデーション
    $input["mail"] = filter_input(INPUT_POST, "mail", FILTER_VALIDATE_EMAIL);  //INPUT_POST, "mail"  $_POST["mail"]が判定対象。FILTER_VALIDATE_EMAIL  値がe-mailアドレスかを判定。判定結果が有効な場合はその値を返し、判定結果が有効でない場合は「false」を返す
    if (!$input["mail"]) {
        $form_errors["mail"] = "メールアドレスは正しい形で入力してください";
    }

    //3.年齢のバリデーション
    $options = array(
        "options" => array(
            "min_range" => 18,
            "max_range" => 65,  //min_rangeとmax_rangeオプションを使って、数値の有効範囲を指定
        )
    );
    $input["age"] = filter_input(INPUT_POST, "age", FILTER_VALIDATE_INT, $options);  //FILTER_VALIDATE_INT  整数かどうかを判定
    if (is_null($input["age"]) || $input["age"] === false) {
        $form_errors["age"] = "年齢は18歳以上、65歳以下で入力して下さい";  //"age"という要素が存在しないか、判定結果が整数でない場合にエラーメッセージを追加する
    }

    //4.コメントのバリデーション
    $input["comments"] = trim($_POST["comments"] ?? "");  //$_POSTが設定されていないときに備えて、null合体演算子で対応
    if (strlen($input["comments"]) == 0) {  //入力されているかの確認
        $form_errors["comments"] = "コメントを入力してください";
    }

    //5.パスワードのバリデーション
    $input["password"] = trim($_POST["password"] ?? "");  //$_POSTが設定されていない時に備えて、null合体演算子で対応
    if (strlen($input["password"]) == 0) {  //入力されているかの確認
        $form_errors["password"] = "パスワードを入力してください";
    }

    return $form_errors;
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>フォームを作る</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <h1 class="form_title">登録フォーム</h1>
    <!--ファイル間で何らかの情報を引き渡す方法を「POSTメソッド(=POST通信)」という。自ファイルに値を渡す場合actionの中身は何も記入しない-->
    <form method="POST" action="">
        <div class="item">
            <label>名前</label>
            <input type="text" class="text" size="35" name="name" value="<?php echo $_SESSION["name"] ?? ""; ?>">
            <?php if (!empty($errors["name"])) : ?> <!--エラー時や内容修正で戻ってきたときに、一度入力した内容があらかじめ入っている状態にする-->
                <p class="err_message"><?php echo $errors["name"]; ?></p> <!--エラーがあればメッセージを表示する-->
            <?php endif; ?> <!--PHPのif文やfor文の制御機構には別の書き方があり、endif;を使う-->
        </div>
        <div class="item">
            <label>メールアドレス</label>
            <input type="text" class="text" size="35" name="mail" value="<?php echo $_SESSION["mail"] ?? ""; ?>">
            <?php if (!empty($errors["mail"])) : ?>
                <p class="err_message"><?php echo $errors["mail"]; ?></p>
            <?php endif; ?>
        </div>
        <div class="item">
            <label>年齢</label>
            <input type="number" class="text" size="35" name="age" value="<?php echo $_SESSION["age"] ?? ""; ?>">
            <?php if (!empty($errors["age"])) : ?>
                <p class="err_message"><?php echo $errors["age"]; ?></p>
            <?php endif; ?>
        </div>
        <div class="item">
            <label>パスワード</label>
            <input type="password" class="text" size="35" name="password" value="<?php echo $_SESSION["password"] ?? ""; ?>">
            <?php if (!empty($errors["password"])) : ?>
                <p class="err_message"><?php echo $errors["password"]; ?></p>
            <?php endif; ?>
        </div>
        <div class="item">
            <label>コメント</label>
            <textarea cols="35" rows="7" name="comments"><?php echo $_SESSION["comments"] ?? ""; ?></textarea>
            <?php if (!empty($errors["comments"])) : ?>
                <p class="err_message"><?php echo $errors["comments"]; ?></p>
            <?php endif; ?>
        </div>
        <div class="item">
            <input type="submit" class="submit" value="入力内容を確認する">
        </div>
    </form>
</body>

</html>