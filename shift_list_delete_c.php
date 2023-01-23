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
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title>確認画面</title>
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
        </nav>
        <main>
            <article>
                <h2>シフト削除確認</h2>
        <?php
        try{
            $db=connect();
            $sql="SELECT * FROM shift_date WHERE shift_id=?";
            $pre=$db->prepare($sql);
            $pre->bindValue(1,$_POST['shift_id'],PDO::PARAM_INT);
            $pre->execute();
        }catch(PDOException $e){
            print("SQLエラー：".$e->getMessage());
        }
        if($pre->rowCount()>0){
            $row=$pre->fetch();
            $shift_id=$row['shift_id'];
            $date=$row['date'];
            $name=$row['name'];
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
                <td><?=$row['start_time']?>～
                <?=$row['ending_time']?></td>
            </tr>
        </table>
        
        <form method="post" action="shift_list_delete.php" name="shift_list_delete">
            <p>削除理由:<span id="err1"></span></p>
            <select name="shift_d_r">
                <option value="">理由を選択してください</option>
                <option value="スタッフ都合で削除">スタッフ削除希望</option>
                <option value="管理者により削除">管理者により削除</option>
            </select>
            <p>このデータを削除しますか？</p>
            <input type="hidden" name="shift_id" value="<?=$shift_id?>">
            <input type="hidden" name="name" value="<?=$name?>">
            <input type="hidden" name="date" value="<?=$date?>">
            <input type="submit" value="削除" onClick="return registCheck()">
            <input type="button" value="戻る" onClick="location.href='shift_list_all.php'">
        </form>
        
        <?php
        }
        ?>
                </article>
        </main>
        <script>
            //未入力チェック
            function registCheck(){
                var shift_d_r = document.shift_list_delete.shift_d_r.value;

                if(shift_d_r==""){
                    var err1 = document.getElementById('err1');
                    err1.innerText="※削除理由を選択してください";
                    return false;
                }else{
                    var err1 = document.getElementById('err1');
                    err1.innerText="";
                }
            return true;
            }
        </script>
    </body>
</html>