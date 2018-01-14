<?php

include('includes/template.php');
include('includes/db.php');
include('config.php');

$data = array();
$template = new Template('templates/mobydick/about.tpl');
$template->set_markers('{~', '~}');
print $template->parse($data);

?>
