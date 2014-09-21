<!DOCTYPE html>
<html>
	<head>
		<base href="<?php echo Templates::getBaseURL(); ?>" />
		<meta charset="utf-8" />
		<title><?php echo Templates::getVar('pageTitle'); ?></title>
		<link rel="stylesheet" href="<?php echo Templates::getSCSS('style'); ?>" />
	</head>

	<body>
		<div id="aside_wrap">
			<aside>
				<div class="tab">
					<div class="userinfo">
						<?php if (ThemeFunctions::loggedIn()): ?>
							<b><?php echo $user->getUsername(); ?></b>

							<a href="./user.php?id=<?php echo $user->getUserID(); ?>">My Profile</a>
							<a href="./settings.php">Settings</a>
							<a href="./login.php?logout=1">Logout</a>
						<?php else: ?>
							<b>Welcome, Guest.</b>

							<span class="links">
								<a href="./login.php">Log in</a>
								<a href="./register.php">Register</a>
							</span>
						<?php endif; ?>
					</div>

					<div class="avatar">
						<img src="<?php echo ThemeFunctions::getUserAvatar(); ?>" alt="Avatar" />
					</div>
				</div>
			</aside>
		</div>

		<div id="header_wrap">
			<header>
				<div class="logo">
					<img src="/images/quicktalk/wide_translucent_fordark.svg" alt="<?php echo Templates::getVar('pageTitle'); ?>" />
				</div>

				<nav>
					<?php echo ThemeFunctions::getNavigation(); ?>
				</nav>
			</header>
		</div>

		<div id="content">