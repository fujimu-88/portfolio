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

$year=$_SESSION['year'];
$month=$_SESSION['month'];
$ym=$year.'/'.$month;

$_SESSION['message']='';
try{
    $sql="UPDATE shift_deadline SET delete_flag=1 WHERE deadline_month=?";
    $pre=$db->prepare($sql);
    $pre->bindValue(1,$ym,PDO::PARAM_STR);
    $pre->execute();
}catch(PDOException $e){
    print("sqlエラー:".$e->getMessage());
};
$_SESSION['message']=$year."年".$month."月の締切日を削除しました";
header("Location:./setting_d_lineform.php");
?>