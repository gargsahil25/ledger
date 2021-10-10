<?php

include_once "constant.php";
include_once "util.php";
include_once "sessionUtil.php";
include_once "mysql.php";

date_default_timezone_set('Asia/Kolkata');


function loginUserHandler($post) {
	if(isset($post['login-submit']) && 
		!empty($post['password'])) {

		$user = getUserByPassword($post['userId'], $post['password']);
		setLoginUser($user);
	}
}

function buyStuffHandler($post) {
	if(isset($post['buy-submit']) && 
		!empty($post['user-id']) && 
		!empty($post['buy-desc']) && 
		!empty($post['buy-from']) && 
		!empty($post['buy-amount']) && 
		!empty($post['buy-date'])) {

		$factoryId = getStockAccountId($post['user-id']);
		$cashId = getCashAccountId($post['user-id']);
		$date = $post['buy-date']." ".date('H:i:s', time());
		addTransaction($post['user-id'], $post['buy-from'], $factoryId, $post['buy-desc'], $post['buy-amount'], $date);
		if ($post['cash-txn'] == '1') {
			addTransaction($post['user-id'], $cashId, $post['buy-from'], "cash payment for ".$post['buy-desc'], $post['buy-amount'], $date);
		}
		redirect(null, array('userId' => $post['user-id'], 'txn-account' => $post['buy-from']));
	}
}

function sellStuffHandler($post) {
	if(isset($post['sell-submit']) && 
		!empty($post['user-id']) && 
		!empty($post['sell-desc']) && 
		!empty($post['sell-to']) && 
		!empty($post['sell-amount']) && 
		!empty($post['sell-date'])) {

		$factoryId = getStockAccountId($post['user-id']);
		$cashId = getCashAccountId($post['user-id']);
		$date = $post['sell-date']." ".date('H:i:s', time());
		addTransaction($post['user-id'], $factoryId, $post['sell-to'], $post['sell-desc'], $post['sell-amount'], $date);
		if ($post['cash-txn'] == '1') {
			addTransaction($post['user-id'], $post['sell-to'], $cashId, "cash payment for ".$post['sell-desc'], $post['sell-amount'], $date);
		}
		redirect(null, array('userId' => $post['user-id'], 'txn-account' => $post['sell-to']));
	}
}

function payAmountHandler($post) {
	if(isset($post['pay-submit']) && 
		!empty($post['user-id']) && 
		!empty($post['pay-desc']) && 
		!empty($post['pay-to']) && 
		(!empty($post['pay-amount']) || $post['pay-amount'] == 0) && 
		!empty($post['pay-date'])) {

		$cashId = getCashAccountId($post['user-id']);
		$date = $post['pay-date']." ".date('H:i:s', time());
		addTransaction($post['user-id'], $cashId, $post['pay-to'], $post['pay-desc'], $post['pay-amount'], $date);
		redirect(null, array('userId' => $post['user-id'], 'txn-account' => $post['pay-to']));
	}
}

function getPaymentHandler($post) {
	if(isset($post['earn-submit']) && 
		!empty($post['user-id']) && 
		!empty($post['earn-desc']) && 
		!empty($post['earn-from']) && 
		(!empty($post['pay-amount']) || $post['pay-amount'] == 0) && 
		!empty($post['earn-date'])) {

		$cashId = getCashAccountId($post['user-id']);
		$date = $post['earn-date']." ".date('H:i:s', time());
		addTransaction($post['user-id'], $post['earn-from'], $cashId, $post['earn-desc'], $post['earn-amount'], $date);
		redirect(null, array('userId' => $post['user-id'], 'txn-account' => $post['earn-from']));
	}
}

function newClientHandler($post) {
	if(isset($post['client-submit']) && 		
		!empty($post['user-id']) && 
		!empty($post['client-name']) && 
		!empty($post['account_type'])) {
		$id = addAccount($post['user-id'], $post['client-name'], $post['account_type']);
		redirect(null, array('userId' => $post['user-id'], 'txn-account' => $id));
	}
}

function updateClientHandler($post) {
	if(isset($post['client-update']) && 
		!empty($post['user-id']) && 
		!empty($post['client-update']) && 
		!empty($post['account_type'])) {
		updateAccount($post['user-id'], $post['client-id'], $post['client-name'], $post['account_type']);
		redirect(null, array('userId' => $post['user-id'], 'txn-account' => $post['client-id']));
	}
}

function updateTxnHandler($post) {
	if(isset($post['txn-update-submit']) && 
		!empty($post['user-id']) && 
		!empty($post['txn-update-submit'])) {
		$txnFrom = isset($post['txn-from']) ? $post['txn-from'] : $post['txn-from-old'];
		$txnTo = isset($post['txn-to']) ? $post['txn-to'] : $post['txn-to-old'];
		updateTransaction($post['user-id'], $post['txn-id'], $post['txn-desc'], $txnFrom, $txnTo, $post['txn-amount'], $post['txn-date'], $post['txn-from-old'], $post['txn-to-old']);
		redirect(null, array('userId' => $post['user-id']));
	}
}

function deleteTxnHandler($post) {
	if(isset($post['txn-delete-submit']) && 
		!empty($post['user-id']) && 
		!empty($post['txn-delete-submit'])) {
		deleteTransaction($post['user-id'], $post['txn-id'], $post['txn-from'], $post['txn-to']);
		redirect(null, array('userId' => $post['user-id']));
	}
}

?>