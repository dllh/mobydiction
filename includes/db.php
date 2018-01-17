<?php

class DB{
	var $resource;
	var $server;
	var $un;
	var $pw;
	var $db;
	var $debug = false;
	
	function DB($server, $un, $pw, $db){
		$this->server = $server;
		$this->un = $un;
		$this->pw = $pw;
		$this->db = $db;
	}

	function debug($val = true){
		$this->debug = $val;
	}

	function connect(){
		$this->resource = @mysqli_connect($this->server, $this->un, $this->pw, $this->db );
		if ( ! $this->resource ) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}
error_log( 'DB: ' . var_export( $this->resource, true ) );
	}

	function disconnect(){
		mysqli_close($this->resource);
	}

	function fetch_object(){
		if($this->result){
			return mysqli_fetch_object($this->result);
		}
	}

	function query($query) {
		$args = func_get_args();
		array_shift($args);
		if (isset($args[0]) and is_array($args[0])) { // 'All arguments in one array' syntax
			$args = $args[0];
		}
		_db_query_callback($args, TRUE);
		$query = preg_replace_callback('/(%d|%s|%%|%f|%b)/', '_db_query_callback', $query);
		if($this->debug === true){
			print $query . "<br />\n";
		}
		$this->result = $this->_query($query);
error_log( 'QUERY: ' . $query );
error_log( 'RESULT: ' . var_export( $this->result, true ) );
		return $this->result;
	}

	function _query($query, $debug = 0) {

		$result = mysqli_query($this->resource, $query );
error_log( 'RESULT 2: ' . var_export( $result, true ) );
	
		if ($debug) {
			print '<p>query: '. $query .'<br />error:'. mysqli_error($this->resource) .'</p>';
		}
	
		if ( $result ) {
			return $result;
		} else {
			error_log( 'NO RESULT' );
			return FALSE;
		}
	}
}

function _db_query_callback($match, $init = FALSE) {
	static $args = NULL;
	if ($init) {
		$args = $match;
		return;
	}

	switch ($match[1]) {
		case '%d': // We must use type casting to int to convert FALSE/NULL/(TRUE?)
			return (int) array_shift($args); // We don't need db_escape_string as numbers are db-safe
		case '%s':
			return db_escape_string(array_shift($args));
		case '%%':
			return '%';
		case '%f':
			return (float) array_shift($args);
		//case '%b': // binary data
		//	return db_encode_blob(array_shift($args));
	}
}

function db_escape_string($str){
	return addslashes($str);
}

