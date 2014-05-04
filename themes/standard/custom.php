<?php
	Templates::display('header');

	echo Templates::getVar('customContent');

	Templates::display('footer');
?>