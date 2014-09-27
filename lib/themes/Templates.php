<?php
	require_once LIB . 'themes/ThemeFunctionsInterface.php';
	require_once LIB . 'themes/NavigationItem.php';

	/**
	 * Templates
	 * Holds page variables, displays themes.
	 * 
	 * @package de.gidix.QuickTalk.lib.themes
	 * @author GIDIX
	 */
	class Templates {
		protected static $currentTheme;
		protected static $vars;
		protected static $navigationItems = array();

		/*
			INIT
		 */
		
		public static function init() {
			self::$currentTheme = Config::get('current_theme');

			$functionsFile = PATH . 'themes/' . self::$currentTheme . '/ThemeFunctions.php';

			if (!is_file($functionsFile)) {
				Functions::log(Functions::LOG_ERROR, 'Class "ThemeFunctions" does not exist.');
			}
			
			require_once $functionsFile;

			if (!class_exists('ThemeFunctions')) {
				Functions::log(Functions::LOG_ERROR, 'Class "ThemeFunctions" is not defined.');
			} else if (!Functions::classImplements('ThemeFunctions', 'ThemeFunctionsInterface')) {
				Functions::log(Functions::LOG_ERROR, 'Class "ThemeFunctions" does not implement "ThemeFunctionsInterface".');
			}

			self::initNavigation();
		}

		/**
		 * Adds all necessary navigation items.
		 * This also calls plugins to add additional items where needed.
		 */
		protected static function initNavigation() {
			$items = array(
				'Start'		=>	'./index.php',
				'Forums'	=>	'./forums.php'
			);

			foreach ($items as $title => $url) {
				$item = new NavigationItem($title, $url);

				self::addNavigationItem($item);
			}

			$pluginItems = PluginHelper::delegate('__onCreateNavigation');

			foreach ($pluginItems as $package => $item) {
				if ($item instanceof NavigationItem) {
					self::addNavigationItem($item);
				} else if (is_array($item)) {
					foreach ($item as $i) {
						if ($i instanceof NavigationItem) {
							self::addNavigationItem($i);
						} else {
							Functions::log(Functions::LOG_WARNING, 'Plugin ' . $package . ' supplied invalid navigation item.');
						}
					}
				} else {
					Functions::log(Functions::LOG_WARNING, 'Plugin ' . $package . ' supplied invalid navigation item.');
				}
			}
		}

		/*
			ASSIGNS
		 */

		/**
		 * Assign a value to a key for use in themes.
		 * 
		 * @param string $key Key
		 * @param mixed $value Value
		 */
		public static function assign($key, $value) {
			self::$vars[$key] = $value;
		}

		/**
		 * Assign multiple values at once using a key-value-array.
		 * 
		 * @param array $vars Variables (you need to use KEY-VALUE-pairs!)
		 */
		public static function assignVars(array $vars) {
			foreach ($vars as $key => $value) {
				self::$vars[$key] = $value;
			}
		}

		/**
		 * Get a defined value for $key.
		 * 
		 * @param $key Key to get
		 *
		 * @return mixed value
		 */
		public static function getVar($key) {
			return isset(self::$vars[$key]) ? self::$vars[$key] : '';
		}

		/**
		 * Get base URL for the installation.
		 * 
		 * @return string
		 */
		public static function getBaseURL() {
			return '//' . $_SERVER['SERVER_NAME'].'/'.dirname($_SERVER['PHP_SELF']);
		}

		/*
			NAVIGATION
		 */

		/**
		 * Add a navigation item to the global menu.
		 * 
		 * @param NavigationItem $i Item
		 */
		public static function addNavigationItem(NavigationItem $i) {
			self::$navigationItems[] = $i;
		}

		/**
		 * Get all navigation items.
		 * 
		 * @return array Navigation Items
		 */
		public static function getNavigationItems() {
			return self::$navigationItems;
		}

		/*
			THEME RENDERING
		 */

		public static function getThemePath() {
			return PATH . 'themes/' . self::$currentTheme . '/';
		}

		public static function getThemeURL() {
			return './themes/' . self::$currentTheme . '/';
		}

		public static function getSCSS($file) {
			$uncompiled = self::getThemePath() . 'css/' . basename($file) . '.scss';
			$compiled = self::getThemePath() . 'cache/' . basename($file) . '.css';

			if (@filemtime($compiled) < @filemtime($uncompiled)) {
				$scss = new scssc();
				$scss->setFormatter('scss_formatter_compressed');

				try {
					file_put_contents($compiled, $scss->compile(file_get_contents($uncompiled)));
				} catch (Exception $e) {
					Functions::log(Functions::LOG_ERROR, $e->getMessage());
				}
			}

			return self::getThemeURL() . 'cache/' . basename($file) . '.css';
		}

		public static function getReadOnlyUser() {
			global $user;

			if ($user instanceof User) {
				return UserReadOnly::fromUser($user);
			} else {
				return null;
			}
		}

		public static function display($file, $returnValue = false) {
			global $errorMessage, $token;
			
			$user = self::getReadOnlyUser();
			$tplFile = self::getThemePath() . basename($file) . '.php';

			if (!is_file($tplFile)) {
				Functions::log(Functions::LOG_ERROR, 'Template for "' . $file . '.php" missing.');
			}

			if (strpos(file_get_contents($tplFile), 'global $') !== false) {
				Functions::log(Functions::LOG_ERROR, 'Invalid call in template "'.$file . '.php".');
			}

			if ($returnValue) {
				return $token->auto_append(file_get_contents($tplFile));
			} else {
				ob_start();

				include $tplFile;

				$content = ob_get_contents();
				ob_get_clean();

				$content = $token->auto_append($content);
				echo $content;
			}
		}
	}
?>
