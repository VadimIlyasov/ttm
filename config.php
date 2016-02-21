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
				'oauth_access_token' => "204969704-3DKWKi5oR2PbNwQXHplFzTgVN3SsAbWZ3zyHRzjS",
			    'oauth_access_token_secret' => "5wyzTsh45q9tC5kEqynWdr6ozUFHR6eWg5i8qPC1R7iND",
			    'consumer_key' => "MLPSlFVakecrZltJteBVs9wh6",
			    'consumer_secret' => "LxDZGK14Oa1aRwkwR73WjnBXCtRXfHpCu3slVUBTL1kGxwXybp"
			)
		);
	}
}


?>