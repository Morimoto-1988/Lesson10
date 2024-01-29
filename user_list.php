<?php
mb_internal_encoding("utf8");

try {
    $pdo = new PDO("mysql:dbname=php_practice;host=localhost;", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query("SELECT * FROM user");  //SELECT文で固定のSQL文の場合はquery()を使用する。このメソッドは、テーブルから取得した行にアクセスするためのオブジェクトを返す
} catch (PDOException $e) {
    $e->getMessage();
}

$pdo = null;
//文字列キーによる配列としてテーブル全行取得
//PDO::FETCH_ASSOC  文字列キーによる配列として行を取得
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);  //fetchAll()メソッドを使用して取り出したオブジェクト($stmt)からテーブルの中身を全行取り出す

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ユーザーリスト</title>
    <style>
        td {
            padding: 7px 40px;
        }
    </style>
</head>

<body>
    <h1>ユーザーリスト</h1>
    <div class="confirm">
        <table>
            <tr>
                <th>ID</th>
                <th>名前</th>
                <th>メールアドレス</th>
                <th>年齢</th>
                <th>コメント</th>
            </tr>
            <?php
            foreach ($users as $user) {  //取り出した行をforeach文を使って順番に画面表示させる
                echo "<tr>\n";
                echo "<td>{$user['id']}</td>\n";
                echo "<td>{$user['name']}</td>\n";
                echo "<td>{$user['mail']}</td>\n";
                echo "<td>{$user['age']}</td>\n";
                echo "<td>{$user['comments']}</td>\n";
                echo "</tr>\n";
            }
            ?>
        </table>
    </div>
</body>

</html>