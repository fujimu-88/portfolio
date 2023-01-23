<?php
session_start();

//ログインせずページに来た場合
$login_name = $_SESSION['staff_name'];
if($login_name==''){
    header("Location:./login.php");
};


?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title>シフト更新画面</title>
    </head>
    <body id="user_config">
        <header>
            <h1>シフト管理表</h1>
            <form action="./logout.php" method="get" name="menu">
            <div id="logout">
                <input type="submit" value="ログアウト" name="button"
                   onClick="this.form.buttonID.value='L999';" style="width: 100px">
            </div>
            </form>
            <div id="user">
            ログインユーザー：<?=htmlspecialchars($_SESSION['staff_name'],ENT_QUOTES) ?>
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
        <h2>パスワード変更</h2>
                <div id="form">
                    <form method="post" action="user_password.php" name="change_password">
                        <table>
                            <tr>
                                <td>ログインID：<br><span id="id_err"></span></td>
                                <td ><input type="text" name="id" maxlength="20" size="20"></td>
                            </tr>
                            <tr>
                                <td>現在のパスワード：<br><span id="pass_err"></span></td>
                                <td ><input type="password" name="password" maxlength="20" size="20"></td>
                            </tr>
                            <tr class="attention">
                                <td colspan="2">新しいパスワードは英数字含む８桁以上で入力してください</td>
                            </tr>
                            <tr>
                                <td>新しいパスワード：<br><span id="new_pass_err"></span></td>
                                <td><input type="password" name="new_password" maxlength="20" size="20"></td>
                            </tr>
                            <tr>
                                <td>新しいパスワード（確認用）：<br><span id="new_pass2_err"></span></td>
                                <td><input type="password" name="new_password2" maxlength="20" size="20"></td>
                            </tr>
                        </table>
                        <input type="submit" value="変更" onClick="return passCheck()">
                        <input type="reset" value="クリア">
                    </form>
                </div>
<?php
if(!empty($_SESSION["result"])){
    $result = $_SESSION["result"];

    ?>
<div><?=$result?></div>

    <?php
    $result='';
};
?>

            </article>
        </main>
        <script>
            function passCheck(){
                var id =document.change_password.id.value;
                var password  =document.change_password.password.value;
                var new_password  =document.change_password.new_password.value;
                var new_password2 =document.change_password.new_password2.value;
                
                if( id=="" || password=="" || new_password=="" || new_password2==""){
                    if(id==""){
                        var idE = document.getElementById('id_err');
                        idE.innerText="※ログインIDを入力してください";
                    }else{
                        var idE = document.getElementById('id_err');
                        idE.innerText="";  
                    }
                    if(password==""){
                        var passwordE = document.getElementById('pass_err');
                        passwordE.innerText="※パスワードを入力してください";
                    }else{
                        var passwordE = document.getElementById('pass_err');
                        passwordE.innerText="";  
                    }
                    if(new_password==""){
                        var passE = document.getElementById('new_pass_err');
                        passE.innerText="※パスワードを入力してください";
                    }else{
                        var passE = document.getElementById('new_pass_err');
                        passE.innerText="";  
                    }
                    if(new_password2==""){
                        var pass_sE = document.getElementById('new_pass2_err');
                        pass_sE.innerText="※パスワード確認用を入力してください";
                    }else{
                        var pass_sE = document.getElementById('new_pass2_err');
                        pass_sE.innerText="";
                    }
                    return false;
                }
                
                if(new_password === new_password2){
                    var passE = document.getElementById('new_pass_err');
                        passE.innerText="";
                        }else{
                        var passE = document.getElementById('new_pass_err');
                        passE.innerText="※新しいパスワードと新しパスワード（確認用）が違います";
                        var pass_sE = document.getElementById('new_pass2_err');
                        pass_sE.innerText="";
                            return false;
                        }
                if(password !== new_password){
                    var passE = document.getElementById('new_pass_err');
                        passE.innerText="";
                        }else{
                        var passE = document.getElementById('new_pass_err');
                        passE.innerText="※現在のパスワードと新しいパスワードが同じです";
                        var pass_sE = document.getElementById('new_pass2_err');
                        pass_sE.innerText="";
                            return false;
                }
                if(new_password.length<=8 || new_password2.length<=8){
                    
                    if(new_password.length>=8){
                        if(new_password.match(/^(?=.*?[a-z])(?=.*?\d)[a-z\d]{8,15}$/i)){
                        var passE = document.getElementById('new_pass_err');
                        passE.innerText="";
                        }else{
                        var passE = document.getElementById('new_pass_err');
                        passE.innerText="※パスワードは英数字8桁以上で入力してください";
                        }
                    }
                    if(new_password2.length>=8){
                        if(new_password2.match(/^(?=.*?[a-z])(?=.*?\d)[a-z\d]{8,15}$/i)){
                            var pass_sE = document.getElementById('new_pass_err');
                        pass_sE.innerText="";
                    }else{
                        var pass_sE = document.getElementById('new_pass_err');
                        pass_sE.innerText="※パスワードは英数字8桁以上で入力してください";
                        var pass_sE = document.getElementById('new_pass2_err');
                        pass_sE.innerText="";
                    }
                    }
                    return false; 
                }
                return true;
            }
        </script>
        
    </body>
</html>