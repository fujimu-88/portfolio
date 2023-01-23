<?php
session_start();
require("db_connect.php");
$db=connect();
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
        <title>スタッフ一覧</title>
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
                <h2>スタッフ一覧</h2>
        <?php
        try{
            $sql1="SELECT * FROM staff WHERE delete_flag=0 ORDER BY id,position";
            $pre1=$db->prepare($sql1);
            $pre1->execute();
            print("検索結果は".$pre1->rowCount()."件です。<br>");
        }catch(PDOException $e)
            {
            print("SQL実行エラー：".$e->getMessage());
            }
            if($pre1->rowCount()<1){
                print("検索結果がありません<br>");
            }else{
        ?>
        <div id="staffList">
        <table>
            <tr>
                <th>社員番号</th>
                <th>サポート</th>
                <th>名前</th>
                <th colspan="2"></th>
            </tr>
            
            <tr>
                <?php
                while($row1=$pre1->fetch()){
                ?>
                <td><?=htmlspecialchars($row1['id'],ENT_QUOTES)?></td>
                <td><?=htmlspecialchars($row1['position'],ENT_QUOTES)?></td>
                <td><?=htmlspecialchars($row1['staff_name'],ENT_QUOTES)?></td>
                <td>
                    <form name="update2" method="post" action="staff_updateform.php">
                        <input type="hidden" name="id" value="<?=htmlspecialchars($row1['id'])?>">
                        <input type="submit" value="更新">
                    </form>
                </td>
                <td>
                    <form name="delete2" method="post" action="staff_delete_c.php">
                        <input type="hidden" name="id" value="<?=htmlspecialchars($row1['id'])?>">
                        <input type="submit" value="削除">
                    </form>
                    </td>
            </tr>
            <?php
                }
            ?>
        </table>
        
        <?php
        }
        ?>
        </div>
            </article>
        </main>
    </body>
</html>