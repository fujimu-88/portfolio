<?php
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
        <title>スタッフ情報削除完了</title>
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
        <h2>スタッフ情報削除完了</h2>
        <?php
        require("db_connect.php");
        $db=connect();
        if($_GET['action']=='delete'&&$_GET['id']!=''){
            
        try{
            $db->beginTransaction();
            $sql="UPDATE staff SET delete_flag=1 WHERE id=?";
            $pre=$db->prepare($sql);
            $pre->bindValue(1,$_GET['id'],PDO::PARAM_INT);
            $pre->execute();
            print("データを".$pre->rowCount()."件、削除しました。<br>");
            $db->commit();
        }catch(PDOException $e){
            $db->rollBack();
            print("削除エラー：" . $e->getMessage());
        }
    }
        try{
            $sql="SELECT * FROM staff WHERE delete_flag=0 ORDER BY id";
            $pre=$db->query($sql);
            print("検索結果は".$pre->rowCount()."件です。<br>");
        }catch(PDOException $e)
            {
            print("SQL実行エラー：".$e->getMessage());
            }
            if($pre->rowCount()<1){
                print("検索結果がありません<br>");
            }else{
        ?>
        <table border="1">
            <tr>
                <th>社員番号</th>
                <th>名前</th>
            </tr>
            <?php
                while($row=$pre->fetch()){
            ?>
            <tr>
                <td><?=htmlspecialchars($row['id'],ENT_QUOTES)?></td>
                <td><?=htmlspecialchars($row['staff_name'],ENT_QUOTES)?></td>
                <td>
                    <form name="update2" method="post" action="staff_updateform.php">
                        <input type="hidden" name="id" value="<?=htmlspecialchars($row['id'])?>">
                        <input type="submit" value="更新">
                    </form>
                </td>
                <td>
                    <form name="delete2" method="post" action="staff_delete_c.php">
                        <input type="hidden" name="id" value="<?=htmlspecialchars($row['id'])?>">
                        <input type="submit" value="削除">
                    </form>
                    </td>
            </tr>
            <?php
                }
            ?>
        </table>
        <br><a href="mainmenu.php">メインメニューに戻る</a><br>
        <?php
        }
        ?>
            </article>
        </main>
    </body>
</html>