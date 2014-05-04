<?php
	class User extends UserReadOnly {
		const KEY_ID = 'user_id';
		const KEY_USERNAME = 'username';
		const KEY_EMAIL = 'user_email';
		const KEY_AVATAR = 'user_avatar';
		const KEY_REALNAME = 'user_realname';
		const KEY_REGISTERED = 'user_registered';
		const KEY_LASTVISITED = 'user_lastVisited';
		const KEY_SIGNATURE = 'user_signature';
		const KEY_POINTS = 'user_points';
		const KEY_BANNED = 'user_banned';
		const KEY_COOKIE_TOKEN = 'user_cookie_token';

		protected static $lastUser;

		public static function fromID($id) {
			global $db;

			$res = $db->query('
				SELECT *
				FROM '.TABLE_USERS.'
				WHERE user_id = ?
			', array((int)$id));

			$row = $db->fetchObject($res);

			if ($row) return self::fromRow($row);
			return null;
		}

		public static function fromRow(stdClass $row) {
			return new self($row);
		}

		public function __construct(stdClass $row) {
			$this->userID = $row->user_id;
			$this->username = $row->username;
			$this->email = $row->user_email;
			$this->avatar = $row->user_avatar;
			$this->realname = $row->user_realname;
			$this->registered = $row->user_registered;
			$this->lastVisited = $row->user_lastvisited;
			$this->signature = $row->user_signature;
			$this->points = $row->user_points;
			$this->rank = $row->user_rank;
			$this->banned = $row->user_banned == '1';
			$this->row = $row;
		}

		public function addPoints($add) {
			$this->save(self::KEY_POINTS, $this->getPoints() + $add);
		}

		public function subtractPoints($subtract) {
			$this->save(self::KEY_POINTS, $this->getPoints() - $subtract);
		}

		public function ban($state) {
			$this->save(self::KEY_BANNED, $state ? 1 : 0);
		}

		protected function getRow() {
			return $this->row;
		}

		/**
		 * 	Alias for ban(false)
		 */
		public function unban() {
			$this->ban(false);
		}

		/**
		 * Management
		 */
		
		public function save($key, $value) {
			global $db;

			$db->query("UPDATE " . TABLE_USERS . " SET ".$key." = ? WHERE user_id = ?", array($value, $this->userID));
		}

		/* Static Functions */

		public static function getTotalUsers() {
			global $db;

			return $db->numRows($db->query("SELECT user_id FROM " . TABLE_USERS . ""));
		}

		public static function getLatestUser() {
			global $db;

			if (!self::$lastUser instanceof UserReadOnly) {
				self::$lastUser = UserReadOnly::fromRow($db->fetchObject($db->query("SELECT * FROM " . TABLE_USERS . " ORDER BY user_id LIMIT 1")));
			}

			return self::$lastUser;
		}
	}
?>