<?php
require("db_connect.php");
session_start();
$db = connect();
//ログインせずページに来た場合
$login_name = $_SESSION['staff_name'];
if($login_name==''){
    header("Location:./login.php");
};

//検索月をSESSIONでshift_edit.phpに渡す
$_SESSION['ym']=$_POST['ym'];

header("Location:./shift_edit.php");
?>