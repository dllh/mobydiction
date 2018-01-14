<?php

include('includes/db.php');
include('includes/template.php');
include('config.php');

$data = array();
$template = new Template('templates/mobydick/index.tpl');
$template->set_markers('{~', '~}');
print $template->parse($data);

?>
