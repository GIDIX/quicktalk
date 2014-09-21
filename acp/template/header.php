<?php
	require LANGS . 'ACPHeaderT.php';
	ACPHeaderT::init();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>QuickTalk ACP</title>
		
		<link rel="stylesheet" href="../themes/fonts/segoe-ui/stylesheet.css" />
		<link rel="stylesheet" href="./css/style.php" />
	</head>

	<body>
		<div id="header_wrap">
			<header>
				<div class="logo">
					<img src="../images/quicktalk/wide_translucent_fordark.svg" alt="QuickTalk" />
				</div>

				<div class="title">
					<?php echo Config::get('page_title'); ?>
				</div>

				<div class="username">
					<?php echo $user->getUsername(); ?>
				</div>

				<div class="avatar">
					<img src="<?php echo str_replace('./', '/', $user->getAvatar()); ?>" />
				</div>
			</header>
		</div>

		<div id="content">
			<div class="sidebar">
				<nav>
					<ul>
						<li><a href="./" <?php if ($page == 'index.php'):?>class="active"<?php endif; ?>><?php echo ACPHeaderT::get('dashboard'); ?></a></li>
						<li><a href="./members.php" <?php if ($page == 'members.php'):?>class="active"<?php endif; ?>><?php echo ACPHeaderT::get('members'); ?></a></li>
						<li><a href="./bans.php" <?php if ($page == 'bans.php'):?>class="active"<?php endif; ?>><?php echo ACPHeaderT::get('bans'); ?></a></li>
						<li><a href="./forums.php" <?php if ($page == 'forums.php'):?>class="active"<?php endif; ?>><?php echo ACPHeaderT::get('forums'); ?></a></li>
						<li><a href="./news.php" <?php if ($page == 'news.php'):?>class="active"<?php endif; ?>><?php echo ACPHeaderT::get('news'); ?></a></li>
						<li><a href="./smileys.php" <?php if ($page == 'smileys.php'):?>class="active"<?php endif; ?>><?php echo ACPHeaderT::get('smileys'); ?></a></li>
						<li><a href="./ranks.php" <?php if ($page == 'ranks.php'):?>class="active"<?php endif; ?>><?php echo ACPHeaderT::get('ranks'); ?></a></li>
						<li><a href="./groups.php" <?php if ($page == 'groups.php'):?>class="active"<?php endif; ?>><?php echo ACPHeaderT::get('groups'); ?></a></li>
						<li><a href="./themes.php" <?php if ($page == 'themes.php'):?>class="active"<?php endif; ?>><?php echo ACPHeaderT::get('themes'); ?></a></li>
						<li><a href="./plugins.php" <?php if ($page == 'plugins.php'):?>class="active"<?php endif; ?>><?php echo ACPHeaderT::get('plugins'); ?></a></li>

						<li class="heading"><?php echo ACPHeaderT::get('plugin_settings'); ?></li>

						<?php
							$installedPlugins = PluginHelper::getListedPlugins();

							if (count($installedPlugins) < 1) {
								echo '<li class="nothing">'.ACPHeaderT::get('no_plugins_active').'</li>';
							} else {
								foreach ($installedPlugins as $installedPlugin) {
									echo '
										<li>
											<a href="./pluginSettings.php?package='.$installedPlugin->getPackageName().'" '.($page == 'pluginSettings.php' && $_GET['package'] == $installedPlugin->getPackageName() ? 'class="active"' : '').'>
												'.$installedPlugin->getTitle().'
											</a>
										</li>
									';
								}
							}
						?>
						
					</ul>
				</nav>
			</div>

			<div class="content">