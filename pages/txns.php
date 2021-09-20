<?php
session_start();

include_once "../services/constant.php";
include_once "../services/util.php";
include_once "../services/mysql.php";
include_once "../services/sessionUtil.php";
include_once "../services/display.php";

$user = getLoggedInUser(true);

$userId = isset($_GET['userId']) ? $_GET['userId'] : $user['userId'];
$sort = isset($_GET['sort']) ? $_GET['sort'] : 0;
$users = getAllUsers();

// Getting data for the page
date_default_timezone_set('Asia/Kolkata');
$txns = null;
if ($userId != null) {
	$txns = getTransactions(null, null, null, true, $userId, $sort);
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
			<a href="/index.php"><?php echo getLangText("LEDGER"); ?></a> &gt;
			<a class="active" href="/pages/txns.php"><?php echo "All Transactions" ?></a>
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
				<tr><th><?php echo getLangText('DATE'); ?></th>
				<th><?php echo "Created Date 
					<form type='get' class='sort-btn-form'>
						<button class='glyphicon glyphicon-sort ".($sort ? "selected" : "")."' name='sort' value='".!$sort."' onclick='this.form.submit()'/>
						<input type='hidden' name='userId' value='".$userId."'/>
					</form>
				"; ?></th>
				<th><?php echo getLangText('DESC'); ?></th><th><?php echo "From Account"; ?></th><th><?php echo "To Account"; ?></th><th><?php echo "Amount"; ?></th></tr>
			<?php
				if ($txns == null || sizeof($txns) == 0) {
					echo "<tr><td colspan='6'>".getLangText('NO_TRANSACTION')."</td></tr>";
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