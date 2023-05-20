<?php 

include_once "constant.php";
include_once "util.php";
include_once "sessionUtil.php";

date_default_timezone_set('Asia/Kolkata');

$ALL_ACCOUNTS = array();
$ALL_ACCOUNTS_USER_ID = null;

function mysqlConn() {
	$servername = "localhost:3306";
	$username = "root";
	$password = "";
	$dbname = "ledger";

	$env_servername = getenv("AZURE_MYSQL_HOST");
	$env_username = getenv("AZURE_MYSQL_USERNAME");
	$env_password = getenv("AZURE_MYSQL_PASSWORD");
	$env_dbname = getenv("AZURE_MYSQL_DBNAME");
	if ($env_servername && $env_username && $env_password && $env_dbname) {
		$servername = $env_servername;
		$username = $env_username;
		$password = $env_password;
		$dbname = $env_dbname;
	}	

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 

	mysqli_set_charset($conn,"latin1");

	return $conn;
}

function mysqlQuery($sql) {

	$conn = mysqlConn();
	
	// echo $sql.'<br><br>';
	$result = $conn->query($sql);

	if(!$result) {
	    echo "Error: " . $sql . "<br>" . $conn->error;
	}

	preg_match('/INSERT INTO/', $sql, $matches);
	if (sizeof($matches) > 0) {
		$id = $conn->insert_id;
		// $conn->close();
		return $id;
	}

	//$conn->close();

	return $result;
}

function getAllUsers() {
	$sql = "SELECT id, name, profit FROM users";
	$userRows = mysqlQuery($sql);
	$users = array();
	while($u = $userRows->fetch_assoc()) {
		array_push($users, $u);
	}
	return $users;
}

function getUserByPassword($userId, $password) {
	$sql = "SELECT * FROM users WHERE id = '".$userId."' AND password = '".$password."'";
	$userRows = mysqlQuery($sql);
	$user = $userRows->fetch_assoc();
	return $user;
}

function getUserById($userId) {
	$sql = "SELECT id, name, profit FROM users WHERE id = '".$userId."'";
	$userRows = mysqlQuery($sql);
	$user = $userRows->fetch_assoc();
	return $user;
}

function getAccounts($userId) {
	global $ALL_ACCOUNTS;
	global $ALL_ACCOUNTS_USER_ID;
	if (sizeof($ALL_ACCOUNTS) > 0 && $ALL_ACCOUNTS_USER_ID == $userId) {
		return $ALL_ACCOUNTS;
	}
	$sql = "SELECT * FROM accounts WHERE user_id = ".$userId." ORDER BY name";
	$accountRows = mysqlQuery($sql);
	$ALL_ACCOUNTS = array();
	$ALL_ACCOUNTS_USER_ID = $userId;
	while($account = $accountRows->fetch_assoc()) {
		$account['original_name'] = $account['name'];
		$account['name'] = getLangText($account['name']);
		array_push($ALL_ACCOUNTS, $account);
	}
	return $ALL_ACCOUNTS;
}

function getAccountByName($name, $userId) {
	$allAccounts = getAccounts($userId);
	for($i=0; $i < sizeof($allAccounts); $i++) {
		if ($allAccounts[$i]['original_name'] == $name) {
			return $allAccounts[$i];
		}
	}
}

function getAccountById($id, $userId) {
	$allAccounts = getAccounts($userId);
	for($i=0; $i < sizeof($allAccounts); $i++) {
		if ($allAccounts[$i]['id'] == $id) {
			return $allAccounts[$i];
		}
	}
}

function getCashAccountId($userId) {
	return getAccountByName('CASH', $userId)['id'];
}

function getStockAccountId($userId) {
	return getAccountByName('STOCK', $userId)['id'];
}

function addAccount($userId, $name, $type) {
	$sql = "INSERT INTO `accounts` (`user_id`, `name`, `type`) 
			VALUES (".$userId.", '".$name."', '".$type."')";
	$resp = mysqlQuery($sql);
	$msg = "Adding new account with name: ".$name." and type: ".$type;
	return $resp;
}

