<?php 
include ('config.php');
include (_DOCROOT.'/includes/class.db.php');

$action = isset($_GET['action']) ? $_GET['action'] : 'runinit';
call_user_func($action);

function runinit() {
	
	//var_dump(date("T Z"));
	?>
	<form method="post" action="?action=runinstall">
		<div>
			<label>DB Name</label>
			<input type="text" value="fbclone" />
		</div>
		<div>
			<label>DB Username</label>
			<input type="text" value="root" />
		</div>
		<div>
			<label>DB Password</label>
			<input type="text" value="" />
		</div>
		<button type="sumit">Start!</button>
	</form>
	<script>
	</script>
	<?php
}

function runinstall() {

	// 1. modify config file.
	// 2. write out the sql file.
	// 3. execute the script below.
	
	$db = new DB();
	$db->open();

	$sql = "CREATE DATABASE IF NOT EXISTS "._DBNAME.";";
	mysql_query($sql);
	mysql_select_db(_DBNAME);

	$file_contents = file_get_contents(_DOCROOT.'/sql/create-db.sql');

	$newline_pos = strpos($file_contents,PHP_EOL.PHP_EOL);
	while ($newline_pos !== false) {
		$command = substr($file_contents,0,$newline_pos);
		$file_contents = str_replace($command.PHP_EOL.PHP_EOL,"",$file_contents);
		
		if (strpos($command,"/*") === false) {
			var_dump($command);
			$res = mysql_query($command);
			var_dump($res);
		}
		$newline_pos = strpos($file_contents,PHP_EOL.PHP_EOL);
	}
	
	$db->close();
	
}


?>