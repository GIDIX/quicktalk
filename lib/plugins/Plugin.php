<?php
	/**
	 * Plugin
	 * This is the base class for plugins. Every plugin needs to extend this class.
	 * This class doubles as the main class for plugins and represantions of general
	 * plugins for lists and database actions.
	 * It provides empty dummy functions for plugins that do not support all features
	 * this class offers.
	 * 
	 * @package de.gidix.QuickTalk.lib.plugins
	 * @author GIDIX
	 */
	class Plugin {
		protected $id;
		protected $package;
		protected $title;
		protected $description;
		protected $author;
		protected $url;
		protected $active;

		/**
		 * Create an object of Plugin from its database row.
		 * 
		 * @param stdClass $row
		 * 
		 * @return Plugin
		 */
		public static function fromRow(stdClass $row) {
			return new self(
				$row->package,
				$row->title,
				$row->description,
				$row->author,
				$row->url,
				$row->active,
				$row->id
			);
		}

		/**
		 * Constructor.
		 */
		public function __construct($package, $title, $description, $author, $url, $active, $id = 0) {
			$this->id = $id;
			$this->package = $package;
			$this->title = $title;
			$this->description = $description;
			$this->author = $author;
			$this->url = $url;
			$this->active = $active == 1;
		}

		// Data

		/**
		 * Get the ID of the plugin, if set.
		 * 
		 * @return int
		 */
		public function getID() {
			return $this->id;
		}

		/**
		 * Get the package name of the plugin.
		 * 
		 * @return string
		 */
		public function getPackageName() {
			return $this->package;
		}

		/**
		 * Get the title of the plugin.
		 * 
		 * @return string
		 */
		public function getTitle() {
			return $this->title;
		}

		/**
		 * Get the description of the plugin.
		 * 
		 * @return string
		 */
		public function getDescription() {
			return $this->description;
		}

		/**
		 * Get the author of the plugin.
		 * 
		 * @return string
		 */
		public function getAuthor() {
			return $this->author;
		}

		/**
		 * Get URL of the author of the plugin.
		 * 
		 * @return string
		 */
		public function getURL() {
			return $this->url;
		}

		/**
		 * Is plugin marked as active?
		 * 
		 * @return bool
		 */
		public function isActive() {
			return $this->active;
		}

		// Hooks
		
		/**
		 * Creation method for the plugin. Supplies $db (Database) and $user (Logged in user, if any).
		 * 
		 * @param Database $db
		 * @param User $user
		 */
		public function __onCreate($db, $user) {

		}
		
		/**
		 * Called when an existing page is displayed.
		 */
		public function __onPageDisplay($page) {
			// Needs to be extended.
		}

		/**
		 * Called when the main navigation is created and displayed by the applied theme.
		 * Needs to either return an array full of NavigationItem objects or only one
		 * NavigationItem.
		 * Writes warnings to the log if supplied items are invalid.
		 * 
		 * @return array or NavigationItem
		 */
		public function __onCreateNavigation() {
			// Needs to be extended
		}

		/**
		 * Is called when a page does not exist to look for plugins providing it.
		 * Needs to return an instance of PageCreator or null.
		 * 
		 * @param string $filename Filenamed that was called.
		 * @param array $arguments from $_GET
		 * 
		 * @return PageCreator
		 */
		public function __onCreatePage($filename, array $arguments) {
			// Needs to be extended
		}

		/**
		 * Is called in the ACP.
		 * Needs to return an instance of PluginSettings or null.
		 * 
		 * @return PluginSettings
		 */
		public function __onCreateSettings() {
			// Needs to be extended
		}

		/**
		 * 
		 */
		public function __onFooterDisplay() {
			// Needs to be extended.	
		}

		// Settings
		
		public function getPreferenceValue(Database $db, $key) {
			$res = $db->query("
				SELECT *
				FROM " . TABLE_PLUGINS_SETTINGS . "
				WHERE package = ? AND `key` = ?
			", array(
				$this->package,
				$key
			));

			$row = $db->fetchObject($res);

			if ($row) {
				return $row->value;
			}

			return null;
		}
		
		/**
		 * Save a preference.
		 * 
		 * @param Database §db
		 * @param Preference $p
		 */
		public function savePreference(Database $db, Preference $p) {
			$oldValue = $this->getPreferenceValue($p->getKey());

			if (is_null($oldValue)) {
				$db->query("
					INSERT INTO " . TABLE_PLUGINS_SETTINGS . "
					(package, `key`, value)
					VALUES
					(?, ?, ?)
				", array(
					$this->package,
					$p->getKey(),
					$p->getValue()
				));
			} else {
				$db->query("
					UPDATE " . TABLE_PLUGINS_SETTINGS . "
					SET value = ?
					WHERE `key` = ? AND package = ?
				", array(
					is_null($p->getValue()) ? $p->getDefaultValue() : $p->getValue(),
					$p->getKey(),
					$this->package
				));
			}
		}

		/**
		 * Save multiple preferences at once.
		 * 
		 * @param Database $db
		 * @param array $prefs
		 * 
		 * @throws Exception when a passed preference is no instance of 'Preference'.
		 */
		public function savePreferences(Database $db, array $prefs) {
			$db->beginTransaction();

			$updateStatement = $db->prepare("
				UPDATE " . TABLE_PLUGINS_SETTINGS . "
				SET value = :value
				WHERE `key` = :key AND package = :package
			");

			$insertStatement = $db->prepare("
				INSERT INTO " . TABLE_PLUGINS_SETTINGS . "
				(package, `key`, value)
				VALUES
				(?, ?, ?)
			");

			foreach ($prefs as $pref) {
				if (!$pref instanceof Preference) {
					$db->rollback();
					throw new Exception("Passed preference is no instance of 'Preference'.", 41);
				}

				if ($pref instanceof PreferenceCategory) {
					continue;
				}

				$oldValue = $this->getPreferenceValue($db, $pref->getKey());

				if (is_null($oldValue)) {
					$insertStatement->execute(array(
						$this->package,
						$pref->getKey(),
						is_null($pref->getValue()) ? $pref->getDefaultValue() : $pref->getValue()
					));
				} else {
					$updateStatement->execute(array(
						is_null($pref->getValue()) ? $pref->getDefaultValue() : $pref->getValue(),
						$pref->getKey(),
						$this->package
					));
				}
			}

			$db->commit();
		}
	}
?>