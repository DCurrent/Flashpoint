<?php

	require_once(__DIR__.'/settings.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/libraries/php/classes/session.php');			// Session class.
	require(__DIR__.'/navigation.php');
	require($_SERVER['DOCUMENT_ROOT'].'/libraries/php/classes/record_navigation/main.php');	// Record navigation.
	require_once($_SERVER['DOCUMENT_ROOT'].'/libraries/php/classes/sorting/main.php'); 		// Page cache.
	require_once($_SERVER['DOCUMENT_ROOT'].'/libraries/php/classes/cache/main.php'); 		// Page cache.
	require(__DIR__.'/data_main.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/libraries/php/classes/database/main.php'); 	// Database class.
	require_once(__DIR__.'/dc/access/config.class.php');
	require_once(__DIR__.'/dc/access/DataAccount.class.php');
	require_once(__DIR__.'/dc/access/DataCommon.class.php');
	require_once(__DIR__.'/dc/access/lookup.class.php');
	require_once(__DIR__.'/dc/access/process.class.php');
	require_once(__DIR__.'/dc/access/status.class.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/libraries/php/classes/url_query/main.php'); 	// URL builder (to include variables).

	require_once(__DIR__.'/time_list.php');
		
	// Replace default session handler.
	$session_handler = new class_session();
	session_set_save_handler($session_handler, TRUE);
		
?>