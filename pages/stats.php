<?php
session_start();

include_once "../services/constant.php";
include_once "../services/util.php";
include_once "../services/mysql.php";
include_once "../services/display.php";
include_once "../services/charts.php";

$user = getLoggedInUser(true);
$userId = isset($_GET['userId']) && $user['isAdmin'] ? $_GET['userId'] : $user['userId'];
$txnType = isset($_GET['txnType']) ? $_GET['txnType'] : null;
$userName = isset($_GET['userName']) ? $_GET['userName'] : null;
$dateRange = isset($_GET['dateRange']) ? $_GET['dateRange'] : null;

$data = getDataForStats();
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
			<a href="/index.php?userId=<?php echo $userId; ?>"><?php echo $user['userName'].' '.getLangText("LEDGER"); ?></a> &gt;
			<a class="active" href="/pages/stats.php"><?php echo "Stats" ?></a> 
		</h5>
	</section>
	<section>
		<div id="purchase" class="chart"></div>
		<div id="sale" class="chart"></div>
		<div id="home_expense" class="chart"></div>
		<div id="business_expense" class="chart"></div>
		<div id="debit" class="chart"></div>
		<div id="credit" class="chart"></div>
		<div id="capital" class="chart"></div>
		<div id="factory_property" class="chart"></div>
	</section>
	<section>
		<div class="txns">
			<div class="txns-heading">
				<form method="get">
					<select name="txnType" class="chart-selection">
						<?php displayStatsTxnTypes($data, $txnType); ?>
					</select>
					<select name="userName" class="chart-selection">
						<?php displayStatsUsers($data, $txnType, $userName); ?>
					</select>
					<select name="dateRange" class="chart-selection">
						<?php displayStatsDateRanges($data, $txnType, $userName, $dateRange); ?>
					</select>
					<input type="submit" class="btn btn-primary chart-selection" name="submit" value="Submit"/>
				</form>
			</div>
			<table>
				<tr><th><?php echo getLangText('DATE'); ?></th><th><?php echo "Created Date"; ?></th><th><?php echo getLangText('DESC'); ?></th><th><?php echo "From Account"; ?></th><th><?php echo "To Account"; ?></th><th><?php echo "Amount"; ?></th></tr>
			<?php
				if ($txnData == null) {
					echo "<tr><td colspan='6'>".getLangText('NO_TRANSACTION')."</td></tr>";
				} else {
					echo "<tr><td colspan='6'>Value: <strong>".getMoneyFormat($txnData['value'])."</strong></td></tr>";
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
			renderChart("home_expense", "Home Expense", $data['home_expense']);
			renderChart("business_expense", "Business Expense", $data['business_expense']);
			renderChart("debit", "Debit", $data['debit']);
			renderChart("credit", "Credit", $data['credit']);
			renderChart("capital", "Capital", $data['capital']);
			renderChart("factory_property", "Factory Property", $data['factory_property']);
		?>
		}
	</script>
</body>
</html>
