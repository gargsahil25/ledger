<?php

include_once "services/mysql.php";
include_once "services/handler.php";
include_once "services/display.php";

$account = getAccountById($_GET['id']);
$txns = getTransactions($_GET['id']);

?>

<html>
<head>
<title>Ledger - <?php echo $account['name']; ?></title>
<link rel="stylesheet" href="css/style.css"> 
</head>
<body>
	<section>
		<h1>Ledger - <?php echo $account['name']; ?>
		<span class="summary"><span class="title">Cash</span> <?php echo getBalanceByType('cash'); ?></span>
		<span class="summary"><span class="title">Profit</span> <?php echo getBalanceByType('factory') * -1; ?></span> 
		<span class="summary"><span class="title">Home</span> <?php echo getBalanceByType('home'); ?></span>
		<span class="summary"><span class="title">Client</span> <?php echo getBalanceByType('client'); ?></span>
		<span class="nav-links"><a href="./index.php">Home</a> . <a href="./accounts.php">Accounts</a></span></h1>
		<hr>
	</section>
	<section>
		<div class="txns">
			<table>
			<tr><th>Date</th><th>From</th><th>To</th><th>Description</th><th>Amount</th><th>Balance</th></tr>
			<?php displayTxnsWithBalance($txns, $_GET['id']); ?>
			</table>
		</div>
	</section>
</body>
</html>