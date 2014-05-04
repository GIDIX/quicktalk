<?php
	class ForumPost {
		const FEATURE_MARKDOWN = 1;
		const FEATURE_URLS = 2;
		const FEATURE_SMILEYS = 3;

		protected $id;
		protected $topicID;
		protected $forumID;
		protected $userID;
		protected $content;
		protected $date;

		protected $markdown_enabled;
		protected $smileys_enabled;
		protected $URLs_enabled;
		protected $signature_enabled;

		protected $editDate = 0;
		protected $editUserID = 0;

		protected $topic;
		protected $forum;
		protected $user;
		protected $editUser;

		protected $selfLiked;

		public static function fromID($id) {
			global $db;

			$res = $db->query("SELECT * FROM ".TABLE_POSTS." WHERE id = ?", array($id));
			$row = $db->fetchObject($res);

			if ($row) {
				return self::fromRow($row);
			}

			return null;
		}

		public static function fromRow($row, $topic = null, $forum = null) {
			$post = new self(
				$topic instanceof ForumTopic ? $topic : $row->topic_id,
				$forum instanceof Forum ? $forum : $row->forum_id,
				$row->user_id,
				$row->post_text,
				$row->post_time,
				$row->id
			);

			$post->setMarkdownEnabled($row->enable_markdown == '1');
			$post->setSmileysEnabled($row->enable_smilies == '1');
			$post->setURLsEnabled($row->enable_urls == '1');

			if ($row->post_edit_time > 0) {
				$post->setEdit($row->post_edit_time, $row->post_edit_user_id);
			}

			return $post;
		}

		public function __construct($topic, $forum, $userID, $content, $date, $id = 0) {
			if ($topic instanceof ForumTopic) {
				$this->topic = $topic;
				$this->topicID = $topic->getID();
			} else {
				$this->topicID = $topic;
			}

			if ($forum instanceof Forum) {
				$this->forum = $forum;
				$this->forumID = $forum->getID();
			} else {
				$this->forumID = $forum;
			}

			$this->userID = $userID;
			$this->content = $content;
			$this->date = $date;
			$this->id = $id;
		}

		public function getID() {
			return $this->id;
		}

		public function isMarkdownEnabled() {
			return $this->markdown_enabled;
		}

		public function areURLsEnabled() {
			return $this->URLs_enabled;
		}

		public function areSmileysEnabled() {
			return $this->smileys_enabled;
		}

		public function setMarkdownEnabled($state) {
			$this->markdown_enabled = $state;
		}

		public function setURLsEnabled($state) {
			$this->URLs_enabled = $state;
		}

		public function setSmileysEnabled($state) {
			$this->smileys_enabled = $state;
		}

		public function setEdit($editDate, $editUserID) {
			$this->editDate = $editDate;
			$this->editUserID = $editUserID;
		}

		public function getEditDate() {
			return $this->editDate;
		}

		public function getEditFormattedDate() {
			return date("d.m.Y, H:i", $this->editDate);
		}

		public function getEditUserID() {
			return $this->editUserID;
		}

		public function getEditUser() {
			if (!$this->editUser instanceof User) {
				$this->editUser = User::fromID($this->editUserID);
			}

			return $this->editUser;
		}

		public function isEdited() {
			return $this->editDate > 0;
		}

		public function getUser() {
			if (!$this->user instanceof User) {
				$this->user = User::fromID($this->userID);
			}

			return $this->user;
		}

		public function getUserID() {
			return $this->userID;
		}

		public function getTopic() {
			if (!$this->topic instanceof ForumTopic) {
				$this->topic = ForumTopic::fromID($this->topicID, $this->getForum());
			}

			return $this->topic;
		}

		public function getTopicID() {
			return $this->topicID;
		}

		public function setTopicID($tid) {
			$this->topicID = (int)$tid;
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

		public function getContent() {
			return $this->content;
		}

		public function getShortContent($limit = 192) {
			$content = mb_strlen($this->content) > $limit ? mb_substr($this->content, 0, $limit - 3) . '...' : $this->content;
			$content = preg_replace('^\[(\S+)\]^', '', $content);

			return $content;
		}

		public function setContent($content) {
			$this->content = $content;
		}

		public function getDate() {
			return $this->date;
		}

		public function setDate($date) {
			$this->date = $date;
		}

		public function getFormattedDate() {
			return date("d.m.Y, H:i", $this->date);
		}

		// Database methods
		
		public static function insertPost(ForumPost $p) {
			global $db, $user;

			$db->query("
				INSERT INTO forums_posts
				(topic_id, forum_id, user_id, post_text,
					enable_markdown, enable_smilies, enable_urls, enable_signatur,
					is_topic, post_time)
				VALUES
				(
					:tid,
					:fid,
					:uid,
					:content,
					:markdown,
					:smileys,
					:urls,
					:signature,
					:isTopic,
					:date
				)
			", array(
				$p->getTopicID(),
				$p->getForumID(),
				$p->getUserID(),
				$p->getContent(),
				$p->isMarkdownEnabled() ? '1' : '0',
				$p->areSmileysEnabled() ? '1' : '0',
				$p->areURLsEnabled() ? '1' : '0',
				1,
				0,
				$p->getDate()
			));

			$db->query("
				UPDATE forums_topics
				SET last_post_time = :date
				WHERE id = :tid
			", array(
				$p->getDate(),
				$p->getTopicID()
			));

			$user->addPoints(Config::get('points_newpost'));

			return $db->insert_id();
		}

		public static function updatePost(ForumPost $p) {
			global $db;

			$db->query("
				UPDATE ".TABLE_POSTS."
				SET
					post_text = :content,
					enable_markdown = :markdown,
					enable_smilies = :smilies,
					enable_urls = :urls,
					enable_signatur = :signature,
					post_edit_user_id = :edituid,
					post_edit_time = :editdate
				WHERE id = :pid
			", array(
				$p->getContent(),
				$p->isMarkdownEnabled() ? '1' : '0',
				$p->areSmileysEnabled() ? '1' : '0',
				$p->areURLsEnabled() ? '1' : '0',
				1,
				$p->getEditUserID(),
				$p->getEditDate(),
				$p->getID()
			));
		}

		public static function deletePost(ForumPost $p) {
			global $db;

			$db->query("
				DELETE FROM ".TABLE_POSTS."
				WHERE id = ?
			", array($p->getID()));
		}
	}
?>