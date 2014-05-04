<?php
	include 'base.php';

	$profileUser = UserReadOnly::fromID((int) $_GET['id']);

	if (!$profileUser instanceof UserReadOnly) {
		$errorMessage = 'User does not exist.';

		echo ErrorMessage::setText($errorMessage, true);
	} else {
		Templates::assign('profileUser', $profileUser);
	}

	Templates::display('user');
?>