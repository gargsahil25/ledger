<?php

include_once "constant.php";
include_once "util.php";
include_once "sessionUtil.php";
include_once "mysql.php";

date_default_timezone_set('Asia/Kolkata');


function loginUserHandler($post) {
	if(isset($post['login-submit']) && 
		!empty($post['password'])) {

		$user = getUserByPassword($post['username'], $post['password']);
		setLoginUser($user);
	}
}

function buyStuffHandler($post) {
	if(isset($post['buy-submit']) && 
		!empty($post['buy-desc']) && 
		!empty($post['buy-from']) && 
		!empty($post['buy-amount']) && 
		!empty($post['buy-date'])) {

		$factoryId = getStockAccountId();
		$cashId = getCashAccountId();
		$date = $post['buy-date']." ".date('H:i:s', time());
		addTransaction($post['buy-from'], $factoryId, $post['buy-desc'], $post['buy-amount'], $date);
		if ($post['cash-txn'] == '1') {
			addTransaction($cashId, $post['buy-from'], "cash payment for ".$post['buy-desc'], $post['buy-amount'], $date);
		}
		redirect('/?txn-account='.$post['buy-from']);
	}
}

function sellStuffHandler($post) {
	if(isset($post['sell-submit']) && 
		!empty($post['sell-desc']) && 
		!empty($post['sell-to']) && 
		!empty($post['sell-amount']) && 
		!empty($post['sell-date'])) {

		$factoryId = getStockAccountId();
		$cashId = getCashAccountId();
		$date = $post['sell-date']." ".date('H:i:s', time());
		addTransaction($factoryId, $post['sell-to'], $post['sell-desc'], $post['sell-amount'], $date);
		if ($post['cash-txn'] == '1') {
			addTransaction($post['sell-to'], $cashId, "cash payment for ".$post['sell-desc'], $post['sell-amount'], $date);
		}
		redirect('/?txn-account='.$post['sell-to']);
	}
}

function payAmountHandler($post) {
	if(isset($post['pay-submit']) && 
		!empty($post['pay-desc']) && 
		!empty($post['pay-to']) && 
		!empty($post['pay-amount']) && 
		!empty($post['pay-date'])) {

		$cashId = getCashAccountId();
		$date = $post['pay-date']." ".date('H:i:s', time());
		addTransaction($cashId, $post['pay-to'], $post['pay-desc'], $post['pay-amount'], $date);
		redirect('/?txn-account='.$post['pay-to']);
	}
}

function getPaymentHandler($post) {
	if(isset($post['earn-submit']) && 
		!empty($post['earn-desc']) && 
		!empty($post['earn-from']) && 
		!empty($post['earn-amount']) && 
		!empty($post['earn-date'])) {

		$cashId = getCashAccountId();
		$date = $post['earn-date']." ".date('H:i:s', time());
		addTransaction($post['earn-from'], $cashId, $post['earn-desc'], $post['earn-amount'], $date);
		redirect('/?txn-account='.$post['earn-from']);
	}
}

function newClientHandler($post) {
	if(isset($post['client-submit']) && !empty($post['client-name']) && !empty($post['account_type'])) {
		$id = addAccount($post['client-name'], $post['account_type']);
		redirect('/?txn-account='.$id);
	}
}

function updateClientHandler($post) {
	if(isset($post['client-update']) && !empty($post['client-update']) && !empty($post['account_type'])) {
		updateAccount($post['client-id'], $post['client-name'], $post['account_type']);
		redirect('/?txn-account='.$post['client-id']);
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
		deleteTransaction($post['txn-id'], $post['txn-from'], $post['txn-to']);
		redirect();
	}
}

?>