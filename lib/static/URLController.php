<?php
	/**
	 * URLController
	 * This class controls every URL used in the system (except the ACP).
	 * Modify this to your own needs if you want to use mod_rewrite (Apache) / rewrite (nginx).
	 * 
	 * @package de.gidix.QuickTalk.lib.static
	 * @author GIDIX
	 */
	abstract class URLController {
		public static function get($o) {
			if ($o instanceof Forum) {
				return self::getForumURL($o);
			} else if ($o instanceof ForumTopic) {
				return self::getTopicURL($o);
			} else if ($o instanceof ForumPost) {
				return self::getPostURL($o);
			} else if ($o instanceof UserReadOnly) {
				return self::getUserURL($o);
			}
		}

		private static function getForumURL(Forum $f) {
			return './viewforum.php?id=' . $f->getID();
		}

		private static function getTopicURL(ForumTopic $t) {
			return './viewtopic.php?id=' . $f->getID();
		}

		private static function getPostURL(ForumPost $p) {
			return './viewtopic.php?id=' . $p->getTopicID() . '&pid=' . $p->getID();
		}

		private static function getUserURL(UserReadOnly $u) {
			return './user.php?id=' . $u->getID();
		}
	}
?>