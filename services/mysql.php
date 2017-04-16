<?php 

function mysqlQuery($sql) {
	
	$servername = "localhost:3306";
	$username = "root";
	$password = "root";
	$dbname = "ledger";

	$url = getenv("CLEARDB_DATABASE_URL");
	if ($url) {
		$url = parse_url($url);
		$servername = $url["host"];
		$username = $url["user"];
		$password = $url["pass"];
		$dbname = substr($url["path"],1);
	}	

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	} 

	//echo $sql;
	$result = $conn->query($sql);

	if(!$result) {
	    echo "Error: " . $sql . "<br>" . $conn->error;
	}

	$conn->close();

	return $result;
}

function getAccounts($type = null) {
	$sql = "SELECT * FROM accounts";
	if ($type) {
		$sql .= " WHERE type = '".$type."'";
	}
	$sql .= " ORDER BY name";
	$accountRows = mysqlQuery($sql);
	$accounts = array();
	while($account = $accountRows->fetch_assoc()) {
		$account['balance'] = getBalanceByAccountId($account['id']);
		array_push($accounts, $account);
	}
	return $accounts;
}

function getAccountById($id) {
	$sql = "SELECT * FROM accounts WHERE id = ".$id;
	$accountRows = mysqlQuery($sql);
	$accounts = array();
	while($account = $accountRows->fetch_assoc()) {
		array_push($accounts, $account);
	}
	return $accounts[0];
}

function addAccount($name, $type) {
	$sql = "INSERT INTO `accounts` (`name`, `type`) VALUES ('".$name."', '".$type."')";
	return mysqlQuery($sql);
}

function getTransactions($txnAccount = null, $txnDate = null, $txnMonth = null) {
	$sql = "SELECT t.id AS id, t.date AS date, t.description AS description, t.amount AS amount, fa.id AS from_account_id, fa.name AS from_account_name, ta.id AS to_account_id, ta.name AS to_account_name FROM transactions t JOIN accounts fa ON t.from_account = fa.id JOIN accounts ta ON t.to_account = ta.id WHERE 1 = 1";
	if ($txnAccount) {
		$sql .= " AND t.from_account = ".$txnAccount." OR t.to_account = ".$txnAccount;
	}
	if ($txnDate) {
		$date = date_create($txnDate);
		$sql .= " AND t.date LIKE '".date_format($date, "Y-m-d")."%'";
	}
	if ($txnMonth) {
		$date = date_create($txnMonth);
		$sql .= " AND t.date LIKE '".date_format($date, "Y-m")."%'";
	}

	$sql .= " ORDER BY date desc LIMIT 0,10";
	$txnRows = mysqlQuery($sql);
	$txns = array();
	while($txn = $txnRows->fetch_assoc()) {
		array_push($txns, $txn);
	}
	return $txns;
}

function addTransaction($fromAccount, $toAccount, $description, $amount, $date) {
	$sql = "INSERT INTO `transactions` (`from_account`, `to_account`, `description`, `amount`, `date`) VALUES (".$fromAccount.", ".$toAccount.", '".$description."', ".$amount.", '".$date."')";
	return mysqlQuery($sql);
}

function updateTransaction($txnId, $desc, $from, $to, $amount, $date) {
	$sql = "UPDATE `transactions` SET `from_account` = ".$from.", `to_account` = ".$to.", `description` = '".$desc."', `amount` = '".$amount."', `date` = '".$date."' WHERE id = ".$txnId;
	return mysqlQuery($sql);
}

function deleteTransaction($txnId) {
	$sql = "DELETE FROM `transactions` WHERE id = ".$txnId;
	return mysqlQuery($sql);
}

function getBalanceByType($type) {
	$sql = "SELECT sum(amount) AS from_amount FROM transactions t JOIN accounts fa ON t.from_account = fa.id WHERE fa.type = '".$type."'";
	$txnRows = mysqlQuery($sql);
	$fromAmount = 0;
	while($txn = $txnRows->fetch_assoc()) {
		$fromAmount += $txn['from_amount'];
	}
	$sql = "SELECT sum(amount) AS to_amount FROM transactions t JOIN accounts ta ON t.to_account = ta.id WHERE ta.type = '".$type."'";
	$txnRows = mysqlQuery($sql);
	$toAmount = 0;
	while($txn = $txnRows->fetch_assoc()) {
		$toAmount += $txn['to_amount'];
	}
	return $toAmount - $fromAmount;
}

function getBalanceByAccountId($id) {
	$sql = "SELECT sum(amount) AS from_amount FROM transactions t WHERE t.from_account = '".$id."'";
	$txnRows = mysqlQuery($sql);
	$fromAmount = 0;
	while($txn = $txnRows->fetch_assoc()) {
		$fromAmount += $txn['from_amount'];
	}
	$sql = "SELECT sum(amount) AS to_amount FROM transactions t WHERE t.to_account = '".$id."'";
	$txnRows = mysqlQuery($sql);
	$toAmount = 0;
	while($txn = $txnRows->fetch_assoc()) {
		$toAmount += $txn['to_amount'];
	}
	return $toAmount - $fromAmount;
}

?>