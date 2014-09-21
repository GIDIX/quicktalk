<?php
	include 'SamplePageCreator.php';
	include 'SamplePreferences.php';

	class TestDrivePlugin extends Plugin implements PluginInterface {
		protected $db;
		protected $user;

		public function __onCreate($db, $user) {
			$this->db = $db;
			$this->user = $user;
		}

		public function __onCreateNavigation() {
			return array(
				new NavigationItem('Sample Page', './sample.php?argument=sample')
			);
		}

		public function __onCreatePage($pageName, array $args) {
			if ($pageName == 'sample') {
				return new SamplePageCreator($this->db, $this->user, $pageName, $args);
			}
		}

		public function __onCreateSettings() {
			return new SamplePreferences();
		}
	}
?>