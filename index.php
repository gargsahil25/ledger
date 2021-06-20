<?php
session_start();

include_once "services/constant.php";
include_once "services/util.php";
include_once "services/sessionUtil.php";
include_once "services/mysql.php";
include_once "services/handler.php";
include_once "services/display.php";

$user = getLoggedInUser(true);

// Post request handlers
buyStuffHandler($_POST);
sellStuffHandler($_POST);
payAmountHandler($_POST);
getPaymentHandler($_POST);
newClientHandler($_POST);
updateClientHandler($_POST);
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

if ($txnAccount) {
	$account = getAccountById($txnAccount);
	$balance = $account['balance'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<title><?php echo getLangText("LEDGER"); ?></title>
<?php include('includes/header.php'); ?>
</head>
<body>
	<div class="loader" style="display:none;"></div>
	<section class="page-header">
		<h5>			
			<span class="glyphicon glyphicon-plus left collapsed" data-toggle="modal" data-target="#add-account"></span>
			<a class="active" href="/"><?php echo $user['userName'].' '.getLangText("LEDGER"); ?></a> <a href="/balance.php"><?php //echo getLangText("PROFIT_LOSS"); ?></a>
			<span class="header-menu" data-cookie="PHPSESSID" data-reload="true" data-removecookie="true"><span class="glyphicon glyphicon-off collapsed"></span></span>
			<span class="header-menu" data-cookie="entry"><span id="entryButton" class="glyphicon glyphicon-edit collapsed" data-toggle="collapse" data-target="#entry"></span></span>
			<span class="header-menu" data-cookie="hindi" data-reload="true"><span id="hindiButton" class="glyphicon glyphicon-header collapsed"></span></span>

		</h5>
	</section>
	<section>
		<div id="add-account" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
	      			<div class="modal-header">
	      				<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h2><?php echo getLangText("NEW_ACCOUNT"); ?></h2>
					</div>
					<div class="modal-body">
						<form method="post">
						<input type="text" required autocomplete="off" name="client-name" placeholder="<?php echo getLangText('ACCOUNT_NAME'); ?>"/>
						<select name="account_type" required>
							<?php displayAccountTypes(); ?>
						</select>
						<input type="submit" class="btn btn-warning btn-lg" name="client-submit" value="<?php echo getLangText('SUBMIT'); ?>"/>
						</form>
					</div>
				</div>
			</div>
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
							<input required autocomplete="off" type="text" name="buy-desc" placeholder="<?php echo getLangText("DESC"); ?>"/>
							<select required name="buy-from">
								<option value=""><?php echo getLangText("PURCHASE_FROM"); ?></option>
								<?php displayAccounts($accounts, $ACCOUNT_TYPE['CLIENT'], $txnAccount); ?>
							</select>
							<input required autocomplete="off" type="number" name="buy-amount" placeholder="<?php echo getLangText("AMOUNT"); ?>"/>
							<?php displayTxnType(); ?>
							<input required type="date" name="buy-date" value="<?php echo date("Y-m-d") ?>"/>
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
							<input required autocomplete="off" type="text" name="sell-desc" placeholder="<?php echo getLangText("DESC"); ?>"/>
							<select required name="sell-to">
								<option value=""><?php echo getLangText("SELL_TO"); ?></option>
								<?php displayAccounts($accounts, $ACCOUNT_TYPE['CLIENT'], $txnAccount); ?>
							</select>
							<input required autocomplete="off" type="number" name="sell-amount" placeholder="<?php echo getLangText("AMOUNT"); ?>"/>
							<?php displayTxnType(); ?>
							<input required type="date" name="sell-date" value="<?php echo date("Y-m-d") ?>"/>
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
							<input required autocomplete="off" type="text" name="pay-desc" placeholder="<?php echo getLangText("DESC"); ?>"/>
							<select required name="pay-to">
								<option value=""><?php echo getLangText('PAID_TO'); ?></option>
								<?php displayAccounts($accounts, null, $txnAccount); ?>
							</select>
							<input required autocomplete="off" type="number" name="pay-amount" placeholder="<?php echo getLangText("AMOUNT"); ?>"/>
							<input required type="date" name="pay-date" value="<?php echo date("Y-m-d") ?>"/>
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
							<input required autocomplete="off" type="text" name="earn-desc" placeholder="<?php echo getLangText("DESC"); ?>"/>
							<select required name="earn-from">
								<option value=""><?php echo getLangText('PAYMENT_FROM'); ?></option>
								<?php displayAccounts($accounts, null, $txnAccount); ?>
							</select>
							<input required autocomplete="off" type="number" name="earn-amount" placeholder="<?php echo getLangText("AMOUNT"); ?>"/>
							<input required type="date" name="earn-date" value="<?php echo date("Y-m-d") ?>"/>
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
			<input type="date" name="txn-date" value="<?php echo $txnDate; ?>" max="<?php echo date_format(date_create(), "Y-m-d"); ?>"/>
			<select name="txn-account">
				<option value=""><?php echo getLangText('ACCOUNT'); ?></option>
				<?php displayAccounts($accounts, "all", $txnAccount, true); ?>
			</select>
		</div>
		<div class="txns">
			<div class="txns-heading">
				<?php
					
					if($txnDate) {
						$date = date_create($txnDate);
						echo getLangText('TRANSACTION')." - <strong>".date_format($date, "j F Y")."</strong>";
					} else if ($txnAccount) {
						$class = "balance";
						if ($balance >= 0) {
							$class = "balance green";
						}
						echo getLangText('ACCOUNT')." - <strong>".$account['name']."</strong>";
						echo " <span class='".$class."'> ".getMoneyFormat($balance)."</span>"; ?>

						<?php if (isAccountEditable($account)) { ?>
							<span class='glyphicon glyphicon-edit' data-toggle='modal' data-target='#update-account'></span>
							<div id="update-account" class="modal fade" role="dialog">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
											<h2><?php echo getLangText('UPDATE'); ?></h2>
										</div>
										<div class="modal-body">
											<form method="post">
											<input type="hidden" name="client-id" value="<?php echo $account['id']; ?>"/>
											<input required autocomplete="off" type="text" name="client-name" value="<?php echo $account['original_name']; ?>"/>
											<select name="account_type" required>
												<?php displayAccountTypes($account['type']); ?>
											</select>
											<input type="submit" class="btn btn-warning btn-lg" name="client-update" value="<?php echo getLangText('UPDATE'); ?>"/>
											</form>
										</div>
									</div>
								</div>
							</div>
						<?php } ?>
					<?php } ?>
			</div>
			<table>
				<tr><th><?php echo getLangText('DATE'); ?></th><th><?php echo getLangText('DESC'); ?></th><th><?php echo getLangText('CREDIT'); ?></th><th><?php echo getLangText('DEBIT'); ?></th><th><?php echo getLangText('BALANCE'); ?></th></tr>
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
							<input required autocomplete="off" type="text" name="txn-desc" placeholder="<?php echo getLangText('DESC'); ?>" value="<?php echo $txn['description']; ?>"/>
							<select required name="txn-from">
								<option value=""><?php echo getLangText('FROM_ACCOUNT'); ?></option>
								<?php displayAccounts($accounts, "all", $txn['from_account_id']); ?>
							</select>
							<select required name="txn-to">
								<option value=""><?php echo getLangText('TO_ACCOUNT'); ?></option>
								<?php displayAccounts($accounts, "all", $txn['to_account_id']); ?>
							</select>
							<input required autocomplete="off" type="number" name="txn-amount" placeholder="<?php echo getLangText("AMOUNT"); ?>" value="<?php echo $txn['amount']; ?>"/>
							<?php $d = date_create($txn['date']); ?>
							<input required type="date" name="txn-date" value="<?php echo date_format($d, 'Y-m-d'); ?>"/>
							<input type="hidden" name="txn-from-old" value="<?php echo $txn['from_account_id']; ?>"/>
							<input type="hidden" name="txn-to-old" value="<?php echo $txn['to_account_id']; ?>"/>
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
	<?php include('includes/footer.php'); ?>
</body>
</html>