<?php

include_once "mysql.php";
include_once "util.php";

date_default_timezone_set('Asia/Kolkata');

function buyStuffHandler($post) {
	if(isset($post['buy-submit']) && 
		!empty($post['buy-desc']) && 
		!empty($post['buy-from']) && 
		!empty($post['buy-amount']) && 
		!empty($post['buy-date'])) {

		$factoryId = getAccounts('factory')[0]['id'];
		$date = $post['buy-date']." ".date('H:i:s', time());
		addTransaction($post['buy-from'], $factoryId, $post['buy-desc'], $post['buy-amount'], $date);
		header('Location: /');
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
		addTransaction($factoryId, $post['sell-to'], $post['sell-desc'], $post['sell-amount'], $date);
		header('Location: /');
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
		addTransaction($cashId, $post['pay-to'], $post['pay-desc'], $post['pay-amount'], $date);
		header('Location: /');
	}
}

function getPaymentHandler($post) {
	if(isset($post['earn-submit']) && 
		!empty($post['earn-desc']) && 
		!empty($post['earn-from']) && 
		!empty($post['earn-amount']) && 
		!empty($post['earn-date'])) {

		$cashId = getAccounts('cash')[0]['id'];
		$date = $post['earn-date']." ".date('H:i:s', time());
		addTransaction($post['earn-from'], $cashId, $post['earn-desc'], $post['earn-amount'], $date);
		header('Location: /');
	}
}

function newClientHandler($post) {
	if(isset($post['client-submit']) && !empty($post['client-name'])) {
		addAccount($post['client-name'], 'client');
		header('Location: /');
	}
}

function updateTxnHandler($post) {
	if(isset($post['txn-update-submit']) && !empty($post['txn-update-submit'])) {
		updateTransaction($post['txn-id'], $post['txn-desc'], $post['txn-from'], $post['txn-to'], $post['txn-amount'], $post['txn-date']);
		redirect();
	}
}

function deleteTxnHandler($post) {
	if(isset($post['txn-delete-submit']) && !empty($post['txn-delete-submit'])) {
		deleteTransaction($post['txn-id']);
		header('Location: /');
	}
}

?>