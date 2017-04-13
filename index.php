<?php

include_once "services/util.php";
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

$cashBalance = getBalanceByType('cash'); 
$clientBalance = getBalanceByType('client');

?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Ledger</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="css/style.css?v1"> 
</head>
<body>
	<div class="page-header">
		<h5>Ledger
	</div>
	<div class="summary">
		<div class="label label-success"><span class="title">Cash</span><?php echo getMoneyFormat($cashBalance); ?></div>
		<div class="label <?php if ($clientBalance >= 0) { echo 'label-success'; } else { echo 'label-danger'; } ?>"><span class="title">Client</span><?php echo getMoneyFormat($clientBalance); ?></div>
	</div>
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
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>
</html>