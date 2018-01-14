<?php

include('includes/db.php');
include('includes/template.php');
include('config.php');


$data = array();
$data['total'] = 0;
$data['words'] = array();

$result = $db->query('select word from ignored_words order by word');
while($row = $db->fetch_object($result)){
	$data['words'][] = array('word' => $row->word);	
	$data['total']++;
}


$db->disconnect();

$template = new Template('templates/mobydick/omissions.tpl');
$template->set_markers('{~', '~}');
print $template->parse($data);

?>
