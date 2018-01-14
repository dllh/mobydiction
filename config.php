<?php

if(class_exists('DB')){
	$db = new DB('localhost', '', '', '');
	$db->connect();
}

?>
