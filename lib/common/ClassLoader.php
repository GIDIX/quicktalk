<?php
	/**
	 * Automatic Class Loader
	 * This class automatically handles all necessary inclusions/requires
	 * for any class. Just use it.
	 * 
	 * @package de.gidix.QuickTalk.lib.base
	 * @author GIDIX
	 */
	class ClassLoader {
		private $loadedClasses = array();

		public function __construct() {
			spl_autoload_register(array($this, 'base'));
			spl_autoload_register(array($this, 'common'));
			spl_autoload_register(array($this, 'plugins'));
			spl_autoload_register(array($this, 'themes'));
			spl_autoload_register(array($this, 'acp'));
			spl_autoload_register(array($this, 'translations'));
			spl_autoload_register(array($this, 'user'));
			spl_autoload_register(array($this, 'functions'));
			spl_autoload_register(array($this, 'statics'));
			spl_autoload_register(array($this, 'forum'));
		}

		/**
		 * /lib/attachments
		 */
		private function acp($className) {
			$this->tryClass('acp', $className);
		}

		/**
		 * /lib/base
		 */
		private function base($className) {
			$this->tryClass('base', $className);
		}

		/**
		 * /lib/common
		 */
		private function common($className) {
			$this->tryClass('common', $className);
		}

		/**
		 * /lib/functions
		 */
		private function forum($className) {
			$this->tryClass('forum', $className);
		}

		/**
		 * /lib/functions
		 */
		private function functions($className) {
			$this->tryClass('functions', $className);
		}

		/**
		 * /lib/plugins
		 */
		private function plugins($className) {
			$this->tryClass('plugins', $className);
		}

		/**
		 * /lib/static
		 */
		private function statics($className) {
			$this->tryClass('static', $className);
		}

		/**
		 * /lib/translations
		 */
		private function translations($className) {
			$this->tryClass('translations', $className);
		}

		/**
		 * /lib/themes
		 */
		private function themes($className) {
			$this->tryClass('themes', $className);
		}

		/**
		 * /lib/user
		 */
		private function user($className) {
			$this->tryClass('user', $className);
		}

		/**
		 * Try to include a class, if it exists.
		 * Otherweise, ignore it.
		 * 
		 * @param string $dir Directory to search in
		 * @param string $class Name of the class
		 * 
		 * @return boolean
		 */
		private function tryClass($dir, $class) {
			$fullPath = LIB . $dir . '/' . basename($class) . '.php';

			if (is_file($fullPath)) {
				$this->loadedClasses[] = $fullPath;
				require $fullPath;
				return true;
			}
		}

		/**
		 * Get a list of all currently used classes.
		 * 
		 * @return array full of class names
		 */
		public function getLoadedClasses() {
			return $this->loadedClasses;
		}
	}

	$cl = new ClassLoader();
?>