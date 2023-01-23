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
<html lang="ja">
    <head>
        <meta charset="ja">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title>更新完了</title>
    </head>
    <body id="shiftAll">
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
        <h2>更新完了</h2>
        <?php
        
                $shift_id=$_SESSION['shift_id'];
                $name=$_POST['name'];
                $date=$_POST['date'];
                $start_t=$_POST['start_h'] . ':' . $_POST['start_m'];
                $end_t=$_POST['end_h'] . ':' . $_POST['end_m'];
                
                try{
                    $db->beginTransaction();
                    $sql="UPDATE shift_date SET name=?, date=?, start_time=?, ending_time=? WHERE shift_id ='".$shift_id."'";
                    $pre=$db->prepare($sql);
                    $pre->bindValue(1,$name,PDO::PARAM_STR);
                    $pre->bindValue(2,$date,PDO::PARAM_STR);
                    $pre->bindValue(3,$start_t,PDO::PARAM_STR);
                    $pre->bindValue(4,$end_t,PDO::PARAM_STR);
                    $pre->execute();
                    print("データを更新しました。");
            ?>
        <table border="1">
            <tr>
                <td>日付</td>
                <td><?=$date?></td>
            </tr>
            <tr>
                <td>名前</td>
                <td><?=$name?></td>
            </tr>
            <tr>
                <td>開始～終了</td>
                <td><?=$start_t?>～<?=$end_t?></td>
            </tr>
        </table>
        <?php
                    $db->commit();
                }catch(PDOException $e){
                    $db->rollBack();
                    print("SQLエラー：".$e->getMessage());
                }
                
                
                //オリジナルデータに変更を追加
                $change_t=$start_t.'～'.$end_t;
                try{
                    $db->beginTransaction();
                    $sql="UPDATE original_shift_date SET change_t=? WHERE name=? AND date=?";
                    $pre=$db->prepare($sql);
                    $pre->bindValue(1,$change_t,PDO::PARAM_STR);
                    $pre->bindValue(2,$name,PDO::PARAM_STR);
                    $pre->bindValue(3,$date,PDO::PARAM_STR);
                    $pre->execute();
                    $db->commit();
                }catch(PDOException $e){
                    $db->rollBack();
                    print("SQLエラー：".$e->getMessage());
                }
        ?>
                </article>
        </main>
    </body>
</html>