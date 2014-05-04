<?php
	/**
	 * Token
	 * Secures all GET and POST requests with a user-specific token from CRSF.
	 * 
	 * @package de.gidix.QuickTalk.lib.base
	 * @author ProFusion
	 */
	class Token {
		private $token = null;
		private $session = 'quicktalk_token';
		private $token_name = 'token';
		private $exceptions = array();
		private $file = '';
		private $env = 0;

		public function __construct() {
			if (!isset($_SESSION[$this->session])) {
				$_SESSION[$this->session] = md5(uniqid(time(), true));
			}

			$this->token = $_SESSION[$this->session];

			$parameter = explode('?', basename($_SERVER['REQUEST_URI']));
			$this->file = current($parameter);

			$this->env = RANK_USER;

			if (preg_match('#\/acp\/#U', $_SERVER['REQUEST_URI'])) {
				$this->env = RANK_ADMIN;
			}
		}

		public function regenerate() {
			session_regenerate_id();
		}

		public function getTokenName() {
			return $this->token_name;
		}

		public function _($file, $methods, $env = 0) {
			$this->exceptions[$env][$file] = explode(',', str_replace(' ', '', $methods));
		}

		private function check_exception($file, $method, $env = 0) {
			if (isset($this->exceptions[$env][$file])) {
				$methods = $this->exceptions[$env][$file];

				if (in_array($method, $methods)) {
					return true;
				}
			}

			return false;
		}

		public function check($method, $array) {
			global $userManager;

			if ($this->check_exception($this->file, $method, $this->env)) {
				return true;
			}

			$token_is_valid = false;

			if (count($array) == 0) {
				$token_is_valid = true;
			}

			if (array_key_exists($this->token_name, $array)) {
				if ($array[$this->token_name] == $this->token) {
					$token_is_valid = true;
				}
			}

			if (!$token_is_valid && $userManager->loggedIn()) {
				echo ErrorMessage::setText('An error occured. Please reload this page.', true);
				//echo 'An error occured. Please reload this page.';
				exit;
			}

			return false;
		}

		private function fix_link($matches) {
			$link = $matches[1];

			if ($link[0] == '?') {
				$link = $this->file.$link;
			}

			if (preg_match('#(.*)\.php#U', $link)) {
				$parameter = explode('?', $link);
				$file = current($parameter);
				unset($parameter[0]);

				$parameterValues = explode('&', $parameter[1]);

				// $file = str_replace('./', '', $file);

				if ($this->check_exception(str_replace('./', '', $file), 'GET', $this->env)) {
					return $matches[0];
				}

				if (in_array($this->token_name.'='.$this->token, $parameterValues)) {
					return $matches[0];
				}

				if (count($parameter) >= 1) {
					$parameter = '?'.implode('', $parameter).'&'.$this->token_name.'='.$this->token;
				} else {
					$parameter = '';
				}

				if (substr($matches[0], 0, 4) == 'href') {
					return 'href="'.$file.$parameter.'"';
				} else {
					return 'action="'.$file.$parameter.'"';
				}
			} else {
				return $matches[0];
			}
		}

		public function auto_append($content) {
			// POST
			if (!$this->check_exception($this->file, 'POST', $this->env)) {
				$content = str_replace('</form>', '<input name="'.$this->token_name.'" value="'.$this->token.'" type="hidden" /></form>', $content);
			}

			// GET
			$content = preg_replace_callback('#href\="(.*)"#U', array($this, 'fix_link'), $content);

			// FORM
			$content = preg_replace_callback('#action\="(.*)"#U', array($this, 'fix_link'), $content);

			return $content;
		}
	}
?>