<?php

include_once "constant.php";
include_once "util.php";
include_once "mysql.php";

function displayUsers($userNames) {
	echo '<option value="">'.getLangText("SELECT_USER").'</option>';
	foreach($userNames as $u) {
		echo '<option value="'.$u['name'].'">'.$u['name'].'</option>';
	}
}

function displayAccountTypes($selectedAccountType = null) {
	global $ACCOUNT_TYPE;
	$accTypes = array(
		array(
			"type" => $ACCOUNT_TYPE['CLIENT'],
			"name" => getLangText('CLIENT')
		),
		array(
			"type" => $ACCOUNT_TYPE['FACTORY_EXPENSE'],
			"name" => getLangText('FACTORY_EXPENSE')
		),
		array(
			"type" => $ACCOUNT_TYPE['HOME'],
			"name" => getLangText('HOME_EXPENSE')
		)
	);

	echo '<option value="">'.getLangText("ACCOUNT_TYPE").'</option>';
	foreach($accTypes as $at) {
		if ($selectedAccountType == $at['type']) {
			echo '<option selected value="'. $at['type'].'">'.$at['name'].'</option>';
		} else {
			echo '<option value="'. $at['type'].'">'.$at['name'].'</option>';
		}
	}
}

function displayTxnType() {
	echo '<input type="radio" id="credit" name="cash-txn" value="0" checked>';
	echo '<label for="credit">Credit</label>';
	echo '<input type="radio" id="cash" name="cash-txn" value="1">';
	echo '<label for="cash">Cash</label>';
}

