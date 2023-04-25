<?php
	// Pablo Navarro © 2023. MIT License
	// If you use it, ping me in Twitter at @rawrlabgames!
	
	// CONFIGURATION:
	// 1. Change the $secret variable to whatever you like and the $base_folder.
	// 2. Download geoip2.phar and rename it so it will include this secret value in its name.
	// 3. Download GeoIP2 Lite Country database and rename it so it will include this secret value in its name.
	// 4. You should have those 4 files in the same folder: 
	//		geoip2-$secret.phar
	//		GeoLite2-Country-$secret.mmdb
	//		.htaccess
	//		index.php
	// 5. Configure the variables from below
	// 6. Create your worldwide Nintendo eShop URLs!
	//   URL examples:
	//    - For games in the list: 
	//       https://rawrlab.com/nlink/murtop
	//   - For games not in the list (requires enabling $public_mode): 
	//       https://rawrlab.com/nlink/0100123019BEA000
	// -------------
	
	// SCRIPT OPTIONS! Configure those variables to make it work
	$secret = 'randomlettersandnumbers';					// Used to hide certain dependencies. Change it and rename the GeoIP files accordingly!
	$base_folder = '/nlink/';								// Web server public folder name
	$public_mode = true;									// If enabled, anyone can use it to redirect any game id, not just the ones on the list
	$default_country_code = 'US';							// In case of doubt, send the user to the USA eShop
	
	// List of enabled games
	$list_of_games = Array(
	//  'gamenameinlowercaseandalltogether' => Array('GLOBAL_ESHOP_GAME_ID_UPPERCASE','HK_ESHOP_GAME_ID_UPPERCASE')
		'murtop' => Array('0100123019BEA000',''),
		'donutdodo' => Array('010017901913A000','70010000064826'),
		'spybros' => Array('0100A6E0193F4000','70010000065621'),
		'spaceducks' => Array('0100123019BEA000',''),
		'explosivedinosaurs' => Array('0100123019BEA000',''),
		'tikibrawl' => Array('0100123019BEA000','')
		);
		
	// Only those are valid countries
	$valid_countries = Array(
		'BR','MX','HK','CA','JP','KR','US','PL','CZ',
		'AU','NZ','NO','AT','BE','BG','CY','DE','EE',
		'ES','FI','FR','GR','HR','HU','IE','IT','LT',
		'LU','LV','MT','NL','PT','RO','SI','SK', /* 'RU', */
		'DK','SE','ZA','GB','CH');
	
	// Redirect certain countries to another countries' eShop (currently not used)
	$redirected_countries = Array(
		'' => ''
		);
	
	// END OF SCRIPT OPTIONS 
	// ----------------------------------------------------------------------
	
	// Check if the dependencies are in their place (feel free to comment those lines for optimization)
	if(!file_exists('geoip2-' . $secret . '.phar')) {
		die('Missing dependency: geoip2-xxxxxx.phar');
	}	
	if(!file_exists('GeoLite2-Country-' . $secret . '.mmdb')) {
		die('Missing dependency: GeoLite2-Country-xxxxxxx.mmdb');
	}
	
	$target_base_url = 'https://ec.nintendo.com/apps/';		// Base URL for redirection
	$target_base_url_hk = 'https://store.nintendo.co.kr/';  // Exception for South Korea
	
	require_once('geoip2-' . $secret . '.phar');
	use GeoIp2\Database\Reader;
	
	// Get the eShop game ID or the game name cleaned up
	$game_id = str_replace("/","",strtolower(str_replace($base_folder, '', $_SERVER['REQUEST_URI'])));
	$game_id_hk = "";
	
	// Check if it's one of the games in the list
	if(array_key_exists($game_id, $list_of_games)) {
		$game_id_hk = $list_of_games[$game_id][1];
		$game_id = $list_of_games[$game_id][0];
	} else {
		$game_id = strtoupper($game_id);
	}
	
	// Check if the Game ID is correct (specially for public!)
	if(!preg_match('/^[A-Z0-9]{16}$/', $game_id)) {
		die("Error: Wrong Nintendo eShop game ID!");
	}
	
	// Find out the country	
	$reader = new Reader('GeoLite2-Country-' . $secret . '.mmdb');
	$record = $reader->country($_SERVER['REMOTE_ADDR']);
	$isocode = $record->country->isoCode;
	
	// Redirect certain countries to other eShops
	$isocode = str_replace(array_keys($redirected_countries), $redirected_countries, $isocode);
	
	// Check if it is a valid country
	if(in_array($isocode, $valid_countries)) {
		$country_suffix = '/' . $isocode;
	} else {
		$country_suffix = '/' . $default_country_code;
	}
	
	// Create the final URL
	if($isocode == 'HK' and !empty($game_id_hk)) { 		// Exception for South Korea
		$url = $target_base_url_hk . $game_id_hk;
	} else {
		$url = $target_base_url . $game_id . $country_suffix;
	}
	
	// Echo or redirect to that final URL
	if(!isset($_GET['debug'])) {
		header("Location:" . $url);
	} else {
		echo $url;
	}
?>