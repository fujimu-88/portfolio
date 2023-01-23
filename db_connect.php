<?php
function connect(){
    $user='root';
    $pass='';

    $dsn='mysql:host=localhost; dbname=shift; charset=utf8';

    try{
        $pdo=new PDO($dsn,$user,$pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
    }catch(PDOException $e){
        die("データベースに接続エラー：".$e->getMessage());
    }
    return $pdo;
}
?>