function updateAccount($userId, $accountId, $accountName, $type) {
	$sql = "UPDATE `accounts` SET `name` = '".$accountName."', `type` = '".$type."' WHERE id = ".$accountId.
				" AND user_id = ".$userId;
	return mysqlQuery($sql);
}

function updateAccountBalance($accountId, $userId) {
	$balance = getBalanceByAccountId($accountId, null, $userId)['balance'];
	$sql = "UPDATE `accounts` SET `balance` = ".$balance." WHERE id = ".$accountId.
				" AND user_id = ".$userId;
	mysqlQuery($sql);
	return $balance;
}

function getTransactions($txnAccount = null, $txnDate = null, $txnMonth = null, $showDeleted = false, $userId, $sortByCreatedDate = 0) {
	$sql = "SELECT 
			t.id AS id, 
			t.date AS date, 
			t.description AS description, 
			t.amount AS amount, 
			fa.id AS from_account_id, 
			fa.name AS from_account_name, 
			fa.type AS from_account_type, 
			ta.id AS to_account_id, 
			ta.name AS to_account_name, 
			ta.type AS to_account_type, 
			t.is_deleted AS is_deleted,
			t.created_date AS created_date
		FROM transactions t 
			JOIN accounts fa ON t.from_account = fa.id 
			JOIN accounts ta ON t.to_account = ta.id 
		WHERE 
			fa.user_id = ".$userId." 
			AND ta.user_id = ".$userId;

	if (!$showDeleted) {
		$sql .= " AND t.is_deleted = 0"; 
	}

	if ($txnAccount) {
		$sql .= " AND (t.from_account = ".$txnAccount." OR t.to_account = ".$txnAccount.")";
	}
	if ($txnDate) {
		$date = date_create($txnDate);
		$sql .= " AND t.date LIKE '".getDateFormat($date, "Y-m-d")."%'";
	}
	if ($txnMonth) {
		$date = date_create($txnMonth);
		$sql .= " AND t.date LIKE '".getDateFormat($date, "Y-m")."%'";
	}

	$sql .= " ORDER BY ";

	if ($sortByCreatedDate) {
		$sql .= "t.created_date desc";
	} else {
		$sql .= "date desc";
	}
	
	$txnRows = mysqlQuery($sql);
	$txns = array();
	while($txn = $txnRows->fetch_assoc()) {
		array_push($txns, $txn);
	}
	return $txns;
}

function addTransaction($userId, $fromAccount, $toAccount, $description, $amount, $date, $updateBalance = true) {
	$sql = "INSERT INTO `transactions` (`from_account`, `to_account`, `description`, `amount`, `date`) 
			VALUES (".$fromAccount.", ".$toAccount.", '".$description."', ".$amount.", '".$date."')";
	mysqlQuery($sql);
	if ($updateBalance) {
		updateAccountBalance($fromAccount, $userId);
		updateAccountBalance($toAccount, $userId);
	}
}

function updateTransaction($userId, $txnId, $desc, $from, $to, $amount, $date, $fromOld, $toOld) {
	addTransaction($userId, $from, $to, $desc, $amount, $date, false);
	deleteTransaction($userId, $txnId, $from, $to);
	if ($from != $fromOld) {
		updateAccountBalance($fromOld, $userId);
	}
	if ($to != $toOld) {
		updateAccountBalance($toOld, $userId);
	}
}

function deleteTransaction($userId, $txnId, $from, $to) {
	$sql = "UPDATE `transactions` SET `is_deleted` = 1 WHERE id = ".$txnId;
	mysqlQuery($sql);
	updateAccountBalance($from, $userId);
	updateAccountBalance($to, $userId);
}

function getBalanceByType($type, $userId) {
	$sql = "SELECT sum(amount) AS from_amount FROM transactions t JOIN accounts fa ON t.from_account = fa.id WHERE fa.type = '".$type."' AND t.is_deleted = 0 fa.user_id = ".$userId;
	$txnRows = mysqlQuery($sql);
	$fromAmount = 0;
	while($txn = $txnRows->fetch_assoc()) {
		$fromAmount += $txn['from_amount'];
	}
	$sql = "SELECT sum(amount) AS to_amount FROM transactions t JOIN accounts ta ON t.to_account = ta.id WHERE ta.type = '".$type."' AND t.is_deleted = 0 AND ta.user_id = ".$userId;
	$txnRows = mysqlQuery($sql);
	$toAmount = 0;
	while($txn = $txnRows->fetch_assoc()) {
		$toAmount += $txn['to_amount'];
	}

	$factoryId = getStockAccountId($userId);
	$cashId = getCashAccountId($userId);

	if ($id == $factoryId || $id == $cashId) {
		return $toAmount - $fromAmount;
	}
	return $fromAmount - $toAmount;
}

