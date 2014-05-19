<?php
	/**
	 * Database
	 * Access databases using PDO and MySQL. Supports prepared statements.
	 * 
	 * @package de.it-talent.QuickTalk.lib.base
	 * @author GIDIX
	 */
	
	class Database {
		private $db;
		public $lastQuery;

		public function __construct($host, $username, $password, $database) {
			$this->db = new PDO('mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8', $username, $password);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		}

		/**
		 * 	Send a query.
		 *  This method supports prepared statements. Just write ? or use placeholders, like ':id' in your
		 *  $sql and provide $args with its values.
		 * 
		 *  @param string $sql Query to send.
		 *  @param array $args Params to bind, if preparing.
		 *  
		 *  @return PDOStatement PDO Statement
		 */
		public function query($sql, array $args = null) {
			try {
				if (!is_null($args)) {
					$this->lastQuery = $this->db->prepare($sql);
					$this->lastQuery->execute($args);
				} else {
					$this->lastQuery = $this->db->query($sql);
				}

				return $this->lastQuery;
			} catch (PDOException $e) {
				$this->error($e->getMessage(), $sql, $e->getCode());
			}
		}

		/**
		 * 	Prepare a statement and return it without executing.
		 * 
		 *  @param string $query Query to send.
		 */
		public function prepare($sql) {
			try {
				return $this->db->prepare($sql);
			} catch (PDOException $e) {
				$this->error($e->getMessage(), $sql, $e->getCode());
			}
		}

		public function fetchObject(PDOStatement $stmt = null) {
			if (!is_null($stmt)) {
				return $stmt->fetch(PDO::FETCH_OBJ);
			} else {
				return $this->lastQuery->fetch(PDO::FETCH_OBJ);
			}
		}

		public function numRows(PDOStatement $stmt = null) {
			if (!is_null($stmt)) {
				return $stmt->rowCount();
			} else {
				return $this->lastQuery->rowCount();
			}
		}

		public function insertID() {
			return $this->db->lastInsertId();
		}

		public function beginTransaction() {
			return $this->db->beginTransaction();
		}

		public function commit() {
			return $this->db->commit();
		}

		public function rollback() {
			return $this->db->rollBack();
		}

		protected function error($msg, $sql, $code) {
			Functions::log(Functions::LOG_ERROR, $msg . '<br /><br /><code class="important">' . $sql . '</code>');
			
			/* die('
				<h1>Database Error ('.$code.')</h1>

				'.(!is_null($sql) ? '<code>' . $sql . '</code>' : '').'
				<p>'.$msg.'</p>
			'); */
		}
	}
?>