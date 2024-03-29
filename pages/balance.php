<?php
session_start();

include_once "../services/constant.php";
include_once "../services/util.php";
include_once "../services/mysql.php";
include_once "../services/handler.php";
include_once "../services/display.php";

$user = getLoggedInUser(true);
$userId = isset($_GET['userId']) && $user['isAdmin'] ? $_GET['userId'] : $user['userId'];

$accounts = getAccounts($userId);

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
<title><?php echo getLangText("LEDGER") . ' - ' . getLangText("PROFIT_LOSS"); ?></title>
<?php include('../includes/header.php'); ?> 
</head>
<body>
	<section class="page-header">
		<h5>			
		<a href="/index.php?userId=<?php echo $userId; ?>"><?php echo $user['userName'].' '.getLangText("LEDGER"); ?></a> &gt;
			<a class="active" href="/pages/balance.php"><?php echo getLangText("PROFIT_LOSS"); ?></a>
		</h5>
	</section>
	<?php include('../includes/userSelection.php'); ?>
	<section>
		<div class="txns">
			<div class="txns-heading">
				<?php
					echo getLangText('PROFIT_LOSS')." on <strong>".getDateFormat()."</strong> <span class='totalprofitloss balance'></span>";
				?>
			</div>
			<table class="accounts">
				<tr><th><?php echo getLangText('ACCOUNT'); ?></th><th><?php echo getLangText('BALANCE'); ?></th><th><?php echo getLangText('ACTUAL_BALANCE'); ?></th><th><?php echo getLangText('PROFIT_LOSS'); ?></th></tr>
				<?php
					if ($accounts == null || sizeof($accounts) == 0) {
						echo "<tr><td colspan='4'>No accounts found</td></tr>";
					} else {
						displayAccountBalance($accounts);
					}
				?>
			</table>
		</div>
	</section>
	<?php include('../includes/footer.php'); ?>

</body>
</html>