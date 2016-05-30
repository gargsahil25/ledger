<?php

include_once "services/mysql.php";
include_once "services/handler.php";
include_once "services/display.php";

newClientHandler($_POST);

$accounts = getAccounts();

?>

<html>
<head>
<title>Ledger - Accounts</title>
<link rel="stylesheet" href="css/style.css"> 
</head>
<body>
	<section>
		<h1>Ledger Accounts
		<span class="summary"><span class="title">Cash</span> <?php echo getBalanceByType('cash'); ?></span>
		<span class="summary"><span class="title">Profit</span> <?php echo getBalanceByType('factory') * -1; ?></span> 
		<span class="summary"><span class="title">Home</span> <?php echo getBalanceByType('home'); ?></span>
		<span class="summary"><span class="title">Client</span> <?php echo getBalanceByType('client'); ?></span>
		<span class="nav-links"><a href="./index.php">Home</a></span></h1>
		<hr>
	</section>
	<section>
		<div class="accounts">
			<table>
			<tr><th>Name</th><th>Balance</th></tr>
			<?php displayAccountsWithBalance($accounts); ?>
			</table>
		</div>
	</section>
	<section>
		<div class="accounts form">
			<h2>Add New Client</h2>
			<form method="post">
			<input type="text" name="client-name" placeholder="Name of Client"/>
			<input type="submit" name="client-submit" value="Submit"/>
			</form>
		</div>
	</section>
</body>
</html>