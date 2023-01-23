<?php
session_start();
require("db_connect.php");

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
        <title>スタッフ情報削除確認</title>
    </head>
    <body id="staff_all">
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
        <h2>スタッフ情報削除確認</h2>
        <?php
        try{
            $db=connect();
            $sql="SELECT * FROM staff WHERE id=?";
            $pre=$db->prepare($sql);
            $pre->bindValue(1,$_POST['id'],PDO::PARAM_INT);
            $pre->execute();
        }catch(PDOException $e){
            print("SQLエラー：".$e->getMessage());
        }
        if($pre->rowCount()>0){
            $row=$pre->fetch();
        ?>
        <hr>
        <table border="1">
            <tr>
                <td>社員番号</td>
                <td><?=($row['id'])?></td>
            </tr>
            <tr>
                <td>名前</td>
                <td><?=($row['staff_name'])?></td>
            </tr>
        </table>
        
        
            <p>このデータを削除しますか？</p>
        <form>
            <input type="button" value="削除" onClick="location.href='staff_delete.php?action=delete&id=<?=htmlspecialchars($row['id'])?>'">
            <input type="button" value="戻る" onClick="location.href='staff_list.php'">
        </form>
        
        <?php
        }
        ?>
            </article>
        </main>
    </body>
</html>