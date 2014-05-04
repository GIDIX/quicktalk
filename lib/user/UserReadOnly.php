<?php
	class UserReadOnly {
		protected $userID;
		protected $username;
		protected $email;
		protected $avatar;
		protected $realname;
		protected $registered;
		protected $lastVisited;
		protected $signature;
		protected $points;
		protected $rank;
		protected $banned;

		protected $row;

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

		public static function fromUser(User $u) {
			return new self($u->getRow());
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

		public function get($field) {
			return $this->row->$field;
		}

		public function getID() {
			return $this->userID;
		}

		public function getUserID() {
			return $this->userID;
		}

		public function getUsername() {
			return $this->username;
		}

		public function getEmail() {
			return $this->email;
		}

		public function getAvatar() {
			return self::getAvatarDirectory() . (empty($this->avatar) ? Config::get('default_avatar') : $this->avatar);
		}

		public static function getAvatarDirectory() {
			return './images/avatars/';
		}

		public function getRealname() {
			return $this->realname;
		}

		public function getRegisterDate() {
			return $this->registered;
		}

		public function getLastVisitDate() {
			return $this->lastVisited;
		}

		public function getSignature() {
			return $this->signature;
		}

		public function getPoints() {
			return $this->points;
		}

		public function isBanned() {
			return $this->banned;
		}

		public function isOnline() {
			return $this->getOnlineTime() < Config::get('max_time_online');
		}

		public function isAdmin() {
			return $this->rank == RANK_ADMIN;
		}

		public function isMod() {
			return $this->rank == RANK_MOD;
		}

		public function isAdminOrMod() {
			return $this->isAdmin() || $this->isMod();
		}

		public function getOnlineTime() {
			return time() - $this->lastVisited;
		}
	}
?>