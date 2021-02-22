<?php	
	
	/*
	* Configuration file. This should be added to all PHP scripts to set up commonly used includes, 
	* functions, objects, variables and so on.	
	*/
	
	require_once($_SERVER['DOCUMENT_ROOT'].'/libraries/php/classes/location/main.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/libraries/php/classes/url_query/main.php'); 			// URL request var builder.
	require_once($_SERVER['DOCUMENT_ROOT'].'/libraries/php/classes/session.php');					// Session class.

    /*
    * Legacy config options. These are items that
    * still need moving to ini file.
    */
    abstract class APPLICATION_SETTINGS
	{
		const VERSION 		= '1.0';
		const NAME			= 'Flashpoint';
		const ADMINS		= 'dwhibb0, dvcask2';
		const ADMIN_MAIN 	= 'dvcask2';
		const AUTHENTICATE_URL = 'https://ehs.uky.edu/apps/flashpoint';
	}
	
	abstract class DATABASE
	{
		const NAME	= 'ehsinfo';
	}

	abstract class MAILING
	{
		const TO		= '';
        const CC		= '';
        const BCC		= '';
        const SUBJECT   = 'UK Fire Marshal';
        const FROM 	    = 'ehs_noreply@uky.edu';
	}
	
	abstract class ROOM_SELECT
	{
		const OUTSIDE = -1;
	}
	
	abstract class STATUS_SELECT
	{		
		const S_PUBLIC	= 1; 
        const S_PRIVATE	= 0;
	}

	abstract class SORTING_FIELDS
	{
		const NAME 		= 1;
        const LOCATION	= 2;
        const STATUS	= 3;
        const REPORTED	= 4;
        const CREATED	= 5;
        const UPDATED 	= 6;
	}

	
	
?>


