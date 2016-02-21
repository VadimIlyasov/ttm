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

	case 'trends': 
		$stmt = $db->query('SELECT k.id, k.keyword, count(m.id) as total FROM keywords k LEFT JOIN mentions m ON k.id = m.keyword_id GROUP BY k.id');

		$keywords = $stmt->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($keywords);
		break;

	case 'compare':
		$trends = $_POST['trends'];

		$results = array();
		foreach ($trends as $keywordId) {
			$result = array();

			$stmt = $db->prepare('SELECT id, keyword FROM keywords WHERE id = ?');
			$stmt->execute(array($keywordId));
			$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

			$result['id'] = $res[0]['id'];
			$result['name'] = $res[0]['keyword'];

			// Mentions
			$stmt = $db->prepare('SELECT count(*) as total FROM mentions WHERE keyword_id = ?');
			$stmt->execute(array($keywordId));
			$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$result['mentions'] = $res[0]['total'];

			// Top 3 Countries
			$stmt = $db->prepare('SELECT * FROM (SELECT country_code, count(country_code) as num FROM tweets WHERE keyword_id = ? AND country_code != "" GROUP BY country_code) t ORDER by num DESC LIMIT 0, 3');
			$stmt->execute(array($keywordId));
			$result['countries'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

			// Satisfaction
			$stmt = $db->prepare('SELECT COUNT(IF(satisfaction>0,1,NULL)) as positive, COUNT(IF(satisfaction<0,1,NULL)) as negative FROM satisfactions WHERE keyword_id = ?');
			$stmt->execute(array($keywordId));
			$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$result['satisfaction'] = $res[0];

			$results[] = $result;
		}

		echo json_encode($results);

		break;

	case 'chart': 
		$trends = $_POST['trends'];

		// Get timeseries data
		$placeholders = array();
		for ($i=0; $i<count($trends); $i++) {
			$placeholders[] = '?';
		}
		$placeholders = implode(',', $placeholders);
		$stmt = $db->prepare('SELECT keyword_id, COUNT(*) as num, DATE_FORMAT(time, \'%Y-%m-%d %h:00\') AS `period` FROM `mentions` WHERE keyword_id IN ('.$placeholders.') GROUP BY keyword_id, period ORDER BY period ASC');
		$stmt->execute($trends);
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

		// Get keywords
		$stmt = $db->prepare('SELECT id, keyword FROM keywords WHERE id IN ('.$placeholders.')');
		$stmt->execute($trends);
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$keywords = array();
		$header = array('Time');
		foreach ($res as $row) {
			$keywords[$row['id']] = $row['keyword'];
			$header[] = $row['keyword'];
		}

		$stats = array($header);

		$prevTime = 0;
		foreach ($results as $row) {
			if (!$prevTime) {
				$newRow = array($row['period']);
				foreach ($keywords as $k=>$v) {
					$newRow[$k] = 0;
				}
			}
			if ($prevTime && ($prevTime != $row['period'])) {
				$stats[] = array_values($newRow);

				$newRow = array($row['period']);
				foreach ($keywords as $k=>$v) {
					$newRow[$k] = 0;
				}
			}
			$newRow[$row['keyword_id']] = $row['num'];
			$prevTime = $row['period'];
		}
		$stats[] = array_values($newRow);

		echo json_encode($stats);
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