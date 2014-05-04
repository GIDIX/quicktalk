<?php
	/**
	 * PageCreator interface
	 * This interface makes sure, that every plugin that creates custom pages follows
	 * the rules.
	 * 
	 * @package de.gidix.QuickTalk.lib.plugins.interfaces
	 * @author GIDIX
	 */
	interface PageCreator {
		/**
		 * Get the title of the created page.
		 * 
		 * @return string
		 */
		public function getTitle();

		/**
		 * Get the name of the page (no extension).
		 * 
		 * @return string
		 */
		public function getName();

		/**
		 * Get the content of the created page.
		 * 
		 * @return string
		 */
		public function getContent();
	}
?>