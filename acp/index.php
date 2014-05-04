<?php
	require '../base.php';
	ob_start();

	require LANGS . 'ACPDashboardT.php';
	ACPDashboardT::init();

	require LIB . 'forum/Forum.php';

	if (!$userManager->loggedIn() || !$user->isAdmin()) {
		echo ErrorMessage::setText('You are not allowed to view this page.', true);
	}

	include 'template/header.php';

	echo '
		<h1>
			Dashboard
		</h1>

		<section class="statistics">
			<div class="row">
				<div class="item">
					<span>'.Forum::getTotalTopics().'</span>
					'.ACPDashboardT::get('topics').'
				</div>

				<div class="item">
					<span>'.Forum::getTotalPosts().'</span>
					'.ACPDashboardT::get('posts').'
				</div>

				<div class="item">
					<span>'.User::getTotalUsers().'</span>
					'.ACPDashboardT::get('members').'
				</div>
			</div>

			<div class="row">
				<div class="item">
					<span><a href="../user.php?id='.User::getLatestUser()->getID().'">'.User::getLatestUser()->getUsername().'</a></span>
					'.ACPDashboardT::get('latest_member').'
				</div>
			</div>
		</section>
	';

	include 'template/footer.php';
?>