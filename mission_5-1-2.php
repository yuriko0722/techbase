<!DOCTYPE html>
<html lang = "ja">
<head>
    <meta charset = "UTF-8">
    <title>mission_5-1</title>
</head>
<body>

～いまのきもち掲示板～<br><br>

<?PHP
// DB接続設定 //SQLの文字列リテラルはシングルクォーテーションで囲む
$dsn = 'データベース名';//$dsnの式の中にスペースを入れないこと！
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//テーブルを作成
$sql = "CREATE TABLE IF NOT EXISTS table_5"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY," //idは自動設定かつプライマリーキーを使い識別
    . "name varchar(32)," //nameカラムは32文字以内可変で保存
    . "comment TEXT," //テキスト型
    . "date DATETIME," //時刻型
    . "pass char(16)" //16文字固定で保存
    .");";
$stmt = $pdo->query($sql);

//投稿機能
if(isset($_POST["name"]) && $_POST["name"] != "" &&
   isset($_POST["comment"]) && $_POST["comment"] != "" && 
   isset($_POST["pass"]) && $_POST["pass"] != "" &&
   empty($_POST["id_edit2"])){
    //レコード挿入準備
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $date = date('Y/m/d H:i:s');
    $pass = $_POST["pass"];
    //sql文「レコードを挿入する」
    $sql = 'INSERT INTO table_5 (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)';
    //フォームからの入力情報をsql文に含める準備（prepare）
    $statement = $pdo -> prepare($sql);
    //sql文にフォームからの入力情報を代入
    $statement -> bindParam(':name', $name, PDO::PARAM_STR); //bindParam('パラメータ', 変数, 型)
    $statement -> bindParam(':comment', $comment, PDO::PARAM_STR);
    $statement -> bindParam(':date', $date, PDO::PARAM_STR);
    $statement -> bindParam(':pass', $pass, PDO::PARAM_STR);
    //SQLを実行
    $statement -> execute();
}
//削除機能
if(isset($_POST["id_del"]) && $_POST["id_del"] != "" && 
   isset($_POST["pass_del"]) && $_POST["pass_del"] != "" ){
    //入力フォームから削除対象番号とパスワードを取得
    $id = $_POST["id_del"];
    $pass = $_POST["pass_del"];
    //番号とパスワードが入力フォームからの情報と一致するレコードを削除する
    //sql文「レコードを削除する」
    $sql = 'DELETE from table_5 where id=:id and pass=:pass';
    //フォームからの入力情報をsql文に含める準備（prepare）
    $stmt = $pdo -> prepare($sql);
    //フォームからの入力情報をsql文にbind
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);//パラメータ（:id）に変数（$id）を代入。ただし数値に限る。
    $stmt -> bindParam(':pass', $pass, PDO::PARAM_STR);
    //SQLを実行
    $stmt->execute();
}

