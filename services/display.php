<?php

include_once "mysql.php";

function displayAccount($accounts, $type) {
	foreach($accounts as $account) {
		if ($account['type'] == $type) {
			echo '<option value="'.$account['id'].'">'.$account['name'].'</option>';
		}
	}
}

function displayTxns($txns) {
	foreach($txns as $txn) {
		echo '<tr><td>'.$txn["date"].'</td><td><a href="./transactions.php?id='.$txn["from_account_id"].'">'.$txn["from_account_name"].'</a></td><td><a href="./transactions.php?id='.$txn["to_account_id"].'">'.$txn["to_account_name"].'</a></td><td>'.$txn["description"].'</td><td>'.$txn["amount"].'</td></tr>';
	}
}

function displayTxnsWithBalance($txns, $id) {
	$balance = getBalanceByAccountId($id);
	foreach($txns as $txn) {
		$amount = $txn["amount"];
		if ($txn["from_account_id"] == $id) {
			$amount = $amount * -1;
		}
		echo '<tr><td>'.$txn["date"].'</td><td><a href="./transactions.php?id='.$txn["from_account_id"].'">'.$txn["from_account_name"].'</a></td><td><a href="./transactions.php?id='.$txn["to_account_id"].'">'.$txn["to_account_name"].'</a></td><td>'.$txn["description"].'</td><td>'.$amount.'</td><td>'.$balance.'</td></tr>';
		$balance = $balance - $amount;
	}
}

function displayAccountsWithBalance($accounts) {
	foreach($accounts as $account) {
			echo '<tr><td><a href="./transactions.php?id='.$account["id"].'">'.$account['name'].'</a></td><td>'.getBalanceByAccountId($account['id']).'</td></tr>';
	}
}

?>