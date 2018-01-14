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
	$template = new Template('templates/mobydick/index.tpl');
}
if(isset($_GET['chapter'])){
	$data['chapter'] = $_GET['chapter'];
	$data['next_chapter'] = ($_GET['chapter'] == 136) ? 0 : ($_GET['chapter'] + 1);
	$data['prev_chapter'] = ($_GET['chapter'] == 0) ? 136 : ($_GET['chapter'] - 1);
	$data['words'] = array();
	$title_row = $db->fetch_object($db->query('SELECT title, content FROM chapters WHERE id = %d', $_GET['chapter']));
	$data['chapter_title'] = $title_row->title;
	$data['chapter_text'] = $title_row->content;
	$result = $db->query('select word, number from frequency where chapter_id = %d and word not in (select word from ignored_words) order by number desc limit 50;', $_GET['chapter']);
	while($row = $db->fetch_object($result)){
		$data['total'] += $row->num;
		$data['words'][] = array('word' => $row->word, 'num' => $row->number);	
	}
	$template = new Template('templates/mobydick/chapter.tpl');

}

$db->disconnect();

if(!isset($template)){
	$template = new Template('templates/mobydick/index.tpl');
}
$template->set_markers('{~', '~}');
print $template->parse($data);

?>
