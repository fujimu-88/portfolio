<?php
require("db_connect.php");
session_start();
$db = connect();
//ログインせずページに来た場合
$login_name = $_SESSION['staff_name'];
if($login_name==''){
    header("Location:./login.php");
};

//POSTで更新するシフト月を取得
$ym=$_POST['ym']; 
$first=$ym.'/01';
$last=$ym.'/31';

/*公開する編集中シフトがなければshift_edit.php
に戻るため初めに編集中シフトを取得*/

//公開する編集中シフト取得
try{
    $sql="SELECT * FROM edit_shift_date WHERE date BETWEEN '".$first."' AND '".$last."'ORDER by date";
    $pre=$db->prepare($sql);
    $pre->execute();
}catch(PDOException $e){
    print("SQLエラー：".$e->getMessage());
}
//編集シフトを配列にする
while($row=$pre->fetch()){
    $id=$row['id'];
    $date=$row['date'];
    $name=$row['name'];
    $position=$row['position'];
    $start_t=$row['start_time'];
    $end_t=$row['ending_time'];
    $flag=$row['delete_flag'];
    $edit[$id]=['date'=>$date,'name'=>$name,'position'=>$position,'start_t'=>$start_t,'end_t'=>$end_t,'flag'=>$flag];
}
print_r($edit);

if($pre->rowCount()==0){//shiftデータベースのedit_shift_dateに該当月シフトがなければ
    $_SESSION['err']="調整用シフトが登録されていないため公開できません";
    header("Location:./shift_edit.php");

}else{//shiftデータベースのedit_shift_dateに該当月シフトがあれば
    //shiftデータベース内のrelease_dateを公開日・最終更新日を更新
    try{//主キーを作成してないので重複チェックはshift_dateで
        $sql="SELECT * FROM release_date WHERE shift_date=?";
        $pre=$db->prepare($sql);
        $pre->bindValue(1,$ym,PDO::PARAM_STR);
        $pre->execute();
    }catch(PDOException $e){
        print("SQLエラー：".$e->getMessage());
    }
    if($pre->rowCount()!=0){
        while($row=$pre->fetch()){
            $release_check=$row['shift_date'];
        }
    }
    $date=date("Y/m/d");
    $date2=$date;

    if(empty($release_check)){//初めての公開するシフト月の場合、公開日・最終更新日を挿入
        try{
            $sql="INSERT INTO release_date(shift_date,release_date,update_date)VALUES(?,?,?)";
            $pre=$db->prepare($sql);
            $pre->bindValue(1,$ym,PDO::PARAM_STR);
            $pre->bindValue(2,$date,PDO::PARAM_STR);
            $pre->bindValue(3,$date2,PDO::PARAM_STR);
            $pre->execute();
        }catch(PDOException $e){
            print('SQLエラー1：'.$e->getMessage());
        }; 
    }else{//公開内容を更新する場合、最終更新日のみ更新
        try{
            $sql="UPDATE release_date SET update_date=? WHERE shift_date=?";
            $pre=$db->prepare($sql);
            $pre->bindValue(1,$date2,PDO::PARAM_STR);
            $pre->bindValue(2,$ym,PDO::PARAM_STR);
            $pre->execute();
        }catch(PDOException $e){
            print('SQLエラー2：'.$e->getMessage());
        }
    }
}



//公開するシフトをデータベース内のrelease_shitに挿入

