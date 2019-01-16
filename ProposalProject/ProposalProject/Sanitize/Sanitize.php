<?php
	/*  
		OOP in php.
		class Grade has a field called arr which
		takes name=>grade pair.
	*/
	//declare(strict_types=1);
	
	namespace Sanitize;
	
	class Sanitize {
		private $sanitizedStr;
	
		public function __construct() {
			$this->sanitizedStr = "";
		}

		public function get() {
			return $this->sanitizedStr;
		}

		// set name=>grade pair.
		public function set(string $sanitizedStr) {
			$this->sanitizedStr = $sanitizedStr;
		}
	
		// not really used in this project, but required.
		public function __toString() {
			return $sanitizedStr;
		}

		// sanitize function.
		public function sanitize_string($db_connection, string $string) {
			if (get_magic_quotes_gpc()) {
				$string = stripslashes($string);
			}
			return htmlentities($db_connection->real_escape_string($string));
		}		
		
	}
?>