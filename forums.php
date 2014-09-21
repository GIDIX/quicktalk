<?php
	require 'base.php';

	function __autoload($c) {
		require LIB . 'forum/' . basename($c) . '.php';
	}

	$categories = ForumCategory::getAllCategories();
	Templates::assign('categories', $categories);

	PluginHelper::delegate('__onPageDisplay', array($page));
	Templates::display('forums');
?>