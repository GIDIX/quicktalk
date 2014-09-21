<?php
	/**
	 * NavigationItem
	 * An item in the main navigation (who would've thought of this :O).
	 * 
	 * @package de.gidix.QuickTalk.lib.themes
	 * @author GIDIX
	 */
	class NavigationItem {
		protected $title;
		protected $url;
		protected $active = false;
		protected $subItems = array();

		/**
		 * Constructor.
		 */
		public function __construct($title, $url) {
			$this->title = $title;
			$this->url = $url;
		}

		/**
		 * Get the title of the item.
		 * 
		 * @return string
		 */
		public function getTitle() {
			return $this->title;
		}

		/**
		 * Where does this item point to?
		 * 
		 * @return string
		 */
		public function getURL() {
			return $this->url;
		}

		/**
		 * Is this item currently being visited, therefore active?
		 * 
		 * @return boolean
		 */
		public function isActive() {
			return $this->active;
		}

		/**
		 * Make this item active.
		 * 
		 * @param boolean $state
		 */
		public function setActive($state) {
			$this->active = (boolean) $state;
		}

		/**
		 * Get all sub-items of this item.
		 * 
		 * @return array
		 */
		public function getSubItems() {
			return $this->subItems;
		}

		/**
		 * Add a sub-item.
		 * 
		 * @param NavigationItem $i Item to add.
		 */
		public function addSubItem(NavigationItem $i) {
			$this->subItems[] = $i;
		}
	}
?>