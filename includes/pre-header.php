<?php 
include (_DOCROOT.'/includes/class.db.php');
$strposlogin = strpos($_SERVER['SCRIPT_FILENAME'],'/login.php');

if (_USERNAME == '' && $strposlogin === false) {
	header('location: /login.php');
}
define ('_TITLE',"Facebook Clone!");
$db = new DB();
$db->open();

?>
