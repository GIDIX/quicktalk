<?php
	/**
	 * 	InputPreference
	 *  Resembles a preference with an input field for the ACP.
	 * 
	 *	@package de.gidix.QuickTalk.lib.acp
	 *  @author GIDIX
	 */
	class InputPreference extends Preference {
		protected $type = 'text';

		/**
		 * Set the type of the input.
		 * This can be text, password, email, date, ...
		 * 
		 * @param string $t Type
		 * 
		 * @return $this
		 */
		public function setType($t) {
			$this->type = $t;

			return $this;
		}

		/**
		 * Get the type of the input field.
		 * 
		 * @return string
		 */
		public function getType() {
			return $this->type;
		}
	}
?>