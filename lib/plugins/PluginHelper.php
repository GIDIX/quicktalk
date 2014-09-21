<?php
	/**
	 * PluginHelper
	 * Helper for plugin management, like starting and stopping a plugin,
	 * delegating methods to plugins, install them, remove them etc.
	 * 
	 * @package de.gidix.QuickTalk.lib.plugins
	 * @author GIDIX
	 */
	class PluginHelper {
		protected static $loadedPlugins = array();

		/**
		 * Get all plugins from database with argument $arg (SQL query).
		 * 
		 * @param string $arg Query
		 * @param array $arr Arguments for $arg (preparation)
		 * 
		 * @return Resource
		 */
		private static function getPluginsResByArgument($arg, $arr = null) {
			global $db;
			$res = $db->query("SELECT * FROM " . TABLE_PLUGINS . " " . $arg, $arr);

			return $res;
		}

		/**
		 * Make an array of plugins from Resource $res.
		 * 
		 * @param Resource $res Database Resource to use
		 * 
		 * @return array
		 */
		private static function convertResToArray($res) {
			global $db;

			$plugins = array();

			while ($row = $db->fetchObject($res)) {
				$plugins[] = Plugin::fromRow($row);
			}

			return $plugins;
		}

		/**
		 * Install a plugin by its manifest.
		 * 
		 * @return Plugin
		 */
		public static function installPlugin(PluginManifest $manifest) {
			global $db;

			$pluginClashCheck = self::getPluginByPackage($manifest->getPackageName());

			if ($pluginClashCheck instanceof Plugin) {
				throw new Exception("Package '" . $manifest->getPackageName() . "' does already exist.", 23);
			}

			$db->query("
				INSERT INTO " . TABLE_PLUGINS . "
				(package, title, description, author, url, active)
				VALUES
				(:p, :t, :d, :a, :u, 1)
			", array(
				$manifest->getPackageName(),
				$manifest->getTitle(),
				$manifest->getDescription(),
				$manifest->getAuthor(),
				$manifest->getURL()
			));

			return self::getPluginByPackage($manifest->getPackageName());
		}

		/**
		 * Uninstall a plugin.
		 * This does not delete its contents from disk, but wipes its settings.
		 * 
		 * @return boolean
		 */
		public static function uninstallPlugin($packageName) {
			global $db;

			$db->beginTransaction();

			$db->query("
				DELETE FROM " . TABLE_PLUGINS_SETTINGS . "
				WHERE package = ?
			", array($packageName));

			$db->query("
				DELETE FROM " . TABLE_PLUGINS . "
				WHERE package = ?
			", array($packageName));

			$db->commit();

			return true;
		}

		/**
		 * Get all currently listed plugins.
		 * 
		 * @return array
		 */
		public static function getListedPlugins() {
			$res = self::getPluginsResByArgument("ORDER BY id ASC");
			return self::convertResToArray($res);
		}

		/**
		 * Get all active plugins.
		 * These are plugins that the admin decided to use.
		 * 
		 * @return array
		 */
		public static function getActivePlugins() {
			$res = self::getPluginsResByArgument("WHERE active = 1 ORDER BY id ASC");
			return self::convertResToArray($res);
		}

		/**
		 * Get all available plugins.
		 * Available plugins are plugins, that are in the plugins-folder but not listed in the database.
		 * This function therefore only returns PluginManifest-objects.
		 * 
		 * @return array
		 */
		public static function getAvailablePlugins() {
			$pluginDirectories = glob(PATH . 'plugins/*');
			$available = array();

			foreach ($pluginDirectories as $dir) {
				$packageName = basename($dir);

				$pluginExistsCheck = self::getPluginByPackage($packageName);

				if ($pluginExistsCheck instanceof Plugin) continue;

				try {
					$available[] = PluginManifest::fromPackageName($packageName);
				} catch (InvalidManifestException $e) {
					Functions::log(Functions::LOG_WARNING, $packageName . ' is not a valid plugin folder.');
				}
			}

			return $available;
		}

		/**
		 * Get a specific plugin by its package name.
		 * 
		 * @return array
		 */
		public static function getPluginByPackage($package) {
			$res = self::getPluginsResByArgument("WHERE package = ?", array($package));
			$pluginArray = self::convertResToArray($res);

			if (count($pluginArray) < 1) {
				return null;
			}
			
			$plugin = $pluginArray[0];
			$pluginClass = self::getEntryPointClassForPlugin($plugin);

			// Load class
			include_once PATH . 'plugins/' . $plugin->getPackageName() . '/Plugin.php';

			return new $pluginClass($plugin->getPackageName(), $plugin->getTitle(), $plugin->getAuthor(), $plugin->getURL(), $plugin->isActive(), $plugin->getID());
		}

		/**
		 * Get all plugins loaded.
		 * Don't call this too early as plugins are loaded AFTER everything else has loaded. This needs to be called
		 * after {@link loadActivePlugins()} was called.
		 * 
		 * @return array
		 */
		public static function getLoadedPlugins() {
			return self::$loadedPlugins;
		}

		/**
		 * Load plugins that are marked as active.
		 * This also checks for the implementation of PluginInterface in the main class of the plugin.
		 */
		public static function loadActivePlugins() {
			$plugins = self::getActivePlugins();

			foreach ($plugins as $plugin) {
				try {
					$pluginClass = self::getEntryPointClassForPlugin($plugin);

					// Load class
					include_once PATH . 'plugins/' . $plugin->getPackageName() . '/Plugin.php';

					// Finally, start plugin
					$p = new $pluginClass($plugin->getPackageName(), $plugin->getTitle(), $plugin->getDescription(), $plugin->getAuthor(), $plugin->getURL(), 1, $plugin->getID());
				
					if (!$p instanceof PluginInterface) {
						throw new InvalidManifestException('Plugin ' . $plugin->getPackageName() . ' does not implement PluginInterface.', 42);
					}
					
					$plugins[$plugin->getPackageName()] = $p;
				} catch (InvalidManifestException $e) {
					Functions::log(Functions::LOG_ERROR, $e->getMessage());
				} catch (InvalidPluginException $e) {
					Functions::log(Functions::LOG_ERROR, $e->getMessage());
				}
			}

			self::$loadedPlugins = $plugins;
		}

		public static function getEntryPointClassForPlugin(Plugin $plugin) {
			// What is the entry point?
			$manifest = PluginManifest::fromPackageName($plugin->getPackageName());
			$pluginClass = $manifest->getEntryPoint();

			return $pluginClass;
		}

		/**
		 * Call a method of all plugins, i.e. __onPageDisplay
		 * This accepts arguments passed into an array $args.
		 * 
		 * @param string $method Function to call
		 * @param array $args Arguments for the function
		 * 
		 * @return array Return Values
		 */
		public static function delegate($method, $args = array()) {
			$returns = array();

			foreach (self::$loadedPlugins as $p) {
				$returnValue = call_user_func_array(array($p, $method), $args);

				if (!is_null($returnValue)) {
					$returns[$p->getPackageName()] = $returnValue;
				}
			}

			return $returns;
		}
	}

	/**
	 * Exception for when a plugin is not valid.
	 */
	class InvalidPluginException extends Exception {}
?>