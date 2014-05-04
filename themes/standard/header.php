<!DOCTYPE html>
<html>
	<head>
		<base href="<?php echo Templates::getBaseURL(); ?>" />
		<meta charset="utf-8" />
		<title><?php echo Templates::getVar('pageTitle'); ?></title>
		<link rel="stylesheet" href="<?php echo Templates::getSCSS('style'); ?>" />
	</head>

	<body>
		<div id="header_wrap">
			<header>
				<div class="logo">
					<img src="/images/quicktalk/wide_forlight.svg" alt="<?php echo Templates::getVar('pageTitle'); ?>" />
				</div>

				<nav>
					<?php echo ThemeFunctions::getNavigation(); ?>
				</nav>

				<div class="user">
					<div class="tab">
						<div class="username">
							<?php if (ThemeFunctions::loggedIn()): ?>
								<?php echo $user->getUsername(); ?>
								<span class="rsaquo">&rsaquo;</span>

								<ul>
									<li><a href="./user.php?id=<?php echo $user->getUserID(); ?>">My Profile</a></li>
									<li><a href="./settings.php">Settings</a></li>
									<li><a href="./login.php?logout=1">Logout</a>
								</ul>
							<?php else: ?>
								Welcome, Guest.

								<span class="links">
									<a href="./login.php">Log in</a> or
									<a href="./register.php">Register</a>
								</span>
							<?php endif; ?>
						</div>

						<div class="avatar">
							<img src="<?php echo ThemeFunctions::getUserAvatar(); ?>" alt="Avatar" />
						</div>
					</div>
				</div>
			</header>
		</div>

		<div id="content">