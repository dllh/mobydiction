<?php

$db_url = parse_url(getenv("CLEARDB_DATABASE_URL"));
error_log( print_r( $db_url, true ) );

if(class_exists('DB')){
	$db = new DB($db_url['host'], $db_url['user'], $db_url['pass'], substr($url["path"], 1 ) );
	$db->connect();
}

?>
