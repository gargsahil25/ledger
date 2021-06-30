<?php

include_once "../services/constant.php";
include_once "../services/util.php";
include_once "../services/mysql.php";
include_once "../services/display.php";
include_once "../services/charts.php";

// Getting data for the page
date_default_timezone_set('Asia/Kolkata');
$data = getDataForStats();
$txnType = isset($_GET['txnType']) ? $_GET['txnType'] : null;
$userName = isset($_GET['userName']) ? $_GET['userName'] : null;
$dateRange = isset($_GET['dateRange']) ? $_GET['dateRange'] : null;
$txnData = getStatsTxnData($data, $txnType, $userName, $dateRange);

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
			<a href="/pages/txns.php"><?php echo "All Transactions" ?></a> | 
			<a class="active" href="/pages/stats.php"><?php echo "Stats" ?></a> | 
			<a href="/pages/balance.php"><?php echo getLangText("PROFIT_LOSS"); ?></a>
		</h5>
	</section>
	<section>
		<div id="purchase" class="chart"></div>
		<div id="sale" class="chart"></div>
		<div id="debit" class="chart"></div>
		<div id="credit" class="chart"></div>
		<div id="home_expense" class="chart"></div>
		<div id="business_expense" class="chart"></div>
		<div id="capital" class="chart"></div>
		<div id="factory_property" class="chart"></div>
	</section>
	<section>
		<div class="txns">
			<div class="txns-heading">
				<form method="get">
					<select name="txnType" class="chart-selection" onchange="this.form.submit()">
						<?php displayStatsTxnTypes($data, $txnType); ?>
					</select>
					<select name="userName" class="chart-selection" onchange="this.form.submit()">
						<?php displayStatsUsers($data, $txnType, $userName); ?>
					</select>
					<select name="dateRange" class="chart-selection" onchange="this.form.submit()">
						<?php displayStatsDateRanges($data, $txnType, $userName, $dateRange); ?>
					</select>
				</form>
			</div>
			<table>
				<tr><th><?php echo getLangText('DATE'); ?></th><th><?php echo getLangText('DESC'); ?></th><th><?php echo "From Account"; ?></th><th><?php echo "To Account"; ?></th><th><?php echo "Amount"; ?></th></tr>
			<?php
				if ($txnData == null) {
					echo "<tr><td colspan='5'>".getLangText('NO_TRANSACTION')."</td></tr>";
				} else {
					echo "<tr><td colspan='5'>Value: <strong>".getMoneyFormat($txnData['value'])."</strong></td></tr>";
					displayAllTxns($txnData['txns']);
				}
			?>
			</table>
		</div>
	</section>
	<?php include('../includes/footer.php'); ?>
	<script src="/js/canvasjs.min.js"></script>
	<script>
		window.onload = function () {
		<?php 
			renderChart("purchase", "Purchase", $data['purchase']);
			renderChart("sale", "Sale", $data['sale']);
			renderChart("debit", "Debit", $data['debit']);
			renderChart("credit", "Credit", $data['credit']);
			renderChart("home_expense", "Home Expense", $data['home_expense']);
			renderChart("business_expense", "Business Expense", $data['business_expense']);
			renderChart("capital", "Capital", $data['capital']);
			renderChart("factory_property", "Factory Property", $data['factory_property']);
		?>
		}
	</script>
</body>
</html>