function displayAccounts($accounts, $type, $selectedAccount = null, $showBalance = false) {
	global $ACCOUNT_TYPE;
	foreach($accounts as $account) {
		if (($type == "all") ||				// to show all accounts for viewing ledger or update txn scenario 
			($type == $account['type']) || 	// for sale and purchase entries
			(!$type && 						// for credit and debit entries
				$account['type'] !=  $ACCOUNT_TYPE['CASH'] && 
				$account['type'] !=  $ACCOUNT_TYPE['FACTORY_MALL'])) {	

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

function displayAccountBalance($accounts) {
	global $ACCOUNT_TYPE;
	foreach($accounts as $account) {
		$actualBalance = $account['balance'];
		if ($account['type'] == $ACCOUNT_TYPE['FACTORY_EXPENSE']) {
			$actualBalance = 0;
		}
		echo '<tr class="account-row"><td>'.$account['name'].'</td><td class="account-balance">'.$account['balance'].'</td><td class="editable"><input type="text" class="account-actualbalance" value="'.$actualBalance.'"/></td><td class="account-profitloss"></td></tr>';
	}
}

function displayDateTxns($txns, $txnDate) {
	$i = 0;
	$factoryId = getStockAccountId();
	$cashId = getCashAccountId();
	$cashBalance = getBalanceByAccountId($cashId, $txnDate);
	$cashTitle = "<tr><td colspan='5'><strong>".getLangText('CASH_BALANCE').": ".getMoneyFormat($cashBalance)."</strong></td></tr>";
	$factoryTitle = "<tr><td colspan='5'><strong>".getLangText('FAC_TRANSACTION')."</strong></td></tr>";
	$cashTxns = "";
	$factoryTxns = "";
	foreach($txns as $txn) {
		$class = "debit";
		if ($txn['to_account_id'] == $cashId || $txn['to_account_id'] == $factoryId) {
			$class = "credit";
		}
		$prefix = '<tr class="'.$class.'" data-toggle="modal" data-target="#txn-'.$i.'"><td>'.date_format(date_create($txn["date"]),"j F Y").'</td>';
		$factoryAmount = $txn["amount"];
		$cashAmount = $txn["amount"];
		if ($txn['from_account_id'] == $factoryId) {
			$factoryAmount = $factoryAmount * -1;
			$factoryTxns .= $prefix.'<td>'.$txn['to_account_name'].': '.$txn["description"].'</td><td>&nbsp;</td><td>'.getMoneyFormat($factoryAmount, true).'</td><td>&nbsp;</td></tr>';
		} else if ($txn['to_account_id'] == $factoryId) {
			$factoryTxns .= $prefix.'<td>'.$txn['from_account_name'].': '.$txn["description"].'</td><td>'.getMoneyFormat($factoryAmount, true).'</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
		}
		if ($txn['from_account_id'] == $cashId) {
			$cashAmount = $cashAmount * -1;
			$cashTxns .= $prefix.'<td>'.$txn['to_account_name'].': '.$txn["description"].'</td><td>&nbsp;</td><td>'.getMoneyFormat($cashAmount, true).'</td><td>'.getMoneyFormat($cashBalance, true).'</td></tr>';
			$cashBalance -= $cashAmount;
		} else if($txn['to_account_id'] == $cashId) {
			$cashTxns .= $prefix.'<td>'.$txn['from_account_name'].': '.$txn["description"].'</td><td>'.getMoneyFormat($cashAmount, true).'</td><td>&nbsp;</td><td>'.getMoneyFormat($cashBalance, true).'</td></tr>';
			$cashBalance -= $cashAmount;
		}
		$i++;
	}

	if (!$cashTxns) {
		$cashTxns = "<tr><td colspan='5'>".getLangText('NO_TRANSACTION')."</td></tr>";
	}

	if (!$factoryTxns) {
		$factoryTxns = "<tr><td colspan='5'>".getLangText('NO_TRANSACTION')."</td></tr>";
	}
	
	echo $cashTitle;
	echo $cashTxns;
	echo "<tr><td colspan='5'><strong>".getLangText('STARTING_CASH').": ".getMoneyFormat($cashBalance)."</strong></td></tr>";
	echo "<tr><td colspan='5'>&nbsp;</td></tr>";
	echo $factoryTitle;
	echo $factoryTxns;
}

function displayAccountTxns($txns, $account, $balance) {
	global $ACCOUNT_TYPE;
	$id = $account['id'];
	$factoryId = getStockAccountId();
	$cashId = getCashAccountId();
	$i = 0;
	foreach($txns as $txn) {
		$class = "debit";
		if ($txn['to_account_id'] == $factoryId || $txn['to_account_id'] == $cashId) {
			$class = "credit";
		}
		$description_prefix = "";
		if ($account['type'] == $ACCOUNT_TYPE['CASH'] || $account['type'] == $ACCOUNT_TYPE['FACTORY_MALL']) {
			if($txn["from_account_id"] == $id) {
				$description_prefix = $txn['to_account_name'].": ";
			} else {
				$description_prefix = $txn['from_account_name'].": ";
			}
		}
		echo '<tr class="'.$class.'" data-toggle="modal" data-target="#txn-'.$i.'"><td>'.date_format(date_create($txn["date"]),"j F Y").'</td><td>'.$description_prefix.$txn["description"].'</td>';

		$amount = $txn["amount"];
		if ($class == "credit") {
			echo '<td>'.getMoneyFormat($amount, true).'</td><td>&nbsp;</td>';
		} else {
			$amount = $amount * -1;
			echo '<td>&nbsp;</td><td>'.getMoneyFormat($amount, true).'</td>';
		}
		echo '<td>'.getMoneyFormat($balance, true).'</td></tr>';
		$balance = $balance - $amount;
		$i++;
	}
}

function displayAllTxns($txns) {
	foreach($txns as $txn) {
		$class = "credit";
		if ($txn['is_deleted']) {
			$class = "debit";
		}
		echo '<tr class="'.$class.'">';
		echo '<td>'.date_format(date_create($txn["date"]),"j F Y").'</td>';
		echo '<td>'.$txn["description"].'</td>';
		echo '<td>'.$txn["from_account_name"].'</td>';
		echo '<td>'.$txn["to_account_name"].'</td>';
		echo '<td>'.$txn["amount"].'</td>';
		echo '</tr>';
	}
}

function displayDays($count, $selectedDate) {
	if (!$selectedDate) {
		echo "<option selected value=''>".getLangText('DATE')."</option>";
	} else {
		echo "<option value=''>".getLangText('DATE')."</option>";
	}
	for ($i = 0; $i < $count; $i++) {
		$d = strtotime("-".$i." Days");
		if ($selectedDate == date("Ymd", $d)) {
			echo "<option selected value='".date("Ymd", $d)."'>".date("j F", $d)."</option>";
		} else {
			echo "<option value='".date("Ymd", $d)."'>".date("j F", $d)."</option>";
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