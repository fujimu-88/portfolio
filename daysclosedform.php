<?php
session_start();
require('db_connect.php');
require_once('php/function.php');
$db=connect();
//ログインせずページに来た場合
$login_name = $_SESSION['staff_name'];
if($login_name==''){
    header("Location:./login.php");
};

$ym = $_GET['ym'];
$year=mb_substr($ym, 0, 4);//文字列の切り出し
$month=mb_substr($ym, 4, 2);
$_SESSION['ym'] = $ym;
$yasumi_c=$year.'-'.$month.'-01';

//$monthを2桁にする
$month2 = two_digit($month);
//休館日
$days_closed = array();
try{
    $sql="SELECT * FROM days_closed WHERE delete_flag=0";
    $pre=$db->prepare($sql);
    $pre->execute();
}catch(PDOException $e){
    print("SQLエラー：".$e->getMessage());
};
if($pre->rowCount()>0){
    while($row=$pre->fetch()){
        $date = $row['days_closed'];
        $d_name = $row['d_name'];
        $days_closed[$date] = $d_name;
    }
}

//composerからyasumi（祝日）を取得
require_once 'vendor/autoload.php';
use Yasumi\Yasumi;

function holidays(\DateTimeInterface $currentTime, $country = 'Japan', $locale = 'ja_JP'): array
{
    $holidays = Yasumi::create($country, (int)$currentTime->format('Y'), $locale);
    $results  = [];
    foreach ($holidays->getHolidays() as $holiday) {
        $results[$holiday->format('Y-m-d')] = $holiday->getName();
    }
    return $results;
}
$holiday=holidays(new \DateTime($yasumi_c));




//カレンダーの前月と次月のURL
$prevmonth = date("Ym",strtotime($ym."01"." -1 month "));
$nextmonth = date("Ym",strtotime($ym."01"." +1 month "));
$prev= 'daysclosedform.php?ym='.$prevmonth;
$next= 'daysclosedform.php?ym='.$nextmonth;

if(!empty($_SESSION["closed_message"])){
    $closed_message=$_SESSION["closed_message"];
}


?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title>管理者設定</title>
    </head>
    <body id="user_config">
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
            <h2>休館日設定</h2>
            <table>
                <form method="post" action="daysclosed.php" name="daysclosed">
                    <tr>
                        <td>休館日</td>
                        <td><input type="date" name="date" value="<?=$year?>-<?=$month2?>-01"></td>
                        <td>休館日の名前</td>
                        <td>
                            <select name="d_name">
                                <option>予定を選択してください</option>
                                <option value="休館日">休館日</option>
                                <option value="夏季休館日">夏季休館日</option>
                                <option value="冬季休館日">冬季休館日</option>
                                <option value="臨時休館日">臨時休館日</option>
                            </select>
                        </td>
                        <td><input type="submit" value="休館日設定" onClick="return registCheck()"></td>
                        <td><span id="err1"></span><span id="err2"></span></td>
                    </tr>
                </form>
            </table>
            <?php
            if(!empty($closed_message)){//休館日を設定・削除した場合
                ?>
            <div><?=$closed_message?></div>
            <?php
            unset($_SESSION['closed_message']);
            };
            ?>
            
            <section id="staff">
            <h3><?=$year ?>年<?=$month ?>月</h3>
                <div id="month_c">
                    <p id="prev"><a href="<?=$prev?>">＜前月</a></p>
                    <p id="next"><a href="<?=$next?>">次月＞</a></p>
                </div>
            <table>
                <tr>
                    <?php
                    weeklist_japanese();
                    ?>
                </tr>
                <tr>
<?php
//最初の週の調整
//１日の曜日を取得 w=曜日(0(日),6(土))
$wd1 = date("w",mktime(0,0,0,$month,1,$year));
for($i=1;$i<=$wd1;$i++){
    echo("<td></td>");
};
$day = 1;
while(checkdate($month,$day,$year)){

    //$dayと$monthを2桁にする
    
    $month2 = two_digit($month);
    $day2 = two_digit($day);

    $date2 = $year.'-'.$month2.'-'.$day2;
    
    //祝日
    if(isset($holiday[$date2])&&isset($days_closed[$date2])){//祝日と休館日が重なっていたら
        ?>
                    <td class='holiday' class='delete'><?=$day?>&nbsp;<?=$holiday[$date2]?><br>
                        <span><?=$days_closed[$date2]?></span><br>
                        <form method='post' action='daysclosed_delete.php' name='delete'>
                            <input type='hidden' name='deletedays' value='<?=$date2?>'>
                            <input type='submit' value='削除'>
                        </form>
                    </td>
        <?php
    }else if(isset($holiday[$date2])){//祝日なら
        ?>
                    <td class='holiday'><?=$day?>&nbsp;<?=$holiday[$date2]?><br>
                    </td>
        <?php
    }else if(isset($days_closed[$date2])){//休館日なら
        ?>
                    <td class='delete'><?=$day?><br>
                        <span><?=$days_closed[$date2]?></span><br>
                        <form method='post' action='daysclosed_delete.php' name='delete'>
                            <input type='hidden' name='deletedays' value='<?=$date2?>'>
                            <input type='submit' value='削除'>
                        </form>
                    </td>
        <?php
    }else{
        echo("<td>$day</td>");
    }
    //mktime関数で土曜日を取得 土曜日ならば</tr>
    if(date("w",mktime(0,0,0,$month,$day,$year))==6){
        echo("</tr>");
        //次の週が存在するかcheckdate関数で確認 あれば<tr>
        if(checkdate($month,$day+1,$year)){
            echo("<tr>");
        }
    }
    $day++;
}
//最後の週の調整 (次月の初めの曜日を取得し、引く7を繰り返す)
$wdx=date("w",mktime(0,0,0,$month+1,0,$year));
for($i=1;$i<7-$wdx;$i++){
    echo("<td></td>");
}

?>
                </tr>
            </table>
            </section>
        </main>
        <script>
        //未入力チェック
        function registCheck(){
            var date = document.daysclosed.date.value;
            var d_name = document.daysclosed.d_name.value;
            
            if(date=="" || d_name=="予定を選択してください"){
                    if(date==""){
                        var err1 = document.getElementById('err1');
                        err1.innerText="※日付を選択してください";
                    }else{
                        var err1 = document.getElementById('err1');
                        err1.innerText="";
                    }
                    if(d_name=="予定を選択してください"){
                        var err2 = document.getElementById('err2');
                        err2.innerText="※予定を選択してください";
                    }else{
                        var err2 = document.getElementById('err2');
                        err2.innerText="";
                    }
                    return false;
                }
                return true;
            }
        </script>
    </body>
</html>