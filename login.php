<?php
require('db_connect.php');
session_start();
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title>ログイン画面</title>
    </head>
    <body>
        <div id="header">
            <h1>シフト管理表</h1>
        </div>
        <div id="login" align="center">
        <h2>ログイン画面</h2><br>
        <form action="" method="POST" name="loginform">
            <table>
                <tr><td>ログインID
                    <div id="id_err"></div></td></tr>
                <td><input type="text" name="login_id" size="25" maxlength="40"></td>
                <tr><td>パスワード
                    <div id="pass_err"></div></td></tr>
                <tr><td><input type="password" name="password" size="25" maxlength="40"></td></tr>
            </table>

            <input type="submit" value="ログイン" onClick="return loginCheck()">
        </form>
        <?php

            if(!empty($_POST['login_id']) && !empty($_POST['password'])){
                $db=connect();
                $login_id=$_POST['login_id'];
                $password=$_POST['password'];
                try{
                    $sql="SELECT staff_name FROM manager WHERE id=? AND password=?";
                    $pre=$db->prepare($sql);
                    $pre->bindValue(1,$login_id,PDO::PARAM_INT);
                    $pre->bindValue(2,$password,PDO::PARAM_STR);
                    $pre->execute();
                }catch(PDOException $e){
                    print("SQLエラー:".$e->getMessage());
                }
                if($pre->rowCount()<1){
                    $_SESSION['err_msg']="ログインIDまたはパスワードが違います";
                    $page="./login.php";
                }else{
                    $row=$pre->fetch();
                    $_SESSION['staff_name']=$row['staff_name'];
                    $page="./mainmenu.php";
                    header("Location:".$page);
                }

            }else{
                $_SESSION['err_msg']="";
            }
       ?>

        <div id="message">
                <?=htmlspecialchars($_SESSION['err_msg'],ENT_QUOTES)?>
        </div>
        </div>
        <script>
            function loginCheck(){
                var login_id=document.loginform.login_id.value;
                var password=document.loginform.password.value;
                if(login_id=="" || password==""){
                    if(login_id==""){
                        var idE = document.getElementById('id_err');
                        idE.innerText="※ログインIDを入力してください";
                    }else{
                        var idE = document.getElementById('id_err');
                        idE.innerText="";
                    }
                    if(password==""){
                        var passE = document.getElementById('pass_err');
                        passE.innerText="※パスワードを入力してください";
                    }else{
                        var passE = document.getElementById('pass_err');
                        passE.innerText="";
                    }
                    return false;
                }
                return true;
            }
        </script>
    </body>
</html>
