<?php
	class SamplePreferences implements SettingsContainer {
		private $plugin;
		private $db;
		private $prefs = array();
		
		private $prefKeys = array(
			'testdrive_sample',
			'testdrive_sample_input',
			'testdrive_sample_select'
		);

		public function __onCreate(Plugin $plugin, Database $db) {
			$this->plugin = $plugin;
			$this->db = $db;

			$selectPreference = new SelectPreference('testdrive_sample_select', 'Select a value:', '');
			$selectPreference->setOption('Test Title', 'TestValue')
							 ->setOption('Another Test', 'With another value');

			$this->prefs = array(
				'testdrive_cat_general'		=> new PreferenceCategory('testdrive_cat_general', 'General', null),
				'testdrive_sample'			=> new Preference('testdrive_sample', 'Sample Text Preference:', 'With a Sample Value'),
				'testdrive_sample_input'	=> new InputPreference('testdrive_sample_input', 'Sample Input:', '', 'Sample Default Value'),
				'testdrive_cat_another'		=> new PreferenceCategory('', 'Another Category', '...even with a description!'),
				'testdrive_sample_select'	=> $selectPreference
			);

			return $this->prefs;
		}

		public function __onSave() {
			foreach ($this->prefKeys as $prefKey) {
				if (isset($_POST[$prefKey])) {
					$this->prefs[$prefKey]->setValue($_POST[$prefKey]);
				}
			}

			$this->plugin->savePreferences($this->db, $this->prefs);

			return true;
		}
	}
?>