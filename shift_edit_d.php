<?php
require("db_connect.php");
require_once('php/function.php');
session_start();
$db = connect();
//ログインせずページに来た場合
$login_name = $_SESSION['staff_name'];
if($login_name==''){
    header("Location:./login.php");
};



//POSTで取得
if(!empty($_POST['y-m-d'])){
    $y_m_d = $_POST['y-m-d'];
    
    $y=mb_substr($y_m_d, 0, 4);//文字列の切り出し
    $m=mb_substr($y_m_d, 5, 2);
    $d=mb_substr($y_m_d, 8, 2);
    
    $d2=two_digit($d);
    
}else{
    $y_m_d = $_SESSION['y_m_d'];
    
    $y=mb_substr($y_m_d, 0, 4);//文字列の切り出し
    $m=mb_substr($y_m_d, 5, 2);
    $d=mb_substr($y_m_d, 8, 2);
    
    $d2=two_digit($d);
    
};


$ymd=$y.'/'.$m.'/'.$d2;
$ym=$y.'/'.$m;

$prev=$y.'/'.$m.'/'.($d2-1);
$next=$y.'/'.$m.'/'.($d2+1);
$t=date('Y/m/t', strtotime($y_m_d));

$w=date('w',mktime(0, 0, 0, $m, $d, $y));
$week=['日','月','火','水','木','金','土'];
$week=$week[$w];

//シフト全時間配列
$color=array('09:00','09:30','10:00','10:30',
             '11:00','11:30','12:00','12:30',
             '13:00','13:30','14:00','14:30',
             '15:00','15:30','16:00','16:30',
             '17:00','17:30','18:00','18:30',
             '19:00','19:30','20:00','20:30',
             '21:00','21:30','22:00','22:30');

//祝日配列
//composerからyasumi（祝日）を取得
require_once 'vendor/autoload.php';
use Yasumi\Yasumi;

function holidays(\DateTimeInterface $currentTime, $country = 'Japan', $locale = 'ja_JP'): array
{
    $holidays = Yasumi::create($country, (int)$currentTime->format('Y'), $locale);
    $results  = [];
    foreach ($holidays->getHolidays() as $holiday) {
        $results[$holiday->format('Y/m/d')] = $holiday->getName();
    }
    return $results;
}
$holiday=holidays(new \DateTime($y_m_d));
if(!empty($holiday[$y_m_d])){
    $holiday_n=$y_m_d;
    $holiday_name=$holiday[$y_m_d];
}

//休館日配列
try{
    $sql="SELECT * FROM days_closed WHERE delete_flag=0";
    $pre=$db->prepare($sql);
    $pre->execute();
}catch(PDOException $e){
    print('SQLエラー：'.$e->getMessage());
}
$closed_days=array();
while($row=$pre->fetch()){
    $date=$row['days_closed'];
    $year=mb_substr($date, 0, 4);//文字列の切り出し
    $month=mb_substr($date, 5, 2);
    $day=mb_substr($date, 8, 2);
    $day2 = two_digit($day);
    
    $date=$year.'/'.$month.'/'.$day2;
    $d_name=$row['d_name'];
    $days_closed[$date]=$d_name;
}
$closed_f=isset($days_closed[$ymd]);


//編集中シフト配列　$shift[スタッフ名]=['start']['end']
$shift=array();
try{
    $sql="SELECT * FROM edit_shift_date WHERE date=? AND delete_flag=0";
    $pre=$db->prepare($sql);
    $pre->bindValue(1,$ymd,PDO::PARAM_STR);
    $pre->execute();
}catch(PDOException $e){
    print('SQLエラー：'.$e->getMessage());
}
if($pre->rowCount()>0){
    while($row=$pre->fetch()){
    $name=$row['name'];
    $start_t=$row['start_time'];
    $end_t=$row['ending_time'];
    $shift[$name]=array('start_t'=>$start_t,'end_t'=>$end_t);
    }
}
$e_count=0;//sift_edit_submit.phpに送る$edit配列のカウント用

