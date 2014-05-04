<?php
	/**
	 * 	SettingsContainer
	 *  Interface for a container full of Preference-objects. This is used by the ACP
	 *  for internal settings and plugin settings.
	 * 
	 *	@package de.gidix.QuickTalk.lib.acp
	 *  @author GIDIX
	 */
	interface SettingsContainer {
		/**
		 * Is called when the settings page is created.
		 * You need to create all your settings items here.
		 * 
		 * @return array full of Preference objects
		 */
		public function __onCreate(Plugin $plugin, Database $db);

		/**
		 * Is called when the user saves the settings.
		 */
		public function __onSave();
	}
?>