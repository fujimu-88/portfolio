<?php
require("db_connect.php");
require_once('php/function.php');
session_start();
$db=connect();
//ログインせずページに来た場合
$login_name = $_SESSION['staff_name'];
if($login_name==''){
    header("Location:./login.php");
};
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title>スタッフ登録</title>
    </head>
    <body id="staff_registform">
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
        <nav>
                <ul class="globalNavi">
                    <li><a href="mainmenu.php">メインメニュー</a></li>
                    <li><a href="shift_list_all.php">シフト一覧</a></li>
                    <li><a href="shift_edit.php">シフト調整</a></li>
                    <li><a href="shift_one_registform.php">シフト登録</a></li>
                    <li><a href="staff_list.php">スタッフ一覧</a></li>
                    <li><a href="staff_registform.php">スタッフ登録</a></li>
                    <li><a href="user_configuration.php">ユーザー設定</a></li>
                </ul>
            </nav>
        <main>
            <article>
                <h2>スタッフ登録</h2>
        <div id="form">
        <form method="post" action="./staff_regist.php" name="staff_registform">
            <table border="1">
                <tr>
                    <td class="non_col">社員番号<div id="id_err"></div></td>
                    <td><input type="text" name="id" maxlength="20" size="20"></td>
                </tr>
                <tr>
                    <td class="non_col">サポート<div id="position_err"></div></td>
                    <td>
                        <input type="radio" name="position" value="フロント">フロント&nbsp;&nbsp;&nbsp;
                        <input type="radio" name="position" value="ジム">ジム
                    </td>
                </tr>
                <tr>
                    <td class="non_col">名前<div id="name_err"></div></td>
                    <td><input type="text" name="name" maxlength="20" size="20"></td>
                </tr>
                <tr>
                    <td class="non_col">ふりがな<div id="name_y_err"></div></td>
                    <td><input type="text" name="name_y" maxlength="20" size="20"></td>
                </tr>
                <tr>
                    <td class="non_col">パスワード<span class="w">※英数字含む８桁以上</span>
                        <div id="pass_err"></div></td>
                    <td><input type="password" name="password" maxlength="20" size="20"></td>
                </tr>
                <tr>
                    <td class="non_col">パスワード（確認用）<div id="pass2_err"></div></td>
                    <td><input type="password" name="password2" maxlength="20" size="20"></td>
                </tr>
            </table>
            <input type="submit" value="登録" onClick="return staffRegistCheck()">
            <input type="button" value="クリア" onClick="location.href='staff_registform.php'">
        </form>
        </div>
        <div id="passcheck_err"></div>

        <?php
        if(!empty($_SESSION['err_msg2'])){
            $err_msg2 =$_SESSION['err_msg2'];
        ?>
            <div><?=$err_msg2."<br>"?></div>
        <?php
        $err_msg2="";
        };
        ?>

        <br>
            </article>
        </main>
        
        <script>
            function staffRegistCheck(){
                var id        =document.staff_registform.id.value;
                var position  =document.staff_registform.position.value;
                var name      =document.staff_registform.name.value;
                var name_y    =document.staff_registform.name_y.value;
                var password  =document.staff_registform.password.value;
                var password2 =document.staff_registform.password2.value;
                
                var pass_sE = document.getElementById('passcheck_err');
                pass_sE.innerText="";
                
                if(id=="" || position=="" || name=="" ||  name_y=="" || password=="" || password2==""){
                    if(id==""){
                        var idE = document.getElementById('id_err');
                        idE.innerText="※社員番号を入力してください";
                    }else{
                        var idE = document.getElementById('id_err');
                        idE.innerText="";
                    }
                    if(position==""){
                        var pE = document.getElementById('position_err');
                        pE.innerText="※どちらか選択してください";
                    }else{
                        var pE = document.getElementById('position_err');
                        pE.innerText="";
                    }
                    if(name==""){
                        var nameE = document.getElementById('name_err');
                        nameE.innerText="※名前を入力してください";
                    }else{
                        var nameE = document.getElementById('name_err');
                        nameE.innerText="";
                    }
                    if(name_y==""){
                        var name_yE = document.getElementById('name_y_err');
                        name_yE.innerText="※ふりがなを入力してください";
                    }else{
                        var name_yE = document.getElementById('name_y_err');
                        name_yE.innerText="";
                    }
                    if(password==""){
                        var passE = document.getElementById('pass_err');
                        passE.innerText="※パスワードを入力してください";
                    }else{
                        var passE = document.getElementById('pass_err');
                        passE.innerText="";  
                    }
                    if(password2==""){
                        var pass_sE = document.getElementById('pass2_err');
                        pass_sE.innerText="※パスワード確認用を入力してください";
                    }else{
                        var pass_sE = document.getElementById('pass2_err');
                        pass_sE.innerText="";
                    }
                    return false;
                }
                var idE = document.getElementById('id_err');
                idE.innerText="";
                var pE = document.getElementById('position_err');
                pE.innerText="";
                var nameE = document.getElementById('name_err');
                nameE.innerText="";
                var name_yE = document.getElementById('name_y_err');
                name_yE.innerText="";
                var passE = document.getElementById('pass_err');
                passE.innerText="";
                var pass_sE = document.getElementById('pass2_err');
                pass_sE.innerText="";
                
                if(password.length<8){
                    var passE = document.getElementById('pass_err');
                    passE.innerText="※パスワードは英数字8桁以上で入力してください";
                    return false;
                }
                var passE = document.getElementById('pass_err');
                passE.innerText="";
                if(password.length>=8){
                    if(password.match(/^(?=.*?[a-z])(?=.*?\d)[a-z\d]{8,15}$/i)){
                        var passE = document.getElementById('pass_err');
                        passE.innerText="";
                    }else{
                        var passE = document.getElementById('pass_err');
                        passE.innerText="※パスワードは英数字を含めて入力してください";
                        return false;
                    }
                }
                if(password!=password2){
                    var pass_sE = document.getElementById('passcheck_err');
                    pass_sE.innerText="※パスワードとパスワード（確認用）の内容が違います";
                    var passE = document.getElementById('pass_err');
                    passE.innerText="";
                    return false; 
                }
                return true;
            }
        </script>
    </body>
</html>