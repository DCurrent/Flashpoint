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
	$access_obj = new \dc\access\status();
	$access_obj->get_config()->set_authenticate_url(APPLICATION_SETTINGS::AUTHENTICATE_URL);
	$access_obj->set_redirect($url_query->return_url());
	
	$access_obj->verify();	
	$access_obj->action();
	
	// Start page cache.
	$page_obj = new class_page_cache();
	ob_start();		
		
	// Set up navigaiton.
	$navigation_obj = new class_navigation();
	$navigation_obj->generate_markup_nav();
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
					array($filter->get_status(),		SQLSRV_PARAM_IN),
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
                <h1>Alarm List</h1>
                <p>This is a list of all reported fire/drill incidents.</p>
            </div> 
                    
            <div id="filters">
            
	        <legend>Filters <a type="button" class="btn" data-toggle="collapse" data-target="#filter">(Show/Hide)</a></legend>
            <form class="form-horizontal collapse" role="form" id="filter" method="get" enctype="multipart/form-data">
            	                
                <input type="hidden" name="field" value="<?php echo $sorting->get_sort_field(); ?>" />
                <input type="hidden" name="order" value="<?php echo $sorting->get_sort_order(); ?>" />
            
            	<div class="form-group">
                	<label class="control-label col-sm-2" for="created">Created (from):</label>
                	<div class="col-sm-4">
                		<input 
                        	type	="datetime-local" 
                            class	="form-control"  
                            name	="create_f" 
                            id		="create_f" 
                            placeholder="yyyy-mm-dd"
                            value="<?php echo $filter->get_create_f(); ?>">
                	</div>
                
                	<label class="control-label col-sm-2" for="created">Created (to):</label>
                	<div class="col-sm-4">
                		<input 
                        	type	="datetime-local" 
                            class	="form-control"  
                            name	="create_t" 
                            id		="create_t" 
                            placeholder="yyyy-mm-dd"
                            value="<?php echo $filter->get_create_t(); ?>">
                	</div>
                </div>
                
                <div class="form-group">
                	<label class="control-label col-sm-2" for="created">Updated (from):</label>
                	<div class="col-sm-4">
                		<input 
                        	type	="datetime-local" 
                            class	="form-control"  
                            name	="update_f" 
                            id		="update_f" 
                            placeholder="yyyy-mm-dd"
                            value="<?php echo $filter->get_update_f(); ?>">
                	</div>
                
                	<label class="control-label col-sm-2" for="created">Updated (to):</label>
                	<div class="col-sm-4">
                		<input 
                        	type	="datetime-local" 
                            class	="form-control"  
                            name	="update_t" 
                            id		="update_t" 
                            placeholder="yyyy-mm-dd"
                            value="<?php echo $filter->get_update_t(); ?>">
                	</div>
                </div>
                
                <div class="form-group">
                	<label class="control-label col-sm-2" for="status">Status:</label>
                	<div class="col-sm-10">
                    	    
                            <div class="radio">
                            
                            	<label><input 
                                        type="radio" 
                                        name="status" 
                                        value="<?php echo STATUS_SELECT::S_PRIVATE; ?>"                                             
                                        <?php if($filter->get_status() == STATUS_SELECT::S_PRIVATE) echo ' checked ';?>>Private</label>
                       	 	</div>
                                       
                       		<div class="radio">
                            
                            	<label><input 
                                        type="radio" 
                                        name="status" 
                                        value="<?php echo STATUS_SELECT::S_PUBLIC; ?>"                                             
                                        <?php if($filter->get_status() == STATUS_SELECT::S_PUBLIC) echo ' checked ';?>>Public</label>
                       	 	</div>                        
                        		
                            <div class="radio">
                        
                           		<label><input 
                                        type="radio" 
                                        name="status" 
                                        value=""                                             
                                        <?php if($filter->get_status() == NULL) echo ' checked ';?>>All</label>
                       	 	</div>                   
                	</div>
				</div>
                
                <button 
                                type	="submit"
                                class 	="btn btn-primary btn-block" 
                                name	="set_filter" 
                                id		="set_filter"
                                title	="Apply selected filters to list."
                                >
                                <span class="glyphicon glyphicon-filter"></span>Apply Filters</button>       
                    
            </form>
            
            </div>
            
            <br />
            
            <a href="alarm.php?nav_command=<?php echo RECORD_NAV_COMMANDS::NEW_BLANK; ?>" class="btn btn-success btn-block" data-toggle="tooltip" title="Click here to start entering a new ticket."><span class="glyphicon glyphicon-plus"></span> New Incident</a>
          
            <!--div class="table-responsive"-->
            <table class="table">
                <caption></caption>
                <thead>
                    <tr>
                        <th><a href="<?php echo $sorting->sort_url(SORTING_FIELDS::NAME); ?>">Name <?php echo $sorting->sorting_markup(SORTING_FIELDS::NAME); ?></a></th>
                        <th><a href="<?php echo $sorting->sort_url(SORTING_FIELDS::LOCATION); ?>">Location <?php echo $sorting->sorting_markup(SORTING_FIELDS::LOCATION); ?></a></th>
                        <th>Details</th>
                        <th><a href="<?php echo $sorting->sort_url(SORTING_FIELDS::STATUS); ?>">Status <?php echo $sorting->sorting_markup(SORTING_FIELDS::STATUS); ?></a></th>
                        <th><a href="<?php echo $sorting->sort_url(SORTING_FIELDS::CREATED); ?>">Created <?php echo $sorting->sorting_markup(SORTING_FIELDS::CREATED); ?></a></th>
                        <th><a href="<?php echo $sorting->sort_url(SORTING_FIELDS::UPDATED); ?>">Updated <?php echo $sorting->sorting_markup(SORTING_FIELDS::UPDATED); ?></a></th>
                        <th></th>
                    </tr>
                </thead>
                <tfoot>
                </tfoot>
                <tbody>                        
                    <?php						
						$_obj_data_main = NULL;
					
						$row_class = array(1 => '',
											2 => 'alert-warning',
											NULL => '');
						
						$status = array(FALSE => 'Private',
											TRUE => 'Public',
											NULL => 'Private');
					
                        
						if(is_object($_obj_data_main_list) === TRUE)
						{
							for($_obj_data_main_list->rewind(); $_obj_data_main_list->valid(); $_obj_data_main_list->next())
							{						
								$_obj_data_main = $_obj_data_main_list->current();							
								
								// Let's limit how much is shown in the table to keep row height resonable.
								$details_display = $_obj_data_main->get_details();
																
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
                                <tr class="<?php echo $row_class[$_obj_data_main->get_status()]; ?>">
                                    <td><?php echo $_obj_data_main->get_label(); ?></td>
                                    <td><?php echo $location_display; ?></td>
                                    <td><?php echo $details_display; ?></td>
                                    <td><?php echo $status[$_obj_data_main->get_status()]; ?></td>
                                    <td><?php if(is_object($_obj_data_main->get_log_create()) === TRUE) echo date('Y-m-d H:i:s', $_obj_data_main->get_log_create()->getTimestamp()); ?></td>
                                    <td><?php if(is_object($_obj_data_main->get_log_update()) === TRUE) echo date('Y-m-d H:i:s', $_obj_data_main->get_log_update()->getTimestamp()); ?></td>
                                    <td><a	href		="alarm.php?id=<?php echo $_obj_data_main->get_id(); ?>" 
                                            class		="btn btn-info"
                                            title		="View details or edit this item."
                                            ><span class="glyphicon glyphicon-eye-open"></span></a></td>
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