<?php

function displayAccounts($accounts, $type, $selectedAccount) {
	foreach($accounts as $account) {
		if (!$type || $account['type'] == $type) {
			if ($selectedAccount == $account['id']) {
				echo '<option selected value="'.$account['id'].'">'.$account['name'] .' &#8377; '.$account['balance'].'</option>';
			} else {
				echo '<option value="'.$account['id'].'">'.$account['name'] .' &#8377; '.$account['balance'].'</option>';
			}
		}
	}
}

function displayTxns($txns) {
	$i = 0;
	foreach($txns as $txn) {
		echo '<tr data-toggle="modal" data-target="#txn-'.$i.'"><td>'.date_format(date_create($txn["date"]),"jS M").'</td><td><a href="./transactions.php?id='.$txn["from_account_id"].'">'.$txn["from_account_name"].'</a></td><td><a href="./transactions.php?id='.$txn["to_account_id"].'">'.$txn["to_account_name"].'</a></td><td>'.$txn["description"].'</td><td>'.$txn["amount"].'</td></tr>';
		$i++;
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