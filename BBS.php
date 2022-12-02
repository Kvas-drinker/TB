<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
    <?php
        //データベース接続
        $dsn = 'データベース名';
        $user = 'ユーザー名';
        $password = 'パスワード';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

        //テーブル作成
        $sql = "CREATE TABLE IF NOT EXISTS m5_1"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "date TEXT,"
        . "password char(32)"
        .");";
        $stmt = $pdo->query($sql);

        //編集フォームここから
        //編集ボタンが押された場合
        if(isset($_POST['submit3'])){
            $form_editnum = $_POST['editnum'];
            $form_editpw = $_POST['editpw'];
            //編集対象番号とパスワードを受け取った場合
            if(!empty($form_editnum && $form_editpw)){
                //データ抽出
                $sql = 'SELECT * FROM m5_1';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                foreach ($results as $row){
                    //番号、パスワードが一致した場合
                    if($row['id'] == $form_editnum && $row['password'] == $form_editpw){
                        $error="";
                        $name2 = $row['name'];
                        $comment2 = $row['comment'];
                        $pw2 = $row['password'];
                        $hidden = $row['id'];
                    }
                    //番号一致、パスワード不一致の場合
                    elseif($row['id'] == $form_editnum && $row['password'] != $form_editpw){
                        $error="パスワードが間違っています。"."<br>";
                        $name2 = "";
                        $comment2 = "";
                        $pw2 = "";
                        $hidden = "";
                    }
                    elseif($row['id'] != $form_editnum){
                        $error="";
                        $name2 = "";
                        $comment2 = "";
                        $pw2 = "";
                        $hidden = "";
                    }
                   
                }
            }
            //番号未入力エラー
            elseif(empty($form_editnum)){
                $error="編集対象番号を入力してください。"."<br>";
                $name2 = "";
                $comment2 = "";
                $pw2 = "";
                $hidden = "";
            }
            //PW未入力エラー
            elseif(empty($form_editpw)){
                $error="パスワードを入力してください。"."<br>";
                $name2 = "";
                $comment2 = "";
                $pw2 = "";
                $hidden = "";
            }
        }
        //編集ボタンが押されていない場合、投稿フォームに表示する変数を全て空にする
        else{
            $error = "";
            $name2 = "";
            $comment2 = "";
            $pw2 = "";
            $hidden = "";
        }
        //編集フォームここまで
    ?>
    <form action="m5-1.php" method="POST">
        <p>【投稿フォーム】</p>
        <input type="text" name="name" placeholder="名前" value="<?php echo $name2; ?>"><br>
        <input type="text" name="comment" placeholder="コメント" value="<?php echo $comment2; ?>"><br>
        <input type="password" name="pw" placeholder="パスワード"value="<?php echo $pw2; ?>">
        <input type="submit" name="submit1"><br>
        <!--隠れフォーム-->
        <input type="hidden" name="hidden" value="<?php echo $hidden; ?>">
        <p>【削除フォーム】</p>
        <input type="number" name="deletenum" placeholder="削除対象番号"><br>
        <input type="password" name="deletepw" placeholder="パスワード">
        <input type="submit" name="submit2" value="削除"><br>
        <p>【編集フォーム】</p>
        <input type="number" name="editnum" placeholder="編集対象番号"><br>
        <input type="password" name="editpw" placeholder="パスワード">
        <input type="submit" name="submit3" value="編集">
       
    </form>

    <?php
        //投稿ここから
        //投稿ボタンが押された場合、受信した値を変数として定義
        if(isset($_POST['submit1'])){
            $formname = $_POST['name'];
            $formcomment = $_POST['comment'];
            $formpw = $_POST['pw'];
            $formhidden = $_POST['hidden'];
            $formdate = date("Y/m/d H:i:s");
            //条件：投稿フォームに名前、コメント、パスワードが入力されていて、隠れフォームは空
            if(!empty($formname && $formcomment && $formpw) && empty($formhidden) ){
                //データの挿入
                $sql = $pdo -> prepare("INSERT INTO m5_1 (name, comment, password, date) VALUES (:name, :comment, :password, :date)");
                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                $sql -> bindParam(':password', $pw, PDO::PARAM_STR);
                $name = $formname;
                $comment = $formcomment; 
                $date = $formdate;
                $pw = $formpw;
                $sql -> execute();
            }
            //投稿ここまで

            //編集ここから
            //条件：投稿フォームに名前、コメント、パスワードが入力されていて、隠れフォームにも入力あり
            if(!empty($formname && $formcomment && $formpw && $formhidden)){
                $sql = 'SELECT * FROM m5_1';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                foreach ($results as $row){
                    if($row['id'] == $formhidden){
                        $id = $formhidden; 
                        $name = $formname;
                        $comment = $formcomment; 
                        $pw = $formpw;
                        $sql = 'UPDATE m5_1 SET name=:name,comment=:comment,password=:password WHERE id=:id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                        $stmt->bindParam(':password', $pw, PDO::PARAM_INT);
                        $stmt->execute();
                        echo "書き込みを編集しました。"."<br>";
                    }
                }
            }
            elseif(empty($formname)){
                echo "名前を入力してください。"."<br>";
            }
            elseif(empty($formcomment)){
                echo "コメントを入力してください。"."<br>";
            }
            elseif(empty($formpw)){
                echo "パスワードを入力してください。"."<br>";
            }
        }

        //削除ここから
        //削除ボタンが押された場合、受信した値を変数として定義
        if(isset($_POST['submit2'])){
            $form_deletenum = $_POST['deletenum'];
            $form_deletepw = $_POST['deletepw'];
            //条件：削除フォームに削除対象番号、パスワードが入力されている
            if(!empty($form_deletenum && $form_deletepw)){
                //データベースからデータ抽出
                $sql = 'SELECT * FROM m5_1';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                foreach ($results as $row){
                    //抽出されたデータのカラムを受信したデータと比較
                    //番号、PWが一致した場合
                    if($row['id'] == $form_deletenum && $row['password'] == $form_deletepw){
                        $id = $form_deletenum;
                        $sql = 'delete from m5_1 where id=:id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                        $stmt->execute();
                        echo $id."番のコメントを削除しました。"."<br>";
                    }
                    //番号は一致、PWは不一致の場合
                    elseif($row['id'] == $form_deletenum && $row['password'] != $form_deletepw){
                        echo "パスワードが間違っています。"."<br>";
                    }
                }
            }
            elseif(empty($form_deletenum)){
                echo "削除対象番号を入力してください。"."<br>";
            }
            elseif(empty($form_deletepw)){
                echo "パスワードを入力してください。"."<br>";
            }
            
        }
    ?>
        <strong><h2>好きな食べ物は？</h2></strong>
    <?php
        echo $error;
        echo  "【投稿履歴】"."<br>";
        echo "<hr>";
        //データの抽出＆表示
        $sql = 'SELECT * FROM m5_1';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            echo $row['id'].'<br>';
            echo $row['name'].'<br>';
            echo $row['comment'].'<br>';
            echo $row['date'].'<br>';
            echo "<hr>";
        }
    ?>
</body>
</html>