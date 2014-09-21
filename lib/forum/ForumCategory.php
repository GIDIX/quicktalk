<?php
	class ForumCategory {
		protected $id;
		protected $title;
		protected $isClosed;

		public static function getAllCategories() {
			global $db;

			$res = $db->query("SELECT * FROM ".TABLE_FORUM_CATEGORIES." ORDER BY `order` ASC");

			$categories = array();
			while ($row = $db->fetchObject($res)) {
				$categories[] = self::fromRow($row);
			}

			return $categories;
		}

		public static function fromID($id) {
			global $db;

			$res = $db->query("SELECT * FROM ".TABLE_FORUM_CATEGORIES." WHERE id = ?", array($id));
			$row = $db->fetchObject($res);

			if ($row) {
				return self::fromRow($row);
			}
			
			return null;
		}

		public static function fromRow($row) {
			return new self(
				$row->title,
				$row->order,
				$row->closed == '1',
				$row->id
			);
		}

		public function __construct($title, $order, $isClosed, $id = 0) {
			$this->id = $id;
			$this->order = $order;
			$this->title = $title;
			$this->isClosed = $isClosed;
		}

		public function getID() {
			return $this->id;
		}

		public function getTitle() {
			return $this->title;
		}

		public function setTitle($title) {
			$this->title = $title;
		}

		public function getOrder() {
			return $this->order;
		}

		public function setOrder($order) {
			$this->order = (int)$order;
		}

		public function isClosed() {
			return $this->isClosed;
		}

		public function getForums() {
			global $db, $userManager, $user;

			if ($userManager->loggedIn()) {
				$res = $db->query("
					SELECT *
					FROM ".TABLE_FORUMS." AS f
					LEFT JOIN ".TABLE_FORUMS_TRACK." AS t
						ON t.forum_id = f.id AND t.user_id = :uid
					WHERE f.category_id = :id
					ORDER BY f.`order` ASC
				", array($user->getID(), $this->id));
			} else {
				$res = $db->query("
					SELECT *
					FROM ".TABLE_FORUMS."
					WHERE category_id = ?
					ORDER BY `order` ASC
				", array($this->id));
			}

			$had = array();
			$forums = array();
			while ($row = $db->fetchObject($res)) {
				if (!in_array($row->id, $had)) {
					$forums[] = Forum::fromRow($row, $this);
					$had[] = $row->id;
				}
			}

			return $forums;
		}

		public static function insertCategory(ForumCategory $c) {
			global $db;

			$db->query("
				INSERT INTO ".TABLE_FORUM_CATEGORIES."
				(title, closed, `order`)
				VALUES
				(:title, :closed, :order)
			", array(
				$c->getTitle(),
				$c->isClosed() ? 1 : 0,
				$c->getOrder()
			));
		}

		public static function updateCategory(ForumCategory $c) {
			global $db;

			$db->query("
				UPDATE ".TABLE_FORUM_CATEGORIES."
				SET title = :title,
					closed = :closed,
					`order` = :order
				WHERE id = :id
			", array(
				$c->getTitle(),
				$c->isClosed() ? 1 : 0,
				$c->getOrder(),
				$c->getID()
			));
		}

		public static function deleteCategory(ForumCategory $c) {
			global $db;

			$db->query("
				DELETE FROM ".TABLE_FORUM_CATEGORIES."
				WHERE id = ?
			", array($c->getID()));
		}
	}
?>