<?php
require("db_connect.php");
session_start();
$db = connect();
//ログインせずページに来た場合
$login_name = $_SESSION['staff_name'];
if($login_name==''){
    header("Location:./login.php");
};

//SESSIONでsetting_d_line.phpから送った年月を取得
$year=$_SESSION['year'];
$month=$_SESSION['month'];
$ym=$year.'/'.$month;
$date=$_POST['date'];

$_SESSION['message']='';


try{
    $sql="SELECT * FROM shift_deadline WHERE deadline_month=?";
    $pre=$db->prepare($sql);
    $pre->bindValue(1,$ym,PDO::PARAM_STR);
    $pre->execute();
}catch(PDOException $e){
    print("SQLエラー：".$e->getMessage());
};

if($pre->rowCount()==0){//締め切りたい月の締め切り日を設定していない場合
    try{
        $sql="INSERT INTO shift_deadline(deadline_month, deadline_date, delete_flag) VALUES (?,?,0)";
        $pre=$db->prepare($sql);
        $pre->bindValue(1,$ym,PDO::PARAM_STR);
        $pre->bindValue(2,$date,PDO::PARAM_STR);
        $pre->execute();
    }catch(PDOException $e){
        print("sqlエラー:".$e->getMessage());
    };
    $_SESSION['message']=$year."年".$month."月の締め切り日".$date."に設定しました";
    header("Location:./setting_d_lineform.php");
}else{//締め切りたい月の締め切り日を設定している場合
    while($row=$pre->fetch()){
        $old_date = $row['deadline_date'];
        $delete_flag=$row['delete_flag'];
    }
    if($delete_flag==1){
        try{
            $sql="UPDATE shift_deadline SET deadline_date=?,delete_flag=0 WHERE deadline_month=?";
            $pre=$db->prepare($sql);
            $pre->bindValue(1,$date,PDO::PARAM_STR);
            $pre->bindValue(2,$ym,PDO::PARAM_STR);
            $pre->execute();
        }catch(PDOException $e){
            print("sqlエラー:".$e->getMessage());
        };
        $_SESSION['message']=$year."年".$month."月の締め切り日".$date."に設定しました";
        header("Location:./setting_d_lineform.php");
    }else if($delete_flag==0 && $date==$old_date){//締め切り日が同じ日に設定されているなら
        $_SESSION['message']="同じ日に".$year."年".$month."月の締め切り日が設定されています";
       header("Location:./setting_d_lineform.php");
    }else if($delete_flag==0 && $date!=$old_date){
        try{
            $sql="UPDATE shift_deadline SET deadline_date=? WHERE deadline_month=?";
            $pre=$db->prepare($sql);
            $pre->bindValue(1,$date,PDO::PARAM_STR);
            $pre->bindValue(2,$ym,PDO::PARAM_STR);
            $pre->execute();
        }catch(PDOException $e){
            print("sqlエラー:".$e->getMessage());
        };
        $_SESSION['message']=$year."年".$month."月の締め切り日".$date."に変更しました";
        header("Location:./setting_d_lineform.php");
    }
    print($old_date);
    print($date);
    
}
?>
