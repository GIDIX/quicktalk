<?php
	/**
	 * PluginManifest
	 * Every plugin has a manifest describing itself. This class is just that.
	 * 
	 * @package de.gidix.QuickTalk.lib.plugins
	 * @author GIDIX
	 */
	class PluginManifest {
		const FILENAME = 'manifest.json';

		const FIELD_PACKAGE = "package";
		const FIELD_TITLE = "title";
		const FIELD_DESCRIPTION = "description";
		const FIELD_AUTHOR = "author";
		const FIELD_URL = "url";
		const FIELD_ENTRYPOINT = "entryPoint";

		protected $package;
		protected $title;
		protected $description;
		protected $author;
		protected $url;
		private $entryPoint;

		/**
		 * Create a manifest object from a plugin's package name.
		 * This looks into the plugin's folder like the NSA does...
		 * 
		 * @param string $package
		 * 
		 * @return PluginManifest
		 */
		public static function fromPackageName($package) {
			$json = @json_decode(@file_get_contents(PATH . 'plugins/' . basename($package) . '/' . self::FILENAME), true);

			if (!is_array($json)) {
				throw new InvalidManifestException('Cannot read manifest of plugin ' . $package, 42);
			}

			return new self(
				$json[self::FIELD_PACKAGE],
				$json[self::FIELD_TITLE],
				$json[self::FIELD_DESCRIPTION],
				$json[self::FIELD_AUTHOR],
				$json[self::FIELD_URL],
				$json[self::FIELD_ENTRYPOINT]
			);
		}

		/**
		 * Constructor.
		 */
		public function __construct($package, $title, $description, $author, $url, $entryPoint) {
			$this->package = $package;
			$this->title = $title;
			$this->description = $description;
			$this->author = $author;
			$this->url = $url;
			$this->entryPoint = $entryPoint;
		}

		/**
		 * Get the package name, i.e. de.gidix.testDrive
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
		 * Who is to blame for this plugin?
		 * 
		 * @return string
		 */
		public function getAuthor() {
			return $this->author;
		}

		/**
		 * Where can I find him/her?
		 * 
		 * @return string
		 */
		public function getURL() {
			return $this->url;
		}

		/**
		 * What class starts the plugin?
		 * For internal use only!
		 * 
		 * @return string
		 */
		public function getEntryPoint() {
			return $this->entryPoint;
		}
	}

	/**
	 * If the manifest was bad, this is being thrown.
	 */
	class InvalidManifestException extends Exception {}
?>