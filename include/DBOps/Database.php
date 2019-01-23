<?php

	require_once 'config.php';

	class MySQLDatabase {
		private $instance = null;
		public function __construct(){
			$this->openConnection();
		}

		public function openConnection(){
			try {
				$this->db_instance = new PDO('mysql:host='.DB_SERVER.';dbname='.DB_NAME, DB_USERNAME, DB_PASSWORD);
				$this->db_instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				return $this->db_instance;
			} catch (PDOException $exception) {
				echo "Connection failed: " .$exception->getMessage();
			}
		}
	}