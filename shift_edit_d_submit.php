<?php
require("db_connect.php");
session_start();
$db = connect();
//ログインせずページに来た場合
$login_name = $_SESSION['staff_name'];
if($login_name==''){
    header("Location:./login.php");
};


//POSTで取得
$edit=$_POST['edit'];

foreach($edit as $vals){
    $name=$vals['name'];
    $position=$vals['position'];
    $y_m_d=$vals['date'];
    $_SESSION['y_m_d']=$y_m_d;
  
    $start_h=$vals['start_h'];
    $start_m=$vals['start_m'];
    $start_t=$start_h.':'.$start_m;

    $end_h=$vals['end_h'];
    $end_m=$vals['end_m'];
    $end_t=$end_h.':'.$end_m;

    try{
        $sql="SELECT * FROM edit_shift_date 
        WHERE date='".$y_m_d."' AND name='".$name."' AND position='".$position."' AND start_time='".$start_t."' AND ending_time='".$end_t."'";
        $pre=$db->prepare($sql);
        $pre->execute();
    }catch(PDOException $e){
        print("SQLエラー：".$e->getMessage());
    }
    if($pre->rowCount()==0){
        if($start_t=='0:00' || $end_t=='0:00' || $start_t=='0:30' || $end_t=='0:30'){
            try{
                $sql="UPDATE edit_shift_date SET delete_flag=1, start_time='0:00', ending_time='0:00'
                WHERE date=? AND name=? AND position=?";
                $pre=$db->prepare($sql);
                $pre->bindValue(1,$y_m_d,PDO::PARAM_STR);
                $pre->bindValue(2,$name,PDO::PARAM_STR);
                $pre->bindValue(3,$position,PDO::PARAM_STR);
                $pre->execute();
            }catch(PDOException $e){
                print('SQLエラー：'.$e->getMessage());
            }
        }else{
            try{
                $sql="UPDATE edit_shift_date SET start_time=?, ending_time=? ,delete_flag=0
                WHERE date=? AND name=? AND position=?";
                $pre=$db->prepare($sql);
                $pre->bindValue(1,$start_t,PDO::PARAM_STR);
                $pre->bindValue(2,$end_t,PDO::PARAM_STR);
                $pre->bindValue(3,$y_m_d,PDO::PARAM_STR);
                $pre->bindValue(4,$name,PDO::PARAM_STR);
                $pre->bindValue(5,$position,PDO::PARAM_STR);
                $pre->execute();
            }catch(PDOException $e){
                print('SQLエラー：'.$e->getMessage());
            }
            if(($pre->rowCount()) == 0){
                try{
                    $sql="INSERT INTO edit_shift_date SET date=?, 
                    name=?,position=?, 
                    start_time=?, ending_time=?";
                    $pre=$db->prepare($sql);
                    $pre->bindValue(1,$y_m_d,PDO::PARAM_STR);
                    $pre->bindValue(2,$name,PDO::PARAM_STR);
                    $pre->bindValue(3,$position,PDO::PARAM_STR);
                    $pre->bindValue(4,$start_t,PDO::PARAM_STR);
                    $pre->bindValue(5,$end_t,PDO::PARAM_STR);
                    $pre->execute();
                }catch(PDOException $e){
                    print('SQLエラー：'.$e->getMessage());
                }
            }
        }
    }
};

header("Location:./shift_edit_d.php");
?>
