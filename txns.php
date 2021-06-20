<?php
session_start();

include_once "services/constant.php";
include_once "services/util.php";
include_once "services/sessionUtil.php";
include_once "services/mysql.php";
include_once "services/display.php";

$user = getLoggedInUser(true);

// Getting data for the page
date_default_timezone_set('Asia/Kolkata');
$txns = getTransactions(null, null, null, true);

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
		<div class="txns">
			<div class="txns-heading">
				<strong>All Transactions</strong>
			</div>
			<table>
				<tr><th><?php echo getLangText('DATE'); ?></th><th><?php echo getLangText('DESC'); ?></th><th><?php echo "From Account"; ?></th><th><?php echo "To Account"; ?></th><th><?php echo "Amount"; ?></th></tr>
			<?php
				if (sizeof($txns) == 0) {
					echo "<tr><td colspan='5'>".getLangText('NO_TRANSACTION')."</td></tr>";
				} else {
					displayAllTxns($txns);
				}
			?>
			</table>
		</div>
	</section>
	<?php include('includes/footer.php'); ?>
</body>
</html>