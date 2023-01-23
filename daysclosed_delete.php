<?php
session_start();
require("db_connect.php");
$db = connect();

$ym=$_SESSION['ym'];
$days=$_POST['deletedays'];
$year=mb_substr($days, 0, 4);//文字列の切り出し
$month=mb_substr($days, 5, 2);
$day=mb_substr($days, 8, 2);
$days=$year.'/'.$month.'/'.$day;
//休館日を削除
try{
    $sql="UPDATE days_closed SET delete_flag=1 WHERE days_closed=?";
    $pre=$db->prepare($sql);
    $pre->bindValue(1,$days,PDO::PARAM_INT);
    $pre->execute();
    
    $_SESSION['closed_message'] = $days."の予定を削除しました";
    header("Location:./daysclosedform.php?ym=$ym");
}catch(PDOException $e){
    print("SQLエラー3：".$e->getMessage());
}

?>