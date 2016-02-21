<?php

require_once('config.php');

class DB {
	private static $db = null;

    public static function getDb() {
    	$config = Config::getConfig();

        if (!self::$db) {
        	self::$db = new PDO('mysql:host=localhost;dbname='.$config['db']['name'].';charset=utf8mb4', $config['db']['user'], $config['db']['password']);
        	self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			self::$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }

        return self::$db;
    }
}

?>