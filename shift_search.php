<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>キーワード検索</title>
    </head>
    <body>
        <h1>検索画面</h1>
        <form name="search" method="POST" action="shift_search_list.php">
            検索キーワード:<input type="text" name="search_key"><br>
            <input type="submit" value="検索">
            <br><br>
            検索方法<br>
            日付検索　　2019年1月1日の場合 【2019-01-01】と入力<br>
            名前検索　　名前の一部で検索可能<br>
        </form>
        <br>[<a href="mainmenu.php">メインメニューに戻る</a>]<br>
    </body>
</html>