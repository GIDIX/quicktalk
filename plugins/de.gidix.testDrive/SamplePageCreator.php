<?php
	class SamplePageCreator implements PageCreator {
		private $db;
		private $user;
		private $filename;
		private $arguments;

		public function __construct($databaseObject, $userObject, $filename, array $arguments) {
			$this->db = $databaseObject;
			$this->user = $userObject;
			$this->arguments = $arguments;
			$this->filename = $filename;
		}
		
		public function getTitle() {
			return 'Sample Page';
		}

		public function getName() {
			return $this->filename;
		}

		public function getContent() {
			$arguments = '<ul>';

			foreach ($this->arguments as $key => $value) {
				$arguments .= '
					<li>'.$key.' => ' . $value . '
				';
			}

			$arguments .= '</ul>';

			return '
				<h1>Sample Page</h1>

				<p>
					This is how you create a custom page. This custom page got these arguments:

					'.$arguments.'
				</p>
			';
		}
	}
?>