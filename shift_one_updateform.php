<?php
session_start();

//ログインせずページに来た場合
$login_name = $_SESSION['staff_name'];
if($login_name==''){
    header("Location:./login.php");
};

require("db_connect.php");
$db=connect();

try{
    $sql="SELECT * FROM staff WHERE delete_flag=0 ORDER BY id";
    $pre2=$db->prepare($sql);
    $pre2->execute();
}catch(PDOException $e){
    print("SQLエラー：".$e->getMessage());
}
if($pre2->rowCount()<1){
    print("スタッフが登録されていません<br>");
}else{
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title>シフト更新画面</title>
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
        <h2>シフト更新</h2>
        <?php
        $shift_id=$_POST['shift_id'];
        $_SESSION['shift_id']=$shift_id;
        try{
            $sql="SELECT * FROM shift_date WHERE shift_id=?";
            $pre=$db->prepare($sql);
            $pre->bindValue(1,$shift_id,PDO::PARAM_INT);
            $pre->execute();
        }catch(PDOException $e){
            print("SQL実行エラー：".$e->getMessage());
        }
        if($pre->rowCount()<1){
            print("更新データがありません<br>");
        }else{
            $row=$pre->fetch();
            
            //日付
            $date=$row['date'];
            $year=mb_substr($date, 0, 4);//文字列の切り出し
            $month=mb_substr($date, 5, 2);
            $day=mb_substr($date, 8, 2);
            $date=$year.'/'.$month.'/'.$day;
            
            //時間
            $start_t=$row['start_time'];
            $end_t=$row['ending_time'];
            $start_h=mb_substr($start_t, 0, 2);//文字列の切り出し
            $start_m=mb_substr($start_t, 3, 2);
            $end_h=mb_substr($end_t, 0, 2);//文字列の切り出し
            $end_m=mb_substr($end_t, 3, 2);
            
        ?>
        <div id="form">
        <form name="updateform" method="post" action="shift_one_update.php">
            <table border="1">
                <tr>
                    <td class="non_col">名前<br><span id="name_err"></span></td>
                    <td>
                        <?=htmlspecialchars($row['name'])?><input type="hidden" name="name" value="<?=htmlspecialchars($row['name'])?>">
                    </td>
                </tr>
                <tr>
                    <td class="non_col">勤務日<br><span id="date_err"></span></td>
                    <td><?=$date?><input type="hidden" name="date" value="<?=$date?>"></td>
                </tr>
                <tr>
                    <td colspan="2">開始時間～終了時間　　変更前【<?=$start_t?>～<?=$end_t?>】<br>
                        <div id="start_err"></div>
                        <div id="end_err"></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <select name="start_h">    
                            <option value="0">0</option>
                            <?php
                            for($i=9;$i<=20;$i++){
                                if(preg_match('/^([0-9]{1})$/', $i)){
                                    $i = '0'.$i;
                                }
                            if($start_h==$i){
                            ?>
                            <option selected value="<?=$i?>"><?=$i?></option>
                            <?php
                            }
                            ?>
                            <option value="<?=$i?>"><?=$i?></option>
                            <?php
                            }
                            ?>
                        </select>
                        ：
                        <select name="start_m">
                            <?php
                            if($start_m==30){
                                ?>
                            <option value="00">00</option>
                            <option selected value="30">30</option>
                            <?php
                            }
                            ?>
                            <option value="00">00</option>
                            <option value="30">30</option>
                        </select>
                        ～
                        <select name="end_h">
                        <option value="0">0</option>
                            <?php
                            for($i=12;$i<=23;$i++){

                            if($end_h==$i){
                            ?>
                            <option selected value="<?=$i?>"><?=$i?></option>
                            <?php
                            }
                            ?>
                            <option value="<?=$i?>"><?=$i?></option>
                            <?php
                            }
                            ?>
                        </select>
                        ：
                        <select name="end_m">
                            <?php
                            if($end_m==30){
                                ?>
                            <option value="00">00</option>
                            <option selected value="30">30</option>
                            <?php
                            }
                            ?>
                            <option value="00">00</option>
                            <option value="30">30</option>
                        </select>
                        <div id="s_e"></div>
                    </td>
                </tr>

            </table>
            <input type="submit" value="更新" onClick="return registCheck()">
        </form>
        </div>
        <br><br>
        <br><a href="shift_list_all.php">シフト一覧に戻る</a>
        <?php
        }

        }
        ?>
        </article>
        </main>
        <script>
        //未入力チェック
        function registCheck(){
            var start_h =document.updateform.start_h.value;
            var end_h   =document.updateform.end_h.value;
            
            if( start_h==0 || end_h==0 ||(0<start_h)&&(0<end_h)&&(start_h>=end_h)){
                    
                    if(start_h==0){
                        var startE = document.getElementById('start_err');
                        startE.innerText="※開始時間（時）を選択してください";
                    }else{
                        var startE = document.getElementById('start_err');
                        startE.innerText="";
                    }
                    if(end_h==0){
                        var endE = document.getElementById('end_err');
                        endE.innerText="※終了時間（時）を選択してください";
                    }else{
                        var endE = document.getElementById('end_err');
                        endE.innerText="";
                    }
                    if((0<start_h)&&(0<end_h)&&(start_h>=end_h)){
                        var start_end = document.getElementById('s_e');
                        start_end.innerText="※開始時間か終了時間を変更してください";
                    }else{
                        var start_end = document.getElementById('s_e');
                        start_end.innerText="";
                    }
                    return false;
                }
            
                return true;
            }
    </script>
    </body>
</html>