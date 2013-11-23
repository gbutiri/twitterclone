<?php 
define('_ENV','local');
define('_DBNAME','fbclone');
define('_DBUSER','root');
define('_DBPASS','');
define('_ROOTFOLDER','');
define('_SITE','http://dev.fb-clone.com');
define('_DOCROOT',$_SERVER['DOCUMENT_ROOT']._ROOTFOLDER);
if (isset($_SESSION['fbclone_username'])) {
	define('_USERNAME',$_SESSION['fbclone_username']);
} else {
	define('_USERNAME','');
}
?>