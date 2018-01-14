<?php

include('includes/db.php');
include('includes/template.php');
include('config.php');


$data = array();
$data['total'] = 0;

if($_GET['word']){
	$data['chapters'] = array();
	$data['word'] = $_GET['word'];
	$data['chart_max'] = 0;
	$data['chart_min'] = 0;
	$chart_data_array = array();
	$chart_labels_array = array();
        for($i = 0; $i <= 137; $i++){ 
		$chart_data_array[$i] = 0; 
		$chart_labels_array[$i] = '';
	}
 	$chart_labels_array_empty = $chart_labels_array;
	$result = $db->query('select c.title, f.chapter_id, sum(f.number) as num from frequency f, chapters c where f.word LIKE "%s" and f.chapter_id = c.id group by f.chapter_id', $_GET['word']);
	while($row = $db->fetch_object($result)){
		$chart_data_array[$row->chapter_id] = $row->num;
 		if($row->num > 0){
			$chart_labels_array[$row->chapter_id] = $row->chapter_id;
		}
		$data['total'] += $row->num;
		$data['chapters'][] = array('chapter_id' => $row->chapter_id, 'num' => $row->num, 'title' => $row->title);	
		if($row->num > $data['chart_max']){
			$data['chart_max'] = $row->num;
		}
	}
	$data['chart_data'] = implode(",", $chart_data_array);
	//$data['chart_labels'] = implode("|", $chart_labels_array);
	$chart_labels_even = $chart_labels_array_empty;
	$chart_labels_odd = $chart_labels_array_empty;
	for($i = 0; $i < count($chart_labels_array); $i++){
		if($i == 0 || $i % 2 == 0){
			$chart_labels_even[$i] = $chart_labels_array[$i];
		}
		else{
			$chart_labels_odd[$i] = $chart_labels_array[$i];
		}	
	}
	$data['chart_labels_even'] = implode("|", $chart_labels_even);
	$data['chart_labels_odd'] = implode("|", $chart_labels_odd);
	//die('<pre>' . print_r($data, 1) . '</pre>');
}
else{
	$data['error'] = "You didn't specify a word. You might want to go back to the <a href=\"index.php\">main page</a> and try a search.";
}

$db->disconnect();

$template = new Template('templates/mobydick/word.tpl');
$template->set_markers('{~', '~}');
print $template->parse($data);

?>
