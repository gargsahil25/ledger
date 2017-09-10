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

function displayDateTxns($txns) {
	$i = 0;
	$factoryId = getAccounts('factory')[0]['id'];
	$cashId = getAccounts('cash')[0]['id'];
	$factoryBalance = getBalanceByAccountId($factoryId);
	$cashBalance = getBalanceByAccountId($cashId);
	$cashTitle = "<tr><td colspan='5'><strong>Cash balance: ".getMoneyFormat($cashBalance)."</strong></td></tr>";
	$factoryTitle = "<tr><td colspan='5'><strong>Factory Transactions</strong></td></tr>";
	$cashTxns = "";
	$factoryTxns = "";
	foreach($txns as $txn) {
		$class = "debit";
		if ($txn['from_account_id'] == $cashId) {
			$class = "credit";
		}
		$prefix = '<tr class="'.$class.'" data-toggle="modal" data-target="#txn-'.$i.'"><td>'.date_format(date_create($txn["date"]),"jS M").'</td>';
		$factoryAmount = $txn["amount"];
		$cashAmount = $txn["amount"];
		if ($txn['from_account_id'] == $factoryId) {
			$factoryAmount = $factoryAmount * -1;
			$factoryTxns .= $prefix.'<td>'.$txn['to_account_name'].': '.$txn["description"].'</td><td>&nbsp;</td><td>'.getMoneyFormat($factoryAmount, true).'</td><td>'.getMoneyFormat($factoryBalance, true).'</td></tr>';
			$factoryBalance -= $factoryAmount;
		} else if ($txn['to_account_id'] == $factoryId) {
			$factoryTxns .= $prefix.'<td>'.$txn['from_account_name'].': '.$txn["description"].'</td><td>'.getMoneyFormat($factoryAmount, true).'</td><td>&nbsp;</td><td>'.getMoneyFormat($factoryBalance, true).'</td></tr>';
			$factoryBalance -= $factoryAmount;
		}
		if ($txn['from_account_id'] == $cashId) {
			// $cashAmount = $cashAmount * -1;
			// $cashTxns .= $prefix.'<td>'.$txn['to_account_name'].': '.$txn["description"].'</td><td>&nbsp;</td><td>'.getMoneyFormat($cashAmount, true).'</td><td>'.getMoneyFormat($cashBalance, true).'</td></tr>';
			$cashTxns .= $prefix.'<td>'.$txn['to_account_name'].'</td><td>'.$txn["description"].'</td><td>'.getMoneyFormat($cashAmount, true).'</td><td>&nbsp;</td></tr>';
			$cashBalance -= $cashAmount;
		} else if($txn['to_account_id'] == $cashId) {
			$cashAmount = $cashAmount * -1;
			$cashTxns .= $prefix.'<td>'.$txn['from_account_name'].'</td><td>'.$txn["description"].'</td><td>&nbsp;</td><td>'.getMoneyFormat($cashAmount, true).'</td></tr>';
			// $cashTxns .= $prefix.'<td>'.$txn['from_account_name'].': '.$txn["description"].'</td><td>'.getMoneyFormat($cashAmount, true).'</td><td>&nbsp;</td><td>'.getMoneyFormat($cashBalance, true).'</td></tr>';
			$cashBalance -= $cashAmount;
		}
		$i++;
	}
	// echo $cashTitle;
	echo $cashTxns;
	// echo "<tr><td colspan='5'><strong>Initial Cash: ".getMoneyFormat($cashBalance)."</strong></td></tr>";
	// echo "<tr><td colspan='5'>&nbsp;</td></tr>";
	// echo $factoryTitle;
	// echo $factoryTxns;
}

function displayAccountTxns($txns, $id) {
	$balance = getBalanceByAccountId($id);
	$i = 0;
	foreach($txns as $txn) {
		$class = "debit";
		if ($txn['to_account_id'] == $id) {
			$class = "credit";
		}
		echo '<tr class="'.$class.'" data-toggle="modal" data-target="#txn-'.$i.'"><td>'.date_format(date_create($txn["date"]),"jS M").'</td><td>'.$txn["description"].'</td>';
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