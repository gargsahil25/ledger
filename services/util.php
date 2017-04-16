<?php


function getMoneyFormat($money) {
	$len = strlen($money);
    $m = '';
    $money = strrev($money);
    for($i=0;$i<$len;$i++){
        if(( $i==3 || ($i>3 && ($i-1)%2==0) )&& $i!=$len){
            $m .=',';
        }
        $m .=$money[$i];
    }
    return "&#8377; ".strrev($m);
}

function redirect() {
    $url = '/index.php?'.$_SERVER['QUERY_STRING'];
    header('Location: '.$url);
    exit();
}

?>