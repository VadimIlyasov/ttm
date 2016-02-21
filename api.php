<?php

require_once('loader.php');
set_time_limit(300);

// Init needed objects
$db = DB::getDB();

$action = isset($_GET['action'])?$_GET['action']:'';

switch ($action) {
	case 'keywords': 
		$stmt = $db->query('SELECT k.id, k.keyword, count(t.id) as total FROM keywords k LEFT JOIN tweets t ON k.id = t.keyword_id GROUP BY k.id');

		$keywords = $stmt->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($keywords);
		break;

	case 'satisfaction': 
		$keywordId = $_GET['id'];
		$stmt = $db->prepare('SELECT COUNT(IF(satisfaction>0,1,NULL)) as positive, COUNT(IF(satisfaction<0,1,NULL)) as negative FROM satisfactions WHERE keyword_id = ?');
		$stmt->execute(array($keywordId));
		$stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($stats);
		break;

	case 'points': 
		$keywordId = $_GET['id'];

		// ToDo: add created > NOW() - TWO weeks
		$stmt = $db->prepare('SELECT latitude, longitude FROM tweets WHERE keyword_id = ? ORDER BY id DESC LIMIT 0, 10000');
		$stmt->execute(array($keywordId));
		$points = $stmt->fetchAll(PDO::FETCH_ASSOC);

		echo json_encode($points);
		break;

	case 'add': 
		$keyword = $_GET['keyword'];

		if (!$keyword) {
			echo json_encode(array('status'=>'error', 'message'=>'Keyword is empty'));
			exit;
		}
		$stmt = $db->prepare("SELECT * FROM keywords WHERE keyword=?");
		$stmt->execute(array($keyword));
		$keywords = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (count($keywords)) {
			echo json_encode(array('status'=>'error', 'message'=>'Keyword already exists'));
			exit;
		}

		$stmt = $db->prepare('INSERT INTO keywords SET keyword = ?');
		$stmt->execute(array($keyword));

	    echo json_encode(array('status'=>'ok'));
		break;

	default:
		break;
}

?>