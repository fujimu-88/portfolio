<?php

//$dayと$monthを2桁にする
function two_digit($num){

    if(preg_match('/^([0-9]{1})$/', $num)){
        $num2 = '0'. $num;
    }else{
        $num2 = $num;
    };

    return $num2;
}

//カレンダーテーブル曜日表示
function weeklist_japanese(){
    $weeklist = array("日","月","火","水","木","金","土");
    foreach( $weeklist as $week){

    echo "<th>$week</th>";

    }
}

?>