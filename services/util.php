<?php

include_once "constant.php";

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

function redirect($url = false) {
    if (!$url) {
        $url = '/index.php?'.$_SERVER['QUERY_STRING'];
    }
    header('Location: '.$url);
    exit();
}

function getLangText($key) {
    global $LANG_MAP;
    $lang = 0;
    if (isset($_COOKIE['hindi']) && $_COOKIE['hindi'] == 'true') {
        $lang = 1;
    }
    if (isset($LANG_MAP[$key])) {
        return $LANG_MAP[$key][$lang];
    }
    return $key;
}

?>