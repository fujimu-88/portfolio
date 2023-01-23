<?php
session_start();
require('db_connect.php');
$db=connect();
//ログインせずページに来た場合
$login_name = $_SESSION['staff_name'];
if($login_name==''){
    header("Location:./login.php");
};


//シフト登録がある最新月を取得
try{
    $sql="SELECT * FROM shift_date WHERE delete_flag=0 ORDER BY date desc";
    $pre=$db->prepare($sql);
    $pre->execute();
}catch(PDOException $e){
    print("SQLエラー：".$e->getMessage());
}
if($pre->rowCount()>0){
    while($row=$pre->fetch()){
        $date=$row['date'];
        //年と月を取り出す
        $y=date('Y', strtotime($date));
        $m=date('m', strtotime($date));
        $yearmonth[]=$y.'/'.$m;
    }
}

//最新月
if(isset($yearmonth[0])){
    $new_ym=$yearmonth[0];
};


?>
        
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title></title>
    </head>
    <body>
        <div id="mainmenu">
            <header>
                <h1>シフト管理表</h1>
                <form action="./logout.php" method="get" name="menu">
                <div id="logout">
                    <input type="submit" value="ログアウト" name="button"
                       onClick="this.form.buttonID.value='L999';" style="width: 100px">
                </div>
                </form>
                <div id="user">
                ログインユーザー：<?=htmlspecialchars($_SESSION['staff_name'],ENT_QUOTES)?>
                </div>
            </header>
              
            <ul>
                <li><a href="shift_list_all.php">シフト一覧</a></li>
                <li><a href="shift_edit.php">シフト調整</a></li>
                <li><a href="shift_one_registform.php">シフト登録</a></li>
                <li><a href="staff_list.php">スタッフ一覧</a></li>
                <li><a href="staff_registform.php">スタッフ登録</a></li>
                <li><a href="user_configuration.php">ユーザー設定</a></li>
            </ul>

        </div>

    </body>
</html>