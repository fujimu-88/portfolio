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

//当月を取得
    $year=$_SESSION['year'];
    $month=$_SESSION['month'];
    $ym=$year.'/'.$month;
    $yasumi_c=$year.'-'.$month.'-01';
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

//$monthを2桁にする
$month2 = two_digit($month);


//締め切り日が設定されているか
try{
    $sql="SELECT * FROM shift_deadline WHERE deadline_month=? AND delete_flag=0";
    $pre=$db->prepare($sql);
    $pre->bindValue(1,$ym,PDO::PARAM_STR);
    $pre->execute();
}catch(PDOException $e){
    print("SQLエラー：".$e->getMessage());
};
if($pre->rowCount()>0){
    while($row=$pre->fetch()){
        $deadline_date = $row['deadline_date'].'<a href="setting_d_line_delete.php">締切日削除</a>';
    }
}else{
    $deadline_date = '未設定';
}
if(!empty($_SESSION['message'])){
    $message=$_SESSION['message'];
}else{
    $message='';
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
            <h2>シフト締切日設定</h2>
            <table>
                <form method="post" action="setting_d_line.php" name="deadline">
                    <p id="err1"></p>
                    <tr>
                        <td>シフト締切日設定月</td>
                        <td>
                        <?=$year ?>年<?=$month ?>月
                        </td>
                        <td>提出締切日</td>
                        <td><?=$deadline_date?></td>
                    </tr>
                    <tr>
                        <td>締切日</td>
                        <td><input type="date" name="date"></td>
                        <td><input type="submit" onClick="return registCheck()" value="締切日設定""></td>
                        <td><td>
                        <td><?=$message?></td>
                    </tr>
                </form>
            </table>
            
            <section id="staff">
            <h3><?=$year ?>年<?=$month ?>月</h3>
                <div id="month_c">
                    <p id="prev"><a href="prev_jump.php">＜前月</a></p>
                    <p id="next"><a href="next_jump.php">次月＞</a></p>
                </div>
            <table>
                <tr>
                    <?=weeklist_japanese()?>
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
    $day2 = two_digit($day);

    $month2 = two_digit($month);

    $date2 = $year.'-'.$month2.'-'.$day2;
    
    //祝日
    if(isset($holiday[$date2])&&isset($days_closed[$date2])){//祝日と休館日が重なっていたら
        ?>
                    <td class='holiday' class='delete'><?=$day?>&nbsp;<?=$holiday[$date2]?><br>
                        <span><?=$days_closed[$date2]?></span>
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
                        <span><?=$days_closed[$date2]?></span>
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

$_SESSION['message']='';
$message='';
?>
                </tr>
            </table>
            </section>
        </main>
    </body>
    <script>
        //締切日チェック
        function registCheck(){
            var deadlineDate = (document.deadline.date.value);
            var displayYear = '<?=$year?>';
            var displayMonth = '<?=$month?>';
            var displayDate = displayYear +'-'+ displayMonth + '-01';
            //設定月より1週間前にセットできないようにするため
            //1週間前の日付を取得
            var oneweek=new Date(displayDate);
            oneweek.setDate(oneweek.getDate()-14);
            var year=oneweek.getFullYear();
            var month=oneweek.getMonth()+1;
            if(String(month).length==1){
                month="0"+month;
            }
            var date=oneweek.getDate();
            var prevMonth=year+'-'+month+'-'+date;
            console.log(prevMonth);
            if(deadlineDate==""){//締切日が選択されていない場合
                var err =document.getElementById('err1');
                err1.innerText="※日付を選択してください";
                return false;
            }else if(displayDate<=deadlineDate){
                var err =document.getElementById('err1');
                err1.innerText="※締切日をシフト締切日設定月より前に設定してください";
                return false;
            }else if(prevMonth<=deadlineDate){
                var err =document.getElementById('err1');
                err1.innerText="※シフト締切設定月より2週間以上前に設定してください";
                return false;
            }
        return true;
    }
    </script>
</html>