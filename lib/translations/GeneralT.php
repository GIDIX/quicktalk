<?php
	class GeneralT extends Translation {
		protected static $strings = array();
		protected static $stringsEN;

		public static function init() {
			self::$strings = parent::getStrings(substr(__CLASS__, 0, strlen(__CLASS__) - 1));
		}

		public static function get($key) {
			$string = self::$strings[$key];

			if (!isset($string)) {
				if (!isset(self::$stringsEN)) {
					self::$stringsEN = parent::getStrings(substr(__CLASS__, 0, strlen(__CLASS__) - 1), parent::LANG_EN);
				}

				return self::$stringsEN[$key];
			}

			return $string;
		}

		public static function getFormat($key) {
			$string = self::get($key);
			$args = func_get_args();
			$args[0] = $string;

			return call_user_func_array('sprintf', $args);
		}
	}	
?>