<?php

include_once "services/util.php";
include_once "services/mysql.php";
include_once "services/handler.php";
include_once "services/display.php";

// Post request handlers
buyStuffHandler($_POST);
sellStuffHandler($_POST);
payAmountHandler($_POST);
getPaymentHandler($_POST);

// Getting data for the page
date_default_timezone_set('Asia/Kolkata');
$accounts = getAccounts();
$txns = getTransactions();
$cashBalance = getBalanceByType('cash'); 
$clientBalance = getBalanceByType('client');
$capitalBalance = getBalanceByType('capital');
$homeBalance = getBalanceByType('home');
$profitBalance = getBalanceByType('factory');

?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Ledger</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="css/style.css?v4"> 
</head>
<body>
	<section class="page-header">
		<h5>Ledger
			<span class="header-menu" data-cookie="entry"><span id="entryButton" class="glyphicon glyphicon-edit collapsed" data-toggle="collapse" data-target="#entry"></span></span>
			<span class="header-menu" data-cookie="summary"><span id="summaryButton" class="glyphicon glyphicon-th-large collapsed" data-toggle="collapse" data-target="#summary"></span></span>
		</h5>
	</section>
	<section class="summary">
		<div id="summary" class="collapse">
			<div class="label <?php if ($cashBalance >= 0) { echo 'label-success'; } else { echo 'label-danger'; } ?>"><span class="title">Cash</span><?php echo getMoneyFormat($cashBalance); ?></div>
			<div class="label <?php if ($clientBalance >= 0) { echo 'label-success'; } else { echo 'label-danger'; } ?>"><span class="title">Party</span><?php echo getMoneyFormat($clientBalance); ?></div>
			<div class="label <?php if ($capitalBalance >= 0) { echo 'label-success'; } else { echo 'label-danger'; } ?>"><span class="title">Capital</span><?php echo getMoneyFormat($capitalBalance); ?></div>
			<div class="label <?php if ($homeBalance >= 0) { echo 'label-success'; } else { echo 'label-danger'; } ?>"><span class="title">Home</span><?php echo getMoneyFormat($homeBalance); ?></div>
			<div class="label full <?php if ($profitBalance >= 0) { echo 'label-success'; } else { echo 'label-danger'; } ?>"><span class="title">Profit/Loss</span><?php echo getMoneyFormat($profitBalance).' + Stock'; ?></div>
		</div>
	</section>
	<section class="entry-container">
		<div id="entry" class="collapse">
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
	<script type="text/javascript" src="js/script.js"></script>
</body>
</html>