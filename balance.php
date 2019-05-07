<?php
session_start();

include_once "services/constant.php";
include_once "services/util.php";
include_once "services/sessionUtil.php";
include_once "services/mysql.php";
include_once "services/handler.php";
include_once "services/display.php";

$user = getLoggedInUser(true);
$accounts = getAccounts();

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
<?php include('includes/header.php'); ?> 
</head>
<body>
	<section class="page-header">
		<h5>			
			<a href="/"><?php echo $user['userName'].' '.getLangText("LEDGER"); ?></a> | <a class="active" href="/balance.php"><?php echo getLangText("PROFIT_LOSS"); ?></a> 
			<span class="header-menu" data-cookie="PHPSESSID" data-reload="true" data-removecookie="true"><span class="glyphicon glyphicon-off collapsed"></span></span>
			<span class="header-menu" data-cookie="hindi" data-reload="true"><span id="hindiButton" class="glyphicon glyphicon-header collapsed"></span></span>
		</h5>
	</section>
	<section>
		<div class="txns">
			<div class="txns-heading">
				<?php
					echo getLangText('PROFIT_LOSS')." on <strong>".date_format(date_create(), "j F Y")."</strong> <span class='totalprofitloss balance'></span>";
				?>
			</div>
			<table class="accounts">
				<tr><th><?php echo getLangText('ACCOUNT'); ?></th><th><?php echo getLangText('BALANCE'); ?></th><th><?php echo getLangText('ACTUAL_BALANCE'); ?></th><th><?php echo getLangText('PROFIT_LOSS'); ?></th></tr>
				<?php displayAccountBalance($accounts); ?>
			</table>
		</div>
	</section>
	<?php include('includes/footer.php'); ?>

</body>
</html>