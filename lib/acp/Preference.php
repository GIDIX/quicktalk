<?php
	/**
	 * 	Preference
	 *  Resembles a preference for the ACP.
	 * 
	 *	@package de.gidix.QuickTalk.lib.acp
	 *  @author GIDIX
	 */
	class Preference {
		protected $key;
		protected $title;
		protected $value;
		protected $defaultValue;

		public function __construct($key, $title, $value, $defaultValue = '') {
			$this->key = $key;
			$this->title = $title;
			$this->value = $value;
			$this->defaultValue = $defaultValue;
		}

		/**
		 * Get the key under which this preference is saved in the database.
		 * 
		 * @return string
		 */
		public function getKey() {
			return $this->key;
		}

		/**
		 * Get the title of the preference.
		 * 
		 * @return string
		 */
		public function getTitle() {
			return $this->title;
		}

		/**
		 * Get the value of the preference.
		 * 
		 * @return mixed
		 */
		public function getValue() {
			return $this->value;
		}

		/**
		 * Get the default value.
		 * 
		 * @return mixed
		 */
		public function getDefaultValue() {
			return $this->defaultValue;
		}

		/**
		 * Set the value of this preference.
		 * 
		 * @param mixed $v Value
		 * 
		 * @return $this
		 */
		public function setValue($v) {
			$this->value = $v;

			return $this;
		}

		/**
		 * Set the default value of this preference.
		 * 
		 * @param mixed $v Value
		 * 
		 * @return $this
		 */
		public function setDefaultValue($v) {
			$this->defaultValue = $v;

			return $this;
		}
	}
?>