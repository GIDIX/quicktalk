<?php
	Templates::display('header');
?>

	<?php if ($errorMessage): ?>
		<?php echo ErrorMessage::setText($errorMessage); ?>
	<?php endif; ?>

	<div id="login">
		<h1>Welcome back!</h1>

		<form method="post" action="">
			<input type="text" name="username" placeholder="Username" />
			<input type="password" name="password" placeholder="Password" />

			<input type="submit" name="submit" value="Login" />
		</form>
	</div>

<?php
	Templates::display('footer');
?>