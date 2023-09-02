<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
    <!--入力フォーム POSTメソッド -->
    <form action="" method="post">
        <p>パスワード：<input type="password" name="password" placeholder="password"><br>
        <input type="str" name="name" placeholder="名前">
        <input type="str" name="str" placeholder="コメント">
        <input type="submit" name="submit_button"><br>
        <input type="number" name="delete" placeholder="削除したい番号">
        <input type="submit" name="delete_button", value="削除"><br>
        <input type="number" name="edit_number" placeholder="編集したい番号"value="<?php
        if(isset($_POST["edit_number"]) && empty($_POST["edit_str"])) {
            echo $_POST["edit_number"];
        }
        ?>">
       <input type="str" name="edit_name" placeholder="編集する名前" value="<?php
       if (isset($_POST["edit_number"]) && empty($_POST["edit_str"])) {
           // DB接続設定
           $dsn = '***********';
           $user = '*******';
           $password = '*******';
           $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
           $sql = 'SELECT * FROM comments WHERE id = :id';
           $stmt = $pdo->prepare($sql);
           $stmt->bindParam(':id', $_POST["edit_number"], PDO::PARAM_INT);
           $stmt->execute();
           $results = $stmt->fetch();
           if ($results) {
               if(isset($results['deleted']) && $results['deleted'] == 0){
                   echo $results['name'];
               }
           }
           
       }
       ?>">
       <input type="str" name="edit_str" placeholder="編集後コメント" value="<?php
       if (isset($_POST["edit_number"]) && empty($_POST["edit_str"])) {
           // DB接続設定
           $dsn = '*******';
           $user = '*******';
           $password = '*******';
           $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
           $sql = 'SELECT * FROM comments WHERE id = :id';
           $stmt = $pdo->prepare($sql);
           $stmt->bindParam(':id', $_POST["edit_number"], PDO::PARAM_INT);
           $stmt->execute();
           $results = $stmt->fetch();
           if ($results) {
               if(isset($results['deleted']) && $results['deleted'] == 0){
                   echo $results['comment'];
               }
           }
       }
       ?>">
       <input type="submit" name="edit_button", value="編集"><br><br>
        
    </form>
    <?php
    
    // DB接続設定
    $dsn = '*******';
    $user = '*******';
    $password = '*******';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    //テーブル作成
    //テーブル"comments"作成
    $sql = "CREATE TABLE IF NOT EXISTS comments"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "password CHAR(64),"
    . "create_date TIMESTAMP,"
    . "update_date TIMESTAMP,"
    . "deleted BOOLEAN DEFAULT FALSE"
    .");";
    $stmt = $pdo->query($sql);
    
    //リクエストメソッドがPOSTであるかどうかを識別
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        //名前入力されているかどうか
        $isNameEmpty = (!isset($_POST['name']) || empty($_POST['name']));
        //コメントが入力されているかどうか
        $isStrEmpty = (!isset($_POST['str']) || empty($_POST['str']));
        //削除したい番号が入力されているかどうか
        $isDeleteEmpty = (!isset($_POST['delete']) || empty($_POST['delete']));
        //編集したい番号が入力されているかどうか
        $isEditNumberEmpty = (!isset($_POST['edit_number']) || empty($_POST['edit_number']));
        //パスワードが入力されているかどうか
        $isPasswordEmpty = (!isset($_POST['password']) || empty($_POST['password']));
        
        if ($isNameEmpty && $isDeleteEmpty && $isEditNumberEmpty) {
            echo "<FONT COLOR='RED'>名前が入力されていません<br></FONT>";
        }
        if ($isStrEmpty && $isDeleteEmpty && $isEditNumberEmpty) {
            echo "<FONT COLOR='RED'>コメントが入力されていません<br></FONT>";
        }
        if($isPasswordEmpty){
            echo "<FONT COLOR='RED'>パスワードが入力されていません<br></FONT>";
        }
        //編集番号と削除番号が両方入力されていた場合
        if(!($isDeleteEmpty) && !($isEditNumberEmpty)){
            echo "<FONT COLOR='RED'>削除したい番号と編集したい番号は別々に入力してください<br></FONT>";
            //処理を停止
            exit;
        }
        
        //フォームの入力を取得
        $str = $_POST["str"];
        $name = $_POST["name"];
        $delete = $_POST["delete"];
        $edit_number = $_POST["edit_number"];
        $edit_name = $_POST["edit_name"];
        $edit_str = $_POST["edit_str"];
        $password = $_POST["password"];
        
        //現在時刻を取得
        $date = date("Y/m/d H:i:s");
        
        //正常に投稿された場合
        if(isset($_POST["submit_button"])){
            if (!empty($str) && !empty($name) && !($isPasswordEmpty)) {
                echo "「".$str."」を受け付けました!<br><br>";
                //commentsテーブルに投稿文を挿入
                $sql = "INSERT INTO comments (name, comment, password, create_date, update_date, deleted) VALUES (:name, :comment, :password, :date, :date, FALSE)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $str, PDO::PARAM_STR);
                $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                $stmt->execute();
                //正常メッセージ表示
                echo "<FONT COLOR='RED'>書き込み成功！<br><br><br></FONT>";
            }
        }
        
        
        //削除する番号がある場合
        if(isset($_POST["delete_button"])){
            //削除番号とパスワードが入力されているか確認
            if(!($isDeleteEmpty) && !($isPasswordEmpty)){
                //テーブルの中身を照合
                $sql = 'SELECT * FROM comments WHERE id = :id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $delete, PDO::PARAM_INT);
                //$stmt = $pdo->query($sql);
                $stmt->execute(); // クエリを実行
                $results = $stmt->fetchAll();
                //テーブルにその番号を持つ行が存在しない場合
                if(empty($results)){
                    echo "削除できる番号がありません<br>";
                }else{
                    //テーブルからパスワードを抽出
                    $user_password = $results[0]['password'];
                    //削除されているかどうか（1なら論理削除されている）
                    $is_deleted = $results[0]['deleted'];
                    //パスワードが一致しているかどうかチェック
                    if($user_password == $password){
                        //削除されているかどうかチェック
                        if($is_deleted == 0){
                            $sql = 'UPDATE comments SET update_date=:date,deleted=TRUE WHERE id=:id';
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                            $stmt->bindParam(':id', $delete, PDO::PARAM_INT);
                            $stmt->execute(); // クエリを実行
                            echo "<FONT COLOR='RED'>削除した番号: </FONT>".$delete."<br><br>";
                        }else{
                            //選択されていた番号がすでに論理削除されていた場合
                            echo "<FONT COLOR='RED'>その番号はすでに削除されています<br><br></FONT>";
                        }
                    }else{
                        //パスワードが一致しない場合
                        echo "<FONT COLOR='RED'>パスワードが一致しません<br></FONT>";
                    }
                }
            }
        }
        
        //編集する番号がある場合
        if(isset($_POST["edit_button"])){
            if(!($isEditNumberEmpty) && !($isPasswordEmpty)){
                //編集する番号が論理削除されていた場合
                //テーブルの中身を照合
                $sql = 'SELECT * FROM comments WHERE id = :id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $edit_number, PDO::PARAM_INT);
                //$stmt = $pdo->query($sql);
                $stmt->execute(); // クエリを実行
                $results = $stmt->fetchAll();
                //削除されているかどうか（1なら論理削除されている）
                if($results){
                    $is_deleted = $results[0]['deleted'];
                    if($is_deleted == 1){
                        //削除されている場合
                        echo "<FONT COLOR='RED'>その番号は削除されているため編集できません<br></FONT>";
                    }
                }else{
                    echo "<FONT COLOR='RED'>編集できる番号がありません<br></FONT>";
                }
            }
            //編集番号、編集名前、編集コメント、パスワードがすべて入力されていた場合
            if(!($isEditNumberEmpty) && (!empty($edit_name)) && (!empty($edit_str)) && !($isPasswordEmpty)){
                //テーブルの中身を照合
                $sql = 'SELECT * FROM comments WHERE id = :id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $edit_number, PDO::PARAM_INT);
                //$stmt = $pdo->query($sql);
                $stmt->execute();
                $results = $stmt->fetch();
                if((empty($results)) || ($results['deleted']==1)){
                    echo "<FONT COLOR='RED'>編集できる番号がありません<br></FONT>";
                }else{
                    //テーブルからパスワードを抽出
                    $user_password = $results['password'];
                    if($user_password == $password){
                        //投稿内容を更新
                        $sql = 'UPDATE comments SET name=:edit_name,comment=:edit_str,update_date=:date WHERE id=:id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':edit_name', $edit_name, PDO::PARAM_STR);
                        $stmt->bindParam(':edit_str', $edit_str, PDO::PARAM_STR);
                        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                        $stmt->bindParam(':id', $edit_number, PDO::PARAM_STR);
                        $stmt->execute();
                        echo "<FONT COLOR='RED'>編集した番号: </FONT>".$edit_number."<br><br>";
                    }else{
                        echo "<FONT COLOR='RED'>パスワードが一致しません<br></FONT>";
                    }
                }
            }
        } 
        
    }
        
        
    //テーブルの中身を照合
    $sql = 'SELECT * FROM comments';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    
    if(!(empty($results))){
        foreach ($results as $row){
            if($row['deleted']==0){
                //$rowの中にはテーブルのカラム名が入る
                echo $row['id'].' , ';
                echo $row['name'].' , ';
                echo $row['comment'].' , ';
                echo "<FONT COLOR='GREEN'><b>作成日時: </b></FONT>".$row['create_date'].' , ';
                echo "<FONT COLOR='GREEN'><b>最終更新日時: </b></FONT>".$row['update_date'].'<br>';
                echo "<hr>";
            }
        }
    }
    $stmt->execute();
    ?>
</body>
</html>