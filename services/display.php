<?php

include_once "mysql.php";

function displayAccounts($accounts, $type, $selectedAccount) {
	foreach($accounts as $account) {
		if (!$type || $account['type'] == $type) {
			if ($selectedAccount == $account['id']) {
				echo '<option selected value="'.$account['id'].'">'.$account['name'] .' '.getMoneyFormat($account['balance']).'</option>';
			} else {
				echo '<option value="'.$account['id'].'">'.$account['name'] .' '.getMoneyFormat($account['balance']).'</option>';
			}
		}
	}
}

function displayTxns($txns) {
	$i = 0;
	foreach($txns as $txn) {
		echo '<tr data-toggle="modal" data-target="#txn-'.$i.'"><td>'.date_format(date_create($txn["date"]),"jS M").'</td><td>'.$txn["from_account_name"].'</td><td>'.$txn["to_account_name"].'</td><td>'.$txn["description"].'</td><td>'.getMoneyFormat($txn["amount"], true).'</td></tr>';
		$i++;
	}
}

function displayTxnsWithBalance($txns, $id) {
	$balance = getBalanceByAccountId($id);
	$i = 0;
	foreach($txns as $txn) {
		echo '<tr data-toggle="modal" data-target="#txn-'.$i.'"><td>'.date_format(date_create($txn["date"]),"jS M").'</td><td>'.$txn["description"].'</td>';
		$amount = $txn["amount"];
		if ($txn["from_account_id"] == $id) {
			$amount = $amount * -1;
			echo '<td>&nbsp;</td><td>'.getMoneyFormat($amount, true).'</td>';
		} else {
			echo '<td>'.getMoneyFormat($amount, true).'</td><td>&nbsp;</td>';
		}
		echo '<td>'.getMoneyFormat($balance, true).'</td></tr>';
		$balance = $balance - $amount;
		$i++;
	}
}

function getProfit($txns) {
	$profit = 0;
	$factoryId = getAccounts('factory')[0]['id'];
	foreach($txns as $txn) {
		if ($txn["from_account_id"] == $factoryId) {
			$profit += $txn["amount"];
		} else if ($txn["to_account_id"] == $factoryId) {
			$profit -= $txn["amount"];
		}
	}
	return $profit;
}

function displayDays($count, $selectedDate) {
	if (!$selectedDate) {
		echo "<option selected value=''>Date</option>";
	} else {
		echo "<option value=''>Date</option>";
	}
	for ($i = 0; $i < $count; $i++) {
		$d = strtotime("-".$i." Days");
		if ($selectedDate == date("Ymd", $d)) {
			echo "<option selected value='".date("Ymd", $d)."'>".date("jS M", $d)."</option>";
		} else {
			echo "<option value='".date("Ymd", $d)."'>".date("jS M", $d)."</option>";
		}
	}
}

function displayMonths($count, $selectedDate) {
	if (!$selectedDate) {
		echo "<option selected value=''>Month</option>";
	} else {
		echo "<option value=''>Month</option>";
	}
	for ($i = 0; $i < $count; $i++) {
		$d = strtotime("-".$i." Months");
		if ($selectedDate == date("Ymd", $d)) {
			echo "<option selected value='".date("Ymd", $d)."'>".date("M 'y", $d)."</option>";
		} else {
			echo "<option value='".date("Ymd", $d)."'>".date("M 'y", $d)."</option>";
		}
	}
}

?>