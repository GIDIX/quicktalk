<?php
	require 'base.php';
	require LANGS . 'ForumT.php';

	ForumT::init();

	$topic = ForumTopic::fromID((int)$_GET['id']);

	if (!$topic instanceof ForumTopic) {
		echo ErrorMessage::setText(ForumT::get('topic_doesnt_exist'), true);
	}

	$forum = $topic->getForum();
	$posts = $topic->getPosts(Config::get('max_posts_perpage'), max((int)$_GET['page'], 1));

	Templates::assignVars(array(
		'forum'			=>	$forum,
		'topic'			=>	$topic,
		'posts'			=>	$posts['posts'],
		'pages'			=>	$posts['pages']
	));

	PluginHelper::delegate('__onPageDisplay', array($page));
	Templates::display('viewtopic');
?>