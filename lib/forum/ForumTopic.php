<?php
	class ForumTopic {
		const FLAG_IMPORTANT = 1;
		const FLAG_CLOSED = 2;

		protected $id;
		protected $forumID;
		protected $title;
		protected $userID;
		protected $flags;
		protected $poll;

		protected $newEntriesAvailable = false;

		protected $forum;
		protected $user;
		protected $firstPost;
		protected $lastPost;

		public static function fromID($id, Forum $forum = null) {
			global $db;

			$res = $db->query("SELECT * FROM ".TABLE_TOPICS." WHERE id = ?", array($id));
			$row = $db->fetchObject($res);

			if ($row) {
				return self::fromRow($row);
			}

			return null;
		}

		public static function fromRow($row, Forum $forum = null) {
			global $user, $userManager, $db;

			$topic = new self(
				!is_null($forum) ? $forum : $row->forum_id,
				$row->user_id,
				$row->topic_title,
				$row->id
			);

			$topic->setImportant($row->topic_important == '1');
			$topic->setClosed($row->topic_closed == '1');

			// if user is logged in, use new entry-icons
			if ($userManager->loggedIn()) {
				$available = max($row->mark_time, $user->getRegisterDate()) < $topic->getLastPost()->getDate() ? true : false;
				$topic->setNewEntriesAvailable($available);
			}

			return $topic;
		}

		public function __construct($forum, $userID, $title, $id = 0) {
			if ($forum instanceof Forum) {
				$this->forum = $forum;
				$this->forumID = $forum->getID();
			} else {
				$this->forumID = $forum;
			}

			$this->userID = $userID;
			$this->title = $title;
			$this->id = $id;
		}

		public function getID() {
			return $this->id;
		}

		protected function isFlagSet($flag) {
			return ($this->flags & $flag) == $flag;
		}

		protected function setFlag($flag, $value) {
			if ($value) {
				$this->flags |= $flag;
			} else {
				$this->flags &= ~$flag;
			}
		}

		public function isImportant() {
			return $this->isFlagSet(self::FLAG_IMPORTANT);
		}

		public function isClosed() {
			if ($this->getForum()->isClosed()) {
				return true;
			}

			return $this->isFlagSet(self::FLAG_CLOSED);
		}

		public function setImportant($state) {
			$this->setFlag(self::FLAG_IMPORTANT, $state);
		}

		public function setClosed($state) {
			$this->setFlag(self::FLAG_CLOSED, $state);
		}

		public function close() {
			$this->setClosed(true);
		}

		public function open() {
			$this->setClosed(false);
		}

		public function getUserID() {
			return $this->userID;
		}

		public function getUser() {
			if (!$this->user instanceof User) {
				$this->user = User::fromID($this->userID, false);
			}

			return $this->user;
		}

		public function getForum() {
			if (!$this->forum instanceof Forum) {
				$this->forum = Forum::fromID($this->forumID);
			}

			return $this->forum;
		}

		public function getForumID() {
			return $this->forumID;
		}

		public function setForumID($fid) {
			$this->forumID = $fid;
		}

		public function setForum(Forum $f) {
			$this->forumID = $f->getID();
			$this->forum = $f;
		}

		public function newEntriesAvailable() {
			return $this->newEntriesAvailable;
		}

		protected function setNewEntriesAvailable($state) {
			$this->newEntriesAvailable = $state;
		}

		public function markAs($state) {
			global $db, $user;

			$db->query("DELETE FROM ".TABLE_TOPICS_TRACK." WHERE topic_id = :tid AND user_id = :uid", array($this->id, $user->getID()));

			if ($state == 'read') {
				$db->query("
					INSERT INTO ".TABLE_TOPICS_TRACK."
					(topic_id, user_id, mark_time, forum_id)
					VALUES
					(:tid, :uid, :time, :fid)
				", array($this->id, $user->getID(), time(), $this->forumID));

				$this->setNewEntriesAvailable(false);
			} else if ($state == 'unread') {
				$this->setNewEntriesAvailable(true);
			}
		}

		public function getTitle() {
			return $this->title;
		}

		public function getShortTitle($limit) {
			return mb_strlen($this->title) > $limit ? mb_substr($this->title, 0, $limit - 3) . '...' : $this->title;
		}

		public function setTitle($title) {
			$this->title = $title;
		}

		public function hasPoll() {
			return $this->poll instanceof ForumPoll;
		}

		public function getIcon() {
			$icon = '';

			if ($this->newEntriesAvailable()) {
				$icon .= 'new-';
			}

			if ($this->isImportant()) {
				$icon .= 'important-';
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

		public function getLastPost() {
			if (!$this->lastPost instanceof ForumPost) {
				global $db;

				$res = $db->query("SELECT * FROM ".TABLE_POSTS." WHERE topic_id = ? ORDER BY post_time DESC LIMIT 1", array($this->id));
				$this->lastPost = ForumPost::fromRow($db->fetchObject($res), $this, $this->forum);
			}

			return $this->lastPost;
		}

		public function getFirstPost() {
			if (!$this->firstPost instanceof ForumPost) {
				global $db;

				$res = $db->query("SELECT * FROM ".TABLE_POSTS." WHERE topic_id = ? ORDER BY post_time ASC LIMIT 1", array($this->id));
				$this->firstPost = ForumPost::fromRow($db->fetchObject($res), $this, $this->forum);
			}

			return $this->firstPost;
		}

		public function setFirstPost(ForumPost $p) {
			$this->firstPost = $p;
		}

		public function getPageOfPostID($id) {
			global $db;

			$resMeta = $db->query("SELECT * FROM ".TABLE_POSTS." WHERE topic_id = ?", array($this->id));
			$page = 1;
			$i = 1;

			while ($row = $db->fetchObject($resMeta)) {
				// $post = ForumPost::fromRow($row);
				// $postID = $post->getID();
				$postID = $row->id;

				if ($postID == $id) {
					break;
				}

				if ($i == Config::get('max_posts_perpage')) {
					$i = 0;
					$page++;
				}

				$i++;
			}

			return $page;
		}

		public function getPosts($pageLimit = 0, $currentPage = 1) {
			global $db, $user, $userManager;

			if ($pageLimit == 0) {
				$pageLimit = Config::get('max_posts_perpage');
			}

			$resMeta = $db->query("SELECT * FROM ".TABLE_POSTS." WHERE topic_id = '".$this->id."'");

			$page = (isset($currentPage)) ? max($currentPage, 1) : 1;
			$pages = ceil($db->numRows($resMeta) / $pageLimit);

			if ($userManager->loggedIn()) {
				$res = $db->query("
					SELECT posts.*, track.mark_time
					FROM ".TABLE_POSTS." AS posts
					LEFT JOIN ".TABLE_TOPICS_TRACK." AS track
						ON posts.topic_id = track.topic_id AND track.user_id = :uid
					WHERE posts.topic_id = :tid
					ORDER BY posts.id ASC
					LIMIT :pageCalc,:pageLimit
				", array(
					$user->getID(),
					$this->id,
					$page * $pageLimit - $pageLimit,
					$pageLimit
				));
			} else {
				$res = $db->query("
					SELECT posts.*
					FROM ".TABLE_POSTS." AS posts
					WHERE posts.topic_id = :tid
					ORDER BY posts.id ASC
					LIMIT :pageCalc,:pageLimit
				", array(
					$this->id,
					$page * $pageLimit - $pageLimit,
					$pageLimit
				));
			}

			$posts = array();
			while ($row = $db->fetchObject($res)) {
				$posts[] = ForumPost::fromRow($row, $this);
			}

			return array(
				'posts'	=>	$posts,
				'pages'	=>	$pages
			);
		}

		public function getPostsCount() {
			global $db;

			$res = $db->query("SELECT id FROM ".TABLE_POSTS." WHERE topic_id = ?", array($this->id));
			return number_format($db->numRows($res), 0, ',', '.');
		}

		// Database methods
		
		public static function insertTopic(ForumTopic $t) {
			global $db, $user;

			$db->query("
				INSERT INTO ".TABLE_TOPICS."
				(forum_id, topic_title, user_id, topic_time, last_post_time)
				VALUES
				(:fid, :title, :uid, :ttime, :ptime)
			", array(
				$t->getForumID(),
				$t->getTitle(),
				$user->getUserID(),
				$t->getFirstPost()->getDate(),
				$t->getFirstPost()->getDate()
			));

			$topicID = $db->insert_id();
			$user->addPoints(Config::get('points_newtopic'));

			return $topicID;
		}

		public static function updateTopic(ForumTopic $t) {
			global $db;

			$db->query("
				UPDATE ".TABLE_TOPICS."
				SET
					topic_title = :title,
					topic_important = :important,
					topic_closed = :closed
				WHERE id = :tid
			", array(
				$t->getTitle(),
				$t->isImportant() ? '1' : '0',
				$t->isClosed() ? '1' : '0',
				$t->getID()
			));
		}

		public static function deleteTopic(ForumTopic $t) {
			global $db;

			$db->query("DELETE FROM ".TABLE_TOPICS." WHERE id = '".$t->getID()."'");
			$db->query("DELETE FROM ".TABLE_TOPICS_TRACK." WHERE topic_id = '".$t->getID()."'");
			$db->query("DELETE FROM ".TABLE_POSTS." WHERE topic_id = '".$t->getID()."'");
		}

		public static function updateForumOfTopic(ForumTopic $t) {
			global $db;

			$db->query("
				UPDATE ".TABLE_TOPICS."
				SET forum_id = :fid
				WHERE id = :pid
			", array(
				$t->getForumID(),
				$t->getID()
			));

			$db->query("
				UPDATE ".TABLE_POSTS."
				SET forum_id = :fid
				WHERE topic_id = :tid
			", array(
				$t->getForumID(),
				$t->getID()
			));
		}

		public static function getLatestTopics($limit = 20) {
			global $db;

			$res = $db->query("SELECT * FROM ".TABLE_TOPICS." ORDER BY last_post_time DESC LIMIT ?", array((int)$limit));
			
			$topics = array();
			while ($row = $db->fetchObject($res)) {
				$topics[] = self::fromRow($row);
			}

			return $topics;
		}
	}
?>