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
	
	/* Page caching. */
	$page_obj = new \dc\Prudhoe\PageCache();		
		
	/* Main navigaiton. */
	$obj_navigation_main = new Navigation();
	$obj_navigation_main->generate_markup_nav_public();
	$obj_navigation_main->generate_markup_footer();	
	
    /* 
    * Record paging control. Note there is
    * additional code to receive necessary
    * control feedback after we get records
    * from database.
    */
    $paging_config = new \dc\record_navigation\PagingConfig();
    $paging_config->set_url_query_instance($url_query);
	$paging = new \dc\record_navigation\Paging($paging_config);
	
    /* Record sorting and filtering. */
	$sorting = new \dc\sorting\Sorting();
	$sorting->set_sort_field(SORTING_FIELDS::CREATED);
	$sorting->set_sort_order(\dc\sorting\SORTING_ORDER_TYPE::DECENDING);
	$sorting->populate_from_request();
	
	$filter = new class_filter();
	$filter->populate_from_request();
		
	$sql_string = 'EXEC dc_flashpoint_fire_alarm_list :page_current,														 
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
		
	    $dbh_pdo_statement->bindValue(':page_current', $paging->get_page_current(), \PDO::PARAM_INT);
        $dbh_pdo_statement->bindValue(':page_rows', $paging->get_row_max(), \PDO::PARAM_INT);
        $dbh_pdo_statement->bindValue(':create_from', $filter->get_create_f(), \PDO::PARAM_STR);
        $dbh_pdo_statement->bindValue(':create_to', $filter->get_create_t(), \PDO::PARAM_STR);
        $dbh_pdo_statement->bindValue(':update_from', $filter->get_update_f(), \PDO::PARAM_STR);
        $dbh_pdo_statement->bindValue(':update_to', $filter->get_update_t(), \PDO::PARAM_STR);
        $dbh_pdo_statement->bindValue(':status', $filter->get_status(), \PDO::PARAM_INT);
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

    $count = 0;
    while($_row_object = $dbh_pdo_statement->fetchObject('data_fire_alarm', array()))
    {  
        $count++;
        $_obj_data_main_list->push($_row_object);
    }
    
    
    /*
    * Now we need the paging information for 
    * our paging control.
    */

    try
    {         
        $dbh_pdo_statement->nextRowset();        
        
        $_paging_data = $dbh_pdo_statement->fetchObject('dc\record_navigation\data_paging', array());
        
        $paging->set_page_last($_paging_data->get_page_count());
        $paging->set_row_count_total($_paging_data->get_record_count());
    }
    catch(\PDOException $e)
    {
        die('Database error : '.$e->getMessage());
    }
?>

