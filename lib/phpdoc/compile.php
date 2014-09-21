<?php
	function startsWith($haystack, $needle) {
		return substr($haystack, 0, strlen($needle)) == $needle;
	}

	function parse($haystack, $tag) {
		return substr($haystack, strlen($tag) + 1);
	}

	$tags = array(
		'@author',
		'@package',
		'@param',
		'@return',
		'@throws'
	);

	$file = '../themes/Templates.php';
	$file = '../plugins/Plugin.php';

	$content = file_get_contents($file);
	$contentLines = file($file);

	$blocks = array();

	$i = 0;
	$log = false;
	$oneLastLog = false;

	foreach ($contentLines as $line) {
		$line = htmlspecialchars($line);

		if (strpos($line, '/**') !== false) {
			$log = true;
		}

		if ($log || $oneLastLog) {
			$blocks[$i][] = $line;

			if ($oneLastLog) {
				$oneLastLog = false;
				$i++;
			}
		}

		if (strpos($line, '*/') !== false) {
			$log = false;
			$oneLastLog = true;
		}
	}

	foreach ($blocks as &$block) {
		foreach ($block as &$p) {
			$p = str_replace('/*', '', $p);
			$p = str_replace('*/', '', $p);
			$p = str_replace('*', '', $p);
			$p = str_replace(' {', '', $p);
			$p = trim($p);
		}
	}

	$blockInfos = array();

	foreach ($blocks as $block) {
		$blockInfo = array();

		$blockInfo['title'][] = $block[1];
		$blockInfo['description'] = array();

		unset($block[0]);

		for ($i = 2; $i < count($block); $i++) {
			if (empty($block[$i])) {
				break;
			}

			$blockInfo['description'][] .= $block[$i];
		}

		if (empty($blockInfo['title'][0]) && empty($blockInfo['description'][0])) {
			continue;
		}

		foreach ($block as $line) {
			foreach ($tags as $tag) {
				if (startsWith($line, $tag)) {
					$blockInfo[$tag][] = parse($line, $tag);
				}
			}
		}

		$blockInfo['codeLine'] = $block[count($block)];

		$blockInfos[] = $blockInfo;
	}

	// Finally, display it
	
	foreach ($blockInfos as $block) {
		if (startsWith($block['codeLine'], 'class')) {
			echo 'Class **'.$block['title'][0].'**
===
';
			continue;
		}

		echo '
'.$block['title'][0].'
---
';

		if (count($block['description']) > 0 && !empty($block['description'])) {
			echo implode($block['description'], '') . "\n\n";
		}

		if (count($block['@param']) > 0) {
			echo '<ul>';

			foreach ($block['@param'] as $param) {
				echo '<li>``' . $param . "``\n";
			}

			echo "</ul>\n\n";
		}

		echo "\t" . $block['codeLine'];

		echo "\n<br /><br />\n";
	}
?>