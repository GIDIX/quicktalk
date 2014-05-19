<?php
	/**
	 * Represents a Forum.
	 * This also includes functions to manage a forum.
	 * 
	 * @package de.gidix.QuickTalk.lib.forum
	 * @author GIDIX
	 */
	class Forum {
		protected $id;
		protected $categoryID;
		protected $title;
		protected $description;
		protected $order;
		protected $isClosed;

		protected $newEntriesAvailable;
		protected $category;

		/**
		 * Get forum by ID.
		 * 
		 * @param int $id ID of the forum
		 * 
		 * @return Forum
		 */
		public static function fromID($id) {
			global $db;

			$res = $db->query("SELECT * FROM ".TABLE_FORUMS." WHERE id = ?", array($id));
			$row = $db->fetchObject($res);

			if ($row) {
				return self::fromRow($row);
			}

			return null;
		}

		/**
		 * Create object from database row.
		 * 
		 * @param stdClass $row Database row
		 * @param ForumCategory $category Used for caching the category, if present.
		 * 
		 * @return Forum
		 */
		public static function fromRow($row, ForumCategory $category = null) {
			global $user, $userManager;

			$forum = new self(
				!is_null($category) ? $category : $row->category_id,
				$row->title,
				$row->description,
				$row->order,
				$row->closed == '1',
				$row->id
			);

			if ($userManager->loggedIn()) {
				$lastPost = $forum->getLastPost();
				$lastPostTime = $lastPost instanceof ForumPost ? $lastPost->getDate() : 0;

				$available = max($row->mark_time, $user->getRegisterDate()) < $lastPostTime ? true : false;
				$forum->setNewEntriesAvailable($available);
			}

			return $forum;
		}

		/**
		 * @param ForumCategory $category Category of the forum
		 * @param string $title
		 * @param string $description
		 * @param int $order Order number (1 = lowest)
		 * @param boolean $isClosed Is this forum closed?
		 * @param int $id Only used when creating from databse.
		 */
		public function __construct($category, $title, $description, $order, $isClosed, $id = 0) {
			$this->id = $id;

			if ($category instanceof ForumCategory) {
				$this->categoryID = $category->getID();
				$this->category = $category;
			} else {
				$this->categoryID = $category;
			}

			$this->title = $title;
			$this->description = $description;
			$this->order = $order;
			$this->isClosed = $isClosed;
		}

		/**
		 * Get Forum ID
		 * 
		 * @return int
		 */
		public function getID() {
			return $this->id;
		}

		/**
		 * Get Forum Title
		 * 
		 * @return string
		 */
		public function getTitle() {
			return $this->title;
		}

		/**
		 * Get Forum Description
		 * 
		 * @return string
		 */
		public function getDescription() {
			return $this->description;
		}

		/**
		 * Get Order Number
		 * 
		 * @return int
		 */
		public function getOrder() {
			return $this->order;
		}

		/**
		 * Get Forum Category
		 * Is cached after first request.
		 * 
		 * @return ForumCategory
		 */
		public function getCategory() {
			if (!$this->category instanceof ForumCategory) {
				$this->category = ForumCategory::fromID($this->categoryID);
			}

			return $this->category;
		}

		/**
		 * Get Categroy ID
		 * 
		 * @return int
		 */
		public function getCategoryID() {
			return $this->categoryID;
		}

		/**
		 * Set ForumCategory this forum belongs to by object
		 * 
		 * @param ForumCategory $c
		 */
		public function setCategory(ForumCategory $c) {
			$this->category = $c;
			$this->categoryID = $c->getID();
		}

		/**
		 * Set ForumCategory this forum belongs to by ID.
		 * This forces a recache.
		 * 
		 * @param int $id ID of the category
		 */
		public function setCategoryID($id) {
			$this->category = null;
			$this->categoryID = $id;
		}

		/**
		 * Is this forum closed?
		 * 
		 * @return boolean
		 */
		public function isClosed() {
			return $this->isClosed;
		}

		/**
		 * Set close state.
		 * 
		 * @param boolean $state
		 */
		public function setClosed($state) {
			$this->isClosed = (boolean) $state;
		}

		/**
		 * Set title.
		 * 
		 * @param string $title
		 */
		public function setTitle($title) {
			$this->title = $title;
		}

		/**
		 * Set description.
		 * 
		 * @param string $desc
		 */
		public function setDescription($desc) {
			$this->description = $desc;
		}

		/**
		 * Set order number.
		 * 
		 * @param int $order
		 */
		public function setOrder($order) {
			$this->order = $order;
		}

		/**
		 * Close this forum.
		 * Alias for setClosed(true)
		 */
		public function close() {
			$this->isClosed = true;
		}

		/**
		 * Open this forum.
		 * Alias for setClosed(false)
		 */
		public function open() {
			$this->isClosed = false;
		}

		/**
		 * Are new entries (posts, topics) available?
		 * 
		 * @return boolean
		 */
		public function newEntriesAvailable() {
			return $this->newEntriesAvailable;
		}

		/**
		 * Tell forum that new entries are available (only relevant for the icon).
		 * 
		 * @param boolean $state
		 */
		protected function setNewEntriesAvailable($state) {
			$this->newEntriesAvailable = $state;
		}

		/**
		 * Get icon filename.
		 * You need to manually add a suffix, depending on your preferred file format.
		 * 
		 * @return string
		 */
		public function getIcon() {
			$icon = '';

			if ($this->newEntriesAvailable()) {
				$icon .= 'new-';
			}

			if ($this->isClosed()) {
				$icon .= 'closed-';
			}

			$icon = substr($icon, 0, -1);

			if (empty($icon)) {
				$icon = 'topic';
			}

			return $icon;
		}

		/**
		 * Mark a forum as read or unread.
		 * 
		 * @param string $state Either 'read' or 'unread'
		 */
		public function markAs($state) {
			global $db, $user;

			$db->query("DELETE FROM ".TABLE_FORUMS_TRACK." WHERE forum_id = :fid AND user_id = :uid", array($this->id, $user->getID()));

			if ($state == 'read') {
				$db->query("
					INSERT INTO ".TABLE_FORUMS_TRACK."
					(forum_id, user_id, mark_time)
					VALUES
					(:fid, :uid, :time)
				", array($this->id, $user->getID(), time()));

				$this->setNewEntriesAvailable(false);
			} else if ($state == 'unread') {
				$this->setNewEntriesAvailable(true);
			}
		}

		/**
		 * Get $pageLimit topics.
		 * 
		 * @param int $pageLimit How many pages per page?
		 * @param int $currentPage On what page are we currently? (you should not set this manually!)
		 * 
		 * @return array Array containing topics and pages.
		 */
		public function getTopics($pageLimit = 20, $currentPage = 1) {
			global $db, $user, $userManager;

			$resMeta = $db->query("SELECT * FROM ".TABLE_TOPICS." WHERE forum_id = ?", array($this->id));

			$page = (isset($currentPage)) ? max($currentPage, 1) : 1;
			$pages = ceil($db->numRows($resMeta) / $pageLimit);

			if ($userManager->loggedIn()) {
				$res = $db->query("
					SELECT *
					FROM ".TABLE_TOPICS." AS topics
					LEFT JOIN ".TABLE_TOPICS_TRACK." AS t
						ON t.topic_id = topics.id AND t.user_id = :uid
					WHERE topics.forum_id = :id
					ORDER BY topics.topic_important DESC, last_post_time DESC
					LIMIT :pageCalc,:pageLimit
				", array($user->getID(), $this->id, $page * $pageLimit - $pageLimit, $pageLimit));
			} else {
				$res = $db->query("
					SELECT *
					FROM ".TABLE_TOPICS." AS topics
					WHERE forum_id = :id
					ORDER BY topics.topic_important DESC, last_post_time DESC
					LIMIT :pageCalc,:pageLimit
				", array($this->id, $page * $pageLimit - $pageLimit, $pageLimit));
			}

			$topics = array();
			
			while ($row = $db->fetchObject($res)) {
				$topics[] = ForumTopic::fromRow($row, $this);
			}

			return array(
				'topics'	=>	$topics,
				'pages'	=>	$pages
			);
		}

		/**
		 * Get last post of newest topic in this forum.
		 * 
		 * @return ForumPost
		 */
		public function getLastPost() {
			global $db;

			$res = $db->query("SELECT * FROM ".TABLE_POSTS." WHERE forum_id = ? ORDER BY post_time DESC LIMIT 1", array($this->id));
			$row = $db->fetchObject($res);

			if ($row) return ForumPost::fromRow($row, null, $this);
			return null;
		}

		/**
		 * How many topics does this forum have?
		 * 
		 * @return string Formatted string (i.e. 1.337; 9.001)
		 */
		public function getTopicsCount() {
			global $db;

			$res = $db->query("SELECT id FROM ".TABLE_TOPICS." WHERE forum_id = ?", array($this->id));
			return number_format($db->numRows($res), 0, ',', '.');
		}

		/**
		 * How many topics does the whole community have?
		 * 
		 * @return string Formatted string (i.e. 42; 3,141)
		 */
		public static function getTotalTopics() {
			global $db;

			$res = $db->query("SELECT id FROM ".TABLE_TOPICS);
			return number_format($db->numRows($res), 0, ',', '.');
		}

		/**
		 * How many posts does the whole community have?
		 * 
		 * @return string Formatted string (i.e. 84; 1,4142)
		 */
		public static function getTotalPosts() {
			global $db;

			$res = $db->query("SELECT id FROM ".TABLE_POSTS);
			return number_format($db->numRows($res), 0, ',', '.');
		}

		/**
		 * Mark all forums as read.
		 * 
		 * @param string $state (currently ignored)
		 */
		public static function markAll($state) {
			global $db, $user;

			$db->query("DELETE FROM ".TABLE_FORUMS_TRACK." WHERE user_id = '".$user->getUserID()."'");

			$res = $db->query("SELECT id FROM ".TABLE_FORUMS." ORDER BY id ASC");
			
			while ($row = $db->fetchObject($res)) {
				$db->query("
					INSERT INTO ".TABLE_FORUMS_TRACK."
					(forum_id, user_id, mark_time)
					VALUES
					(:fid, :uid, :time)
				", array(
					$row->id,
					$user->getID(),
					time()
				));
			}
		}

		/**
		 * Insert forum.
		 * 
		 * @param Forum $f
		 */
		public static function insertForum(Forum $f) {
			global $db;

			$db->query("
				INSERT INTO forums
				(category_id, title, description, `order`, closed)
				VALUES
				(:cid, :title, :desc, :order, :closed)
			", array(
				$f->getCategoryID(),
				$f->getTitle(),
				$f->getDescription(),
				$f->getOrder(),
				$f->isClosed() ? 1 : 0
			));
		}

		/**
		 * Delete forum.
		 * 
		 * @param Forum $f
		 */
		public static function deleteForum(Forum $f) {
			global $db;

			$db->query("DELETE FROM ".TABLE_FORUMS." WHERE id = ?", array($f->getID()));
			$db->query("DELETE FROM ".TABLE_TOPICS." WHERE forum_id = ?", array($f->getID()));
			$db->query("DELETE FROM ".TABLE_TOPICS_TRACK." WHERE forum_id = ?", array($f->getID()));
			$db->query("DELETE FROM ".TABLE_POSTS." WHERE forum_id = ?", array($f->getID()));
		}

		/**
		 * Update forum.
		 * 
		 * @param Forum $f
		 */
		public static function updateForum(Forum $f) {
			global $db;

			$db->query("
				UPDATE forums
				SET category_id = :cid,
					title = :title,
					description = :desc,
					`order` = :order,
					closed = :closed
				WHERE id = :fid
			", array(
				$f->getCategoryID(),
				$f->getTitle(),
				$f->getDescription(),
				$f->getOrder(),
				$f->isClosed() ? 1 : 0,
				$f->getID()
			));
		}
	}
?>