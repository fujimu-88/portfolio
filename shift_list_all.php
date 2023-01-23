<?php
session_start();
require("db_connect.php");
require_once("php/function.php");

//ログインせずページに来た場合
$login_name = $_SESSION['staff_name'];
if($login_name==''){
    header("Location:./login.php");
};

$db=connect();

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
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <script type="text/javascript" src="https://code.jquery.com/jquery-3.4.0.min.js"></script>
        <title>シフト一覧</title>
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
            
<?php
    //シフト登録がある最新月をセレクトメニューで表示
try{
    $sql="SELECT * FROM shift_date WHERE delete_flag=0 ORDER BY date desc";
    $pre=$db->prepare($sql);
    $pre->execute();
}catch(PDOException $e){
    print("SQLエラー：".$e->getMessage());
}
if($pre->rowCount()>0){
    while($row=$pre->fetch()){
        $date=$row['date'];
        //年と月を取り出す
        $y=date('Y', strtotime($date));
        $m=date('m', strtotime($date));
        $yearmonth[]=$y.'/'.$m;
    }

$yearmonth=array_unique($yearmonth);//配列中の重複削除
$yearmonth=array_values($yearmonth);//配列キーを整頓
$yearmonth_c=count($yearmonth);
$ymt = date("Ymt");//年　月　月末日
$year=mb_substr($ymt, 0, 4);//文字列の切り出し
$month=mb_substr($ymt, 4, 2);
$y_m='';
?>
<div id="search">
            <form name="search" method="POST" action="shift_search_list.php"> 
                <table>
                    <tr>
                        <td>年月：</td>
                        <td>
<?php
for($i=0;$i<=$yearmonth_c-1;$i++){
    $y_m.='<option value="'.$yearmonth[$i].'">'.$yearmonth[$i].'</option>';
}
echo('
<select name="ym">
'.$y_m.'</select>');
?>
                        </td>
                        <td>日：</td>
                        <td>
                        <select name="day">
                            <option value="all">-</option>
<?php
for($i=1;$i<=31 ; $i++) {
    $day2 = two_digit($i);
    
		echo '<option value="'.$day2.'">'.$day2.'</option>';
}

?>  
                            </select>
                        </td>
                        <td>名前検索：</td>
                        <td>
                            <input type="text" name="search_key">
                        </td>
                        <td>サポート：</td>
                        <td>
                            <select name="position">
                                <option value="フロント/ジム">フロント/ジム</option>
                                <option value="フロント">フロント</option>
                                <option value="ジム">ジム</option>
                            </select>
                        </td>
                        <td><input type="submit" value="検索"></td>
                    </tr>
                </table>
        </form>
                </div>
            <?php
            //提出状況
            //登録されているシフトデータの最新月を取得
            $new_ym = $yearmonth[0];
            $year=mb_substr($new_ym, 0, 4);//文字列の切り出し
            $month=mb_substr($new_ym, 5, 2);
            $first = $new_ym.'/01';
            $last = $new_ym.'/31';
            $name2=array();
            ?>
                <div id="submission">
                    <input type="button" value="<?=$new_ym?>月分シフト提出状況">
                    <div id="submissionTable">
                        <table border="1">
                            <tr>
                                <th>提出状況</th>
                                <th>名前</th>
                            </tr>
                            <?php
                            try{
                                $sql2="SELECT * FROM shift_date WHERE delete_flag=0 AND date BETWEEN '".$first."' AND '".$last."' ORDER BY date,start_time";
                                $pre2=$db->prepare($sql2);
                                $pre2->execute();
                            }catch(PDOException $e){
                                print("SQLエラー：".$e->getMessage());
                            }
                            while($row2=$pre2->fetch()){//シフト登録している名前だけを抜き出す
                               $name=$row2['name'];
                                $name2[]=$name;
                            }
                            $name2=array_unique($name2);//重複する名前を削除
                            $name2=array_values($name2);//配列欠番を詰める
                           
                           array_unshift($name2, "0");//[0]は比較対象外なので配列の先頭に0
                            //現在登録されているスタッフを取得しシフト登録しているスタッフと比較
                            try{
                                $sql1="SELECT * FROM staff WHERE delete_flag=0 ORDER BY id,position";
                                $pre1=$db->prepare($sql1);
                                $pre1->execute();
                            }catch(PDOException $e){
                                print("SQLエラー：".$e->getMessage());
                            }
                            while($row1=$pre1->fetch()){
                                $name=$row1['staff_name'];
                                $flag=array_search($name,$name2);
                            ?>
                            <tr>


                                <?php
                                if($flag!=false){
                                    ?>
                                <td><?=$name?></td>
                                <td>○</td>
                                <?php
                                }else{
                                        ?>
                                <td class="not_s"><?=$name?></td>
                                <td class="not_s">×</td>
                                    <?php
                                    }
                                ?>
                            </tr>
                            <?php
                            }

                            ?>
                        </table>
                    </div>
                </div>
            
            
            <article id="shift_all">
                <div id="shift">
                <h2>シフト一覧</h2>
                <p><?=$year?>年<?=$month?>月のシフト一覧</p>
                <?php
                //シフトに登録されている最新月を取得
                $new_ym = $yearmonth[0];
                $first = $new_ym.'/01';
                $last = $new_ym.'/31';
                try{
                    $sql="SELECT * FROM shift_date WHERE delete_flag=0 AND date BETWEEN '".$first."' AND '".$last."' ORDER BY date,start_time";
                    $pre=$db->prepare($sql);
                    $pre->execute();
                    print("検索結果は".$pre->rowCount()."件です。<br>");
                }catch(PDOException $e){
                    print("SQL実行エラー：".$e->getMessage());
                    }
                    if($pre->rowCount()<1){
                        print("検索結果がありません<br>");
                    }else{
                
                        $shift_date=array();
                        while($row=$pre->fetch()){
                            $id=htmlspecialchars($row['shift_id'],ENT_QUOTES);
                            $position=htmlspecialchars($row['position'],ENT_QUOTES);
                            $date=htmlspecialchars($row['date'],ENT_QUOTES);
                            $year=mb_substr($date, 0, 4);//文字列の切り出し
                            $month=mb_substr($date, 5, 2);
                            $day=mb_substr($date, 8, 2);
                            $date=$year.'/'.$month.'/'.$day;
                            $time=htmlspecialchars($row['start_time']).'～'.htmlspecialchars($row['ending_time']);
                            $name=htmlspecialchars($row['name'],ENT_QUOTES);
                    
                       //シフト登録がある日の配列
                        $shift_date[]=array('id'=>$id,'date'=>$date,'position'=>$position,'time'=>$time,'name'=>$name);
                        }
                    ?>
                    
                
                <div id="shiftTable">
                    <table>
                        <tr>
                            <th>日付</th>
                            <th>サポート</th>
                            <th>時間</th>
                            <th>氏名</th>
                            <th colspan="2"></th>
                        </tr>
                    <?php
                    //シフトが入っている日と空白の日を人数分×月日数分繰り返し配列内にセット    
                        //配列内のシフトをカウント-1にして[0]からチェックできるようにする
                        $s_c=count($shift_date)-1;
                        //カレンダー シフトが登録されている最新月で作成
                        $day=1;
                        $year=mb_substr($new_ym, 0, 4);//文字列の切り出し
                        $month=mb_substr($new_ym, 5, 2);
                        
                        while(checkdate($month,$day,$year)){
                            //$dayとを2桁にする
                            $day2 = two_digit($day);
                            
                            $date2 = $year.'/'.$month.'/'.$day2;
                            
                            //曜日を取得
                            $tStamp = mktime(0, 0, 0, $month, $day, $year);
                            $w=date('w',$tStamp);
                            $week=['日','月','火','水','木','金','土'];
                            
                            if(isset($days_closed[$date2])){
                                $id="";
                                $d=$date2.'('.$week[$w].')';
                                $p=$days_closed[$date2];
                                $t="";
                                $n="";
                            }
                            //$shift_date配列数分だけ$date2と比較
                            for($i=0;$i<=$s_c;$i++){
                                if($shift_date[$i]['date']==$date2){ //日付が同じなら更に配列に引き渡す
                                    $id=$shift_date[$i]['id'];
                                    $d=$shift_date[$i]['date'].'('.$week[$w].')';
                                    $p=$shift_date[$i]['position'];
                                    $t=$shift_date[$i]['time'];
                                    $n=$shift_date[$i]['name'];
                                }else if(isset($days_closed[$date2])){//休館日の場合
                                    $id="";
                                    $d=$date2.'('.$week[$w].')';
                                    $p=$days_closed[$date2];
                                    $t="";
                                    $n="";
                                }else if($shift_date[$i]['date']!==$date2){//なければ最終時間を配列に
                                    
                                    $id="";
                                    $d=$date2.'('.$week[$w].')';
                                    $p="";
                                    $t="23:00～23:30";//マルチソートする時に最終になるように
                                    $n="";
                            }
                            
                        //シフト登録がない日+シフト登録がある日の配列
                        $shiftDateAll[]=array('id'=>$id,'date'=>$d,'position'=>$p,'time'=>$t,'name'=>$n);
                            }
                            $day++;
                        }
                        
                        $shiftDateAll=array_unique($shiftDateAll, SORT_REGULAR); //配列内の同じ内容を削除
                        $shiftDateAll=array_values($shiftDateAll); //配列欠番整列
                        //配列を日付、時間順にソート①ソート基準にする要素だけ取り出す
                        foreach($shiftDateAll as $value){
                            $date_array[] = $value['date'];
                            $time_array[] = $value['time'];
                        }
                        //配列を日付、時間順にソート②ソート基準の順番,タイプ,ソートする配列
                        array_multisort( $date_array, SORT_ASC, SORT_STRING, $time_array, SORT_ASC, SORT_STRING, $shiftDateAll);

                        
                        //配列内のシフトをカウント-1にして[0]からチェックできるようにする
                        $s_c2=count($shiftDateAll)-1;
                        //配列内をすべて繰り返し
                        for($i=0;$i<=$s_c2;$i++){
                                if($shiftDateAll[$i]['name']!==''){//名前登録があるシフトならテーブルに
                                    $date=$shiftDateAll[$i]['date'];
                                    ?>
                        <tr>
                            <td><?=$date?></td>
                            <td><?=$shiftDateAll[$i]['position']?></td>
                            <td><?=$shiftDateAll[$i]['time']?></td>
                            <td><?=$shiftDateAll[$i]['name']?></td>
                            <td>
                                <form name="update" method="post" action="shift_one_updateform.php">
                                    <input type="hidden" name="shift_id" value="<?=$shiftDateAll[$i]['id']?>">
                                    <input type="submit" value="更新">
                                </form>
                            </td>
                            <td>
                                <form name="delete" method="post" action="shift_list_delete_c.php">
                                    <input type="hidden" name="shift_id" value="<?=$shiftDateAll[$i]['id']?>">
                                    <input type="submit" value="削除">
                                </form>
                            </td>
                        </tr>
                        <?php
                                        $c_date=$date; //既にテーブルにある日付かチェック用に保存
                                }else if($date!==$shiftDateAll[$i]['date']){ //既にテーブルにある日付かチェック
                                    //テーブルに日付がなければ
                                    $date=$shiftDateAll[$i]['date'];
                                    ?>
                        <tr>
                            <td><?=$date?></td>
                            <td colspan="5"><?=$shiftDateAll[$i]['position']?></td>
                        </tr>
                        <?php
                                        $c_date=$date;
                                }else{//テーブルに日付がなければスキップ
                                    continue;
                                }
                    ?>
                
                    <?php
                    }
                    }
                    ?>
                    </table>
                    </div>
                </div> 
                
                <!-- シフト希望元データ -->
                <div id="original">
                    <input type="button" value="管理者追加・変更・削除シフトリスト">
                    <div id="originalTable">
                        <p>黒：管理者追加入力&nbsp;&nbsp;
                            <span class="add">青：管理者時間変更</span>&nbsp;&nbsp;
                            <span class="delete">グレー：削除済み</span></p>
                        <table>
                            <tr>
                                <th>日付</th>
                                <th>サポート</th>
                                <th>時間</th>
                                <th>氏名</th>
                                <th id="tableSpan"></th>
                            </tr>
                    
                <?php
                try{
                    $sql="SELECT * FROM original_shift_date WHERE (add_flag=1 OR change_t!='NULL' OR delete_flag=1) AND date BETWEEN '".$first."' AND '".$last."' ORDER BY date,start_time";
                    $pre=$db->prepare($sql);
                    $pre->execute();
                    print("検索結果は".$pre->rowCount()."件です。<br>");
                }catch(PDOException $e){
                    print("SQL実行エラー：".$e->getMessage());
                    }
                    if($pre->rowCount()<1){
                        print("検索結果がありません<br>");
                    }else{
                       while($row=$pre->fetch()){
                           $position=$row['position'];
                           $date=$row['date'];
                           $year=mb_substr($date, 0, 4);//文字列の切り出し
                           $month=mb_substr($date, 5, 2);
                           $day=mb_substr($date, 8, 2);
                           
                           $start_t=$row['start_time'];
                           $end_t=$row['ending_time'];
                           $name=$row['name'];
                           $change_t=$row['change_t'];
                           $delete_r=$row['delete_r'];
                           
                           //曜日を取得
                           $tStamp = mktime(0, 0, 0, $month, $day, $year);
                           $w=date('w',$tStamp);
                           $week=['日','月','火','水','木','金','土'];
                           
                           $date=$year.'/'.$month.'/'.$day.'('.$week[$w].')';
                           
                           if(!empty($change_t)){//時間変更をしたシフト
                               ?>
                    <tr class='add'>
                        <td><?=$date?></td>
                        <td><?=$position?></td>
                        <td><?=$start_t?>～<?=$end_t?></td>
                        <td><?=$name?></td>
                        <td>変更後(<?=$change_t?>)</td>
                    </tr>        
                            <?php
                           }else if($delete_r!==''){//シフト提出後管理画面で削除したシフトの場合
                ?>
                    <tr class='delete'>
                        <td><?=$date?></td>
                        <td><?=$position?></td>
                        <td><?=$start_t?>～<?=$end_t?></td>
                        <td><?=$name?></td>
                        <td><?=$delete_r?></td>
                    </tr>
                <?php
                           }else if($row['add_flag']==1){//シフト提出後管理画面で追加したシフトの場合
                ?>
                    <tr>
                        <td><?=$date?></td>
                        <td><?=$position?></td>
                        <td><?=$start_t?>～<?=$end_t?></td>
                        <td><?=$name?></td>
                        <td></td>
                    </tr>
                
                <?php      
                           }else{ //管理画面で操作していないシフト
                ?>
                    <tr>
                        <td><?=$date?></td>
                        <td><?=$position?></td>
                        <td><?=$start_t?>～<?=$end_t?></td>
                        <td><?=$name?></td>
                        <td></td>
                    </tr>
                <?php   
                           }
                       }
                    }
                ?>
                
                        </table>
                    </div>
                </div>
                <?php
                $_SESSION['message']='';
            }else{
                print("シフトが登録されていません<br>");
            }
                ?>
                
                
            </article>
        </main>
        <script>
            /*シフト提出状況一覧*/
            $(function(){
                    $("#submission input").click(function(){
                       if($("#submissionTable").css("display")=="none"){
                           $("#submissionTable:not(:animated)").slideDown("fast");
                       }else{
                           $("#submissionTable").slideUp("fast");
                       };
                });
            });
            
            /*オリジナルシフト一覧*/
            $(function(){
                    $("#original input").click(function(){
                       if($("#originalTable").css("display")=="none"){
                           $("#originalTable:not(:animated)").css("display","block");
                       }else{
                           $("#originalTable:not(:animated)").css("display","none");
                       };
                });
            });
            
        </script>
    </body>
</html>