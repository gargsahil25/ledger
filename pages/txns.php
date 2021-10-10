<?php
session_start();

include_once "../services/constant.php";
include_once "../services/util.php";
include_once "../services/mysql.php";
include_once "../services/sessionUtil.php";
include_once "../services/display.php";

$user = getLoggedInUser(true);
$userId = isset($_GET['userId']) && $user['isAdmin'] ? $_GET['userId'] : $user['userId'];
$sort = isset($_GET['sort']) ? $_GET['sort'] : 0;

$txns = getTransactions(null, null, null, true, $userId, $sort);

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
			<a class="active" href="/pages/txns.php"><?php echo "All Transactions" ?></a>
		</h5>
	</section>
	<?php include('../includes/userSelection.php'); ?>
	<section>
		<div class="txns">
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