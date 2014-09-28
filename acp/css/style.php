<?php
	require '../../lib/themes/scss/scss.inc.php';

	function getSCSS($file) {
		$uncompiled = basename($file) . '.scss';
		$compiled = 'cache/' . basename($file) . '.css';
		$scssContent = '';

		if (!is_writable('cache')) {
			die('/* Not writable: ' . $compiled . ' */');
		}

		if (@filemtime($compiled) < @filemtime($uncompiled)) {
			$scss = new scssc();
			$scss->setFormatter('scss_formatter_compressed');

			try {
				$return = file_put_contents($compiled, $scss->compile(file_get_contents($uncompiled)));
			} catch (Exception $e) {
				$scssContent .= "/*\n" . $e->getMessage() . "\n*/\n";
			}
		}

		$scssContent .= file_get_contents($compiled);

		return $scssContent;
	}

	header("Content-Type: text/css");

	echo getSCSS('style');
?>