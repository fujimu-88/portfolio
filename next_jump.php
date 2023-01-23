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


$nextmonth= date("Ym",strtotime($year.$month."01"." +1 month "));
 $_SESSION['year']=mb_substr($nextmonth, 0, 4);//文字列の切り出し
 $_SESSION['month']=mb_substr($nextmonth, 4, 2);

header("Location:./setting_d_lineform.php");
?>
