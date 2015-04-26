<?php 
class Model {

	protected $OK = array('status' => 200, 'message' => 'OK', 'data' => array());
	protected $ERROR = array('status' => 400, 'message' => 'ERROR', 'data' => array());
	protected $NOAUTH = array('status' => 401, 'message' => 'NOT AUTHORIZED', 'data' => array());

	protected $db;

	public function __construct() {
		try {
			include './database.inc.php';

			$this->db = new PDO("mysql:host=$host;dbname=$name", $user, $pass);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			date_default_timezone_set('America/New_York');

		}catch(PDOExecption $e) {
			echo $e->getMessage();
		}
	}

	public function __destruct() {
		$this->db = null;
	}
}