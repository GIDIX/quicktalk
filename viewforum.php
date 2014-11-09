<?php
	require 'base.php';
	require LANGS . 'ForumT.php';

	ForumT::init();

	$forum = Forum::fromID((int)$_GET['id']);

	if (!$forum instanceof Forum) {
		echo ErrorMessage::setText(ForumT::get('forum_doesnt_exist'), true);
	}

	$topics = $forum->getTopics();

	Templates::assignVars(array(
		'forum'			=>	$forum,
		'topics'		=>	$topics['topics'],
		'topics_pages'	=>	$topics['pages']
	));

	PluginHelper::delegate('__onPageDisplay', array($page));
	Templates::display('viewforum');
?>