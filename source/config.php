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
		const AUTHENTICATE_URL = 'http://ehs.uky.edu/apps/flashpoint';
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

	/**
	* Site specific configuration settings. File
	* is located in PHP installation directory.
	* 
	* To get location of our file, get path info
	* array for PHP.ini and read file path element.
	* Then add filename of our custom .ini file.
	* This gives us the full path with filename
	* of our config file.
	**/
	$config_file_info = pathinfo(php_ini_loaded_file());
	$config_file_full = $config_file_info['dirname'].'\dc_flashpoint.ini';

	// Load class using namespace.
	function app_load_class($class_name_arg) 
	{
        $file_name = '';
        $namespace = '';
		$class_name = $class_name_arg;
	
		/**
		* Use the root path as our base directory, then
		* find the string position of last namespace 
		* separator in class name.
		**/
		$include_path = $_SERVER['DOCUMENT_ROOT'].'\libraries';
		
		$last_namespace_position = strripos($class_name, '\\');		
		
		/**
		* If we found the namespace separator, let's build a 
		* file name string.
		**/
        if ($last_namespace_position)
		{
			// Namespace is the portion of of class name starting
			// from 0 and ending at last namespace separator.
            $namespace = substr($class_name, 0, $last_namespace_position);
			
			// Crop namespace from class name to leave only class name itself.
            $class_name = substr($class_name, $last_namespace_position + 1);
			
			// Add directory separator to namespace to start a file path.
            $file_name = str_replace('\\', DIRECTORY_SEPARATOR, $namespace).DIRECTORY_SEPARATOR;
			
		}
		
		/**
		* Add suffix to file name, then add include path to build
		* full file name path.
		**/
        $file_name .= $class_name.'.php';
				
        $file_name_full = $include_path . DIRECTORY_SEPARATOR . $file_name;
		
		/** 
		* If complete file path exists, then load it. 
		* Otherwise just die on the spot. It's a little
		* redudant since require() would fail anyway, but
		* I'd rather control what the error looks like.
		**/
        if (file_exists($file_name_full)) 
		{
            require($file_name_full);
			
        	echo '<!-- '.$class_name_arg.', loaded successfully. -->'.PHP_EOL;
		} 
		else 
		{
			error_log('Autoloader Error: '.$file_name_full.' not found.');
			die('Autoloader Error: '.$class_name_arg.' not found. Please contact administrator.');
        }
    }
	
    spl_autoload_register('app_load_class');

	/* 
	* DC Yukon is the database controller. Most
	* other libraries will accept it as an injected
	* dependency.
	*/
	$dc_yukon_connect_config = new \dc\yukon\ConnectConfig($config_file_full);
	$dc_yukon_connection = new \dc\yukon\Connect($dc_yukon_connect_config);

	/*
	* DC Nahoni replaces PHP's native session 
	* handling and sends the session data to
	* an RDBMS table.
	*/
	$dc_nahoni_config = new \dc\nahoni\SessionConfig($config_file_full);		
	$dc_nahoni_config->set_database($dc_yukon_connection->get_connection());

	$dc_nahoni_session = new \dc\nahoni\Session($dc_nahoni_config);
	session_set_save_handler($dc_nahoni_session, TRUE);
	session_start();
	
	$_SESSION['TEST_SES'] = 'Damon Caskey';	
	echo $_SESSION['TEST_SES'];
	
?>


