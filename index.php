<?php

include_once "services/util.php";
include_once "services/mysql.php";
include_once "services/handler.php";
include_once "services/display.php";

// Post request handlers
newEntryHandler($_POST);
buyStuffHandler($_POST);
sellStuffHandler($_POST);
payAmountHandler($_POST);
getPaymentHandler($_POST);
newClientHandler($_POST);
updateTxnHandler($_POST);
deleteTxnHandler($_POST);

// Get Params
$txnDate = isset($_GET['txn-date']) ? $_GET['txn-date'] : null;
$txnMonth = isset($_GET['txn-month']) ? $_GET['txn-month'] : null;
$txnAccount = isset($_GET['txn-account']) ? $_GET['txn-account'] : null;
$txnAccountName = "";
if ($txnAccount != null) {
	$txnAccountName = getAccountById($txnAccount)['name'];
}

if ($txnDate == null && $txnMonth == null && $txnAccount == null) {
	$txnDate = date('Y-m-d');
}

// Getting data for the page
date_default_timezone_set('Asia/Kolkata');
$accounts = getAccounts();
$txns = getTransactions($txnAccount, $txnDate, $txnMonth);
$cashBalance = getBalanceByType('cash'); 
$clientBalance = getBalanceByType('client');
$capitalBalance = getBalanceByType('capital');
$homeBalance = getBalanceByType('home');
$profitBalance = getBalanceByType('factory');
$cashId = getAccounts('cash')[0]['id'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Ledger</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="shortcut icon" href="images/icon/icon-128.png" type="image/x-icon" />
<link id="manifest" rel="manifest" href="manifest.json">
<link rel="stylesheet" href="css/style.css?v11">
</head>
<body>
	<div class="loader" style="display:none;"></div>
	<section class="page-header">
		<h5>			
			<span class="glyphicon glyphicon-plus left collapsed" data-toggle="modal" data-target="#add-account"></span>
			<a href="/">Ledger</a>
			<?php if ($txnAccount != null) { ?>
			<span class="header-menu"><span id="newEntryButton" class="glyphicon glyphicon-edit collapsed" data-target="#newEntry" data-toggle="modal"></span></span>
			<?php } ?>
			<!-- <span class="header-menu" data-cookie="entry"><span id="entryButton" class="glyphicon glyphicon-edit collapsed" data-toggle="collapse" data-target="#entry"></span></span> -->
			<!-- <span class="header-menu" data-cookie="summary"><span id="summaryButton" class="glyphicon glyphicon-th-large collapsed" data-toggle="collapse" data-target="#summary"></span></span> -->
		</h5>
	</section>
	<section>
		<div id="add-account" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
	      			<div class="modal-header">
	      				<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h2>Add Party</h2>
					</div>
					<div class="modal-body">
						<form method="post">
						<input type="text" name="client-name" placeholder="Party Name"/>
						<input type="submit" class="btn btn-warning btn-lg" name="client-submit" value="Submit"/>
						</form>
					</div>
				</div>
			</div>
		</div>
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
		<div id="entry">
			<!-- <div class="entry">
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
					Debit
				</button>
				<button type="button" class="btn btn-warning btn-lg" data-toggle="modal" data-target="#earn">
					<span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span> 
					Credit
				</button>
			</div> -->

			<div id="newEntry" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
		      			<div class="modal-header">
		      				<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h2>Entry - <?php echo $txnAccountName; ?></h2>
						</div>
						<div class="modal-body">
							<form method="post">
							<input type="date" name="entry-date" value="<?php echo date("Y-m-d") ?>"/>
							<input type="hidden" name="entry-account" value="<?php echo $txnAccount ?>"/>
							<input type="radio" name="entry-type" id="credit" value="credit"/> 
							<label for="credit">Credit</label>
							<input type="radio" name="entry-type" id="debit" value="debit"/>
							<label for="debit">Debit</label>
							<input type="number" name="entry-amount" placeholder="Amount"/>
							<input type="text" name="entry-desc" placeholder="Item Description"/>
							<input type="submit" class="btn btn-warning btn-lg" name="entry-submit" value="Submit"/>
							</form>
						</div>
					</div>
				</div>
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
								<?php displayAccounts($accounts, 'client', null); ?>
							</select>
							<input type="number" name="buy-amount" placeholder="Amount"/>
							<input type="date" name="buy-date" value="<?php echo date("Y-m-d") ?>"/>
							<input type="submit" class="btn btn-warning btn-lg" name="buy-submit" value="Submit"/>
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
								<?php displayAccounts($accounts, 'client', null); ?>
							</select>
							<input type="number" name="sell-amount" placeholder="Amount"/>
							<input type="date" name="sell-date" value="<?php echo date("Y-m-d") ?>"/>
							<input type="submit" class="btn btn-warning btn-lg" name="sell-submit" value="Submit"/>
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
							<h2>Debit</h2>
						</div>
						<div class="modal-body">
							<form method="post">
							<input type="text" name="pay-desc" placeholder="Payment Description"/>
							<select name="pay-to">
								<option value="">Party Name</option>
								<?php displayAccounts($accounts, null, null); ?>
							</select>
							<input type="number" name="pay-amount" placeholder="Amount"/>
							<input type="date" name="pay-date" value="<?php echo date("Y-m-d") ?>"/>
							<input type="submit" class="btn btn-warning btn-lg" name="pay-submit" value="Submit"/>
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
							<h2>Credit</h2>
						</div>
						<div class="modal-body">
							<form method="post">
							<input type="text" name="earn-desc" placeholder="Item Description"/>
							<select name="earn-from">
								<option value="">Party Name</option>
								<?php displayAccounts($accounts, null, null); ?>
							</select>
							<input type="number" name="earn-amount" placeholder="Amount"/>
							<input type="date" name="earn-date" value="<?php echo date("Y-m-d") ?>"/>
							<input type="submit" class="btn btn-warning btn-lg" name="earn-submit" value="Submit"/>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section>
		<div class="txn-selector">
			<select name="txn-date">
				<?php displayDays(10, $txnDate); ?>
			</select>
			<!-- <select name="txn-month">
				<?php displayMonths(10, $txnMonth); ?>
			</select> -->
			<select name="txn-account">
				<option value="">Account</option>
				<?php displayAccounts($accounts, null, $txnAccount, true); ?>
			</select>
		</div>
		<div class="txns">
			<div class="txns-heading">
				<?php
					if($txnDate) {
						$date = date_create($txnDate);
						echo "Transactions on <strong>".date_format($date, "jS M")."</strong>";
					} else if ($txnAccount) {
						$class = "balance success";
						$balance = getBalanceByAccountId($txnAccount);
						if ($balance < 0) {
							$class = "balance failure";
						}
						echo "Transactions for <strong>".$txnAccountName."</strong>";
						echo " <span class='".$class."'> ".getMoneyFormat($balance)."</span>";
					}
				?>
			</div>
			<table>
			<?php
				if (sizeof($txns) == 0) {
					echo "<tr><th>Date</th><th>Desc</th><th>Credit</th><th>Debit</th><th>Balance</th></tr>";
					echo "<tr><td colspan='5'>No transactions</td></tr>";
				} else if ($txnAccount) {
					echo "<tr><th>Date</th><th>Desc</th><th>Credit</th><th>Debit</th><th>Balance</th></tr>";
					displayAccountTxns($txns, $txnAccount);
				} else {
					echo "<tr><th>Date</th><th>Client</th><th>Desc</th><th>Credit</th><th>Debit</th></tr>";
					displayDateTxns($txns);
				}
			?>
			</table>

			<?php for($i = 0; $i < sizeof($txns); $i++) { 
				$txn = $txns[$i];
				$tAccountId = $txn['to_account_id'];
				if ($txn['to_account_id'] == $cashId) { 
					$tAccountId = $txn['from_account_id']; 
				}
				$tAccountName = getAccountById($tAccountId)['name'];
			?>
			<div id="txn-<?php echo $i; ?>" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
	    	  			<div class="modal-header">
	    	  				<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h2>Update - <?php echo $tAccountName; ?></h2>
						</div>
						<div class="modal-body">
							<form method="post">
							<input type="hidden" name="txn-id" value="<?php echo $txn['id']; ?>"/>
							<?php $d = date_create($txn['date']); ?>
							<input type="date" name="txn-date" value="<?php echo date_format($d, 'Y-m-d'); ?>"/>
							<input type="radio" name="txn-type" id="credit" value="credit" <?php if ($txn['from_account_id'] == $cashId) { echo "checked"; } ?>/> 
							<label for="credit">Credit</label>
							<input type="radio" name="txn-type" id="debit" value="debit" <?php if ($txn['to_account_id'] == $cashId) { echo "checked"; } ?>/>
							<label for="debit">Debit</label>
							<input type="number" name="txn-amount" placeholder="Amount" value="<?php echo $txn['amount']; ?>"/>
							<input type="hidden" name="txn-account" value="<?php echo $tAccountId; ?>"/> 
							<input type="text" name="txn-desc" placeholder="Item Description" value="<?php echo $txn['description']; ?>"/>
							<input type="hidden" name="txn-delete-submit" value=""/>
							<input type="submit" class="btn btn-danger btn-lg half" data-index="<?php echo $i; ?>" name="txn-delete-confirm" value="Delete" data-dismiss="modal"/>							
							<input type="submit" class="btn btn-warning btn-lg half" name="txn-update-submit" value="Update"/>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div id="confirm-<?php echo $i; ?>" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
	    	  			<div class="modal-header">
							<h2>Confirm delete?</h2>
						</div>
						<div class="modal-footer">
							<button type="button" data-dismiss="modal" class="btn btn-danger delete">Delete</button>
							<button type="button" data-dismiss="modal" class="btn">Cancel</button>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</section>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	<script type="text/javascript" src="js/script.js"></script>
</body>
</html>