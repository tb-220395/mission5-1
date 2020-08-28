<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>mission5-1</title>
</head>
<body>


<?php
    //DB接続設定4-1
    $dsn = 'mysql:dbname=データベース名;host=localhost';
	$user = 'ユーザ名';
	$password = 'パスワード';
	//// MySQL接続,array以降は、警告を表すためのもの
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    //テーブル作成4-2
    $sql = "CREATE TABLE IF NOT EXISTS ms51tb"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32) not null,"//データを追加するときに必ず値を設定する必要があるカラムにはnot nullと設定
    . "comment TEXT not null,"
    . "date TEXT not null,"
    . "pass TEXT not null"
	.");";
    $stmt = $pdo->query($sql);

  //編集ボタンが押されたとき（投稿フォームに表示）
  if(!empty($_POST["editNo"])&&!empty($_POST["editpass"])){
    $editNo=$_POST["editNo"];
    $edpass=$_POST["editpass"];
    $sql = 'SELECT * FROM ms51tb';//データ抽出4-6
	 $stmt = $pdo->query($sql);
	 $results = $stmt->fetchAll();
	 foreach ($results as $row){
      if($editNo==$row['id'] && $edpass==$row['pass']){
         //投稿フォームに呼び起こす
         $editName=$row['name'];
         $editComment=$row['comment'];
         $editNumber=$row['id'];
         $editPass=$row['pass']; 
      }
     }
    }

  //投稿フォームの動き
  if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass"])){
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $pass = $_POST["pass"];
    $date=date("Y/m/d H:i:s");
    $edit_post=$_POST["edit_post"];
    //新規投稿の場合4-5
     if(empty($edit_post)){
        $sql = $pdo -> prepare("INSERT INTO ms51tb (name, comment,date,pass) VALUES (:name, :comment,:date,:pass)");
        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
        $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
        $name = $_POST["name"];
        $comment = $_POST["comment"];
        $date=date("Y/m/d H:i:s");
        $pass = $_POST["pass"];
        $sql -> execute();
    //編集の場合（上書きする）
    }else{
        $id = $edit_post; //変更する投稿番号4-7
	    $name = $_POST["name"];
        $comment = $_POST["comment"];
        $date=date("Y/m/d H:i:s");
        $pass = $_POST["pass"];
	    $sql = 'UPDATE ms51tb SET name=:name,comment=:comment,date=:date,pass=:pass WHERE id=:id';
	    $stmt = $pdo->prepare($sql);// ←差し替えるパラメータを含めて記述したSQLを準備し、
	    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
	    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
	    $stmt->execute(); // ←SQLを実行する。
        
    }
  }

   //削除ボタンが押されたとき
   if(!empty($_POST["deleteNo"]) && !empty($_POST["deletepass"])){
    $deleteNo=$_POST["deleteNo"];
    $deletepass=$_POST["deletepass"];
     $sql = 'SELECT * FROM ms51tb';//データ抽出
	 $stmt = $pdo->query($sql);
	 $results = $stmt->fetchAll();
	 foreach ($results as $row){
      if($deleteNo==$row['id'] && $deletepass==$row['pass']){//番号とパスワード一致しているとき
          $id=$deleteNo;//4-8
        $sql = 'delete from ms51tb where id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute(); 
      }
     }
   }
	
?>

<span style="font-size:30px; color:maroon">好きな季節を教えてください。</span>
<form action="" method="post">
    <input type="text" name="name" placeholder="名前" value="<?php if(!empty($editName)){echo $editName;} ?>"><br>
    <input type="text" name="comment" placeholder="コメント"value="<?php if(!empty($editComment)){echo $editComment;}?>"><br>
    <input type="text" name="pass" placeholder="パスワード" value="<?php if(!empty($editPass)){echo $editPass;}?>"><br>
    <input type="hidden" name="edit_post" value="<?php if(!empty($editNumber)){echo $editNumber;} ?>">
    <input type="submit" name="submit"><br>
    <br>
    <input type="text" name="deleteNo" placeholder="削除対象番号"><br>
    <input type="text" name="deletepass" placeholder="パスワード"><br>
    <input type="submit" name="deletebt" value="削除"><br>
    <br>
    <input type="text" name="editNo" placeholder="編集対象番号"><br>
    <input type="text" name="editpass" placeholder="パスワード"><br>
    <input type="submit" name="editbt" value="編集"><br>
    <hr>
  </form>
  <span style="font-size:20px; color:maroon">《投稿内容》</span><br>  
    
<?php
//表示機能（4の抽出機能を用いる。)4-6
 $sql = 'SELECT * FROM ms51tb';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].',';
    echo $row['name'].',';
    echo $row['comment'].',';
		echo $row['date'].'<br>';
	echo "<hr>";
	}
?>
</body>
</html>