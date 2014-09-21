<?php
	/**
	 * 	UserManager
	 *  Responsible for handling a user to log in/out. Manages sessions and cookies.
	 * 
	 *	@package de.gidix.QuickTalk.lib.base
	 *  @author GIDIX
	 */
	class UserManager {
		/*
		 *	How long is a user logged in when inactive?
		 */
		const ONLINE_MAX_LIFETIME = 1800;

		/*
		 *	How long does a session live?
		 */
		const SESSION_MAX_LIFETIME = 7200;

		/*
		 *	COOKIE INFORMATION - DO NOT CHANGE.
		 */
		const COOKIE_AUTO_USERNAME = 'auto_qt_u';
		const COOKIE_AUTO_TOKEN = 'auto_qt_t';
		const COOKIE_AUTO_LIFETIME = 2678400;

		private $user;

		/*
		 *	How should the session be called?
		 */
		private $sessionName = 'quicktalk';

		public function __construct() {
			global $db;

			// if SESSION died
			if (empty($_SESSION[$this->sessionName])) {
				// if cookie is set
				if (isset($_COOKIE[self::COOKIE_AUTO_USERNAME]) && isset($_COOKIE[self::COOKIE_AUTO_TOKEN])) {
					// try to log in
					$this->login($_COOKIE[self::COOKIE_AUTO_USERNAME], $_COOKIE[self::COOKIE_AUTO_TOKEN], true, null);
				} else {
					$this->updateOnlineList();
				}
			} else {
				// get user from session
				$this->user = User::fromID($_SESSION[$this->sessionName]);
				$this->setLastVisit();
				$this->updateOnlineList();
			}
		}

		/**
		 * Is a user logged in?
		 * 
		 * @return bool
		 */
		public function loggedIn() {
			return $this->user instanceof User;
		}

		/**
		 * Get current user
		 * 
		 * @return User
		 */
		public function getUser() {
			return $this->user;
		}

		/**
		 * Log in a user
		 * 
		 * @param string $username Username
		 * @param string $password Password (in cleartext) / Cookie Token (used if $autologin == TRUE)
		 * @param bool $autologin Is this log in called via COOKIE?
		 * @param string redirect Redirect a user to this page after successful login
		 * 
		 * @return User (if successful) logged in user
		 */
		public function login($username, $password, $autologin = false, $redirect = null) {
			global $db, $token;

			// if login via cookie, select info by cookie token and username
			if ($autologin) {
				$res = $db->query('
					SELECT *
					FROM '.TABLE_USERS.'
					WHERE username = :username AND user_cookie_token = :token
				', array($username, $password));
			} else {
				$res = $db->query('
					SELECT *
					FROM '.TABLE_USERS.'
					WHERE username = :username
				', array($username));
			}

			$row = $db->fetchObject($res);

			// user does exist?
			if (!$row || (!$autologin && !self::checkPassword($password, $row->user_password))) {
				return false;
			} else {
				$this->user = User::fromRow($row);
				$_SESSION[$this->sessionName] = $this->user->getID();

				// set cookie, if not logged in via cookie
				if (!$autologin) {
					$cookieToken = self::generateCookieToken($this->user);

					setcookie(self::COOKIE_AUTO_USERNAME, $this->user->getUsername(), time() + self::COOKIE_AUTO_LIFETIME, '/', null);
					setcookie(self::COOKIE_AUTO_TOKEN, $cookieToken, time() + self::COOKIE_AUTO_LIFETIME, '/', null);

					$this->user->save(User::KEY_COOKIE_TOKEN, $cookieToken);
				}

				$this->updateOnlineList();

				if (!empty($redirect)) {
					/* Prevent CRLF header injection */
					if (strpos($redirect, "\n") !== FALSE || strpos($redirect, "\r") !== FALSE) {
						return true;
					}
					
					header('Location: ' . $redirect);
					exit;
				}

				return $this->user;
			}
		}

		/**
		 * 	Log out the current user
		 */
		public function logout() {
			if (!$this->loggedIn()) return;

			global $db, $token;

			// delete session
			unset($_SESSION[$this->sessionName]);

			// delete cookies
			setcookie(self::COOKIE_AUTO_USERNAME, '', time()-3600, '/');
			setcookie(self::COOKIE_AUTO_TOKEN, '', time()-3600, '/');

			// really delete cookies
			unset($_COOKIE[self::COOKIE_AUTO_USERNAME]);
			unset($_COOKIE[self::COOKIE_AUTO_TOKEN]);

			// remove entry in online list
			$db->query("DELETE FROM " . TABLE_ONLINE . " WHERE user_id = :uid", array($this->user->getID()));

			// reset user and token
			$this->user->save(User::KEY_COOKIE_TOKEN, '');
			$this->user = null;

			$token->regenerate();
		}

		/**
		 * Update the current user's last visit time.
		 */
		public function setLastVisit() {
			global $db;

			$db->query("
				UPDATE ".TABLE_USERS."
				SET user_ip = ?,
					user_lastvisited = ?
				WHERE user_id = ?
			", array($_SERVER['REMOTE_ADDR'], time(), $this->user->getID()));
		}

		/**
		 * Update the global online list
		 * Update the current user's info and delete anyone who has not been active for ONLINE_MAX_LIFETIME.
		 */
		public function updateOnlineList() {
			global $db;

			if ($this->loggedIn()) {
				$db->query("
					DELETE FROM " . TABLE_ONLINE . "
					WHERE (user_ip = :ip AND user_id = :uid) OR lastvisit < :time
				", array($_SERVER['REMOTE_ADDR'], $this->user->getID(), time() - self::ONLINE_MAX_LIFETIME));

				$db->query("
					INSERT INTO " . TABLE_ONLINE . "
					(user_id, user_ip, useragent, lastvisit)
					VALUES (:uid, :ip, :agent, :time)
				", array($this->user->getID(), $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], time()));
			} else {
				$db->query("
					DELETE FROM " . TABLE_ONLINE . "
					WHERE lastvisit < :time
				", array(time() - self::ONLINE_MAX_LIFETIME));
			}
		}

		/**
		 * Crypt a password
		 * This function crypts password using whirlpool and bcrypt. The salt is random.
		 * 
		 * @param string $pw Password to crypt
		 * @param string $rounds How many rounds do you want? (default: 08)
		 * 
		 * @return string Crypted version of $password.
		 */
		public static function cryptPassword($pw, $rounds = '08') {
			$string = hash_hmac('whirlpool', str_pad($pw, strlen($pw) * 4, 'QUICKTALK', STR_PAD_BOTH), SALT, true);
			$salt = substr(str_shuffle('./0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 22);
			return crypt($string, '$2a$' . $rounds . '$' . $salt);
		}

		/**
		 * Checks a password against a crypted one
		 * Just give it a password and let it see if it matches against the stored one.
		 * 
		 * @param string $password Password, the user enters.
		 * @param string $stored The crypted version of the password stored in the database.
		 * 
		 * @return bool matched?
		 */
		public static function checkPassword($password, $stored) {
			$string = hash_hmac("whirlpool", str_pad ($password, strlen ($password) * 4, 'QUICKTALK', STR_PAD_BOTH), SALT, true);
    		return crypt($string, substr($stored, 0, 30 )) == $stored;
		}

		/**
		 * Generates a random token used for the cookie.
		 * We don't want to save the password inside a cookie. It uses the user's username and a completely random salt
		 * with sha512.
		 * 
		 * @param User $user User, this token should be generated for.
		 * 
		 * @return string Token
		 */
		public static function generateCookieToken(User $user) {
			$salt = substr(str_shuffle('./0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 22);
			return hash('sha512', $user->getUsername() . $salt);
		}
	}
?>