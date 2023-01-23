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
    $sql="SELECT * FROM shift_date WHERE shift_id=? AND name=? AND date=?";
    $pre=$db->prepare($sql);
    $pre->bindValue(1,$_POST['shift_id'],PDO::PARAM_STR);
    $pre->bindValue(2,$_POST['name'],PDO::PARAM_STR);
    $pre->bindValue(3,$_POST['date'],PDO::PARAM_STR);
    $pre->execute();
}catch(PDOException $e){
    print("削除エラー：" . $e->getMessage());
}
if($pre->rowCount()==0){
    $_SESSION['message']="削除するデータがありません<br>";
    header("Location:./shift_list_all.php");
}else{
    try{
        $db->beginTransaction();
        $sql1="UPDATE shift_date SET delete_flag=1 WHERE shift_id=? AND name=? AND date=?";
        $pre1=$db->prepare($sql1);
        $pre1->bindValue(1,$_POST['shift_id'],PDO::PARAM_STR);
        $pre1->bindValue(2,$_POST['name'],PDO::PARAM_STR);
        $pre1->bindValue(3,$_POST['date'],PDO::PARAM_STR);
        $pre1->execute();
        $db->commit();
    }catch(PDOException $e){
        $db->rollBack();
        print("削除エラー：" . $e->getMessage());
    }
    try{
        $db->beginTransaction();
        $sql2="UPDATE original_shift_date SET delete_flag=1, delete_r=? WHERE name=? AND date=? AND delete_flag=0";
        $pre2=$db->prepare($sql2);
        $pre2->bindValue(1,$_POST['shift_d_r'],PDO::PARAM_STR);
        $pre2->bindValue(2,$_POST['name'],PDO::PARAM_STR);
        $pre2->bindValue(3,$_POST['date'],PDO::PARAM_STR);
        $pre2->execute();
        $db->commit();
        $_SESSION['message']="データを".$pre1->rowCount()."件、削除しました。<br>";
        header("Location:./shift_list_all.php");
    }catch(PDOException $e){
        $db->rollBack();
        print("削除エラー：" . $e->getMessage());
    }
}



                    
        