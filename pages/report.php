<?php
session_start();

include_once "../services/constant.php";
include_once "../services/util.php";
include_once "../services/sessionUtil.php";
include_once "../services/report.php";
include_once "../services/mysql.php";
include_once "../services/display.php";

$user = getLoggedInUser(true);
$userId = isset($_GET['userId']) ? $_GET['userId'] :  $user['userId'];
$users = getAllUsers();

$profitPercent = isset($_GET['profit']) ? $_GET['profit'] : $user['profit'];

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
			<div class="txns-heading">
				<form method="get">
					<select name="userId" onchange="this.form.submit()">
						<?php displayUsers($users, $userId); ?>
					</select>
				</form>
			</div>
			<?php
				displayReport($profitPercent, $userId);
			?>
		</div>
	</section>
	<?php include('../includes/footer.php'); ?>
</body>
</html>