function getBalanceByAccountId($id, $date = null, $userId) {
	$amounts = array(
		"credit" => 0,
		"debit" => 0,
		"balance" => 0
	);

	if (!$id) {
		return $amounts;
	}

	$sql = "SELECT sum(amount) AS from_amount FROM transactions t WHERE t.from_account = '".$id."' AND t.is_deleted = 0";
	if ($date) {
		$sql .= " AND date <= '".getDateFormat($date, "Y-m-d")." 23:59:59'";
	}
	$txnRows = mysqlQuery($sql);
	$fromAmount = 0;
	while($txn = $txnRows->fetch_assoc()) {
		$fromAmount += $txn['from_amount'];
	}
	$sql = "SELECT sum(amount) AS to_amount FROM transactions t WHERE t.to_account = '".$id."' AND t.is_deleted = 0";
	if ($date) {
		$sql .= " AND date <= '".getDateFormat($date, "Y-m-d")." 23:59:59'";
	}
	$txnRows = mysqlQuery($sql);
	$toAmount = 0;
	while($txn = $txnRows->fetch_assoc()) {
		$toAmount += $txn['to_amount'];
	}
	$factoryId = getStockAccountId($userId);
	$cashId = getCashAccountId($userId);

	if ($id == $factoryId || $id == $cashId) {
		$amounts['credit'] = $toAmount;
		$amounts['debit'] = $fromAmount;
		$amounts['balance'] = $toAmount - $fromAmount;
		return $amounts;
	}
	$amounts['credit'] = $fromAmount;
	$amounts['debit'] = $toAmount;
	$amounts['balance'] = $fromAmount - $toAmount;
	return $amounts;
}

function getTransactionsForAllUsers() {
	$sql = "SELECT 
			t.id AS id, t.date AS date, 
			t.description AS description, 
			t.amount AS amount, 
			fa.id AS from_account_id, 
			fa.name AS from_account_name, 
			fa.type AS from_account_type, 
			ta.id AS to_account_id, 
			ta.name AS to_account_name, 
			ta.type AS to_account_type,
			t.created_date AS created_date,
			u.id AS user_id,
			u.name AS user_name
		FROM transactions t 
			JOIN accounts fa ON t.from_account = fa.id 
			JOIN accounts ta ON t.to_account = ta.id 
			JOIN users u ON fa.user_id = u.id
		WHERE t.is_deleted = 0 
			AND t.date > '2021-04-01'
		ORDER BY date asc";

	$txnRows = mysqlQuery($sql);
	$txns = array();
	while($txn = $txnRows->fetch_assoc()) {
		array_push($txns, $txn);
	}
	return $txns;
}

function getTransactionsByUserId($userId) {
	$sql = "SELECT 
			t.id AS id, t.date AS date, 
			t.description AS description, 
			t.amount AS amount, 
			fa.id AS from_account_id, 
			fa.name AS from_account_name, 
			fa.type AS from_account_type, 
			ta.id AS to_account_id, 
			ta.name AS to_account_name, 
			ta.type AS to_account_type,
			t.created_date AS created_date
		FROM transactions t 
			JOIN accounts fa ON t.from_account = fa.id 
			JOIN accounts ta ON t.to_account = ta.id 
		WHERE t.is_deleted = 0 
			AND fa.user_id = ".$userId."
			AND ta.user_id = ".$userId."
			AND t.date > '2010-04-01'
		ORDER BY date asc";

	$txnRows = mysqlQuery($sql);
	$txns = array();
	while($txn = $txnRows->fetch_assoc()) {
		array_push($txns, $txn);
	}
	return $txns;
}

?>
