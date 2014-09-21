# QuickTalk Forum Software

## Introduction

This is the official QuickTalk forum software GitHub repo. This software is *unfinished*. Do NOT use it in production just yet!

## Requirements

You need PHP 5.3 or higher for QuickTalk to work.

## Installation

QuickTalk currently does NOT have an installer. You need to create a database yourself. After that simply import `database.sql` into your database. This will create all tables and fill it with sample values.

After that create `lib/static/config.php`:

	<?php
	$DBCRED = array(
		'host'		=>	'127.0.0.1',
		'username'	=>	'USERNAME',
		'password'	=>	'PASSWORD',
		'database'	=>	'DATABASE',
		'prefix'	=>	'qt_'
	);
	?>
	
Change values depending on your needs but do NOT change the prefix.

### Rewrites

QuickTalk only needs one rewrite for error pages. This enables plugins to hook into it to create custom pages:

#### nginx
`error_page 404 /handler.php?page=$uri&$args;`

#### Apache
SOON

### Default user
* Username: admin
* Password: quicktalk

## Contribution

You are invited to contribute to QuickTalk if you have experience with PHP OOP. Just create a fork and create Pull Requests for changes.

## To Do

Project Management is done entirely with [Codeante](http://codeante.com).