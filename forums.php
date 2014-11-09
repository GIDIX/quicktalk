<?php
	require 'base.php';

	$categories = ForumCategory::getAllCategories();
	Templates::assign('categories', $categories);

	PluginHelper::delegate('__onPageDisplay', array($page));
	Templates::display('forums');
?>