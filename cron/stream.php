<?php

chdir(dirname(__FILE__));
require_once('../loader.php');
set_time_limit(300);

require_once('../libs/phirehose/lib/Phirehose.php');
require_once('../libs/phirehose/lib/OauthPhirehose.php');

$config = Config::getConfig();

/**
 * Example of using Phirehose to display a live filtered stream using track words
 */
class FilterTrackConsumer extends OauthPhirehose
{
  /**
   * Enqueue each status
   *
   * @param string $status
   */
  public function enqueueStatus($status)
  {
    $db = DB::getDb();

    $stmt = $db->prepare("INSERT INTO raw_tweets (response) VALUES(?)");
    $stmt->execute(array($status));
  }

  public function checkFilterPredicates()
  {
    $db = DB::getDb();

    $stmt = $db->query('SELECT * FROM keywords');
    $keywords = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $words = array();
    foreach ($keywords as $word) {
      $words[] = $word['keyword'];
    }
    $this->setTrack($words);
  }
}

// The OAuth credentials you received when registering your app at Twitter
define("TWITTER_CONSUMER_KEY", $config['twitter']['consumer_key']);
define("TWITTER_CONSUMER_SECRET", $config['twitter']['consumer_secret']);


// The OAuth data for the twitter account
define("OAUTH_TOKEN", $config['twitter']['oauth_access_token']);
define("OAUTH_SECRET", $config['twitter']['oauth_access_token_secret']);

// Start streaming
$sc = new FilterTrackConsumer(OAUTH_TOKEN, OAUTH_SECRET, Phirehose::METHOD_FILTER);
$sc->consume();

?>