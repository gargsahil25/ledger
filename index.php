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
<link rel="stylesheet" href="css/style.css?v2"> 
</head>
<body>
	<section class="page-header">
		<h5>Ledger<h5>
	</section>
	<section class="summary">
		<div class="label label-success"><span class="title">Cash</span><?php echo getMoneyFormat($cashBalance); ?></div>
		<div class="label <?php if ($clientBalance >= 0) { echo 'label-success'; } else { echo 'label-danger'; } ?>"><span class="title">Client</span><?php echo getMoneyFormat($clientBalance); ?></div>
	</section>
	<section>
		<div class="entry">
			<button type="button" class="btn btn-warning btn-lg" data-toggle="modal" data-target="#purchase">
				<span class="glyphicon glyphicon-object-align-left" aria-hidden="true"></span> 
				Purchase
			</button>
			<button type="button" class="btn btn-warning btn-lg" data-toggle="modal" data-target="#sale">
				<span class="glyphicon glyphicon-object-align-right" aria-hidden="true"></span> 
				Sale
			</button>
			<button type="button" class="btn btn-warning btn-lg" data-toggle="modal" data-target="#pay">
				<span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span> 
				Payment
			</button>
			<button type="button" class="btn btn-warning btn-lg" data-toggle="modal" data-target="#earn">
				<span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span> 
				Earn
			</button>
		</div>
		
		<div id="purchase" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
	      			<div class="modal-header">
	      				<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h2>Purchase</h2>
					</div>
					<div class="modal-body">
						<form method="post">
						<input type="text" name="buy-desc" placeholder="Item Description"/>
						<select name="buy-from">
							<option value="">Purchase from</option>
							<?php displayAccount($accounts, 'client'); ?>
						</select>
						<input type="number" name="buy-amount" placeholder="Amount"/>
						<input type="date" name="buy-date" value="<?php echo date("Y-m-d") ?>"/>
						<input type="submit" name="buy-submit" value="Submit"/>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div id="sale" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
	      			<div class="modal-header">
	      				<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h2>Sale</h2>
					</div>
					<div class="modal-body">
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
				</div>
			</div>
		</div>
		<div id="pay" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
	      			<div class="modal-header">
	      				<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h2>Pay</h2>
					</div>
					<div class="modal-body">
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
				</div>
			</div>
		</div>
		<div id="earn" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
	      			<div class="modal-header">
	      				<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h2>Earn</h2>
					</div>
					<div class="modal-body">
						<form method="post">
						<input type="text" name="buy-desc" placeholder="Item Description"/>
						<select name="buy-from">
							<option value="">Earn from</option>
							<?php displayAccount($accounts, 'client'); ?>
						</select>
						<input type="number" name="buy-amount" placeholder="Amount"/>
						<input type="date" name="buy-date" value="<?php echo date("Y-m-d") ?>"/>
						<input type="submit" name="buy-submit" value="Submit"/>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section>
		<div class="txns">
			<table>
			<tr><th>Date</th><th>From</th><th>To</th><th>Desc</th><th>Amount</th></tr>
			<?php displayTxns($txns); ?>
			</table>
		</div>
	</section>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>
</html>