<?php
	require '../base.php';
	ob_start();

	require LANGS . 'ACPPluginsT.php';
	ACPPluginsT::init();

	function __autoload($c) {
		require LIB . 'plugins/' . basename($c) . '.php';
	}

	if (!$userManager->loggedIn() || !$user->isAdmin()) {
		echo ErrorMessage::setText('You are not allowed to view this page.', true);
	}

	if (isset($_GET['install'])) {
		$packageName = basename($_GET['install']);

		try {
			$manifest = PluginManifest::fromPackageName($packageName);
			PluginHelper::installPlugin($manifest);

			header("Location: ./plugins.php");
			exit;
		} catch (Exception $e) {
			echo AdminErrorMessage::setText(ACPPluginsT::getFormat('install_error', htmlspecialchars($packageName), $e->getMessage()), true);
		}
	} else if (isset($_GET['uninstall'])) {
		$packageName = basename($_GET['uninstall']);
		
		if ($_GET['ok'] == 1) {
			PluginHelper::uninstallPlugin($packageName);

			header("Location: ./plugins.php");
			exit;
		} else {
			echo AdminQuestionMessage::setText(ACPPluginsT::get('uninstall_confirmation'), true)
				->addLink(GeneralT::get('yes'), './plugins.php?uninstall=' . htmlspecialchars($packageName) . '&ok=1')
				->addLink(GeneralT::get('no'), './plugins.php');
		}
	}

	include 'template/header.php';

	echo '
		<h1>'.ACPPluginsT::get('active_plugins').'</h1>

		<div class="plugins">
	';

	$installedPlugins = PluginHelper::getActivePlugins();

	foreach ($installedPlugins as $plugin) {
		$parsedURL = parse_url($plugin->getURL());

		$url = is_array($parsedURL) ? $plugin->getURL() : '#';

		echo '
			<div class="item">
				<div class="meta">
					<h2>'.htmlspecialchars($plugin->getTitle()).' <small>'.GeneralT::getFormat('by', '<a href="'.$url.'" target="_blank">'.htmlspecialchars($plugin->getAuthor()).'</a>').'</small></h2>

					<p>
						'.htmlspecialchars($plugin->getDescription()).'
					</p>
				</div>

				<div class="actions">
					<a href="./plugins.php?uninstall=' . htmlspecialchars($plugin->getPackageName()) . '" class="button redB">'.ACPPluginsT::get('uninstall').'</a>
				</div>
			</div>
		';
	}

	if (count($installedPlugins) < 1) {
		echo InfoMessage::setText(ACPPluginsT::get('no_plugins_active'));
	}

	echo '
		</div>

		<br />
		
		<h1>'.ACPPluginsT::get('available_plugins').'</h1>

		<div class="plugins">
	';

	$availablePlugins = PluginHelper::getAvailablePlugins();

	foreach ($availablePlugins as $plugin) {
		$parsedURL = parse_url($plugin->getURL());

		$url = is_array($parsedURL) ? $plugin->getURL() : '#';

		echo '
			<div class="item">
				<div class="meta">
					<h2>'.htmlspecialchars($plugin->getTitle()).' <small>'.GeneralT::getFormat('by', '<a href="'.$url.'" target="_blank">'.htmlspecialchars($plugin->getAuthor()).'</a>').'</small></h2>

					<p>
						'.htmlspecialchars($plugin->getDescription()).'
					</p>
				</div>

				<div class="actions">
					<a href="./plugins.php?install=' . htmlspecialchars($plugin->getPackageName()) . '" class="button greenB">'.ACPPluginsT::get('install').'</a>
				</div>
			</div>
		';
	}

	if (count($availablePlugins) < 1) {
		echo InfoMessage::setText(ACPPluginsT::get('no_new_plugins'));
	}

	echo '
		</div>
	';

	include 'template/footer.php';
?>