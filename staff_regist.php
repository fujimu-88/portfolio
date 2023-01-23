<?php
require('db_connect.php');
session_start();

//ログインせずページに来た場合
$login_name = $_SESSION['staff_name'];
if($login_name==''){
    header("Location:./login.php");
};
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title>スタッフ新規登録</title>
    </head>
    <body id="staff_regist">
        <header>
            <h1>シフト管理表</h1>
            <form action="./logout.php" method="get" name="menu">
            <div id="logout">
                <input type="submit" value="ログアウト" name="button"
                   onClick="this.form.buttonID.value='L999';" style="width: 100px">
            </div>
            </form>
            <div id="user">
            ログインユーザー：<?=htmlspecialchars($_SESSION['staff_name'],ENT_QUOTES)?>
            </div>
        </header>
        <nav>
                <ul class="globalNavi">
                    <li><a href="mainmenu.php">メインメニュー</a></li>
                    <li><a href="shift_list_all.php">シフト一覧</a></li>
                    <li><a href="shift_edit.php">シフト調整</a></li>
                    <li><a href="shift_one_registform.php">シフト登録</a></li>
                    <li><a href="staff_list.php">スタッフ一覧</a></li>
                    <li><a href="staff_registform.php">スタッフ登録</a></li>
                    <li><a href="user_configuration.php">ユーザー設定</a></li>
                </ul>
            </nav>
        <main>
            <article>
                <h2>登録結果</h2>
        <?php
        try{
            $db=connect();
            $db->beginTransaction();
            $sql="SELECT * FROM staff WHERE id = ? AND delete_flag=0";
            $pre=$db->prepare($sql);
            $pre->bindValue(1,$_POST['id'],PDO::PARAM_INT);
            $pre->execute();
        }catch(PDOException $e){
            print("SQLエラー：".$e->getMessage());
        }
        if($pre->rowCount()>0){
            $_SESSION['err_msg2']=$_POST['id']."は登録済みの社員番号です";
            header("Location:./staff_registform.php");
        }else if($_POST['password']!=$_POST['password2']){
            $_SESSION['err_msg2']="パスワードとパスワード（確認用）の入力内容が違います";
            header("Location:./staff_registform.php");
        }else{
            $_SESSION['err_msg2']='';
            try{
                $sql="INSERT INTO staff SET id=?, position=?, staff_name=?, staff_name_y=?, password=?";
                $pre=$db->prepare($sql);
                $pre->bindValue(1,$_POST['id'],PDO::PARAM_INT);
                $pre->bindValue(2,$_POST['position'],PDO::PARAM_INT);
                $pre->bindValue(3,$_POST['name'],PDO::PARAM_STR);
                $pre->bindValue(4,$_POST['name_y'],PDO::PARAM_STR);
                $pre->bindValue(5,$_POST['password'],PDO::PARAM_STR);
                $pre->execute();
                print("登録しました");
                $db->commit();
            }catch(PDOException $e){
                $db->rollBack();
                print("SQLエラー2：".$e->getMessage());
            }
            ?>
        <table border="1">
            <tr>
                <td>社員番号</td>
                <td><?=htmlspecialchars($_POST['id'])?></td>
            </tr>
            <tr>
                <td>サポート</td>
                <td><?=htmlspecialchars($_POST['position'])?></td>
            </tr>
            <tr>
                <td>名前</td>
                <td><?=htmlspecialchars($_POST['name'])?></td>
            </tr>
            <tr>
                <td>名前</td>
                <td><?=htmlspecialchars($_POST['name_y'])?></td>
            </tr>
            
        </table>
        <?php
        }
        ?>
        <br>
            </article>
        </main>
    </body>
</html>