?>
<!DOTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title>シフト登録完了</title>
    </head>
    <body id="shift_edit">
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
        </header>
        <main>
            <article>
                <h2>シフト調整</h2>
                <?php
                if(!empty($holiday_n)){//祝日なら
                    ?>
                    <p><span class="holiday_n"><?=$ymd?>（<?=$week?>）<?=$holiday_name?></span>のシフト</p>
                    <?php
                }else if($w==0){//日曜日なら
                    ?>
                    <p class="holidayFont"><?=$ymd?>（<?=$week?>）</span>のシフト</p>
                    <?php
                }else if($w==6){//土曜日なら
                    ?>
                    <p class="saturdayFont"><?=$ymd?>（<?=$week?>）</span>のシフト</p>
                    <?php
                }else{//祝日・土日以外なら
                    ?>
                    <p><?=$ymd?>（<?=$week?>）</span>のシフト</p>
                    <?php
                }
                ?>

                <div id="d">
                    <table>
                        <td class="d">
                <?php
                $ym1=$y.'/'.$m.'/01';
                if($ym1!==$ymd){
                    ?>
                
                            <form method="post" action="shift_edit_d.php">
                                <button type="submit" name="y-m-d" value="<?=$prev?>">&lt;前日</button>
                            </form>
                
                <?php
                }
                ?>
                        </td>
                        <td id="m">
                            <form method="post" action="shift_edit_m.php">
                                <button type="submit" name="ym" value="<?=$ym?>">月表示に戻る</button>
                            </form>
                        </td>
                        <td class="d">
                <?php
                if($t!==$ymd){
                    ?>
                            <form method="post" action="shift_edit_d.php">
                                <button type="submit" name="y-m-d" value="<?=$next?>">翌日&gt;</button>
                            </form>
                <?php
                }
                ?>
                        </td>
                    </table>
                </div>
                    <?php
                    
                if($closed_f=='1'){//休館日の場合
                    $c=$days_closed[$ymd];
                    ?>
                    <p><?=$c?>のためシフト登録がありません</p>
                <?php
                }else if($closed_f=='0'){//開館日の場合

                //スタッフシフト希望時間
                try{
                    $sql="SELECT * FROM shift_date WHERE date=? AND delete_flag=0 ORDER BY position,start_time";
                    $pre=$db->prepare($sql);
                    $pre->bindValue(1,$y_m_d,PDO::PARAM_STR);
                    $pre->execute();
                }catch(PDOException $e){
                    print("SQLエラー：".$e->getMessage());
                }
                if($pre->rowCount()==0){//希望シフトが登録されていなければ
                    ?>
                <p>シフト登録しているスタッフがいません</p>
                <?php
                    
                }else{
                    
                ?>
                <div id=shift_edit_d>
                <form method="post" action="shift_edit_d_submit.php"><input type="submit" value="更新">
                    <table>
                        <tr> <!-テーブルヘッダー->
                            <th id="n"  class="border">名前<br>【希望時間】</th>
                            <th id="e_t"  class="border">時間調整</th>
                            <?php
                            for($i=9;$i<=22;$i++){//テーブルヘッダーの時間9:00～22:00を表示
                                ?>
                                <th class='t' colspan="2"><?=$i?>:00</th>
                                <?php
                            }
                            ?>             
                        </tr>
                        <tr>
                            <?php
                    
                    //スタッフシフト希望時間
                    while($row=$pre->fetch()){
                        $name=$row['name'];
                        $o_start=$row['start_time'];
                        $o_end=$row['ending_time'];
                        $time=$o_start.'～'.$o_end;
                        $position=$row['position'];

                        $start_h=0;
                        $start_m=0;
                        $end_h=0;
                        $end_m=0;

                        
                        //編集シフト配列から時間を取り出す
                        if(!empty($shift[$name])){//編集シフトに時間が登録されていたらselectのvalueに
                            $start_t=$shift[$name]['start_t'];
                            $start_h=mb_substr($start_t, 0, 2);//文字列の切り出し
                            $start_m=mb_substr($start_t, 3, 2);
                            $end_t=$shift[$name]['end_t'];
                            $end_h=mb_substr($end_t, 0, 2);//文字列の切り出し
                            $end_m=mb_substr($end_t, 3, 2);
                        
                            if(($o_start>$start_t || $end_t>$o_end) &&
                            (!empty($start_t) && !empty($end_t))&&(!empty($shift[$name]))){//編集中シフトが希望時間内かチェック
                                $err="<span class='err'><br>希望時間外<span>";
                            };
                        };
                        
                    ?>
                            
                            <td rowspan="3" class="border"><?=$name?><br>
                                【<?=$time?>】
                                <?php
                                if(!empty($err)){//編集中シフトが希望時間外なら
                                    ?>
                                    <?=$err?>
                                    <?php
                                }
                                ?>
                                </td>
                            <td rowspan="3" class="border" class="e_t">
                                <input type="hidden" name="edit[<?=$e_count?>][name]" value="<?=$name?>">
                                <input type="hidden" name="edit[<?=$e_count?>][position]" value="<?=$position?>">
                                <input type="hidden" name="edit[<?=$e_count?>][date]" value="<?=$y_m_d?>">
                                <select name="edit[<?=$e_count?>][start_h]">
                                    <option value="0">0</option>
                                    <?php
                                    for($i=9;$i<=20;$i++){
                                        if(preg_match('/^([0-9]{1})$/', $i)){
                                            $i = '0'.$i;
                                        }
                                
                                    if($start_h==$i){//編集シフトに登録済みselectボタンで初期表示
                                    ?>
                                    <option selected value="<?=$i?>"><?=$i?></option>
                                    <?php
                                    }else{//編集シフト登録なしselectボタン0
                                    ?>
                                    <option value="<?=$i?>"><?=$i?></option>
                                    <?php
                                    }
                                }
                                    ?>
                                </select>
                                ：
                                <select name="edit[<?=$e_count?>][start_m]">
                                    <?php
                                    if($start_m==30){//編集シフトに登録済みselectボタンで初期表示
                                        ?>
                                    <option value="00">00</option>
                                    <option selected value="30">30</option>
                                    <?php
                                    }else{
                                    ?>
                                    <option value="00">00</option>
                                    <option value="30">30</option>
                                    <?php
                                }
                                ?>
                                </select>
                                ～
                                <select name="edit[<?=$e_count?>][end_h]">
                                <option value="0">0</option>
                                    <?php
                                    for($i=12;$i<=23;$i++){

                                    if($end_h==$i){//編集シフトに登録済みselectボタンで初期表示
                                    ?>
                                    <option selected value="<?=$i?>"><?=$i?></option>
                                    <?php
                                    }else{//編集シフト登録なしselectボタン0
                                    ?>
                                    <option value="<?=$i?>"><?=$i?></option>
                                    <?php
                                    }
                                    }
                                    ?>
                                </select>
                                ：
                                <select name="edit[<?=$e_count?>][end_m]">
                                    <?php
                                    if($end_m==30){//編集シフトに登録済みselectボタンで初期表示
                                        ?>
                                    <option value="00">00</option>
                                    <option selected value="30">30</option>
                                    <?php
                                    }else{
                                    ?>
                                    <option value="00">00</option>
                                    <option value="30">30</option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                <br>  
                            </td>
                            <?php
                                
                            for($i=0;$i<=27;$i++){
                                if($i&1){//奇数ならば
                                    ?>
                            <td class="odd"></td>
                            <?php
                                }else{
                                    ?>
                            <td class="even"></td>
                            <?php
                                }
                            }
                            ?>
                            
                        </tr>
                        <tr>
                            <?php
                        $color_f=0;
                        for($i=0;$i<=27;$i++){
                            if($i&1){//奇数ならば
                                
                                    if(!empty($shift[$name])&&$color[$i]==$start_t && $color_f==0){//開始時間があれば
                                        ?>
                                            <td class="color_odd"></td>
                                            <?php
                                                $color_f=1;
                                        
                                                }else if(!empty($shift[$name])&&$color[$i]!==$end_t && $color_f==1){//開始時間があり終了時間がなければ
                                        ?>
                                            <td class="color_odd"></td>
                                            <?php
                                        
                                                }else if(!empty($shift[$name])&&$color[$i]==$end_t){
                                        ?>
                                            <td class="n_color_odd"></td>
                                            <?php
                                                $color_f=0;
                                        
                                                }
                                else if($color_f==0){
                        ?>
                            <td class="n_color_odd"></td>
                            <?php
                                }
                            }else if(!($i&1)){//偶数ならば
                                
                                    if(!empty($shift[$name])&&$color[$i]==$start_t && $color_f==0){//開始時間があれば
                                        ?>
                                            <td class="color_even"></td>
                                            <?php
                                                $color_f=1;
                                        
                                                }else if(!empty($shift[$name])&&$color[$i]!==$end_t && $color_f==1){//開始時間があり終了時間がなければ
                                        ?>
                                            <td class="color_even"></td>
                                            <?php
                                        
                                                }else if(!empty($shift[$name])&&$color[$i]==$end_t){
                                        ?>
                                            <td class="n_color_even"></td>
                                            <?php
                                                $color_f=0;
                                        
                                                }
                                else if($color_f==0){
                        ?>
                            <td class="n_color_even"></td>
                            <?php
                                }
                            }
                        }
                        ?>
                        </tr>
                        <tr class="border-bottom">
                            <?php
                            for($i=0;$i<=27;$i++){
                                if($i&1){//奇数ならば
                                    ?>
                            <td class="odd"></td>
                            <?php
                                }else{
                                    ?>
                            <td class="even"></td>
                            <?php
                                }
                            }
                            ?>
                        </tr>
                        <?php
                        $err="";
                        //
                        $start_t='';
                        $end_t='';
                        $e_count=$e_count+1;//sift_edit_submit.phpに送る$edit配列のカウント用
                    }
                }
                }
                            ?>
                    </table>
                    </form>
                </div>
            </article>
        </main>
        <script>
        </script>
    </body>
</html>