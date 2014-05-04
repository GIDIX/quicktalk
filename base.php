<?php
	/**
	 * 	Base file
	 * 
	 * 	To use QuickTalk, this file needs to be included. It automatically
	 *  includes all necessary base files, establishes a valid
	 *  database connection and loads all plugins.
	 * 
	 *	@package de.gidix.QuickTalk.base
	 *  @author GIDIX
	 */

	session_start();
	header('Content-Type: text/html; charset=utf-8');

	define('PATH', dirname(__FILE__) . '/');
	define('LIB', PATH . 'lib/');

	$page = basename($_SERVER['SCRIPT_FILENAME']);

	// If config file is NOT present, redirect to installation process.
	if (!is_file(LIB . 'static/config.php')) {
		header("Location: ./install/");
	}

	error_reporting(E_ALL & ~E_NOTICE);

	/*
	 *=	INCLUDES: DO NOT DELETE ANY OF THESE LINES. =*
	 */
	// Config
	require LIB . 'static/config.php';
	define('PREFIX', $DBCRED['prefix']);

	// Base
	require LIB . 'static/definitions.php';

	// Languages
	require LIB . 'translations/Translation.php';
	define('LANG', Translation::getLang());
	define('LANGS', LIB . 'translations/');

	require LANGS . 'GeneralT.php';
	GeneralT::init();

	// Classes
	require LIB . 'base/Database.php';
	require LIB . 'base/Config.php';
	require LIB . 'base/Token.php';
	require LIB . 'base/Functions.php';
	require LIB . 'base/UserManager.php';
	require LIB . 'base/InfoMessage.php';
	require LIB . 'base/AdminInfoMessage.php';

	require LIB . 'user/UserReadOnly.php';
	require LIB . 'user/User.php';

	require LIB . 'themes/scss/scss.inc.php';
	require LIB . 'static/URLController.php';
	require LIB . 'themes/Templates.php';

	// ACP stuff
	require LIB . 'acp/SettingsContainer.php';

	// Plugin interfaces
	require LIB . 'plugins/interfaces/PluginInterface.php';
	require LIB . 'plugins/interfaces/PageCreator.php';

	// Plugin Classes
	require LIB . 'plugins/PluginManifest.php';
	require LIB . 'plugins/Plugin.php';
	require LIB . 'plugins/PluginHelper.php';

	// Connect to database
	$db = new Database($DBCRED['host'], $DBCRED['username'], $DBCRED['password'], $DBCRED['database']);

	// Unset $DBCRED for security reasons
	unset($DBCRED);

	$userManager = new UserManager();
	$user = $userManager->getUser();

	// Tokens
	$token = new Token();

	$token->_('user.php', 'GET', RANK_USER);
	$token->_('viewforum.php', 'GET', RANK_USER);
	$token->_('viewtopic.php', 'GET', RANK_USER);

	if (isset($disableTokenHere) && is_array($disableTokenHere)) {
		foreach ($disableTokenHere as $ex) {
			$token->_($ex, 'GET, POST', RANK_USER);
			$token->_($ex, 'GET, POST', RANK_ADMIN);
		}
	}

	// Plugins
	$activePlugins = array();
	PluginHelper::loadActivePlugins();
	PluginHelper::delegate('__onCreate', array($db, $user));

	Templates::init();

	Templates::assignVars(array(
		'pageTitle'	=>	Config::get('page_title')
	));

	$token->check('POST', $_POST);
	$token->check('GET', $_GET);
?>