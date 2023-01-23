<?php
session_start();
require("db_connect.php");
$db = connect();

$ym=$_SESSION['ym'];
$days = $_POST['date'];
$year=mb_substr($days, 0, 4);//文字列の切り出し
$month=mb_substr($days, 5, 2);
$day=mb_substr($days, 8, 2);
$days=$year.'/'.$month.'/'.$day;

//休館日を設定しているか確認
try{
    $sql="SELECT * FROM days_closed WHERE days_closed=? AND delete_flag=0";
    $pre=$db->prepare($sql);
    $pre->bindValue(1,$_POST['date'],PDO::PARAM_STR);
    $pre->execute();
}catch(PDOException $e){
    print("SQLエラー：".$e->getMessage());
}
if($pre->rowCount()>0){
    
    $_SESSION['closed_message'] = $days."に予定はすでに設定されています";
    header("Location:./daysclosedform.php?ym=$ym");
}else{
    //休館日を登録
    try{
        $sql="INSERT INTO days_closed SET
        days_closed=?,
        d_name=?";
        $pre=$db->prepare($sql);
        $pre->bindValue(1,$_POST['date'],PDO::PARAM_STR);
        $pre->bindValue(2,$_POST['d_name'],PDO::PARAM_STR);
        $pre->execute();
    }catch(PDOException $e){
        print("SQLエラー2：".$e->getMessage());
    }
    $_SESSION['closed_message'] = $days."に".$_POST['d_name']."を設定しました";
    header("Location:./daysclosedform.php?ym=$ym");
};

?>