<!DOCtype html>
<html lang="en">
    <head>
    	<!-- Disable IE compatability mode. Must be FIRST tag in header. -->
    	<meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
        <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1" />
        <title><?php echo APPLICATION_SETTINGS::NAME; ?></title>        
        
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
        <link rel="stylesheet" href="source/css/style.css" />
        <link rel="stylesheet" href="source/css/print.css" media="print" />
        
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
        
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
                <h1>Alarm List</h1>
                <p>This is a list of all reported fire/drill incidents.</p>
            </div> 
            
            <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 id="h41" class="panel-title">
                        <a class="accordion-toggle" data-toggle="collapse" href="#collapse_module_1"><span class="glyphicon glyphicon-filter"></span><span class="glyphicon glyphicon-menu-down pull-right"></span> Filters</a>
                        </h4>
                    </div>
                
                	<div style="" id="collapse_module_1" class="panel-collapse collapse">
                        <div class="panel-body"> 
                                                        
                            <!--legend></legend-->                           
                            <form class="form-horizontal" role="form" id="filter" method="get" enctype="multipart/form-data">
            	                
                                <input type="hidden" name="field" value="<?php echo $sorting->get_sort_field(); ?>" />
                                <input type="hidden" name="order" value="<?php echo $sorting->get_sort_order(); ?>" />
                            
                                <div class="form-group row">
                                    <label class="control-label col-sm-2" for="created">Created (from)</label>
                                    <div class="col-sm-4">
                                        <input 
                                            type	="datetime-local" 
                                            class	="form-control"  
                                            name	="create_f" 
                                            id		="create_f" 
                                            placeholder="yyyy-mm-dd"
                                            value="<?php echo $filter->get_create_f(); ?>">
                                    </div>
                                
                                    <label class="control-label col-sm-2" for="created">Created (to)</label>
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
                                
                                <div class="form-group row">
                                    <label class="control-label col-sm-2" for="created">Updated (from)</label>
                                    <div class="col-sm-4">
                                        <input 
                                            type	="datetime-local" 
                                            class	="form-control"  
                                            name	="update_f" 
                                            id		="update_f" 
                                            placeholder="yyyy-mm-dd"
                                            value="<?php echo $filter->get_update_f(); ?>">
                                    </div>
                                
                                    <label class="control-label col-sm-2" for="created">Updated (to)</label>
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
                                    <label class="control-label col-sm-2" for="status">Status</label>
                                    <div class="col-sm-10">

                                            <div class="radio">

                                                <label><input 
                                                        type="radio" 
                                                        name="status" 
                                                        value="<?php echo STATUS_SELECT::S_PRIVATE; ?>"                                             
                                                        <?php if($filter->get_status() == STATUS_SELECT::S_PRIVATE) echo ' checked ';?>> Private</label>
                                            </div>

                                            <div class="radio">

                                                <label><input 
                                                        type="radio" 
                                                        name="status" 
                                                        value="<?php echo STATUS_SELECT::S_PUBLIC; ?>"                                             
                                                        <?php if($filter->get_status() == STATUS_SELECT::S_PUBLIC) echo ' checked ';?>> Public</label>
                                            </div>                        

                                            <div class="radio">

                                                <label><input 
                                                        type="radio" 
                                                        name="status" 
                                                        value="-1"                                             
                                                        <?php if($filter->get_status() == NULL) echo ' checked ';?>> All</label>
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
                    </div>
                </div>     
            
            <br />
            
            <?php
				// Clickable rows. Clicking on table rows
				// should take user to a detail page for the
				// record in that row. To do this we first get
				// the base name of this file, and remove "list".
				// 
				// The detail file will always have same name 
				// without "list". Example: area.php, area_list.php
				//
				// Once we have the base name, we can use script to
				// make table rows clickable by class selector
				// and passing a completed URL (see the <tr> in
				// data table we are making clickable).
				//
				// Just to ease in development, we verify the detail
				// file exists before we actually include the script
				// and build a complete URL string. That way if the
				// detail file is not yet built, clicking on a table
				// row does nothing at all instead of giving the end
				// user an ugly 404 error.
				//
				// Lastly, if the base name exists we also build a 
				// "new item" button that takes user directly
				// to detail page with a blank record.	
			 
				$target_url 	= '#';
				$target_name	= basename(__FILE__, '_list.php').'.php';
				$target_file	= __DIR__.'/'.$target_name;				
				
				// Does the file exisit? If so we can
				// use the URL, script, and new 
				// item button.
				if(file_exists($target_file))
				{
					$target_url = $target_name;
				?>
                	<script>
						// Clickable table row.
						jQuery(document).ready(function($) {
                            
                            //alert "ready.";
                            
							$(".clickable-row").click(function() {
                                //alert '<?php echo $target_url; ?>?id=' + $(this).data("href");
								window.document.location = '<?php echo $target_url; ?>?id=' + $(this).data("href");
							});
						});
					</script>
                                
                    <a href="<?php echo $target_url; ?>&#63;nav_command=<?php echo \dc\record_navigation\RECORD_NAV_COMMANDS::NEW_BLANK;?>" class="btn btn-success btn-block" data-toggle="tooltip" title="Click here to start entering a new ticket."><span class="glyphicon glyphicon-plus"></span> New Incident</a>
                <?php
				}
				
			?>
                  
            <!--div class="table-responsive"-->
            <table class="table table-hover">
                <caption></caption>
                <thead>
                    <tr>
                        <th><a href="<?php echo $sorting->sort_url(SORTING_FIELDS::NAME); ?>">Name <?php echo $sorting->sorting_markup(SORTING_FIELDS::NAME); ?></a></th>
                        <th><a href="<?php echo $sorting->sort_url(SORTING_FIELDS::LOCATION); ?>">Location <?php echo $sorting->sorting_markup(SORTING_FIELDS::LOCATION); ?></a></th>
                        <th>Details</th>
                        <th><a href="<?php echo $sorting->sort_url(SORTING_FIELDS::STATUS); ?>">Status <?php echo $sorting->sorting_markup(SORTING_FIELDS::STATUS); ?></a></th>
                        <th><a href="<?php echo $sorting->sort_url(SORTING_FIELDS::CREATED); ?>">Created <?php echo $sorting->sorting_markup(SORTING_FIELDS::CREATED); ?></a></th>
                                                
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th><a href="<?php echo $sorting->sort_url(SORTING_FIELDS::NAME); ?>">Name <?php echo $sorting->sorting_markup(SORTING_FIELDS::NAME); ?></a></th>
                        <th><a href="<?php echo $sorting->sort_url(SORTING_FIELDS::LOCATION); ?>">Location <?php echo $sorting->sorting_markup(SORTING_FIELDS::LOCATION); ?></a></th>
                        <th>Details</th>
                        <th><a href="<?php echo $sorting->sort_url(SORTING_FIELDS::STATUS); ?>">Status <?php echo $sorting->sorting_markup(SORTING_FIELDS::STATUS); ?></a></th>
                        <th><a href="<?php echo $sorting->sort_url(SORTING_FIELDS::CREATED); ?>">Created <?php echo $sorting->sorting_markup(SORTING_FIELDS::CREATED); ?></a></th>
                                                
                    </tr>
                </tfoot>
                <tbody>                        
                    <?php						
						$_obj_data_main = NULL;
					
						$row_class = array(0 => '',
                                            1 => '',
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
								
                                /* 
                                * Let's limit how much is shown in the table 
                                * to keep row height resonable.
                                */
								$label_display = strip_tags($_obj_data_main->get_label());
											
								if (strlen($label_display) > 13)
								{
   									$label_display = substr($label_display, 0, 10) . '...';
								}
                                
								/* 
                                * Let's limit how much is shown in the table 
                                * to keep row height resonable.
                                */
								$details_display = strip_tags($_obj_data_main->get_details());
											
								if (strlen($details_display) > 50)
								{
   									$details_display = substr($details_display, 0, 47) . '...';
								}
								
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
                                <tr class="clickable-row <?php echo $row_class[$_obj_data_main->get_status()]; ?>" role="button" data-href="<?php echo $_obj_data_main->get_id(); ?>">
                                    <td><?php echo $label_display; ?></td>
                                    <td><?php echo $location_display; ?></td>
                                    <td><?php echo $details_display; ?></td>
                                    <td><?php echo $status[$_obj_data_main->get_status()]; ?></td>
                                    <td><?php echo $_obj_data_main->get_log_update(); ?></td>
                                                                        
                                </tr>                                    
                        <?php								
                        	}
						}
                    ?>
                </tbody>                        
            </table>  

            <?php

				echo $paging->generate_paging_markup();
				echo $obj_navigation_main->get_markup_footer(); 
				echo '<!--Page Time: '.$page_obj->time_elapsed().' seconds-->';
			?>
        </div><!--container-->  
        
        
        
    <script>
  
          $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
</body>
</html>

<?php
	/* Collect and output page markup. */
	$page_obj->markup_and_flush();	
	$page_obj->output_markup();
?>