<?php
	require 'base.php';

	if ($userManager->loggedIn()) {
		if ($_GET['logout'] == 1) {
			$userManager->logout();

			header("Location: ./login.php");
			exit;
		} else {
			header("Location: ./");
			exit;
		}
	}

	$errorMessage = false;

	if (isset($_POST['submit'])) {
		$username = $_POST['username'];
		$password = $_POST['password'];

		if ($userManager->login($username, $password, false, '/')) {
			header("Location: ./");
			exit;
		} else {
			$errorMessage = 'Wrong username or password.';
		}
	}

	PluginHelper::delegate('__onPageDisplay', array($page));
	Templates::display('login');
?>