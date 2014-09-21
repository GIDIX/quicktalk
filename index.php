<?php
	require 'base.php';

	PluginHelper::delegate('__onPageDisplay', array($page));
	Templates::display('index');
?>