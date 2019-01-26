<?php

include_once "services/constant.php";
include_once "services/util.php";
include_once "services/mysql.php";
include_once "services/handler.php";
include_once "services/display.php";

// Post request handlers
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

if ($txnDate == null && $txnMonth == null && $txnAccount == null) {
	$txnDate = date('Y-m-d');
}

// Getting data for the page
date_default_timezone_set('Asia/Kolkata');
$accounts = getAccounts();
$txns = getTransactions($txnAccount, $txnDate, $txnMonth);

$account = getAccountById($txnAccount);
$balance = getBalanceByAccountId($txnAccount);

// $cashBalance = getBalanceByType('cash'); 
// $clientBalance = getBalanceByType('client');
// $capitalBalance = getBalanceByType('capital');
// $homeBalance = getBalanceByType('home');
// $profitBalance = getBalanceByType('factory');

/**
 * PROFIT: (ActualFactoryMallValue - FactoryMallAccountBalance) 
 *			+ (ActualFactoryPropertyValue - FactoryPropertyAccountBalance) 
 *			- (FactoryExpensesAccountBalance)
 * OR
 * 	   ActualFactoryMallValue - (FactoryMallAccountBalance + FactoryExpensesAccountBalance)
 */

?>

<!DOCTYPE html>
<html lang="en">
<head>
<title><?php echo getLangText("LEDGER"); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="shortcut icon" href="images/icon/icon-128.png" type="image/x-icon" />
<link id="manifest" rel="manifest" href="manifest.json">
<link rel="stylesheet" href="css/style.css?v10"> 
</head>
<body>
	<div class="loader" style="display:none;"></div>
	<section class="page-header">
		<h5>			
			<span class="glyphicon glyphicon-plus left collapsed" data-toggle="modal" data-target="#add-account"></span>
			<a href="/"><?php echo getLangText("LEDGER"); ?></a> 
			<span class="header-menu" data-cookie="entry"><span id="entryButton" class="glyphicon glyphicon-edit collapsed" data-toggle="collapse" data-target="#entry"></span></span>
			<span class="header-menu" data-cookie="hindi"><span id="hindiButton" class="glyphicon glyphicon-header collapsed"></span></span>
			<!-- <span class="header-menu" data-cookie="summary"><span id="summaryButton" class="glyphicon glyphicon-th-large collapsed" data-toggle="collapse" data-target="#summary"></span></span> -->
		</h5>
	</section>
	<section>
		<div id="add-account" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
	      			<div class="modal-header">
	      				<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h2><?php echo getLangText("NEW_PARTY"); ?></h2>
					</div>
					<div class="modal-body">
						<form method="post">
						<input type="text" name="client-name" placeholder="<?php echo getLangText('PARTY_NAME'); ?>"/>
						<input type="submit" class="btn btn-warning btn-lg" name="client-submit" value="<?php echo getLangText('SUBMIT'); ?>"/>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section class="summary">
		<div id="summary" class="collapse">
			<!-- <div class="label <?php if ($cashBalance >= 0) { echo 'label-success'; } else { echo 'label-danger'; } ?>"><span class="title">Cash</span><?php //echo getMoneyFormat($cashBalance); ?></div>
			<div class="label <?php if ($clientBalance >= 0) { echo 'label-success'; } else { echo 'label-danger'; } ?>"><span class="title">Party</span><?php //echo getMoneyFormat($clientBalance); ?></div>
			<div class="label <?php if ($capitalBalance >= 0) { echo 'label-success'; } else { echo 'label-danger'; } ?>"><span class="title">Capital</span><?php //echo getMoneyFormat($capitalBalance); ?></div>
			<div class="label <?php if ($homeBalance >= 0) { echo 'label-success'; } else { echo 'label-danger'; } ?>"><span class="title">Home</span><?php //echo getMoneyFormat($homeBalance); ?></div>
			<div class="label full <?php if ($profitBalance >= 0) { echo 'label-success'; } else { echo 'label-danger'; } ?>"><span class="title">Profit/Loss</span><?php //echo getMoneyFormat($profitBalance).' + Stock'; ?></div> -->
		</div>
	</section>
	<section class="entry-container">
		<div id="entry" class="collapse">
			<div class="entry">
				<button type="button" class="btn btn-warning btn-lg" data-toggle="modal" data-target="#purchase">
					<span class="glyphicon glyphicon-object-align-left" aria-hidden="true"></span> 
					<?php echo getLangText("PURCHASE"); ?>
				</button>
				<button type="button" class="btn btn-warning btn-lg" data-toggle="modal" data-target="#sale">
					<span class="glyphicon glyphicon-object-align-right" aria-hidden="true"></span> 
					<?php echo getLangText("SALE"); ?>
				</button>
				<button type="button" class="btn btn-warning btn-lg" data-toggle="modal" data-target="#pay">
					<span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span> 
					<?php echo getLangText("DEBIT"); ?>
				</button>
				<button type="button" class="btn btn-warning btn-lg" data-toggle="modal" data-target="#earn">
					<span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span> 
					<?php echo getLangText("CREDIT"); ?>
				</button>
			</div>
			
			<div id="purchase" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
		      			<div class="modal-header">
		      				<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h2><?php echo getLangText("PURCHASE"); ?></h2>
						</div>
						<div class="modal-body">
							<form method="post">
							<input type="text" name="buy-desc" placeholder="<?php echo getLangText("DESC"); ?>"/>
							<select name="buy-from">
								<option value=""><?php echo getLangText("PURCHASE_FROM"); ?></option>
								<?php displayAccounts($accounts, $ACCOUNT_TYPE['CLIENT'], null); ?>
							</select>
							<input type="number" name="buy-amount" placeholder="<?php echo getLangText("AMOUNT"); ?>"/>
							<input type="date" name="buy-date" value="<?php echo date("Y-m-d") ?>"/>
							<input type="submit" class="btn btn-warning btn-lg" name="buy-submit" value="<?php echo getLangText('SUBMIT'); ?>"/>
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
							<h2><?php echo getLangText("SALE"); ?></h2>
						</div>
						<div class="modal-body">
							<form method="post">
							<input type="text" name="sell-desc" placeholder="<?php echo getLangText("DESC"); ?>"/>
							<select name="sell-to">
								<option value=""><?php echo getLangText("SELL_TO"); ?></option>
								<?php displayAccounts($accounts, $ACCOUNT_TYPE['CLIENT'], null); ?>
							</select>
							<input type="number" name="sell-amount" placeholder="<?php echo getLangText("AMOUNT"); ?>"/>
							<input type="date" name="sell-date" value="<?php echo date("Y-m-d") ?>"/>
							<input type="submit" class="btn btn-warning btn-lg" name="sell-submit" value="<?php echo getLangText('SUBMIT'); ?>"/>
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
							<h2><?php echo getLangText("DEBIT"); ?></h2>
						</div>
						<div class="modal-body">
							<form method="post">
							<input type="text" name="pay-desc" placeholder="<?php echo getLangText("DESC"); ?>"/>
							<select name="pay-to">
								<option value=""><?php echo getLangText('PAID_TO'); ?></option>
								<?php displayAccounts($accounts, null, null); ?>
							</select>
							<input type="number" name="pay-amount" placeholder="<?php echo getLangText("AMOUNT"); ?>"/>
							<input type="date" name="pay-date" value="<?php echo date("Y-m-d") ?>"/>
							<input type="submit" class="btn btn-warning btn-lg" name="pay-submit" value="<?php echo getLangText('SUBMIT'); ?>"/>
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
							<h2><?php echo getLangText("CREDIT"); ?></h2>
						</div>
						<div class="modal-body">
							<form method="post">
							<input type="text" name="earn-desc" placeholder="<?php echo getLangText("DESC"); ?>"/>
							<select name="earn-from">
								<option value=""><?php echo getLangText('PAYMENT_FROM'); ?></option>
								<?php displayAccounts($accounts, null, null); ?>
							</select>
							<input type="number" name="earn-amount" placeholder="<?php echo getLangText("AMOUNT"); ?>"/>
							<input type="date" name="earn-date" value="<?php echo date("Y-m-d") ?>"/>
							<input type="submit" class="btn btn-warning btn-lg" name="earn-submit" value="<?php echo getLangText('SUBMIT'); ?>"/>
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
				<?php //displayMonths(10, $txnMonth); ?>
			</select> -->
			<select name="txn-account">
				<option value=""><?php echo getLangText('ACCOUNT'); ?></option>
				<?php displayAccounts($accounts, null, $txnAccount, true); ?>
			</select>
		</div>
		<div class="txns">
			<div class="txns-heading">
				<?php
					if($txnDate) {
						$date = date_create($txnDate);
						echo getLangText('TRANSACTION')." - <strong>".date_format($date, "jS M")."</strong>";
					} else if ($txnAccount) {
						$class = "balance success";
						if ($balance < 0) {
							$class = "balance failure";
						}
						echo getLangText('ACCOUNT')." - <strong>".$account['name']."</strong>";
						echo " <span class='".$class."'> ".getMoneyFormat($balance)."</span>";
					}
				?>
			</div>
			<table>
				<tr><th><?php echo getLangText('DATE'); ?></th><th><?php echo getLangText('DESC'); ?></th><th><?php echo getLangText('DEBIT'); ?></th><th><?php echo getLangText('CREDIT'); ?></th><th><?php echo getLangText('BALANCE'); ?></th></tr>
			<?php
				if (sizeof($txns) == 0) {
					echo "<tr><td colspan='5'>".getLangText('NO_TRANSACTION')."</td></tr>";
				} else if ($txnAccount) {
					displayAccountTxns($txns, $account, $balance);
				} else {
					displayDateTxns($txns, $txnDate);
				}
			?>
			</table>

			<?php for($i = 0; $i < sizeof($txns); $i++) { $txn = $txns[$i]; ?>
			<div id="txn-<?php echo $i; ?>" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
	    	  			<div class="modal-header">
	    	  				<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h2><?php echo getLangText('UPDATE'); ?></h2>
						</div>
						<div class="modal-body">
							<form method="post">
							<input type="hidden" name="txn-id" value="<?php echo $txn['id']; ?>"/>
							<input type="text" name="txn-desc" placeholder="<?php echo getLangText('DESC'); ?>" value="<?php echo $txn['description']; ?>"/>
							<select name="txn-from">
								<option value=""><?php echo getLangText('FROM_ACCOUNT'); ?></option>
								<?php displayAccounts($accounts, null, $txn['from_account_id']); ?>
							</select>
							<select name="txn-to">
								<option value=""><?php echo getLangText('TO_ACCOUNT'); ?></option>
								<?php displayAccounts($accounts, null, $txn['to_account_id']); ?>
							</select>
							<input type="number" name="txn-amount" placeholder="<?php echo getLangText("AMOUNT"); ?>" value="<?php echo $txn['amount']; ?>"/>
							<?php $d = date_create($txn['date']); ?>
							<input type="date" name="txn-date" value="<?php echo date_format($d, 'Y-m-d'); ?>"/>
							<input type="hidden" name="txn-delete-submit" value=""/>
							<input type="submit" class="btn btn-danger btn-lg half" data-index="<?php echo $i; ?>" name="txn-delete-confirm" value="<?php echo getLangText('DELETE'); ?>" data-dismiss="modal"/>
							<input type="submit" class="btn btn-warning btn-lg half" name="txn-update-submit" value="<?php echo getLangText('UPDATE'); ?>"/>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div id="confirm-<?php echo $i; ?>" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
	    	  			<div class="modal-header">
							<h2><?php echo getLangText('CONFIRM_DELETE'); ?></h2>
						</div>
						<div class="modal-footer">
							<button type="button" data-dismiss="modal" class="btn btn-danger delete"><?php echo getLangText('DELETE'); ?></button>
							<button type="button" data-dismiss="modal" class="btn"><?php echo getLangText('CANCEL'); ?></button>
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