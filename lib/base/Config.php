<?php
	/**
	 * 	Config
	 *  Holds and sets config values in the database.
	 * 
	 *	@package de.gidix.QuickTalk.lib.base
	 *  @author GIDIX
	 */
	abstract class Config {
		private static $init = false;
		private static $values = array();

		private static function init() {
			global $db;

			$res = $db->query("SELECT * FROM " . TABLE_CONFIG . "");

			while ($row = $db->fetchObject($res)) {
				self::$values[$row->key] = $row->value;
			}

			ksort(self::$values);

			self::$init = true;
		}

		/**
		 *	Get a config value
		 * 
		 *  @param string $key Key
		 * 
		 *  @return string value
		 */
		public static function get($key) {
			if (!self::$init) self::init();

			return self::$values[$key];
		}

		/**
		 *	Save a config value
		 * 
		 *  @param string $key Key
		 *  @param string $value Value
		 */
		public static function save($key, $value) {
			if (!self::$init) self::init();

			global $db;

			$db->query("UPDATE " . TABLE_CONFIG . " SET value = :value WHERE key = :key", array($value, $key));
			self::$values[$key] = $value;
		}
	}
?>