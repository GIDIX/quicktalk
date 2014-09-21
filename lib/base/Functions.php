<?php
	/**
	 * 	Functions
	 *  Internal functions. Nothing to see here ;)
	 * 
	 *	@package de.gidix.QuickTalk.lib.base
	 *  @author GIDIX
	 */
	abstract class Functions {
		const LOG_DEBUG = 'debug';
		const LOG_ERROR = 'error';
		const LOG_INFO = 'info';
		const LOG_WARNING = 'warning';

		public static function log($type, $message) {
			if ($type == self::LOG_DEBUG) {
				$msg = "> DEBUG:
".$message."\n\n";

				file_put_contents(PATH . 'acp/logs/DEBUG.log', $msg, FILE_APPEND);
			} else if ($type == self::LOG_ERROR) {
				$content = file_get_contents(PATH . 'themes/error/error.html');
				$content = str_replace('{{ERROR}}', $message, $content);

				die($content);
			} else if ($type == self::LOG_INFO) {
				$msg = "> INFO:
".$message."\n\n";

				file_put_contents(PATH . 'acp/logs/INFO.log', $msg, FILE_APPEND);
			} else if ($type == self::LOG_WARNING) {
				$msg = "> WARNING:
".$message."\n\n";

				file_put_contents(PATH . 'acp/logs/WARNING.log', $msg, FILE_APPEND);
			}
		}

		/**
		 * Checks whether a class implements an interface or not.
		 * 
		 * @param string $class Class to check
		 * @param string $interface Interface to check
		 * 
		 * @return boolean Implements?
		 */
		public static function classImplements($class, $interface) {
			$ref = new ReflectionClass($class);

			try {
				return $ref->implementsInterface($interface);
			} catch (Exception $e) {
				Functions::log(Functions::LOG_ERROR, $e->getMessage());
				return false;
			}
		}
	}
?>