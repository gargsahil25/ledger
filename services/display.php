<?php

include_once "mysql.php";

function displayAccounts($accounts, $type, $selectedAccount, $showBalance = null) {
	foreach($accounts as $account) {
		if (!$type || $account['type'] == $type) {
			$balance = '';
			if ($showBalance) {
				$balance = ' '.getMoneyFormat($account['balance']);
			}
			if ($selectedAccount == $account['id']) {
				echo '<option selected value="'.$account['id'].'">'.$account['name'] .$balance.'</option>';
			} else {
				echo '<option value="'.$account['id'].'">'.$account['name'] .$balance.'</option>';
			}
		}
	}
}

function displayDateTxns($txns, $txnDate) {
	$i = 0;
	$factoryId = getAccountByName('FACTORY MALL')['id'];
	$cashId = getAccountByName('CASH')['id'];
	$cashBalance = getBalanceByAccountId($cashId, $txnDate);
	$cashTitle = "<tr><td colspan='5'><strong>Cash balance: ".getMoneyFormat($cashBalance)."</strong></td></tr>";
	$factoryTitle = "<tr><td colspan='5'><strong>Factory Transactions</strong></td></tr>";
	$cashTxns = "";
	$factoryTxns = "";
	foreach($txns as $txn) {
		$class = "debit";
		if ($txn['to_account_id'] == $cashId || $txn['to_account_id'] == $factoryId) {
			$class = "credit";
		}
		$prefix = '<tr class="'.$class.'" data-toggle="modal" data-target="#txn-'.$i.'"><td>'.date_format(date_create($txn["date"]),"jS M").'</td>';
		$factoryAmount = $txn["amount"];
		$cashAmount = $txn["amount"];
		if ($txn['from_account_id'] == $factoryId) {
			$factoryAmount = $factoryAmount * -1;
			$factoryTxns .= $prefix.'<td>'.$txn['to_account_name'].': '.$txn["description"].'</td><td>'.getMoneyFormat($factoryAmount, true).'</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
		} else if ($txn['to_account_id'] == $factoryId) {
			$factoryTxns .= $prefix.'<td>'.$txn['from_account_name'].': '.$txn["description"].'</td><td>&nbsp;</td><td>'.getMoneyFormat($factoryAmount, true).'</td><td>&nbsp;</td></tr>';
		}
		if ($txn['from_account_id'] == $cashId) {
			$cashAmount = $cashAmount * -1;
			$cashTxns .= $prefix.'<td>'.$txn['to_account_name'].': '.$txn["description"].'</td><td>'.getMoneyFormat($cashAmount, true).'</td><td>&nbsp;</td><td>'.getMoneyFormat($cashBalance, true).'</td></tr>';
			$cashBalance -= $cashAmount;
		} else if($txn['to_account_id'] == $cashId) {
			$cashTxns .= $prefix.'<td>'.$txn['from_account_name'].': '.$txn["description"].'</td><td>&nbsp;</td><td>'.getMoneyFormat($cashAmount, true).'</td><td>'.getMoneyFormat($cashBalance, true).'</td></tr>';
			$cashBalance -= $cashAmount;
		}
		$i++;
	}

	if (!$cashTxns) {
		$cashTxns = "<tr><td colspan='5'>No transactions</td></tr>";
	}

	if (!$factoryTxns) {
		$factoryTxns = "<tr><td colspan='5'>No transactions</td></tr>";
	}
	
	echo $cashTitle;
	echo $cashTxns;
	echo "<tr><td colspan='5'><strong>Initial Cash: ".getMoneyFormat($cashBalance)."</strong></td></tr>";
	echo "<tr><td colspan='5'>&nbsp;</td></tr>";
	echo $factoryTitle;
	echo $factoryTxns;
}

function displayAccountTxns($txns, $account, $balance) {
	$id = $account['id'];
	$i = 0;
	foreach($txns as $txn) {
		$class = "debit";
		if ($txn['to_account_id'] == $id) {
			$class = "credit";
		}
		$description_prefix = "";
		if ($account['type'] == 'cash' || $account['type'] == 'factory_mall') {
			if($txn["from_account_id"] == $id) {
				$description_prefix = $txn['to_account_name'].": ";
			} else {
				$description_prefix = $txn['from_account_name'].": ";
			}
		}
		echo '<tr class="'.$class.'" data-toggle="modal" data-target="#txn-'.$i.'"><td>'.date_format(date_create($txn["date"]),"jS M").'</td><td>'.$description_prefix.$txn["description"].'</td>';

		$amount = $txn["amount"];
		if ($txn["from_account_id"] == $id) {
			$amount = $amount * -1;
			echo '<td>'.getMoneyFormat($amount, true).'</td><td>&nbsp;</td>';
		} else {
			echo '<td>&nbsp;</td><td>'.getMoneyFormat($amount, true).'</td>';
		}
		echo '<td>'.getMoneyFormat($balance, true).'</td></tr>';
		$balance = $balance - $amount;
		$i++;
	}
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