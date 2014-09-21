-- Create syntax for TABLE 'qt_config'
CREATE TABLE `qt_config` (
  `key` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'qt_forums'
CREATE TABLE `qt_forums` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(255) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `closed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `forum_order` (`order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PACK_KEYS=0;

-- Create syntax for TABLE 'qt_forums_categories'
CREATE TABLE `qt_forums_categories` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `closed` tinyint(1) DEFAULT '0',
  `order` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'qt_forums_posts'
CREATE TABLE `qt_forums_posts` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `topic_id` int(255) unsigned NOT NULL DEFAULT '0',
  `forum_id` int(255) unsigned NOT NULL DEFAULT '0',
  `user_id` int(255) unsigned NOT NULL DEFAULT '0',
  `post_text` text COLLATE utf8_unicode_ci NOT NULL,
  `enable_markdown` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `enable_smilies` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `enable_urls` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `enable_signatur` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `is_topic` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `post_time` int(255) unsigned NOT NULL DEFAULT '0',
  `post_edit_user_id` int(255) unsigned NOT NULL DEFAULT '0',
  `post_edit_time` int(255) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `topic_id` (`topic_id`),
  KEY `forum_id` (`forum_id`),
  KEY `user_id` (`user_id`),
  KEY `topic_post_id` (`topic_id`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Create syntax for TABLE 'qt_forums_topics'
CREATE TABLE `qt_forums_topics` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `forum_id` int(255) unsigned NOT NULL DEFAULT '0',
  `topic_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(255) unsigned NOT NULL DEFAULT '0',
  `topic_time` int(11) unsigned NOT NULL DEFAULT '0',
  `topic_important` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `topic_closed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `topic_views` int(11) unsigned NOT NULL DEFAULT '0',
  `last_post_time` int(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `forum_id` (`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Create syntax for TABLE 'qt_forums_topics_track'
CREATE TABLE `qt_forums_topics_track` (
  `topic_id` int(255) unsigned NOT NULL DEFAULT '0',
  `user_id` int(255) unsigned NOT NULL DEFAULT '0',
  `mark_time` int(255) unsigned NOT NULL DEFAULT '0',
  `forum_id` int(255) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`topic_id`,`user_id`),
  KEY `forum_id` (`forum_id`),
  KEY `topic_id` (`topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Create syntax for TABLE 'qt_forums_track'
CREATE TABLE `qt_forums_track` (
  `forum_id` int(255) unsigned NOT NULL DEFAULT '0',
  `user_id` int(255) unsigned NOT NULL DEFAULT '0',
  `mark_time` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`forum_id`,`user_id`),
  KEY `forum_id` (`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Create syntax for TABLE 'qt_online'
CREATE TABLE `qt_online` (
  `user_id` int(255) DEFAULT NULL,
  `user_ip` varchar(24) DEFAULT NULL,
  `useragent` varchar(255) DEFAULT NULL,
  `lastvisit` int(11) DEFAULT NULL,
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'qt_plugins'
CREATE TABLE `qt_plugins` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `package` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `author` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `active` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'qt_plugins_settings'
CREATE TABLE `qt_plugins_settings` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `package` varchar(255) DEFAULT NULL,
  `key` varchar(255) DEFAULT NULL,
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'qt_users'
CREATE TABLE `qt_users` (
  `user_id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `user_password` varchar(255) DEFAULT NULL,
  `user_email` varchar(255) DEFAULT '',
  `user_avatar` varchar(16) DEFAULT '',
  `user_realname` varchar(255) DEFAULT '',
  `user_registered` int(16) DEFAULT '0',
  `user_lastvisited` int(16) DEFAULT '0',
  `user_signature` text,
  `user_points` int(11) DEFAULT '0',
  `user_banned` int(1) DEFAULT '0',
  `user_cookie_token` varchar(255) DEFAULT '',
  `user_ip` varchar(24) DEFAULT NULL,
  `user_rank` int(24) DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

###########
# INSERTS #
###########

INSERT INTO `qt_config` (`key`, `value`)
VALUES
  ('page_title', 'QuickTalk Demo'),
  ('max_posts_perpage', '10'),
  ('max_topics_perpage', '25'),
  ('max_posts_perday', '2000'),
  ('current_theme', 'standard'),
  ('default_avatar', 'default.svg'),
  ('points_newpost', '1'),
  ('points_newtopic', '2'),
  ('max_time_online', '600');

INSERT INTO `qt_forums` (`id`, `category_id`, `title`, `description`, `order`, `closed`)
VALUES
  (1, 7, 'Testforum', 'Dies ist ein Testforum für QuickTalk.', 1, 0),
  (2, 7, 'Noch ein Testforum', 'Einfach nur darum.', 2, 0);

INSERT INTO `qt_forums_categories` (`id`, `title`, `closed`, `order`)
VALUES
  (1, 'Testkategorie', 0, 1);

INSERT INTO `qt_forums_posts` (`topic_id`, `forum_id`, `user_id`, `post_text`, `enable_markdown`, `enable_smilies`, `enable_urls`, `enable_signatur`, `is_topic`, `post_time`, `post_edit_user_id`, `post_edit_time`)
VALUES
  (1, 1, 1, 'TestContent', 1, 1, 1, 1, 1, UNIX_TIMESTAMP(), 0, 0);

INSERT INTO `qt_forums_topics` (`id`, `forum_id`, `topic_title`, `user_id`, `topic_time`, `topic_important`, `topic_closed`, `topic_views`, `last_post_time`)
VALUES
  (1, 1, 'Testtopic', 1, UNIX_TIMESTAMP(), 0, 0, 0, UNIX_TIMESTAMP());

INSERT INTO `qt_users` (`user_id`, `username`, `user_password`, `user_email`, `user_avatar`, `user_realname`, `user_registered`, `user_lastvisited`, `user_signature`, `user_points`, `user_banned`, `user_cookie_token`, `user_ip`, `user_rank`)
VALUES
  (1, 'admin', '$2a$08$qa/xTwtH3XcJACREZlMiKO1wQIyJPI.FhX7ptt82z7KEj3fNRpPjC', 'quicktalk@quicktalk.local', '', '', 0, UNIX_TIMESTAMP(), '', 20, 0, '', '127.0.0.1', 42);
