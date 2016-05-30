<?php

include_once "services/mysql.php";
include_once "services/handler.php";
include_once "services/display.php";

buyStuffHandler($_POST);
sellStuffHandler($_POST);
payAmountHandler($_POST);
getPaymentHandler($_POST);

$accounts = getAccounts();
$txns = getTransactions();

date_default_timezone_set('Asia/Kolkata');

?>

<html>
<head>
<title>Ledger</title>
<link rel="stylesheet" href="css/style.css"> 
</head>
<body>
	<section>
		<h1>Ledger
		<span class="summary"><span class="title">Cash</span> <?php echo getBalanceByType('cash'); ?></span>
		<span class="summary"><span class="title">Profit</span> <?php echo getBalanceByType('factory') * -1; ?></span> 
		<span class="summary"><span class="title">Capital</span> <?php echo getBalanceByType('capital'); ?></span>
		<span class="summary"><span class="title">Home</span> <?php echo getBalanceByType('home'); ?></span>
		<span class="summary"><span class="title">Client</span> <?php echo getBalanceByType('client'); ?></span>
		<span class="nav-links"><a href="./accounts.php">Accounts</a><span></h1>
		<hr>
	</section>
	<section>
		<div class="main-block form">
			<h2>Buy Stuff</h2>
			<form method="post">
			<input type="text" name="buy-desc" placeholder="Item Description"/>
			<select name="buy-from">
				<option value="">Buy from</option>
				<?php displayAccount($accounts, 'client'); ?>
			</select>
			<input type="number" name="buy-amount" placeholder="Amount"/>
			<input type="date" name="buy-date" value="<?php echo date("Y-m-d") ?>"/>
			<input type="submit" name="buy-submit" value="Submit"/>
			</form>
		</div>
		<div class="main-block form">
			<h2>Pay Amount</h2>
			<form method="post">
			<input type="text" name="pay-desc" placeholder="Payment Description"/>
			<select name="pay-to">
				<option value="">Pay to</option>
				<?php displayAccount($accounts, 'capital'); ?>
				<?php displayAccount($accounts, 'home'); ?>
				<?php displayAccount($accounts, 'factory'); ?>
				<?php displayAccount($accounts, 'client'); ?>
			</select>
			<input type="number" name="pay-amount" placeholder="Amount"/>
			<input type="date" name="pay-date" value="<?php echo date("Y-m-d") ?>"/>
			<input type="submit" name="pay-submit" value="Submit"/>
			</form>
		</div>
		<div class="main-block form">
			<h2>Sell Stuff</h2>
			<form method="post">
			<input type="text" name="sell-desc" placeholder="Item Description"/>
			<select name="sell-to">
				<option value="">Sell to</option>
				<?php displayAccount($accounts, 'client'); ?>
			</select>
			<input type="number" name="sell-amount" placeholder="Amount"/>
			<input type="date" name="sell-date" value="<?php echo date("Y-m-d") ?>"/>
			<input type="submit" name="sell-submit" value="Submit"/>
			</form>
		</div>
		<div class="main-block form">
			<h2>Get Payment</h2>
			<form method="post">
			<input type="text" name="payment-desc" placeholder="Payment Description"/>
			<select name="payment-from">
				<option value="">Payment from</option>
				<?php displayAccount($accounts, 'capital'); ?>
				<?php displayAccount($accounts, 'home'); ?>
				<?php displayAccount($accounts, 'client'); ?>
			</select>
			<input type="number" name="payment-amount" placeholder="Amount"/>
			<input type="date" name="payment-date" value="<?php echo date("Y-m-d") ?>"/>
			<input type="submit" name="payment-submit" value="Submit"/>
			</form>
		</div>
	</section>
	<section>
		<div class="txns">
			<table>
			<tr><th>Date</th><th>From</th><th>To</th><th>Description</th><th>Amount</th></tr>
			<?php displayTxns($txns); ?>
			</table>
		</div>
	</section>
</body>
</html>