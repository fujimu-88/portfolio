<?php
session_start();

//ログインせずページに来た場合
$login_name = $_SESSION['staff_name'];
if($login_name==''){
    header("Location:./login.php");
};

require("db_connect.php");
$db=connect();

$d=date('Y-m-01', strtotime('+1 month')); //来月の日付を勤務日に表示

try{
    $sql="SELECT * FROM staff WHERE delete_flag=0 ORDER BY position,id";
    $pre=$db->prepare($sql);
    $pre->execute();
}catch(PDOException $e){
    print("SQLエラー：".$e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title>シフト登録</title>
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
        <div>
            <h2>シフト登録</h2>
        </div>
        <?php
        if($pre->rowCount()==0){
            print("スタッフが登録されていません<br>");
        }else{
        ?>
        <div id="form">
        <form method="post" action="./shift_one_regist.php" name="registform">
            <div>
                <table border="1">
                    <tr>
                        <td class="non_col">勤務日<br><span id="date_err"></span></td>
                        <td>
                            <input type="date" name="date" value="<?=$d ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class="non_col">氏名<br><span id="name_err"></span></td>
                        <td>
                            <select name="name">
                                <option>スタッフを選択してください</option>
<?php
    while($row=$pre->fetch()){
        $staff_id=$row["id"];
        $position=$row["position"];
        $name=$row["staff_name"];
        $name_y=$row["staff_name_y"];
        //(staff_id)/(名前)/(ふりがな)/(position)でvalueで送信
        $s_name=$staff_id.','.$name.','.$name_y.','.$position;
?>
                                <option value="<?=$s_name?>"><?=$name."&nbsp;&nbsp;(".$position.")"?></option>
<?php
    }

?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="non_col">開始時間<div id="start_err"></div></td>
                        <td>
                            <select name="start_h">
                                <option value="0">0</option>
                                <option value="09">9</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                                <option value="13">13</option>
                                <option value="14">14</option>
                                <option value="15">15</option>
                                <option value="16">16</option>
                                <option value="17">17</option>
                                <option value="18">18</option>
                                <option value="19">19</option>
                                <option value="20">20</option>
                            </select>
                            ：
                            <select name="start_m">
                                <option value="00">00</option>
                                <option value="30">30</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="non_col">終了時間<div id="end_err"></div></td>
                        <td>
                            <select name="end_h">
                                <option value="0">0</option>
                                <option value="12">12</option>
                                <option value="13">13</option>
                                <option value="14">14</option>
                                <option value="15">15</option>
                                <option value="16">16</option>
                                <option value="17">17</option>
                                <option value="18">18</option>
                                <option value="19">19</option>
                                <option value="20">20</option>
                                <option value="21">21</option>
                                <option value="22">22</option>
                                <option value="23">23</option>
                            </select>
                            ：
                            <select name="end_m">
                                <option value="00">00</option>
                                <option value="30">30</option>
                            </select>
                            
                        </td>
                    </tr>
                </table>
            </div>
            <div id="s_e"></div>
            <div>
                <input type="submit" value="登録" onClick="return registCheck()">
                <input type="reset" value="クリア">
            </div>
        </form>
        <?php
        }
        ?>
    </div>
                </article>
        </main>
        <script>
        //未入力チェック
        function registCheck(){
            var date  =document.registform.date.value;
            var name    =document.registform.name.value;
            var start_h =document.registform.start_h.value;
            var end_h   =document.registform.end_h.value;
            
            if(date=="" || name=="スタッフを選択してください" || start_h==0 || end_h==0 ||(0<start_h)&&(0<end_h)&&(start_h>=end_h)){
                    if(date==""){
                        var dateE = document.getElementById('date_err');
                        dateE.innerText="※日付を選択してください";
                    }else{
                        var dateE = document.getElementById('date_err');
                        dateE.innerText="";
                    }
                    if(name=="スタッフを選択してください"){
                        var nameE = document.getElementById('name_err');
                        nameE.innerText="※スタッフを選択してください";
                    }else{
                        var nameE = document.getElementById('name_err');
                        nameE.innerText="";
                    }
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