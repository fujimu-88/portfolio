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
        <title>スタッフ情報編集</title>
    </head>
    <body id="staff_all">
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
                <h2>スタッフ情報編集</h2>
        <?php
        require("db_connect.php");
        $db=connect();
        $id=$_POST['id'];
        $_SESSION['id']=$id;
        try{
            $sql="SELECT * FROM staff WHERE id=?";
            $pre=$db->prepare($sql);
            $pre->bindValue(1,$id,PDO::PARAM_INT);
            $pre->execute();
        }catch(PDOException $e){
            print("SQL実行エラー：".$e->getMessage());
        }
        if($pre->rowCount()<1){
            print("更新データがありません<br>");
        }else{
            $row=$pre->fetch();
            $position=$row['position'];
            if($position=='フロント'){
                $checked1='checked';
                $checked2='';
            }else if($position=='ジム'){
                $checked2='checked';
                $checked1='';
            }
        ?>
        <div id="staff_updateform">
        <form name="updateform2" method="post" action="staff_update.php">
            
            <table border="1">
                <tr>
                    <td class="non_col">社員番号
                    <div id="id_err"></div>
                    </td>
                    
                    <td><input type="hidden" name="id" value="<?=$row['id']?>" maxlength="15" size="10"><?=$row['id']?></td>
                </tr>
                <tr>
                    <td class="non_col">サポート
                    <div id="position_err"></div>
                    </td>
                    <td>
                        <input type="radio" name="position" value="フロント" <?=$checked1?>>フロント&nbsp;&nbsp;&nbsp;
                        <input type="radio" name="position" value="ジム" <?=$checked2?>>ジム
                    </td>
                </tr>
                <tr>
                    <td class="non_col">名前
                    <div id="name_err"></div>
                    </td>
                    <td><input type="text" name="staff_name" value="<?=htmlspecialchars($row['staff_name'])?>"></td>
                </tr>
                <tr>
                    <td class="non_col">ふりがな
                    <div id="name_y_err"></div>
                    </td>
                    <td><input type="text" name="staff_name_y" value="<?=htmlspecialchars($row['staff_name_y'])?>"></td>
                </tr>
            </table>
            <input type="submit" value="更新" onClick="return updateCheck()">
        </form>
        </div>
        <?php
        }
        ?>
        <br>
            </article>
        </main>
        <script>
            function updateCheck(){
                var position = document.updateform2.position.value;
                var name = document.updateform2.staff_name.value;
                var name_y = document.updateform2.staff_name_y.value;
                if(position==""||name==""||name_y==""){
                    if(position==""){
                        var p_err = document.getElementById("position_err");
                        n_err.innerText="名前を入力してください";
                    }else{
                        var p_err = document.getElementById("position_err");
                        n_err.innerText="";
                    }
                    if(name==""){
                        var n_err = document.getElementById("name_err");
                        n_err.innerText="名前を入力してください";
                    }else{
                        var n_err = document.getElementById("name_err");
                        n_err.innerText="";
                    }
                    if(name_y==""){
                        var ny_err = document.getElementById("name_y_err");
                        ny_err.innerText="ふりがなを入力してください";
                    }else{
                        var ny_err = document.getElementById("name_y_err");
                        ny_err.innerText="";
                    }
                    return false;
                }
                return true;
            }
        </script>
    </body>
</html>