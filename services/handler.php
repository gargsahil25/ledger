<?php

include_once "mysql.php";

date_default_timezone_set('Asia/Kolkata');

function buyStuffHandler($post) {
	if(isset($post['buy-submit']) && 
		!empty($post['buy-desc']) && 
		!empty($post['buy-from']) && 
		!empty($post['buy-amount']) && 
		!empty($post['buy-date'])) {

		$factoryId = getAccounts('factory')[0]['id'];
		$date = $post['buy-date']." ".date('H:i:s', time());
		return addTransaction($post['buy-from'], $factoryId, $post['buy-desc'], $post['buy-amount'], $date);
	}
}

function sellStuffHandler($post) {
	if(isset($post['sell-submit']) && 
		!empty($post['sell-desc']) && 
		!empty($post['sell-to']) && 
		!empty($post['sell-amount']) && 
		!empty($post['sell-date'])) {

		$factoryId = getAccounts('factory')[0]['id'];
		$date = $post['sell-date']." ".date('H:i:s', time());
		return addTransaction($factoryId, $post['sell-to'], $post['sell-desc'], $post['sell-amount'], $date);
	}
}

function payAmountHandler($post) {
	if(isset($post['pay-submit']) && 
		!empty($post['pay-desc']) && 
		!empty($post['pay-to']) && 
		!empty($post['pay-amount']) && 
		!empty($post['pay-date'])) {

		$cashId = getAccounts('cash')[0]['id'];
		$date = $post['pay-date']." ".date('H:i:s', time());
		return addTransaction($cashId, $post['pay-to'], $post['pay-desc'], $post['pay-amount'], $date);
	}
}

function getPaymentHandler($post) {
	if(isset($post['payment-submit']) && 
		!empty($post['payment-desc']) && 
		!empty($post['payment-from']) && 
		!empty($post['payment-amount']) && 
		!empty($post['payment-date'])) {

		$cashId = getAccounts('cash')[0]['id'];
		$date = $post['payment-date']." ".date('H:i:s', time());
		return addTransaction($post['payment-from'], $cashId, $post['payment-desc'], $post['payment-amount'], $date);
	}
}

function newClientHandler($post) {
	if(isset($post['client-submit']) && 
		!empty($post['client-name'])) {

		return addAccount($post['client-name'], 'client');
	}
}

?>