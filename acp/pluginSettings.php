<?php
	require '../base.php';
	ob_start();

	function __autoload($c) {
		require LIB . 'acp/' . basename($c) . '.php';
	}

	if (!$userManager->loggedIn() || !$user->isAdmin()) {
		echo ErrorMessage::setText('You are not allowed to view this page.', true);
	}

	$plugin = PluginHelper::getPluginByPackage($_GET['package']);

	if (!$plugin instanceof Plugin) {
		echo AdminErrorMessage::setText('Plugin does not exist.', true);
	}

	include 'template/header.php';

	echo '
		<h1>'.$plugin->getTitle().' Settings</h1>
	';

	$pluginSettings = $plugin->__onCreateSettings();

	if (!$pluginSettings instanceof SettingsContainer) {
		echo InfoMessage::setText('This plugin does not have any settings.');
	} else {
		$pluginSettingsPreferences = $pluginSettings->__onCreate($plugin, $db);

		if (!is_array($pluginSettingsPreferences) || count($pluginSettingsPreferences) < 1) {
			echo InfoMessage::setText('This plugin does not have any settings.');
		} else {
			if (isset($_POST['submit'])) {
				if ($pluginSettings->__onSave()) {
					echo SuccessMessage::setText('Settings saved.');
				} else {
					echo ErrorMessage::setText('Could not save settings.');
				}
			}

			echo '
				<div class="preferences">
					<form method="post" action="">
						<ul>
			';

			foreach ($pluginSettingsPreferences as $pref) {
				if (!$pref instanceof Preference) {
					continue;
				}

				if ($pref instanceof PreferenceCategory) {
					echo '
						<li class="category">
							<h3>'.$pref->getTitle().'</h3>
							'.($pref->getValue() ? '<span>' . $pref->getValue() . '</span>' : '').'
						</li>
					';
				} else if ($pref instanceof InputPreference) {
					echo '
						<li class="text">
							<div class="prefTitle">'.$pref->getTitle().'</div>
							<div class="prefContent">
								<input
									type="'.htmlspecialchars($pref->getType()).'"
									name="'.htmlspecialchars($pref->getKey()).'"
									value="'.htmlspecialchars($pref->getValue()).'"
									placeholder="'.htmlspecialchars($pref->getDefaultValue()).'"
									/>
							</div>
						</li>
					';
				} else if ($pref instanceof SelectPreference) {
					echo '
						<li class="text">
							<div class="prefTitle">'.$pref->getTitle().'</div>
							<div class="prefContent">
								<select name="'.$pref->getKey().'">
					';

					$options = $pref->getOptions();

					foreach ($options as $key => $value) {
						echo '
							<option value="'.$key.'" '.($plugin->getPreferenceValue($db, $pref->getKey()) == $key ? 'selected' : '').'>'.$value.'</option>
						';
					}

					echo '
								</select>
							</div>
						</li>
					';
				} else {
					echo '
						<li class="text">
							<div class="prefTitle">'.$pref->getTitle().'</div>
							<div class="prefContent">'.$pref->getValue().'</div>
						</li>
					';
				}
			}

			echo '
							<li class="save">
								<input type="submit" name="submit" value="'.GeneralT::get('save').'" />
							</li>
						</ul>
					</form>
				</div>
			';
		}
	}

	include 'template/footer.php';
?>