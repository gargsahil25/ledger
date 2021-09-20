<?php
session_start();

include_once "../services/constant.php";
include_once "../services/util.php";
include_once "../services/sessionUtil.php";
include_once "../services/report.php";

$user = getLoggedInUser(true);
$profitPercent = isset($_GET['profit']) ? $_GET['profit'] : 15;

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
			<a href="/index.php"><?php echo $user['userName'].' '.getLangText("LEDGER"); ?></a> &gt;
            <a class="active" href="/pages/report.php"><?php echo "Report"; ?></a>
		</h5>
	</section>
	<section>
		<div class="txns">
			<table>
			<?php
				displayReport($profitPercent);
			?>
			</table>
		</div>
	</section>
	<?php include('../includes/footer.php'); ?>
</body>
</html>