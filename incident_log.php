<?php 		
	
	require(__DIR__.'/source/main.php');
	
	class class_filter
	{
		private
			$create_f	= NULL,
			$create_t	= NULL,
			$update_f	= NULL,
			$update_t 	= NULL,
			$status		= NULL;
		
		// Populate members from $_REQUEST.
		public function populate_from_request()
		{		
			// Interate through each class method.
			foreach(get_class_methods($this) as $method) 
			{		
				$key = str_replace('set_', '', $method);
							
				// If there is a request var with key matching
				// current method name, then the current method 
				// is a set mutator for this request var. Run 
				// it (the set method) with the request var. 
				if(isset($_GET[$key]))
				{					
					$this->$method($_GET[$key]);					
				}
			}
		}
		
		private function validateDate($date, $format = 'Y-m-d')
		{
			$d = DateTime::createFromFormat($format, $date);
			return $d && $d->format($format) == $date;
		}
		
		public function get_create_f()
		{
			return $this->create_f;
		}
		
		public function get_create_t()
		{
			return $this->create_t;
		}
		
		public function get_update_f()
		{
			return $this->update_f;
		}
		
		public function get_update_t()
		{
			return $this->update_t;
		}
		
		public function get_status()
		{
			return $this->status;
		}
		
		public function set_create_f($value)
		{
			if($this->validateDate($value) === TRUE)
			{
				$this->create_f = $value;
			}
		}
		
		public function set_create_t($value)
		{
			if($this->validateDate($value) === TRUE)
			{
				$this->create_t = $value;
			}
		}		
		
		public function set_update_f($value)
		{
			if($this->validateDate($value) === TRUE)
			{
				$this->update_f = $value;
			}
		}
		
		public function set_update_t($value)
		{
			if($this->validateDate($value) === TRUE)
			{
				$this->update_t = $value;
			}
		}
		
		public function set_status($value)
		{		
			$this->status = $value;			
		}
	}
	
	// Prepare redirect url with variables.
	$url_query	= new url_query;
		
	// User access.
	
	
	// Start page cache.
	$page_obj = new class_page_cache();
	ob_start();		
		
	// Set up navigaiton.
	$navigation_obj = new class_navigation();	
	$navigation_obj->generate_markup_nav_public();
	$navigation_obj->generate_markup_footer();	
	
	// Set up database.
	$db_conn_set = new class_db_connect_params();
	$db_conn_set->set_name(DATABASE::NAME);
	
	$db = new class_db_connection($db_conn_set);
	$query = new class_db_query($db);
		
	$paging = new class_paging;
	
	// Establish sorting object, set defaults, and then get settings
	// from user (if any).
	$sorting = new class_sort_control;
	$sorting->set_sort_field(SORTING_FIELDS::CREATED);
	$sorting->set_sort_order(SORTING_ORDER_TYPE::DECENDING);
	$sorting->populate_from_request();
	
	$filter = new class_filter();
	$filter->populate_from_request();
		
	$query->set_sql('{call fire_alarm_list(@page_current 		= ?,														 
										@page_rows 			= ?,
										@page_last 			= ?,
										@row_count_total	= ?,										
										@create_from 		= ?,
										@create_to 			= ?,
										@update_from 		= ?,
										@update_to 			= ?,
										@status				= ?,
										@sort_field 		= ?,
										@sort_order 		= ?)}');
											
	$page_last 	= NULL;
	$row_count 	= NULL;
	$sort_field 		= $sorting->get_sort_field();
	$sort_order 		= $sorting->get_sort_order();	
	
	$params = array(array($paging->get_page_current(), 	SQLSRV_PARAM_IN), 
					array($paging->get_row_max(), 		SQLSRV_PARAM_IN), 
					array($page_last, 					SQLSRV_PARAM_OUT, SQLSRV_PHPTYPE_INT),
					array($row_count, 					SQLSRV_PARAM_OUT, SQLSRV_PHPTYPE_INT),					
					array($filter->get_create_f(),		SQLSRV_PARAM_IN),
					array($filter->get_create_t(),		SQLSRV_PARAM_IN),
					array($filter->get_update_f(),		SQLSRV_PARAM_IN),
					array($filter->get_update_t(),		SQLSRV_PARAM_IN),
					array(STATUS_SELECT::S_PUBLIC,		SQLSRV_PARAM_IN),
					array($sort_field,					SQLSRV_PARAM_IN),
					array($sort_order,					SQLSRV_PARAM_IN));					

	$query->set_params($params);
	$query->query();
	
	$query->get_line_params()->set_class_name('class_fire_alarm_data');
	$_obj_data_main_list = $query->get_line_object_list();

	// Send control data from procedure to paging object.
	$paging->set_page_last($page_last);
	$paging->set_row_count_total($row_count);

		

