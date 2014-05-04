<?php
	Templates::display('header');

	$profileUser = Templates::getVar('profileUser');
?>

	<!-- End Content -->
	</div>

	<div id="profile">
		<div class="profile_header_wrap">
			<header>
				<div class="avatar">
					<img src="<?php echo $profileUser->getAvatar(); ?>" alt="Avatar" />
				</div>

				<div class="meta">
					<h1><?php echo $profileUser->getUsername(); ?></h1>

					<?php if ($profileUser->isOnline()): ?>

					<?php endif; ?>
				</div>
			</header>
		</div>
	</div>

	<!-- Re-start content -->

	<div id="content">

<?php
	Templates::display('footer');
?>