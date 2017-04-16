<?php


function getMoneyFormat($money, $skipSymbol = false) {
    $sign = '';
    if ($money < 0) {
        $sign = '-';
        $money = str_replace('-', '', $money); 
    }
	$len = strlen($money);
    $m = '';
    $money = strrev($money);
    for($i=0;$i<$len;$i++){
        if(( $i==3 || ($i>3 && ($i-1)%2==0) )&& $i!=$len){
            $m .=',';
        }
        $m .=$money[$i];
    }
    if ($skipSymbol) {
        return $sign.strrev($m);
    } else {
        return "&#8377; ".$sign.strrev($m);
    }
}

function redirect() {
    $url = '/index.php?'.$_SERVER['QUERY_STRING'];
    header('Location: '.$url);
    exit();
}

?>