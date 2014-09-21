<?php
	/**
	 * 	SelectPreference
	 *  Resembles a preference with a drop-down select field for the ACP.
	 * 
	 *	@package de.gidix.QuickTalk.lib.acp
	 *  @author GIDIX
	 */
	class SelectPreference extends Preference {
		protected $options = array();

		/**
		 * Add an option.
		 * <option value="$value">$title</option>
		 * 
		 * @param string $title User-visible title
		 * @param string $value Internal value
		 * 
		 * @return $this
		 */
		public function setOption($title, $value) {
			$this->options[$value] = $title;

			return $this;
		}

		/**
		 * Set multiple options.
		 * This needs an associative array, where the key of the array item is the internal value and
		 * the value of the array item is the user-visible title.
		 * 
		 * @param array $options Options Array
		 * 
		 * @return $this
		 */
		public function setOptions(array $options) {
			$this->options = $options;

			return $this;
		}

		/**
		 * Get all options.
		 * 
		 * @return array
		 */
		public function getOptions() {
			return $this->options;
		}
	}
?>