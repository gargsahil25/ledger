<?php

include_once "../services/constant.php";
include_once "../services/util.php";
include_once "../services/mysql.php";
include_once "../services/display.php";

$userId = isset($_GET['userId']) ? $_GET['userId'] : null;
$users = getAllUsers();

// Getting data for the page
date_default_timezone_set('Asia/Kolkata');
$txns = null;
if ($userId != null) {
	$txns = getTransactions(null, null, null, true, $userId);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<title><?php echo getLangText("LEDGER"); ?></title>
<?php include('../includes/header.php'); ?>
</head>
<body>
	<section class="page-header">
		<h5>			
			<a href="/index.php"><?php echo getLangText("LEDGER"); ?></a> | 
			<a class="active" href="/pages/txns.php"><?php echo "All Transactions" ?></a> | 
			<a href="/pages/balance.php"><?php echo getLangText("PROFIT_LOSS"); ?></a>
		</h5>
	</section>
	<section>
		<div class="txns">
			<div class="txns-heading">
				<form method="get">
					<select name="userId" onchange="this.form.submit()">
						<?php displayUsers($users, $userId); ?>
					</select>
				</form>
			</div>
			<table>
				<tr><th><?php echo getLangText('DATE'); ?></th><th><?php echo getLangText('DESC'); ?></th><th><?php echo "From Account"; ?></th><th><?php echo "To Account"; ?></th><th><?php echo "Amount"; ?></th></tr>
			<?php
				if ($txns == null || sizeof($txns) == 0) {
					echo "<tr><td colspan='5'>".getLangText('NO_TRANSACTION')."</td></tr>";
				} else {
					displayAllTxns($txns);
				}
			?>
			</table>
		</div>
	</section>
	<?php include('../includes/footer.php'); ?>
</body>
</html>