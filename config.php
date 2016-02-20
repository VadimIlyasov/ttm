<?php

class Config {
	public static function getConfig() {
		return array(
			'db' => array(
				'name' => 'ttm',
				'user' => 'root',
				'password' => ''
			),

			'twitter' => array(
				'oauth_access_token' => "",
			    'oauth_access_token_secret' => "",
			    'consumer_key' => "",
			    'consumer_secret' => ""
			)
		);
	}
}


?>