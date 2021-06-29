<?php
session_start();

include_once "services/util.php";
include_once "services/sessionUtil.php";
include_once "services/mysql.php";
include_once "services/handler.php";
include_once "services/display.php";

loggedInRedirect();

// Post request handlers
loginUserHandler($_POST);

$users = getAllUsers();

?>

<!DOCTYPE html>
<html lang="en">
<head>
<title><?php echo getLangText("LEDGER") . ' - ' . getLangText("LOGIN"); ?></title>
<?php include('includes/header.php'); ?> 
</head>
<body>
	<section class="page-header">
		<h5>			
			<a href="/"><?php echo getLangText("LEDGER"); ?></a> | <a class="active" href="/balance.php"><?php echo getLangText("LOGIN"); ?></a> 
			<span class="header-menu" data-cookie="hindi" data-reload="true"><span id="hindiButton" class="glyphicon glyphicon-header collapsed"></span></span>
		</h5>
	</section>
	<section class= "login-form-container">
		<form method="post">
			<select required name="userId">
				<?php displayUsers($users); ?>
			</select>
			<input required autocomplete="off" type="password" name="password" placeholder="<?php echo getLangText("PASSWORD"); ?>"/>
			<input type="submit" class="btn btn-warning btn-lg" name="login-submit" value="<?php echo getLangText('LOGIN'); ?>"/>
		</form>
	</section>
	<?php include('includes/footer.php'); ?>

</body>
</html>