//公開シフトに存在しているかチェック
try{
    $sql="SELECT * FROM release_shift WHERE date BETWEEN '".$first."' AND '".$last."'";
    $pre=$db->prepare($sql);
    $pre->execute();
}catch(PDOException $e){
    print("SQLエラー10:".$e->getMessage());
}
if($pre->rowCount()==0){//公開シフト存在していない場合
    foreach($edit as $id=>$set){//編集シフト配列(delte_flag=0も1も全て含む)全て挿入
        $date=$set['date'];
        $name=$set['name'];
        $position=$set['position'];
        $start_t=$set['start_t'];
        $end_t=$set['end_t'];
        $flag=$set['flag'];
        try{
            $sql="INSERT INTO release_shift(id,name,position,date,start_time,ending_time,delete_flag) VALUES (?,?,?,?,?,?,?)";
            $pre=$db->prepare($sql);
            $pre->bindValue(1,$id,PDO::PARAM_STR);
            $pre->bindValue(2,$name,PDO::PARAM_STR);
            $pre->bindValue(3,$position,PDO::PARAM_STR);
            $pre->bindValue(4,$date,PDO::PARAM_STR);
            $pre->bindValue(5,$start_t,PDO::PARAM_STR);
            $pre->bindValue(6,$end_t,PDO::PARAM_STR);
            $pre->bindValue(7,$flag,PDO::PARAM_STR);
            $pre->execute();
        }catch(PDOException $e){
            print("SQLエラー20:".$e->getMessage());
        }
    }
}else{//公開シフト存在している場合

    while($row=$pre->fetch()){//公開シフトを配列にする
        $id=$row['id'];
        $date=$row['date'];
        $name=$row['name'];
        $position=$row['position'];
        $start_t=$row['start_time'];
        $end_t=$row['ending_time'];
        $flag=$row['delete_flag'];

        $release[$id]=['date'=>$date,'name'=>$name,'position'=>$position,'start'=>$start_t,'end'=>$end_t,'flag'=>$flag];
    }
    foreach($edit as $edit_id=>$set){//編集シフト配列(すべてdelte_flag=0も1も全て含む)
        $edit_date=$set['date'];
        $edit_name=$set['name'];
        $edit_position=$set['position'];
        $edit_start=$set['start_t'];
        $edit_end=$set['end_t'];
        $edit_flag=$set['flag'];

        print_r($edit_id);
        //公開シフト配列に存在しているかチェック
        if(empty($release[$edit_id])){
            //編集シフト有　公開シフト無　の場合
            try{
                $sql="INSERT INTO release_shift(id,name,position,date,start_time,ending_time,delete_flag) VALUES (?,?,?,?,?,?,?)";
                $pre=$db->prepare($sql);
                $pre->bindValue(1,$edit_id,PDO::PARAM_STR);
                $pre->bindValue(2,$edit_name,PDO::PARAM_STR);
                $pre->bindValue(3,$edit_position,PDO::PARAM_STR);
                $pre->bindValue(4,$edit_date,PDO::PARAM_STR);
                $pre->bindValue(5,$edit_start,PDO::PARAM_STR);
                $pre->bindValue(6,$edit_end,PDO::PARAM_STR);
                $pre->bindValue(7,$edit_flag,PDO::PARAM_STR);
                $pre->execute();
            }catch(PDOException $e){
                print("SQLエラー22:".$e->getMessage());
            }

        }else if(!empty($release[$edit_id])){
            //編集シフト==公開シフト　の場合
            //skip
            //公開・編集シフト間で開始時間・終了時間・削除フラグに変更がある場合
            if(($edit_start != $release[$edit_id]['start']) || ($edit_end != $release[$edit_id]['end']) ||($edit_flag != $release[$edit_id]['flag'])){
                try{
                    $sql="UPDATE release_shift SET start_time=?,ending_time=?,delete_flag=? WHERE date=? AND name=? AND position=?";
                    $pre=$db->prepare($sql);
                    $pre->bindValue(1,$edit_start,PDO::PARAM_STR);
                    $pre->bindValue(2,$edit_end,PDO::PARAM_STR);
                    $pre->bindValue(3,$edit_flag,PDO::PARAM_STR);
                    $pre->bindValue(4,$edit_date,PDO::PARAM_STR);
                    $pre->bindValue(5,$edit_name,PDO::PARAM_STR);
                    $pre->bindValue(6,$edit_position,PDO::PARAM_STR);
                    $pre->execute();
                }catch(PDOException $e){
                    print("SQLエラー21:".$e->getMessage());
                }
            }    
        }
        
    }
}

header("Location:./shift_edit.php");
?>
