<?php
require("db_connect.php");
session_start();
//ログインせずページに来た場合
$login_name = $_SESSION['staff_name'];
if($login_name==''){
    header("Location:./login.php");
};

$_SESSION["result"]='';
    try{
$db=connect();
    $db->beginTransaction();
    $sql="SELECT * FROM manager WHERE id=? AND password=?";
    $pre=$db->prepare($sql);
    $pre->bindValue(1,htmlspecialchars($_POST['id'],ENT_QUOTES),PDO::PARAM_STR);
    $pre->bindValue(2,htmlspecialchars($_POST['password'],ENT_QUOTES),PDO::PARAM_STR);
    $pre->execute();
}catch(PDOException $e){
    print("SQLエラー：".$e->getMessage());
}
if($pre->rowCount()<1){
    $_SESSION["result"]="ログインIDかパスワードが違います";
    
    header("Location:./user_passwordform.php");
}else if($_POST['password']==($_POST['new_password'] == $_POST['nwe_password2'])){
    $_SESSION["result"]="現在のパスワードと新しいパスワードが同じです";
    header("Location:./user_passwordform.php");
}else{
    $_SESSION["result"]="";
    try{
        $sql2="UPDATE manager SET password=?";
        $pre2=$db->prepare($sql2);
        $pre2->bindValue(1,$_POST['new_password'],PDO::PARAM_STR);
        $pre2->execute();
        $_SESSION["result"]="パスワードを変更しました";
        $db->commit();
        header("Location:./user_passwordform.php");
    }catch(PDOException $e){
        $db->rollback();
        print("SQLエラー2：".$e->getMessage());
    }
}
                