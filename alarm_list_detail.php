<?php 		
	
	require(__DIR__.'/source/main.php');
	
	function boolean_mark($value)
	{
		if($value == TRUE)
		{
			echo '<span class="glyphicon glyphicon-ok" title="Yes"></span>';
		}
		else
		{
			echo '<span class="glyphicon glyphicon-remove" title="No"></span>';
		}
	}
	
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
	//...Public
	
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
	
	// Record navigation.
	$obj_navigation_rec = new class_record_nav();
	
	$query->set_sql('{call fire_alarm_detail(@id = ?,														 
								@sort_field 	= ?,
								@sort_order 	= ?,
								@nav_first		= ?,
								@nav_previous	= ?,
								@nav_next		= ?,
								@nav_last		= ?)}');	
	$nav_first 		= NULL;
	$nav_previous	= NULL;
	$nav_next		= NULL;
	$nav_last 		= NULL;
					
	$params = array(array($obj_navigation_rec->get_id(), 	SQLSRV_PARAM_IN),
					array(NULL, 		SQLSRV_PARAM_OUT, SQLSRV_PHPTYPE_INT),
					array(NULL, 		SQLSRV_PARAM_OUT, SQLSRV_PHPTYPE_INT),
					array($nav_first,	SQLSRV_PARAM_OUT, SQLSRV_PHPTYPE_INT),
					array($nav_previous, SQLSRV_PARAM_OUT, SQLSRV_PHPTYPE_INT),
					array($nav_next, 	SQLSRV_PARAM_OUT, SQLSRV_PHPTYPE_INT),
					array($nav_last, 	SQLSRV_PARAM_OUT, SQLSRV_PHPTYPE_INT));

	$query->set_params($params);
	$query->query();
	
	$query->get_line_params()->set_class_name('class_fire_alarm_data');
	if($query->get_row_exists() === TRUE) 
	{
		$_obj_data_main = $query->get_line_object();
	}
	else
	{
		echo '<h2 style="color:red">Incident ID invalid or not provided. This report cannot be accessed without a valid incident ID.</h2>';
		exit;
	}
	
	
	// Type display.
	$type_of_incident = NULL;
	$_obj_data_display = new class_common_data();
	
	$query->set_sql('{call fire_alarm_type_display(@id = ?)}');	
	
	$params = array(array($_obj_data_main->get_fire(), SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_INT));

	$query->set_params($params);
	$query->query();
	
	$query->get_line_params()->set_class_name('class_common_data');
	if($query->get_row_exists() === TRUE) $_obj_data_display = $query->get_line_object();
	
	$type_of_incident = $_obj_data_display->get_label();
	
	// Cause display.
	$cause_of_incident = NULL;
	$_obj_data_display = new class_common_data();
	
	$query->set_sql('{call fire_alarm_cause_display(@id = ?)}');	
	
	$params = array(array($_obj_data_main->get_cause(), SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_INT));

	$query->set_params($params);
	$query->query();
	
	if($query->get_row_exists() === TRUE) $_obj_data_display = $query->get_line_object();
	
	$cause_of_incident = $_obj_data_display->get_label();
	
	// Party display.
	$responsible_party = NULL;
	$_obj_data_display = new class_common_data();
	
	$query->set_sql('{call fire_alarm_responsible_party_display(@id = ?)}');	
	
	$params = array(array($_obj_data_main->get_responsible_party(), SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_INT));

	$query->set_params($params);
	$query->query();
	
	if($query->get_row_exists() === TRUE) $_obj_data_display = $query->get_line_object();
	
	$responsible_party = $_obj_data_display->get_label();

?>

