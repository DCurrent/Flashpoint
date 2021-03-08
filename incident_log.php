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
	$url_query	= new \dc\fraser\URLFix();
		
	// User access.
	
	
	/* Page caching. */
	$page_obj = new \dc\Prudhoe\PageCache();			
		
	/* Main navigaiton. */
	$obj_navigation_main = new Navigation();	
	$obj_navigation_main->generate_markup_nav_public();
	$obj_navigation_main->generate_markup_footer();
		
    $paging_config = new \dc\record_navigation\PagingConfig();
    $paging_config->set_url_query_instance($url_query);
	$paging = new \dc\record_navigation\Paging($paging_config);
	
	// Establish sorting object, set defaults, and then get settings
	// from user (if any).
	$sorting = new \dc\sorting\Sorting();
	$sorting->set_sort_field(SORTING_FIELDS::REPORTED);
	$sorting->set_sort_order(\dc\sorting\SORTING_ORDER_TYPE::DECENDING);
	$sorting->populate_from_request();
	
	$filter = new class_filter();
	$filter->populate_from_request();

    $sql_string = 'EXEC fire_alarm_list :page_current,														 
										:page_rows,
										:create_from,
										:create_to,
										:update_from,
										:update_to,
										:status,
										:sort_field,
										:sort_order';	
	
    try
    {   
        $dbh_pdo_statement = $dc_yukon_connection->get_member_connection()->prepare($sql_string);
		
        $page_last = NULL;
        $row_count = NULL;
        
	    $dbh_pdo_statement->bindValue(':page_current', $paging->get_page_current(), \PDO::PARAM_INT);
        $dbh_pdo_statement->bindValue(':page_rows', $paging->get_row_max(), \PDO::PARAM_INT);
        //$dbh_pdo_statement->bindValue(':page_rows', 1000, \PDO::PARAM_INT);
        $dbh_pdo_statement->bindValue(':create_from', $filter->get_create_f(), \PDO::PARAM_STR);
        $dbh_pdo_statement->bindValue(':create_to', $filter->get_create_t(), \PDO::PARAM_STR);
        $dbh_pdo_statement->bindValue(':update_from', $filter->get_update_f(), \PDO::PARAM_STR);
        $dbh_pdo_statement->bindValue(':update_to', $filter->get_update_t(), \PDO::PARAM_STR);
        $dbh_pdo_statement->bindValue(':status', STATUS_SELECT::S_PUBLIC, \PDO::PARAM_INT);
        $dbh_pdo_statement->bindValue(':sort_field', $sorting->get_sort_field(), \PDO::PARAM_INT);
        $dbh_pdo_statement->bindValue(':sort_order', $sorting->get_sort_order(), \PDO::PARAM_INT);
        
        $dbh_pdo_statement->execute();
    }
    catch(\PDOException $e)
    {
        die('Database error : '.$e->getMessage());
    }

    /*
    * Build a list of data objects. Each object in the
    * list represents a row of data from our query.
    */
    $_row_object = NULL;
    $_obj_data_main_list = new \SplDoublyLinkedList();	// Linked list object.

    while($_row_object = $dbh_pdo_statement->fetchObject('data_fire_alarm', array()))
    {       
        $_obj_data_main_list->push($_row_object);
    }

    /*
    * Now we need the paging information for 
    * our paging control.
    */

    $dbh_pdo_statement->nextRowset();

    $_paging_data = $dbh_pdo_statement->fetchObject('dc\record_navigation\data_paging', array());
	
    /* 
    * Send control data from procedure 
    * to paging object.
	*/

    echo '<!-- get_page_count: '.$_paging_data->get_page_count().' -->';
    echo '<!-- get_record_count: '.$_paging_data->get_record_count().' -->';

    $paging->set_page_last($_paging_data->get_page_count());
	$paging->set_row_count_total($_paging_data->get_record_count());

    echo '<!-- $paging->get_page_last: '.$paging->get_page_last().' -->';
    echo '<!-- $paging->get_row_count: '.$paging->get_row_count().' -->';

?>

<!DOCtype html>
<html lang="en">
    <head>
    	<!-- Disable IE compatability mode. Must be FIRST tag in header. -->
    	<meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
        <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1" />
        <title><?php echo APPLICATION_SETTINGS::NAME; ?> - Campus Fire Log</title>        
        
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
        <link rel="stylesheet" href="source/css/style.css" />
        <link rel="stylesheet" href="source/css/print.css" media="print" />
        
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
            <?php echo $obj_navigation_main->get_markup_nav(); ?>                                                                                
            <div class="page-header">
                <h1>Campus Fire Log</h1>
                <p>This is a list of all reported fire/drill incidents.</p>
            </div> 
                                  
            <?php echo $paging->generate_paging_markup(); ?>
            
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
                    <tr>
                    	<th><a href="<?php echo $sorting->sort_url(SORTING_FIELDS::REPORTED); ?>">Time Occurred <?php echo $sorting->sorting_markup(SORTING_FIELDS::REPORTED); ?></a></th>
                        <th><a href="<?php echo $sorting->sort_url(SORTING_FIELDS::LOCATION); ?>">Location <?php echo $sorting->sorting_markup(SORTING_FIELDS::LOCATION); ?></a></th>
                        <th>Details</th>                        
                    </tr>
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
                                    <?php $obj_date_time = new DateTime($_obj_data_main->get_time_reported()); ?>
                                    <td><?php echo date('Y-m-d H:i:s', $obj_date_time->getTimestamp()); ?></td>
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

				echo $paging->get_markup();
				echo $obj_navigation_main->get_markup_footer(); 
				echo '<!--Page Time: '.$page_obj->time_elapsed().' seconds-->';
			?>
        </div><!--container-->        
    <script
			  src="https://code.jquery.com/jquery-3.5.1.min.js"
			  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
			  crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
</body>
</html>

<?php
	/* Collect and output page markup. */
	$page_obj->markup_and_flush();	
	$page_obj->output_markup();
?>