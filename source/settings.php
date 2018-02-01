<?php

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
		const
			TO		= '',
			CC		= '',
			BCC		= '',
			SUBJECT = 'UK Fire Marshal',
			FROM 	= 'ehs_noreply@uky.edu';
	}
	
	abstract class ROOM_SELECT
	{
		const
			OUTSIDE = -1;
	}
	
	abstract class STATUS_SELECT
	{		
		const
			S_PUBLIC		= 1, 
			S_PRIVATE		= 0;
	}

	abstract class SORTING_FIELDS
	{
		const
			NAME 		= 1,
			LOCATION	= 2,
			STATUS		= 3,
			REPORTED	= 4,
			CREATED		= 5,
			UPDATED 	= 6;
	}

?>