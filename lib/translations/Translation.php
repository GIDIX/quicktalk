<?php
	class Translation {
		const LANG_DE = 'de';
		const LANG_EN = 'en';

		public static function getLang() {
			$prefLang = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']);
			$pLarr = explode('-', $prefLang);

			if ($_GET['d'] == 'ebug') {
				echo 'Current language string: ' . $prefLang;
			}

			$langToUse = strtoupper($pLarr[0]);

			if ($langToUse != 'DE' && $langToUse != 'EN') {
				$langToUse = 'DE';
			}

			if ($_GET['hl'] == 'en') {
				$langToUse = 'EN';
			} else if ($_GET['hl'] == 'de') {
				$langToUse = 'DE';
			}

			if (($_SESSION['lang'] == 'DE' || $_SESSION['lang'] == 'EN') && !isset($_GET['hl'])) {
				$langToUse = $_SESSION['lang'];
			} else {
				$_SESSION['lang'] = $langToUse;
			}

			return $langToUse;
		}

		protected static function getStrings($section, $lang = LANG) {
			$strings = include strtolower($lang) . '/strings.php';
			return $strings[$section];
		}
	}
?>