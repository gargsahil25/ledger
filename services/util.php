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

function redirect($page = 'index.php', $queryParams = array()) {
    parse_str($_SERVER['QUERY_STRING'], $queryObj);
    $queryStr = http_build_query(array_merge($queryObj, $queryParams));
    $url = '/'.$page.'?'.$queryStr;
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

function isAccountEditable($account) {
    global $ACCOUNT_TYPE;
    if ($account['type'] == $ACCOUNT_TYPE['CLIENT'] ||
        $account['type'] == $ACCOUNT_TYPE['BUSINESS_EXPENSE'] ||
        $account['type'] == $ACCOUNT_TYPE['HOME_EXPENSE']) {
            return true;
        }
    return false;
}

function getDateFormat($date = null, $format = null) {
    if ($date == null) {
        $date = date_create();
    } else if (is_string($date)) {
        $date = date_create($date);
    }
    if ($format == null) {
        $format = "j M Y";
    }
    return date_format($date, $format);
}

?>