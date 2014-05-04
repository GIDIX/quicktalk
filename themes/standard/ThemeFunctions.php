<?php
	class ThemeFunctions implements ThemeFunctionsInterface {
		public static function getNavigation(array $items = null) {
			if (is_null($items)) $items = Templates::getNavigationItems();

			$nav = '
				<ul>
			';

			foreach ($items as $i) {
				$nav .= '<li><a href="' . $i->getURL() . '">'.$i->getTitle().'</a>';

				if (count($i->getSubItems()) > 0) {
					$nav .= self::getNavigation($i->getSubItems());
				}

				$nav .= '</li>';
			}

			$nav .= '</ul>';
			return $nav;
		}

		public static function getUserURL($o) {
			$u = $o->getUser();

			if ($u instanceof UserReadOnly) {
				return '<a href="' . URLController::get($u) . '">' . $u->getUsername() . '</a>';
			} else {
				return 'Unknown';
			}
		}

		public static function loggedIn() {
			global $user;
			return $user instanceof UserReadOnly;
		}

		public static function getUserAvatar() {
			if (self::loggedIn()) {
				global $user;
				return $user->getAvatar();
			} else {
				return UserReadOnly::getAvatarDirectory() . Config::get('default_avatar');
			}
		}
	}
?>