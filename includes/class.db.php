<?php 
Class DB {

	var $link = "";

	var $host = "";
	var $database = "";
	var $username = "";
	var $password = "";
	var $newconn = false;
	
	function __construct() {
		$this->host = "localhost";
		$this->database = _DBNAME; // LIVE DB
		$this->username = _DBUSER;
		$this->password = _DBPASS;
	}
	
	function open() {
		$this->link = mysql_connect($this->host,$this->username,$this->password,$this->newconn);
		if(!$this->link){
			die('Could not connect: '.mysql_error());
		} else {
			mysql_select_db($this->database, $this->link);
			// echo 'success!';
		}
	}
	
	function close() {
		mysql_close($this->link);
	}
	
	function runQuery($sql) {
		mysql_query($sql);
	}

	function getQuery($sql) {
		$res = mysql_query($sql);
		return mysql_fetch_assoc($res);
	}

}
?>