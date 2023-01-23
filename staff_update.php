<?php
session_start();

//ログインせずページに来た場合
$login_name = $_SESSION['staff_name'];
if($login_name==''){
    header("Location:./login.php");
};
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="ja">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title>スタッフ更新完了</title>
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
        <h2>スタッフ更新完了</h2>
        <?php
        require("db_connect.php");
        $db=connect();
        $id=$_SESSION['id'];
        
            try{
                $db->beginTransaction();
                $sql="UPDATE staff SET position=?, id=?, staff_name=?, staff_name_y=? WHERE id='".$id."'";
                $pre=$db->prepare($sql);
                $pre->bindValue(1,$_POST['position'],PDO::PARAM_STR);
                $pre->bindValue(2,$_POST['id'],PDO::PARAM_STR);
                $pre->bindValue(3,$_POST['staff_name'],PDO::PARAM_STR);
                $pre->bindValue(4,$_POST['staff_name_y'],PDO::PARAM_STR);
                $pre->execute();
                print("データを更新しました。");
                $db->commit();
            }catch(PDOException $e){
                $db->rollBack();
                print("SQLエラー：".$e->getMessage());
            }
        
        
        ?>
        <table border="1">
            <tr>
                <td>サポート</td>
                <td><?=htmlspecialchars($_POST['position'])?></td>
            </tr>
            <tr>
                <td>社員番号</td>
                <td><?=htmlspecialchars($_POST['id'])?></td>
            </tr>
            <tr>
                <td>名前</td>
                <td><?=htmlspecialchars($_POST['staff_name'])?></td>
            </tr>
            <tr>
                <td>ふりがな</td>
                <td><?=htmlspecialchars($_POST['staff_name_y'])?></td>
            </tr>
        </table>
        
            </article>
        </main>
    </body>
</html>