<!DOCtype html>
<html lang="en">
    <head>
    	<!-- Disable IE compatability mode. Must be FIRST tag in header. -->
    	<meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
        <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1" />
        <title><?php echo APPLICATION_SETTINGS::NAME; ?></title>        
        
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
                <h1>Fire Incident Report</h1>
                <p>See below for details about this incident.</p>
            </div> 
          
          	<?php	
				if(is_object($_obj_data_main))	
				$row_class = array(1 => '',
									2 => 'alert-warning',
									NULL => '');
				
				$status = array(FALSE => 'Private',
									TRUE => 'Public',
									NULL => 'Private');
				
				
					$building_code_display = NULL;					
					if($_obj_data_main->get_room_id())
					{
						switch($_obj_data_main->get_room_id())
						{
							case -1:
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
						$building_code_display = $room_id_display.', '. $_obj_data_main->get_building_code().' - '.$_obj_data_main->get_building_name(); 
					}
					
					// Created by
					//$lookup = new class_access_lookup;
				
					if($_obj_data_main->get_log_create_by())
					{
						//$lookup->lookup($_obj_data_main->get_log_create_by());
					}									
			?>
          
            <!--div class="table-responsive"-->
            <table class="table">
                <caption>General</caption>
                <thead>
                </thead>
                <tfoot>
                </tfoot>
                <tbody> 
                    <tr>
                        <th>Name of Report</th>
                        <td><?php echo $_obj_data_main->get_label(); ?></td>
                    </tr>
                    <tr>
                        <th>Location</th>
                        <td><?php echo $building_code_display; ?></td>
                    </tr>
                                        
                    <tr>
                        <th>Created</th>                                   
                        <td><?php if(is_object($_obj_data_main->get_log_create()) === TRUE) echo date('Y-m-d H:i:s', $_obj_data_main->get_log_create()->getTimestamp()); ?></td>
                    </tr>
                    
                    <tr>
                        <th>Created By</th>                                   
                        <td><?php echo $_obj_data_main->get_log_create_by(); //$lookup->get_account_data()->name_proper(); ?></td>
                    </tr>
                    
                    <tr>
                        <th>Last Update</th>                                   
                        <td><?php if(is_object($_obj_data_main->get_log_update()) === TRUE) echo date('Y-m-d H:i:s', $_obj_data_main->get_log_update()->getTimestamp()); ?></td>
                    </tr>
                    
                    <?php
						// Update by					
						if($_obj_data_main->get_log_update_by())
						{
							//$lookup->lookup($_obj_data_main->get_log_update_by());
						}
					?>
                    
                    <tr>
                        <th>Last Update By</th>                                   
                        <td><?php echo $_obj_data_main->get_log_create_by(); //$lookup->get_account_data()->name_proper(); ?></td>
                    </tr>
                    
                </tbody>                        
            </table>
            
            <table class="table">
                <caption>Alarm</caption>
                <thead>
                </thead>
                <tfoot>
                </tfoot>
                <tbody> 
                    <tr>
                        <th>Time of Incident</th>                                   
                        <td><?php if(is_object($_obj_data_main->get_time_reported()) === TRUE) echo date('Y-m-d H:i:s', $_obj_data_main->get_time_reported()->getTimestamp()); ?></td>
                    </tr>
                    <tr>
                        <th>Time Silenced</th>
                        <td><?php if(is_object($_obj_data_main->get_time_silenced()) === TRUE) echo date('Y-m-d H:i:s', $_obj_data_main->get_time_silenced()->getTimestamp()); ?></td>                                   
                    </tr>  
                    
                    <tr>
                        <th>Time Reset</th>
                        <td><?php if(is_object($_obj_data_main->get_time_reset()) === TRUE) echo date('Y-m-d H:i:s', $_obj_data_main->get_time_reset()->getTimestamp()); ?></td>                                   
                    </tr> 
                    
                    <tr>
                        <th>Occupied</th>
                        <td><?php echo boolean_mark($_obj_data_main->get_occupied()); ?></td>
                    </tr> 
                    
                    <tr>
                        <th>Evacuated</th>
                        <td><?php echo boolean_mark($_obj_data_main->get_evacuated()); ?></td>
                    </tr>  
                    
                    <tr>
                        <th>Notified</th>
                        <td><?php echo boolean_mark($_obj_data_main->get_notified()); ?></td>
                    </tr>  
                    
                    <tr>
                    	<th>Devices Activated</th>
                        <td>
                        	<table class="table">
                                <caption></caption>
                                <thead>
                                	<th>Pull Station</th>
                                    <th>Sprinkler</th>
                                    <th>Smoke Detector</th>
                                    <th>Stove Supression</th>
                                </thead>
                                <tfoot>
                                </tfoot>
                                <tbody>   
                                	<tr>
                                    	<td><?php echo boolean_mark($_obj_data_main->get_report_device_pull()); ?></td> 
                                        <td><?php echo boolean_mark($_obj_data_main->get_report_device_sprinkler()); ?></td>
                                        <td><?php echo boolean_mark($_obj_data_main->get_report_device_smoke()); ?></td>
                                        <td><?php echo boolean_mark($_obj_data_main->get_report_device_stove()); ?></td>
                                    </tr>                          
                                </tbody>
                            </table>
                        </td>
                    </tr>                            
                        
                </tbody>                        
            </table>
            
            <table class="table">
                <caption>Incident</caption>
                <thead>
                </thead>
                <tfoot>
                </tfoot>
                <tbody> 
                    <tr>
                        <th>Type</th>
                        <td><?php echo $type_of_incident; ?></td>
                    </tr>
                    <tr>
                        <th>Cause</th>
                        <td><?php echo $cause_of_incident; ?></td>
                    </tr>
                    <tr>
                        <th>Responsible Party</th>
                        <td><?php echo $responsible_party; ?></td>
                    </tr>
                    <tr>
                        <th>Fire Extinguisher Used</th>                                   
                        <td><?php echo boolean_mark($_obj_data_main->get_extinguisher()); ?></td>
                    </tr>
                    <tr>
                        <th>Injuries</th>
                        <td><?php echo $_obj_data_main->get_injuries(); ?></td>                                   
                    </tr>  
                    
                    <tr>
                        <th>Fatalities</th>
                        <td><?php echo $_obj_data_main->get_fatalities(); ?></td>                                   
                    </tr> 
                    
                    <tr>
                        <th>Description of Casualties</th>
                        <td><?php echo $_obj_data_main->get_injury_desc(); ?></td>
                    </tr> 
                    
                    <tr>
                        <th>Property Damage</th>
                        <td><?php echo $_obj_data_main->get_property_damage(); ?></td>
                    </tr>  
                    
                    <tr>
                        <th>Details</th>
                        <td><?php echo $_obj_data_main->get_details(); ?></td>
                    </tr> 
                </tbody>                        
            </table> 
            
            <?php echo $navigation_obj->get_markup_footer(); ?>
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