?>

<!DOCtype html>
<html lang="en">
    <head>
    	<!-- Disable IE compatability mode. Must be FIRST tag in header. -->
    	<meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
        <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1" />
        <title><?php echo APPLICATION_SETTINGS::NAME; ?> - Campus Fire Log</title>        
        
         <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <link rel="stylesheet" href="source/css/style.css" />
        <link rel="stylesheet" href="source/css/print.css" media="print" />
        
        <!-- jQuery library -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        
        <!-- Latest compiled JavaScript -->
        <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        
        <style>
		
.in.collapse+a.btn.showdetails:before
{
    content:'Hide details «';
}
.collapse+a.btn.showdetails:before
{
    content:'Show details »';
}
</style>
    </head>
    
    <body>    
        <div id="container" class="container">            
            <?php echo $navigation_obj->get_markup_nav(); ?>                                                                                
            <div class="page-header">
                <h1>Campus Fire Log</h1>
                <p>This is a list of all reported fire/drill incidents.</p>
            </div> 
                                  
            <!--div class="table-responsive"-->
            <table class="table">
                <caption></caption>
                <thead>
                    <tr>
                    	<th><a href="<?php echo $sorting->sort_url(SORTING_FIELDS::REPORTED); ?>">Time Occurred <?php echo $sorting->sorting_markup(SORTING_FIELDS::REPORTED); ?></a></th>
                        <th><a href="<?php echo $sorting->sort_url(SORTING_FIELDS::LOCATION); ?>">Location <?php echo $sorting->sorting_markup(SORTING_FIELDS::LOCATION); ?></a></th>
                        <th>Details</th>                        
                    </tr>
                </thead>
                <tfoot>
                </tfoot>
                <tbody>                        
                    <?php						
						
                        
						if(is_object($_obj_data_main_list) === TRUE)
						{
							for($_obj_data_main_list->rewind(); $_obj_data_main_list->valid(); $_obj_data_main_list->next())
							{						
								$_obj_data_main = $_obj_data_main_list->current();							
								
								// Let's limit how much is shown in the table to keep row height resonable.
								$details_display = $_obj_data_main->get_public_details();
																
								//if (strlen($details_display) > 150)
								//{
   								//	$details_display = substr($details_display, 0, 147) . '...';
								//}
								
								$location_display 	= NULL;
								$room_id_display	= NULL;
								
								if($_obj_data_main->get_room_code())
								{
									switch($_obj_data_main->get_room_code())
									{
										case ROOM_SELECT::OUTSIDE:
											$room_id_display = 'Outside';	
											break;
										default:
											$room_id_display = trim($_obj_data_main->get_room_id());
									}
								}
								else
								{
									$room_id_display = 'Unknown Room';	
								}
													
								
								if($_obj_data_main->get_building_code())
								{
									$location_display = $_obj_data_main->get_building_code().' - '.trim($_obj_data_main->get_building_name()).', '.$room_id_display; 
								}

                        ?>
                                <tr>
                                	<td><?php if(is_object($_obj_data_main->get_time_reported()) === TRUE) echo date('Y-m-d H:i:s', $_obj_data_main->get_time_reported()->getTimestamp()); ?></td>
                                    <td><?php echo $location_display; ?></td>
                                    <td><?php echo $details_display; ?></td>                                    
                                </tr>                                    
                        <?php								
                        	}
						}
                    ?>
                </tbody>                        
            </table>  

            <?php

				echo $paging->generate_paging_markup();
				echo $navigation_obj->get_markup_footer(); 
				echo '<!--Page Time: '.$page_obj->time_elapsed().' seconds-->';
			?>
        </div><!--container-->        
    <script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-40196994-1', 'uky.edu');
  ga('send', 'pageview');
  
  $(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
</body>
</html>

<?php
	// Collect and output page markup.
	$page_obj->markup_from_cache();	
	$page_obj->output_markup();
?>