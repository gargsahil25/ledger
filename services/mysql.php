<?php 

include_once "constant.php";
include_once "util.php";
include_once "sessionUtil.php";

$ALL_ACCOUNTS = array();
$LOGGED_IN_USER = getLoggedInUser();

function mysqlQuery($sql) {
	
	$servername = "localhost:3306";
	$username = "root";
	$password = "root@123";
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

	// echo $sql.'<br><br>';
	$result = $conn->query($sql);

	if(!$result) {
	    echo "Error: " . $sql . "<br>" . $conn->error;
	}

	preg_match('/INSERT INTO/', $sql, $matches);
	if (sizeof($matches) > 0) {
		$id = $conn->insert_id;
		$conn->close();
		return $id;
	}

	$conn->close();

	return $result;
}

function getUserByPassword($password) {
	$sql = "SELECT * FROM users WHERE password = '".$password."'";
	$userRows = mysqlQuery($sql);
	$user = $userRows->fetch_assoc();
	return $user;
}

function getAccounts() {
	global $ALL_ACCOUNTS;
	global $LOGGED_IN_USER;

	if (sizeof($ALL_ACCOUNTS) > 0) {
		return $ALL_ACCOUNTS;
	}
	$sql = "SELECT * FROM accounts WHERE user_id = ".$LOGGED_IN_USER['userId']." ORDER BY type, name";
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
	return mysqlQuery($sql);
}

function updateAccount($accountId, $accountName) {
	global $LOGGED_IN_USER;
	$sql = "UPDATE `accounts` SET `name` = '".$accountName."' WHERE id = ".$accountId.
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

function getTransactions($txnAccount = null, $txnDate = null, $txnMonth = null) {
	global $LOGGED_IN_USER;
	$sql = "SELECT t.id AS id, t.date AS date, t.description AS description, t.amount AS amount, fa.id AS from_account_id, fa.name AS from_account_name, ta.id AS to_account_id, ta.name AS to_account_name FROM transactions t JOIN accounts fa ON t.from_account = fa.id JOIN accounts ta ON t.to_account = ta.id WHERE t.is_deleted = 0 AND fa.user_id = ".$LOGGED_IN_USER['userId']." AND ta.user_id = ".$LOGGED_IN_USER['userId'];

	if ($txnAccount) {
		$sql .= " AND (t.from_account = ".$txnAccount." OR t.to_account = ".$txnAccount.")";
	}
	if ($txnDate) {
		$date = date_create($txnDate);
		$sql .= " AND t.date LIKE '".date_format($date, "Y-m-d")."%'";
	}
	if ($txnMonth) {
		$date = date_create($txnMonth);
		$sql .= " AND t.date LIKE '".date_format($date, "Y-m")."%'";
	}

	$sql .= " ORDER BY date desc";
	$txnRows = mysqlQuery($sql);
	$txns = array();
	while($txn = $txnRows->fetch_assoc()) {
		array_push($txns, $txn);
	}
	return $txns;
}

function addTransaction($fromAccount, $toAccount, $description, $amount, $date) {
	$sql = "INSERT INTO `transactions` (`from_account`, `to_account`, `description`, `amount`, `date`) 
			VALUES (".$fromAccount.", ".$toAccount.", '".$description."', ".$amount.", '".$date."')";
	mysqlQuery($sql);
	updateAccountBalance($fromAccount);
	updateAccountBalance($toAccount);
}

function updateTransaction($txnId, $desc, $from, $to, $amount, $date) {
	$sql = "UPDATE `transactions` SET `from_account` = ".$from.", `to_account` = ".$to.", `description` = '".$desc."', `amount` = '".$amount."', `date` = '".$date."' WHERE id = ".$txnId;
	mysqlQuery($sql);
	updateAccountBalance($from);
	updateAccountBalance($to);
}

function deleteTransaction($txnId) {
	$txnRows = mysqlQuery("SELECT * FROM `transactions` WHERE id = ".$txnId);
	$txn = $txnRows->fetch_assoc();
	$sql = "UPDATE `transactions` SET `is_deleted` = 1 WHERE id = ".$txnId;
	mysqlQuery($sql);
	updateAccountBalance($txn['from_account']);
	updateAccountBalance($txn['to_account']);
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
	return $toAmount - $fromAmount;
}

function getBalanceByAccountId($id, $date = null) {
	if (!$id) {
		return;
	}
	$sql = "SELECT sum(amount) AS from_amount FROM transactions t WHERE t.from_account = '".$id."' AND t.is_deleted = 0";
	if ($date) {
		$sql .= " AND date <= '".date_format(date_create($date), "Y-m-d")." 99:99:99'";
	}
	$txnRows = mysqlQuery($sql);
	$fromAmount = 0;
	while($txn = $txnRows->fetch_assoc()) {
		$fromAmount += $txn['from_amount'];
	}
	$sql = "SELECT sum(amount) AS to_amount FROM transactions t WHERE t.to_account = '".$id."' AND t.is_deleted = 0";
	if ($date) {
		$sql .= " AND date <= '".date_format(date_create($date), "Y-m-d")." 99:99:99'";
	}
	$txnRows = mysqlQuery($sql);
	$toAmount = 0;
	while($txn = $txnRows->fetch_assoc()) {
		$toAmount += $txn['to_amount'];
	}
	return $toAmount - $fromAmount;
}

?>