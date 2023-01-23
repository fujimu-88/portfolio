<?php
require("db_connect.php");
session_start();

//ログインせずページに来た場合
$login_name = $_SESSION['staff_name'];
if($login_name==''){
    header("Location:./login.php");
};

$db = connect();
//staff_id+名前+ふりがな+positionを分割
$n=$_POST['name'];
$s_name=preg_split('/\,/', $n);
$staff_id=$s_name[0];
$name=$s_name[1];
$name_y=$s_name[2];
$position=$s_name[3];

$date=$_POST['date'];
$start_t=$_POST['start_h'] . ':' . $_POST['start_m'];
$end_t=$_POST['end_h'] . ':' . $_POST['end_m'];
?>
<!DOTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title>シフト登録完了</title>
    </head>
    <body id="shift_registform">
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
                <h2>シフト登録完了</h2>
        
        <?php
        try{
            $sql="SELECT * FROM shift_date WHERE name=? AND date=? AND delete_flag=0";
            $pre=$db->prepare($sql);
            $pre->bindValue(1,$name,PDO::PARAM_STR);
            $pre->bindValue(2,$date,PDO::PARAM_STR);
            $pre->execute();
        }catch(PDOException $e){
            print("SQLエラー：".$e->getMessage());
        }
        if($pre->rowCount()>0){
            print("既に下記の日時で登録されています");
            $row=$pre->fetch();
        ?>
        <table border="1">
            <tr>
                <td>日付</td>
                <td><?=$row['date']?></td>
            </tr>
            <tr>
                <td>名前</td>
                <td><?=$row['name']?></td>
            </tr>
            <tr>
                <td>開始～終了</td>
                <td><?=$row['start_time']?>～<?=$row['ending_time']?></td>
            </tr>
        </table>
        
        <?php
        }else{
            try{
                $db->beginTransaction();
                $sql="INSERT INTO original_shift_date SET
                staff_id=?,
                position=?,
                name=?,
                name_y=?,
                date=?,
                start_time=?,
                ending_time=?,
                add_flag='1',
                change_t='0'";
                $pre=$db->prepare($sql);
                $pre->bindValue(1,$staff_id,PDO::PARAM_STR);
                $pre->bindValue(2,$position,PDO::PARAM_STR);
                $pre->bindValue(3,$name,PDO::PARAM_STR);
                $pre->bindValue(4,$name_y,PDO::PARAM_STR);
                $pre->bindValue(5,$date,PDO::PARAM_STR);
                $pre->bindValue(6,$start_t,PDO::PARAM_STR);
                $pre->bindValue(7,$end_t,PDO::PARAM_STR);
                $pre->execute();
                $db->commit();
            }catch(PDOException $e){
                $db->rollBack();
                print("SQLエラー：".$e->getMessage());
            }
            try{
                $db->beginTransaction();
                $sql = "INSERT INTO shift_date SET
                staff_id=?,
                position=?,
                name=?,
                name_y=?,
                date=?,
                start_time=?,
                ending_time=?";
                $pre=$db->prepare($sql);
                $pre->bindValue(1,$staff_id,PDO::PARAM_STR);
                $pre->bindValue(2,$position,PDO::PARAM_STR);
                $pre->bindValue(3,$name,PDO::PARAM_STR);
                $pre->bindValue(4,$name_y,PDO::PARAM_STR);
                $pre->bindValue(5,$date,PDO::PARAM_STR);
                $pre->bindValue(6,$start_t,PDO::PARAM_STR);
                $pre->bindValue(7,$end_t,PDO::PARAM_STR);
                $pre->execute();
                print("データを".$pre->rowCount()."件を登録しました。<br>");
            ?>
        <table border="1">
            <tr>
                <td>サポート</td>
                <td><?=$position?></td>
            </tr>
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
                print("更新エラー：".$e->getMessage());
            }
        }
        
        ?>
        <a href="shift_one_registform.php">入力画面に戻る</a>
            </article>
        </main>
    </body>
</html>