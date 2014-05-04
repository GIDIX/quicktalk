<?php
	/**
	 *	Definitions
	 * 	VERY IMPORTANT. DO NOT MODIFY OR YOUR HAMSTER WILL EXPLODE.
	 */
	
	define('VERSION', '1.0');

	// Base Tables
	define('TABLE_USERS', PREFIX . 'users');
	define('TABLE_ONLINE', PREFIX . 'online');
	define('TABLE_CONFIG', PREFIX . 'config');
	define('TABLE_PLUGINS', PREFIX . 'plugins');

	// Forum tables
	define('TABLE_FORUMS', PREFIX . 'forums');
	define('TABLE_FORUMS_TRACK', PREFIX . 'forums_track');
	define('TABLE_TOPICS', PREFIX . 'forums_topics');
	define('TABLE_TOPICS_TRACK', PREFIX . 'forums_topics_track');
	define('TABLE_POSTS', PREFIX . 'forums_posts');
	define('TABLE_POSTS_LIKES', PREFIX . 'forums_posts_likes');
	define('TABLE_PREFIXES', PREFIX . 'forums_prefixes');
	define('TABLE_FORUM_CATEGORIES', PREFIX . 'forums_categories');
	define('TABLE_FORUM_ABOS', PREFIX . 'forums_abos');

	define('TABLE_PLUGINS_SETTINGS', PREFIX . 'plugins_settings');

	define('RANK_ADMIN', 42);
	define('RANK_MOD', 21);
	define('RANK_USER', 0);
?>