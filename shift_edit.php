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
                <?php
                //シフト登録がある最新月を取得
                    try{
                        $sql="SELECT * FROM shift_date WHERE delete_flag=0 ORDER BY date desc";
                        $pre=$db->prepare($sql);
                        $pre->execute();
                    }catch(PDOException $e){
                        print("SQLエラー：".$e->getMessage());
                    }
                    if($pre->rowCount()==0){
                        print("シフトが登録されていません");
                    }else{
                        while($row=$pre->fetch()){
                            $date=$row['date'];
                            //年と月を取り出す
                            $y=date('Y', strtotime($date));
                            $m=date('m', strtotime($date));
                            $d=date('d', strtotime($date));
                            $yearmonth[]=$y.'/'.$m;
                            
                        }
                        //最新月
                        $new_ym=$yearmonth[0];
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
                    if(isset($date)){
                        $holiday=holidays(new \DateTime($date));
                    }


                    if(isset($new_ym)){
                    if(empty($_SESSION['ym'])){//最新月　月検索POSTなし
                        //表示月の期間
                        $ym=$new_ym;
                        $firstdate=$ym.'/01';
                        $y=mb_substr($ym, 0, 4);//文字列の切り出し
                        $m=mb_substr($ym, 5, 2);
                        $lastdate=date("Y/m/t",mktime(0,0,0,$m,1,$y));
                    }else{//月検索POSTがあり
                        //表示月の期間
                        $ym=$_SESSION['ym'];
                        $firstdate=$ym.'/01';
                        $y=mb_substr($ym, 0, 4);//文字列の切り出し
                        $m=mb_substr($ym, 5, 2);
                        $lastdate=date("Y/m/t",mktime(0,0,0,$m,1,$y));
                    };

                    //表示中の月の編集(edit)シフトデータを配列に
                    $shift_date=array();
                    try{
                    $sql="SELECT * FROM edit_shift_date WHERE delete_flag=0 AND date BETWEEN '".$firstdate."' AND '".$lastdate."' ORDER BY date,start_time";
                        $pre=$db->prepare($sql);
                        $pre->execute();
                    }catch(PDOException $e){
                        print('SQLエラー：'.$e->getMessage());
                    }
                    while($row=$pre->fetch()){
                        $date=$row['date'];
                        $y_date=mb_substr($date, 0, 4);//文字列の切り出し
                        $m_date=mb_substr($date, 5, 2);
                        $d_date=mb_substr($date, 8, 2);
                        $date=$y_date.'/'.$m_date.'/'.$d_date;
                        

                        $name=$row['name'];
                        $start_t=$row['start_time'];
                        $end_t=$row['ending_time'];
                        
                        $shiftdate[$name][$date]=array('start_t'=>$start_t,'end_t'=>$end_t);
                    }
                    

                    //表示中月の希望シフトを配列に
                    $o_shift=array();
                    try{
                        $sql="SELECT * FROM shift_date WHERE delete_flag=0 AND date BETWEEN '".$firstdate."' AND '".$lastdate."' ORDER BY date,start_time";
                        $pre=$db->prepare($sql);
                        $pre->execute();
                    }catch(PDOException $e){
                        print('SQLエラー：'.$e->getMessage());
                    }

                        while($row=$pre->fetch()){
                            $name=$row['name'];
                            $date=$row['date'];
                            $y_date=mb_substr($date, 0, 4);//文字列の切り出し
                            $m_date=mb_substr($date, 5, 2);
                            $d_date=mb_substr($date, 8, 2);
                            $date=$y_date.'/'.$m_date.'/'.$d_date;
                            
                            $start_t=$row['start_time'];
                            $end_t=$row['ending_time'];
                            $o_shift[$name][$date]=array('start_t'=>$start_t,'end_t'=>$end_t);
                        }
                    //表示中月の希望シフトを提出しているスタッフを表示position,staff_id順に
                    try{
                        $sql="SELECT * FROM shift_date WHERE delete_flag=0 AND date BETWEEN '".$firstdate."' AND '".$lastdate."' ORDER BY position,staff_id";
                        $pre=$db->prepare($sql);
                        $pre->execute();
                    }catch(PDOException $e){
                        print('SQLエラー：'.$e->getMessage());
                    }

                    $staff=array();
                    $staff_color=array();
                        while($row=$pre->fetch()){
                            $name=$row['name'];
                            $position=$row['position'];

                            if(in_array($name,$staff)==NULL){
                                $staff[]=$name;
                                $staff_color[$name]=array('position'=>$position);
                            }
                        }
                    //公開・更新ボタン
                    try{
                        $sql="SELECT * FROM release_date WHERE shift_date='".$ym."'";
                        $pre=$db->prepare($sql);
                        $pre->execute();
                    }catch(PDOException $e){
                        print("SQLエラー：".$e->getMessage());
                    }
                    if($pre->rowCount()==0){
                        $release_date='';
                        $update_date='';
                        $button_name='公開';
                    }else{
                        while($row=$pre->fetch()){
                            $release_date=$row['release_date'];
                            $y_r=mb_substr($release_date, 0, 4);//文字列の切り出し
                            $m_r=mb_substr($release_date, 5, 2);
                            $d_r=mb_substr($release_date, 8, 2);
                            $release_date=$y_r.'/'.$m_r.'/'.$d_r;

                            $update_date=$row['update_date'];
                            $y_u=mb_substr($update_date, 0, 4);//文字列の切り出し
                            $m_u=mb_substr($update_date, 5, 2);
                            $d_u=mb_substr($update_date, 8, 2);
                            $update_date=$y_u.'/'.$m_u.'/'.$d_u;
                            
                        }
                            if($update_date=='0'){
                                $release_date='公開日：'.$release_date;
                                $update_date='';
                            }else{
                                $release_date='公開日：'.$release_date;
                                $update_date='最終更新日：'.$update_date;
                            }
                        $button_name='更新';
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

                ?>
                <h2>シフト調整</h2>
                <p><?=$ym?>月のシフト
                <?php
                if(!empty($_SESSION['err'])){
                    $err=$_SESSION['err'];
                    ?>
                    <span class="err"><?=$err?></span>
                    <?php
                    $_SESSION['err']='';
                }
                ?>
                </p>
                <div>
                    <table id="editSearch">
                    <?php
    $yearmonth=array_unique($yearmonth);//配列中の重複削除
    $yearmonth=array_values($yearmonth);//配列キーを整頓
    $yearmonth_c=count($yearmonth)-1;
    $y_m=0;
    for($i=0;$i<=$yearmonth_c;$i++){
        $ym1=$yearmonth[$i];
        
        $y_m.='<option value="'.$ym1.'">'.$ym1.'</option>';
    }
                        ?>
                        
                            <tr>
                                <form method="post" action="shift_edit_m.php">
                                    <td>月：
                                    <select name="ym"><?=$y_m ?></select>
                                    <input id="button" type="submit" value="検索">
                                    </td>
                                </form>
                                <form method="post" action="release.php" onsubmit="return submitCk()">
                                    <td>
                                        <?=$update_date?>&nbsp;&nbsp;<?=$release_date?>
                                        
                                        <input type="hidden" name="ym" value="<?=$ym?>">
                                        <button id="button" type=submit><?=$button_name?></button>
                                        
                                    </td>
                                </form>
                            </tr>
                            </table>
                            <table id="colorTable">
                            <tr>
                                <td class="gym">ジムスタッフ</td>
                                <td class="reception">フロントスタッフ</td>
                                <td class="off_hour">希望時間外</td>
                            </tr>
                        
                    
                    </table>
                </div>
                <div id="editTable">
                    
                    <?php
                            
                    ?>
                    <table>
                         <tr>
                             <td id="name"></td>
                            <?php
                                    $d=1;
                                    while(checkdate($m,$d,$y)){
                                        $d2 = two_digit($d);
                                        
                                        $ymd=$y.'-'.$m.'-'.$d2;
                                        $ymd2=$y.'/'.$m.'/'.$d2;
                                        
                                        $tStamp = mktime(0, 0, 0, $m, $d, $y);
                                        $w=date('w',$tStamp);
                                        $week=['日','月','火','水','木','金','土'];
                                        
                                        if(isset($holiday[$ymd]) || $w==0){
                             ?>
                             <form method="post" action="shift_edit_d.php">
                                 <input type="hidden" name="y-m-d" value="<?=$ymd?>">
                                 
                                 <td class="holiday"><button type="submit"><?=$d?><br><?=$week[$w]?></button></td>
                                 
                             </form>
                             <?php
                                        }else if((isset($holiday[$ymd])==0) && $w==6){
                             ?>
                             <form method="post" action="shift_edit_d.php">
                                 <input type="hidden" name="y-m-d" value="<?=$ymd?>">
                                 
                                 <td class="saturday"><button type="submit"><?=$d?><br><?=$week[$w]?></button></td>
                                 
                             </form>
                             <?php               
                                        }else{
                            ?>
                            <form method="post" action="shift_edit_d.php">
                                 <input type="hidden" name="y-m-d" value="<?=$ymd?>">
                                 
                                 <td class="weekdays"><button type="submit"><?=$d?><br><?=$week[$w]?></button></td>
                                 
                             </form>
                            <?php           
                                        }
                                        $d++;
                                    }
                            ?>
                        </tr>
                        
                        
                            <?php
                        //休館日の行を結合するためのカウント
                            $staff_count=count($staff);
                        //シフト登録しているスタッフ分繰り返す
                            $staff_c=count($staff)-1;
                            $closed_flag=0;
                            for($i=0;$i<=$staff_c;$i++){
                                
                                ?>
                        <tr>
                        <?php
                        //gym or reception 背景色変える
                        $n=$staff[$i];
                        if($staff_color[$n]['position']=='ジム'){//gymの場合
                            ?>
                            <td class="gym"><?=$n?></td>
                            <?php
                        }else{//receptionの場合
                        ?>
                            <td class="reception"><?=$n?></td>
                            <?php
                        }    
                                    
                                $d=1;
                                
                                while(checkdate($m,$d,$y)){
                                    $d2 = two_digit($d);
                                    
                                    $ymd=$y.'/'.$m.'/'.$d2;
                                    if(isset($days_closed[$ymd])&&$closed_flag==0){//休館日の場合
                                    //休日配列に存在するか確認　$closed.$年月日の変数を作成し=0
                                    ?>
                            <td class="daysClosed" rowspan="<?=$staff_count?>"><p><?=$days_closed[$ymd]?></p></td>
                            <?php
                                        $closed_flag=1;
                                    }else if(isset($shiftdate[$staff[$i]][$ymd])){
                                    //編集シフトが登録されている場合
                                        if($o_shift[$staff[$i]][$ymd]['start_t'] > $shiftdate[$staff[$i]][$ymd]['start_t'] || $shiftdate[$staff[$i]][$ymd]['end_t'] > $o_shift[$staff[$i]][$ymd]['end_t']){
                                            ?>
                            <td class="off_hour"><?=$shiftdate[$staff[$i]][$ymd]['start_t']?>～<?=$shiftdate[$staff[$i]][$ymd]['end_t']?></td>
                            <?php
                                        }else{
                                    ?>
                            <td class="time"><?=$shiftdate[$staff[$i]][$ymd]['start_t']?>～<?=$shiftdate[$staff[$i]][$ymd]['end_t']?></td>
                            <?php
                                        }
                                    }else if(isset($days_closed[$ymd])==''){
                                    //編集シフトが未登録かつ休館日でない場合
                                    ?>
                            <td></td>
                            <?php
                                    }
                                    $d++;
                                }
                            ?>
                            
                        </tr>
                                <?php
                            }
                            ?>
                        
                    </table>
                </div>
                <?php
                }
                ?>
                
            </article>
        </main>
        <script>
        //release.php送信前に確認ダイアログ表示
            //shift_edit.php内$release_dateをjsに
            var release_date;//公開or更新表示用
            var release_date = "<?=htmlspecialchars($release_date, ENT_QUOTES,'UTF-8');?>";
            var update_shift;//何月のシフトを更新するか
            var update_shift = "<?=htmlspecialchars($ym, ENT_QUOTES,'UTF-8');?>";

                if(!release_date){//var release_date内がnullの場合
                    function submitCk(){
                    //確認ダイアログの内容
                    var check =confirm(update_shift + "月のシフトを公開してもよろしいですか？\n\n公開しない場合は[キャンセル]ボタンを押してください");
                    //公開しない場合はfalse
                    return check;
                    }
                }else{
                    function submitCk(){//var release_date内が存在している場合
                    //確認ダイアログの内容
                    var check =confirm(update_shift + "月のシフトを更新してもよろしいですか？\n\n更新しない場合は[キャンセル]ボタンを押してください");
                    //更新しない場合はfalse
                    return check;
                    }
                }
        </script>
    </body>
</html>