//編集機能（テーブルの書き換え）
if(isset($_POST["name"]) && $_POST["name"] != "" && 
   isset($_POST["comment"]) && $_POST["comment"] != "" &&
   isset($_POST["pass"]) && $_POST["pass"] != "" &&
   isset($_POST["id_edit2"]) && $_POST["id_edit2"] != ""){
    //入力フォームからid,name,comment,passを取得しそれぞれ変数に代入。dateも変数に代入。
    $id = $_POST["id_edit2"];
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $date = date('Y/m/d H:i:s');
    $pass = $_POST["pass"];
    //idが入力フォームからの情報と一致するレコードを編集する
    //SQL文「レコードを変更する」
    $sql = 'UPDATE table_5 SET name=:name,comment=:comment,date=:date,pass=:pass WHERE id=:id';
    //フォームからの入力情報をSQL文に含める準備（prepare）
    $stmt = $pdo->prepare($sql);
    //フォームからの入力情報をSQL文にbind
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
    $stmt -> bindParam(':pass', $pass, PDO::PARAM_STR);
    //SQL文を実行
    $stmt->execute();    
}
//編集機能（編集する投稿の選択）
if(isset($_POST["id_edit1"]) && $_POST["id_edit1"] != "" &&
   isset($_POST["pass_edit"]) && $_POST["pass_edit"]){
    //入力フォームから編集対象番号とパスワードを取得
    $id = $_POST["id_edit1"];
    $pass = $_POST["pass_edit"];

    // $sql = 'SELECT * FROM table_5'; 
    // $stmt = $pdo->query($sql);
    // $results = $stmt->fetchAll();
    // foreach ($results as $row){
    //     if($row['pass']==$pass && $row['id']==$id){
    //         $edit_id=$row[0]; 
    //         $edit_name=$row[1];
    //         $edit_comment=$row[2];
    //         $edit_pass=$row[4];
    //     }
    // }

    //番号とパスワードが入力フォームからの情報と一致するレコードを抽出する(別の方法（未完成）)
    //SQL文「レコードを抽出する」
    $sql = 'SELECT * FROM table_5 WHERE id=:id and pass=:pass';
    //SQL文を実行（入力フォームからの情報が含まれない命令なので、executeなしですぐに実行される）
    $stmt = $pdo->prepare($sql);
    //フォームからの入力情報をsql文にbind
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);//パラメータ（:id）に変数（$id）を代入。ただし数値に限る。
    $stmt -> bindParam(':pass', $pass, PDO::PARAM_STR);
    //SQLを実行
    $stmt->execute();

    //抽出したレコードを読み込む
    $results = $stmt->fetchAll();
    //入力フォームに表示させるものをそれぞれ変数で用意する
    foreach($results as $row){
        $edit_id = $row['id'];
        $edit_name = $row['name'];
        $edit_comment = $row['comment'];
    }
}
?>
<!--投稿内容（名前、コメント、編集対象番号（隠す））送信フォーム-->
【 投稿フォーム 】
<form action = "" method = "post">
    <input type = "text" name = "name" placeholder = "名前" value = "<?php if(isset($edit_name)){echo $edit_name;} ?>"><br>
    <input type = "text" name = "comment" placeholder = "コメント" value = "<?php if(isset($edit_comment)){echo $edit_comment;} ?>"><br>
    <input type = "password" name = "pass" placeholder = "パスワード">
    <input type = "hidden" name = "id_edit2" placeholder = "編集対象番号" value = "<?php if(isset($edit_id)){echo $edit_id;} ?>">
    <input type = "submit" name ="submit">
</form><br>
<!--削除対象番号送信フォーム-->
【 削除フォーム 】
<form action = "" method = "post">
        <input type = "number" name = "id_del" placeholder = "削除対象番号"><br>
        <input type = "password" name = "pass_del" placeholder = "パスワード">
        <input type = "submit" name = "submit" value = "削除">
</form><br>
<!--編集対象番号送信フォーム-->
【 編集フォーム 】
<form action = "" method = "post">
        <input type = "number" name = "id_edit1" placeholder = "編集対象番号"><br>
        <input type = "password" name = "pass_edit" placeholder = "パスワード">
        <input type = "submit" name = "submit" value = "編集">
</form><br>
<?PHP
//テーブルの内容をブラウザに表示する

echo "【 投稿一覧 】<br>";

$sql = 'SELECT * FROM table_5';
$stmt = $pdo -> query($sql);
$results = $stmt->fetchAll();
foreach ($results as $row){
    echo $row['id'].',';
    echo $row['name'].',';
    echo $row['comment'].',';
    echo $row['date'].'<br>';
    echo "<hr>";
}
// //SQL文「テーブルを表示する」
// $sql = 'SHOW CREATE TABLE table_5';
// $result = $pdo -> query($sql);
// foreach($result as $row){
//     echo $row['id'].',';
//     echo $row['name'].',';
//     echo $row['comment'].',';
//     echo $row['date'].'<br>';
// }
// echo "<hr>";
?>
</body>
</html>