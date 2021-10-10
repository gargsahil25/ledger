<?php
session_start();

include_once "../services/constant.php";
include_once "../services/util.php";
include_once "../services/sessionUtil.php";
include_once "../services/report.php";
include_once "../services/mysql.php";
include_once "../services/display.php";

$user = getLoggedInUser(true);
$userId = isset($_GET['userId']) && $user['isAdmin'] ? $_GET['userId'] : $user['userId'];
$profit = isset($_GET['profit']) ? $_GET['profit'] : null;

$userDetail = getUserById($userId);

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
            <a class="active" href="/pages/report.php"><?php echo "Report"; ?></a>
		</h5>
	</section>
	<?php include('../includes/userSelection.php'); ?>
	<section>
		<div class="txns">
			<?php
				displayReport($userDetail, $profit);
			?>
		</div>
	</section>
	<?php include('../includes/footer.php'); ?>
</body>
</html>