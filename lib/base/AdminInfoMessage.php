<?php
	/**
	 * 	AdminInfoMessage
	 *  Same as InfoMessage only for the ACP.
	 * 
	 *	@package de.gidix.QuickTalk.lib.base
	 *  @author GIDIX
	 */
	class AdminInfoMessage extends InfoMessage {

		public static function setText($message, $withTPL = false) {
			return new self($message, $withTPL);
		}

		protected function __construct($message, $withTPL = false, $type = '') {
			$this->message = $message;
			$this->type = $type;
			$this->displayTPL = $withTPL;
		}

		public function addLink($title, $link) {
			$this->links[$title] = $link;
			return $this;
		}

		public function __toString() {
			global $user, $db, $config, $phpdate, $page_title_all, $page_title, $token;

			if ($this->displayTPL) include 'template/header.php';

			$complete = '
				<div class="info '.$this->type.' '.($this->displayTPL ? 'single' : '').'"'.(!empty($this->customCSS) ? 'style="'.$this->customCSS.'"' : '').'>
					'.$this->message.'
				</div>
			';

			if (count($this->links) > 0) {
				$complete .= '
					<br />
				';

				$i = 0;
				foreach ($this->links as $title => $link) {
					$complete .= '
						'.($i > 0 ? '&nbsp; &nbsp;' : '').'
						<a href="'.$link.'"'.($i == 0 ? ' class="button darkB"' : '').'">'.$title.'</a>
					';

					$i++;
				}

				$complete .= '
					<br /><br /><br />
				';
			}

			if (!$this->displayTPL) {
				return $complete;
			} else {
				echo $token->auto_append($complete);
				include 'template/footer.php';
				die();
			}
		}
	}

	class AdminErrorMessage extends AdminInfoMessage {
		public static function setText($message, $withTPL = false) {
			return new self($message, $withTPL, 'error');
		}
	}

	class AdminSuccessMessage extends AdminInfoMessage {
		public static function setText($message, $withTPL = false) {
			return new self($message, $withTPL, 'success');
		}
	}

	class AdminQuestionMessage extends AdminInfoMessage {
		public static function setText($message, $withTPL = false) {
			return new self($message, $withTPL, 'question');
		}
	}

	class AdminSpecialMessage extends AdminInfoMessage {
		public static function setText($message, $withTPL = false) {
			return new self($message, $withTPL, 'special');
		}
	}

	class AdminSpecialMessageAlt extends AdminInfoMessage {
		public static function setText($message, $withTPL = false) {
			return new self($message, $withTPL, 'special-alt');
		}
	}

	class AdminCustomMessage extends AdminInfoMessage {
		public static function setText($message, $customCSS = '', $withTPL = false) {
			$ret = new self($message, $withTPL, 'special');
			$ret->customCSS = $customCSS;
			return $ret;
		}
	}

?>