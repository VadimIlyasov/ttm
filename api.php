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
		$keywordId = $db->lastInsertId();

		$twitter = Twitter::getTwitter();

		$url = 'https://api.twitter.com/1.1/search/tweets.json';
		$getfield = '?&geocode=37.781157,-122.398720,100000mi&result_type=recent&count=100q=#';
		$requestMethod = 'GET';

		$query = $getfield.$keyword;
		$response = $twitter->setGetfield($getfield)
		             ->buildOauth($url, $requestMethod)
		             ->performRequest();

	     if ($response) {
	     	$response = json_decode($response, true);

	     	$db->beginTransaction();

	     	// Add results to the database, make sure they are unique
	     	foreach ($response['statuses'] as $row) {
	     		if (isset($row['geo']) && $row['geo']['type'] == 'Point') {
	     			$tweet = array();
		     		$tweet['keyword_id'] = $keywordId;
		     		$tweet['tweet_id'] = $row['id'];
		     		$tweet['latitude'] = $row['geo']['coordinates'][0];
		     		$tweet['longitude'] = $row['geo']['coordinates'][1];

					$stmt = $db->prepare("INSERT INTO tweets(keyword_id, tweet_id, latitude, longitude) VALUES(?, ?, ?, ?)");
					$stmt->execute(array($tweet['keyword_id'], $tweet['tweet_id'], $tweet['latitude'], $tweet['longitude']));
	     		}
	     	}
	     	$db->commit();
	     }

	     echo json_encode(array('status'=>'ok'));
		break;

	default:
		break;
}

?>