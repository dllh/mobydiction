<?php

$db_url = parse_url(getenv("CLEARDB_DATABASE_URL"));

if(class_exists('DB')){
	$db = new DB($db_url['host'], $db_url['user'], $db_url['pass'], substr($url["path"], 1 ) );
	$db->connect();
}

?>
