<?php

chdir(dirname(__FILE__));
require_once('../loader.php');
set_time_limit(300);
ini_set('memory_limit', '1024M');

function execInBackground($cmd) { 
    if (substr(php_uname(), 0, 7) == "Windows"){ 
        pclose(popen("start /B ". $cmd, "r"));  
    } 
    else { 
        shell_exec($cmd . " > /dev/null &"); 
    } 
}

// Check if process is available
function checkProcessIsLoaded($processName)
{
	$result = shell_exec('ps aux | grep '.$processName);
	if (substr_count($result, $processName) > 2) {
		return true;
	} else {
		return false;
	}
}

// Run Process if not loaded
$processPath = dirname(__FILE__).'/stream.php';

if (!checkProcessIsLoaded($processPath)) {
	execInBackground('php '.$processPath);
}

// Init needed objects
$db = DB::getDB();

// Load keywords from database
$stmt = $db->query('SELECT * FROM keywords');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$keywords = array();
foreach ($rows as $row) {
	$keywords[$row['id']] = $row['keyword'];
}

// Load raw tweets
$stmt = $db->query('SELECT * FROM raw_tweets WHERE processed = 0 ORDER BY id ASC LIMIT 0, 30000');
//$stmt = $db->query('SELECT * FROM raw_tweets ORDER BY id ASC');
$responses = $stmt->fetchAll(PDO::FETCH_ASSOC);


function splitIntoWords($text) {
	$text = preg_replace("/[^A-Za-z0-9 ]/", ' ', $text);
	return explode(' ', $text);
}

function getSatisfaction($text) {
	$positive = substr_count ($text, ':)');
	$negative = substr_count ($text, ':(');

	if ($positive > $negative) {
		return 1;
	} elseif ($positive < $negative) {
		return -1;
	} else {
		return 0;
	}
}

function getCountryCode($lat, $lng) {
	global $db;

	$stmt = $db->prepare('SELECT iso2 FROM locations WHERE ST_Within(POINT(?, ?), my_polygon_column) > 0');
	$stmt->execute(array($lng, $lat));
	$countries = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if (count($countries)) {
		return $countries[0]['iso2'];
	} else {
		return '';
	}
}

$firstId = 0; $lastId = 0;
$db->beginTransaction();
foreach ($responses as $row) {
	if ($firstId == 0) {
		$firstId = $row['id'];
	}
	$lastId = $row['id'];
	$response = $row['response'];
	if ($response) {
     	$response = json_decode($response, true);

     	if (!isset($response['text'])) continue;

     	$satisfaction = getSatisfaction($response['text']);
     	$words = splitIntoWords($response['text']);
		$intersection = array_intersect($keywords, $words);

 		foreach ($intersection as $keywordId => $keyword) {
 			if (count($intersection)) {
 				// Add mentions
 				foreach ($intersection as $keywordId => $keyword) {
					// Add mentions
					$time = date("Y-m-d H:i:s", $response['timestamp_ms']/1000);
					$stmt = $db->prepare("INSERT INTO mentions (keyword_id, tweet_id, time) VALUES(?, ?, ?)");
					$stmt->execute(array($keywordId, $response['id'], $time));

					// Add satisfaction results
					if ($satisfaction != 0) {
						$stmt = $db->prepare("INSERT INTO satisfactions (keyword_id, tweet_id, satisfaction) VALUES(?, ?, ?)");
						$stmt->execute(array($keywordId, $response['id'], $satisfaction));
					}

					// Check if response has geo tag
			 		if (isset($response['geo']) && $response['geo']['type'] == 'Point') {
			 			$tweet = array();
			     		$tweet['tweet_id'] = $response['id'];
			     		$tweet['satisfaction'] = $satisfaction;
			     		$tweet['latitude'] = $response['geo']['coordinates'][0];
			     		$tweet['longitude'] = $response['geo']['coordinates'][1];

			     		$tweet['country_code'] = getCountryCode($tweet['latitude'], $tweet['longitude']);
						$stmt = $db->prepare("INSERT INTO tweets (keyword_id, tweet_id, latitude, longitude, satisfaction, country_code) VALUES(?, ?, ?, ?, ?, ?)");
						$stmt->execute(array($keywordId, $tweet['tweet_id'], $tweet['latitude'], $tweet['longitude'], $tweet['satisfaction'], $tweet['country_code']));
					}
 				}
 			}
 		}
    }
}
$db->commit();

if ($lastId) {
	$stmt = $db->prepare('UPDATE raw_tweets SET processed = 1 WHERE id>=? AND id<=?');
	$stmt->execute(array($firstId, $lastId));
}

?>