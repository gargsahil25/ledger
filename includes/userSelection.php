<?php if ($user['isAdmin']) { ?>
	<section>
		<div class="txns-heading">
			<form method="get">
				<select name="userId" onchange="this.form.submit()">
					<?php displayUsers(getAllUsers(), $userId); ?>
				</select>
			</form>
		</div>
	</section>
<?php } ?>