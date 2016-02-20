<?php

require_once('../loader.php');
set_time_limit(300);

// Init needed objects
$db = DB::getDB();
$twitter = Twitter::getTwitter();

// Load keywords from database
$stmt = $db->query('SELECT * FROM keywords');
$keywords = $stmt->fetchAll(PDO::FETCH_ASSOC);

$url = 'https://api.twitter.com/1.1/search/tweets.json';
$getfield = '?&geocode=37.781157,-122.398720,100000mi&result_type=recent&count=100q=#';
$requestMethod = 'GET';

// For each keyword load tweets
foreach ($keywords as $keyword) {
	$query = $getfield.$keyword['keyword'];
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
	     		$tweet['keyword_id'] = $keyword['id'];
	     		$tweet['tweet_id'] = $row['id'];
	     		$tweet['latitude'] = $row['geo']['coordinates'][0];
	     		$tweet['longitude'] = $row['geo']['coordinates'][1];

	     		$stmt = $db->prepare("SELECT * FROM tweets WHERE tweet_id=?");
				$stmt->execute(array($tweet['tweet_id']));
				$tweets = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if (!count($tweets)) {
					$stmt = $db->prepare("INSERT INTO tweets(keyword_id, tweet_id, latitude, longitude) VALUES(?, ?, ?, ?)");
					$stmt->execute(array($tweet['keyword_id'], $tweet['tweet_id'], $tweet['latitude'], $tweet['longitude']));
				}
     		}
     	}
     	$db->commit();
     }

	// Update last updated time
	$stmt = $db->prepare("UPDATE keywords SET updated=NOW() WHERE id=?");
	$stmt->execute(array($keyword['id']));
}

?>