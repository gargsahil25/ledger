<?php 

include_once "constant.php";
include_once "util.php";
include_once "sessionUtil.php";
// include_once "sendEmail.php";

date_default_timezone_set('Asia/Kolkata');

$ALL_ACCOUNTS = array();
$LOGGED_IN_USER = getLoggedInUser();

function mysqlConn() {
	$servername = "localhost:3306";
	$username = "root";
	$password = "";
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
	$sql = "SELECT id, name FROM users";
	$userRows = mysqlQuery($sql);
	$users = array();
	while($u = $userRows->fetch_assoc()) {
		array_push($users, $u);
	}
	return $users;
}

function getUserByPassword($userId, $password) {
	$sql = "SELECT * FROM users WHERE id= '".$userId."' AND password = '".$password."'";
	$userRows = mysqlQuery($sql);
	$user = $userRows->fetch_assoc();
	return $user;
}

function getAccounts($userId = null) {
	global $ALL_ACCOUNTS;
	global $LOGGED_IN_USER;
	if ($userId == null) {
		$userId = $LOGGED_IN_USER['userId'];
	}
	if (sizeof($ALL_ACCOUNTS) > 0) {
		return $ALL_ACCOUNTS;
	}
	$sql = "SELECT * FROM accounts WHERE user_id = ".$userId." ORDER BY type, name";
	$accountRows = mysqlQuery($sql);
	$ALL_ACCOUNTS = array();
	while($account = $accountRows->fetch_assoc()) {
		$account['original_name'] = $account['name'];
		$account['name'] = getLangText($account['name']);
		array_push($ALL_ACCOUNTS, $account);
	}
	return $ALL_ACCOUNTS;
}

function getAccountByName($name) {
	global $ALL_ACCOUNTS;
	if (sizeof($ALL_ACCOUNTS) == 0) {
		$ALL_ACCOUNTS = getAccounts();
	}
	for($i=0; $i < sizeof($ALL_ACCOUNTS); $i++) {
		if ($ALL_ACCOUNTS[$i]['original_name'] == $name) {
			return $ALL_ACCOUNTS[$i];
		}
	}
}

function getCashAccountId() {
	return getAccountByName('CASH')['id'];
}

function getStockAccountId() {
	return getAccountByName('STOCK')['id'];
}

function getAccountById($id) {
	global $ALL_ACCOUNTS;
	if (sizeof($ALL_ACCOUNTS) == 0) {
		$ALL_ACCOUNTS = getAccounts();
	}
	for($i=0; $i < sizeof($ALL_ACCOUNTS); $i++) {
		if ($ALL_ACCOUNTS[$i]['id'] == $id) {
			return $ALL_ACCOUNTS[$i];
		}
	}
}

function addAccount($name, $type) {
	global $LOGGED_IN_USER;
	$sql = "INSERT INTO `accounts` (`user_id`, `name`, `type`) 
			VALUES (".$LOGGED_IN_USER['userId'].", '".$name."', '".$type."')";
	$resp = mysqlQuery($sql);
	$msg = "Adding new account with name: ".$name." and type: ".$type;
	// sendEmail($LOGGED_IN_USER, $msg, $sql, $resp);
	return $resp;
}

function updateAccount($accountId, $accountName, $type) {
	global $LOGGED_IN_USER;
	$sql = "UPDATE `accounts` SET `name` = '".$accountName."', `type` = '".$type."' WHERE id = ".$accountId.
				" AND user_id = ".$LOGGED_IN_USER['userId'];
	return mysqlQuery($sql);
}

function updateAccountBalance($accountId) {
	global $LOGGED_IN_USER;
	$balance = getBalanceByAccountId($accountId);
	$sql = "UPDATE `accounts` SET `balance` = ".$balance." WHERE id = ".$accountId.
				" AND user_id = ".$LOGGED_IN_USER['userId'];
	mysqlQuery($sql);
	return $balance;
}

function getTransactions($txnAccount = null, $txnDate = null, $txnMonth = null, $showDeleted = false, $userId = null, $sortByCreatedDate = 0) {
	global $LOGGED_IN_USER;
	if ($userId == null) {
		$userId = $LOGGED_IN_USER['userId'];
	}
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

function addTransaction($fromAccount, $toAccount, $description, $amount, $date, $updateBalance = true) {
	$sql = "INSERT INTO `transactions` (`from_account`, `to_account`, `description`, `amount`, `date`) 
			VALUES (".$fromAccount.", ".$toAccount.", '".$description."', ".$amount.", '".$date."')";
	mysqlQuery($sql);
	if ($updateBalance) {
		updateAccountBalance($fromAccount);
		updateAccountBalance($toAccount);
	}
}

function updateTransaction($txnId, $desc, $from, $to, $amount, $date, $fromOld, $toOld) {
	addTransaction($from, $to, $desc, $amount, $date, false);
	deleteTransaction($txnId, $from, $to);
	if ($from != $fromOld) {
		updateAccountBalance($fromOld);
	}
	if ($to != $toOld) {
		updateAccountBalance($toOld);
	}
}

function deleteTransaction($txnId, $from, $to) {
	$sql = "UPDATE `transactions` SET `is_deleted` = 1 WHERE id = ".$txnId;
	mysqlQuery($sql);
	updateAccountBalance($from);
	updateAccountBalance($to);
}

function getBalanceByType($type) {
	global $LOGGED_IN_USER;
	$sql = "SELECT sum(amount) AS from_amount FROM transactions t JOIN accounts fa ON t.from_account = fa.id WHERE fa.type = '".$type."' AND t.is_deleted = 0 fa.user_id = ".$LOGGED_IN_USER['userId'];
	$txnRows = mysqlQuery($sql);
	$fromAmount = 0;
	while($txn = $txnRows->fetch_assoc()) {
		$fromAmount += $txn['from_amount'];
	}
	$sql = "SELECT sum(amount) AS to_amount FROM transactions t JOIN accounts ta ON t.to_account = ta.id WHERE ta.type = '".$type."' AND t.is_deleted = 0 AND ta.user_id = ".$LOGGED_IN_USER['userId'];
	$txnRows = mysqlQuery($sql);
	$toAmount = 0;
	while($txn = $txnRows->fetch_assoc()) {
		$toAmount += $txn['to_amount'];
	}

	$factoryId = getStockAccountId();
	$cashId = getCashAccountId();

	if ($id == $factoryId || $id == $cashId) {
		return $toAmount - $fromAmount;
	}
	return $fromAmount - $toAmount;
}

function getBalanceByAccountId($id, $date = null) {
	if (!$id) {
		return;
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
	$factoryId = getStockAccountId();
	$cashId = getCashAccountId();

	if ($id == $factoryId || $id == $cashId) {
		return $toAmount - $fromAmount;
	}
	return $fromAmount - $toAmount;
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

?>