<?php
	$pageName = htmlspecialchars(basename($_GET['page']));
	unset($_GET['page']);

	include 'base.php';

	// If custom page
	if (substr($pageName, -4) == '.php') {
		unset($_GET[$token->getTokenName()]);
		unset($_POST[$token->getTokenName()]);

		$pageCreators = PluginHelper::delegate('__onCreatePage', array(substr($pageName, 0, strlen($pageName) - 4), $_GET));

		if (count($pageCreators) == 1) {
			// Get single page creator
			foreach ($pageCreators as $p) {
				$pageCreator = $p;
			}

			// Does it implement PageCreator?
			if (!$pageCreator instanceof PageCreator) {
				Functions::log(Functions::LOG_ERROR, get_class($pageCreator) . ' does not implement interface PageCreator');
			} else {
				Templates::assign('pageTitle', $pageCreator->getTitle());
				Templates::assign('customContent', $pageCreator->getContent());

				Templates::display('custom');
			}

			exit;
		} else if (count($pageCreators) > 1) {
			// CLASH OF PLUGINS!!
			$errorMessage = 'Page Creator conflict for page ' . $pageName . ':<br /><br /><ul>';

			foreach ($pageCreators as $package => $pageCreator) {
				$errorMessage .= '<b>' . $package . '</b>: ' . get_class($pageCreator);
			}

			$errorMessage .= '</ul>';

			Functions::log(Functions::LOG_ERROR, $errorMessage);
			exit;
		}
	}

	// Else, show 404 page
	Templates::display('404');
?>