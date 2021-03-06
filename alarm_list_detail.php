<?php 		
	
	require(__DIR__.'/source/main.php');
	
	function boolean_mark($value)
	{
		if($value == TRUE)
		{
			echo '<span class="glyphicon glyphicon-ok" title="Yes">&#9745;</span>';
		}
		else
		{
			echo '<span class="glyphicon glyphicon-remove" title="No">&#9744;</span>';
		}
	}
	
	class class_filter
	{
		private	$create_f	= NULL;
        private $create_t	= NULL;
        private $update_f	= NULL;
        private $update_t 	= NULL;
        private $status		= NULL;
		
		// Populate members from $_REQUEST.
		public function populate_from_request($prefix = 'set_')
		{		
			// Interate through each class method.
			foreach(get_class_methods($this) as $method) 
			{		
				$key = substr($method, 4); //str_replace($prefix, '', $method);
							
				// If there is a request var with key matching
				// current method name, then the current method 
				// is a set mutator for this request var. Run 
				// it (the set method) with the request var. 
				if(isset($_REQUEST[$key]))
				{					
					$this->$method($_REQUEST[$key]);					
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
	
	/* Page caching. */
	$page_obj = new \dc\Prudhoe\PageCache();		
		
	/* Main navigaiton. */
	$obj_navigation_main = new Navigation();
	$obj_navigation_main->generate_markup_nav_public();
	$obj_navigation_main->generate_markup_footer();		
	
	/* Set up database. */

    /* Record navigation. */
	$obj_navigation_rec = new dc\record_navigation\RecordMenu();

    /* Call and execute delete SP. */            
    $sql_string = 'EXEC dc_flashpoint_fire_alarm_detail :id';

    echo '<!-- ID: '.$obj_navigation_rec->get_id().' -->';

    try
    {   
        $dbh_pdo_statement = $dc_yukon_connection->get_member_connection()->prepare($sql_string);

        $dbh_pdo_statement->bindValue(':id', $obj_navigation_rec->get_id(), \PDO::PARAM_INT);
        
        $_obj_data_main = $dbh_pdo_statement->execute();
        $_obj_data_main = $dbh_pdo_statement->fetchObject('data_fire_alarm', array());
    }
    catch(\PDOException $e)
    {
        die('Database error : '.$e->getMessage());
    }
    
	if($_obj_data_main) 
	{		
	}
	else
	{
		echo '<h2 style="color:red">Incident ID invalid or not provided. This report cannot be accessed without a valid incident ID.</h2>';
		exit;
	}
	
	
	/* Type display. */
    $type_of_incident = NULL;

    $sql_string = 'EXEC fire_alarm_type_display :id';	

	try
    {
        $dbh_pdo_statement = $dc_yukon_connection->get_member_connection()->prepare($sql_string);
        
        $dbh_pdo_statement->bindValue(':id', $_obj_data_main->get_fire(), \PDO::PARAM_INT);
        
        $_obj_data_display = $dbh_pdo_statement->execute();

        $_obj_data_display = $dbh_pdo_statement->fetchObject('class_common_data', array());
    }
    catch(\PDOException $e)
    {
        die('Database error : '.$e->getMessage());
    }

    if($_obj_data_display)
    {
        $type_of_incident = $_obj_data_display->get_label();
    }	
	

    /* Cause display. */
	$cause_of_incident = NULL;
	
    $sql_string = 'EXEC fire_alarm_cause_display :id';	
	
    try
    {

        $dbh_pdo_statement = $dc_yukon_connection->get_member_connection()->prepare($sql_string);
        
        $dbh_pdo_statement->bindValue(':id', $_obj_data_main->get_cause(), \PDO::PARAM_INT);
        
        $_obj_data_display = $dbh_pdo_statement->execute();

        $_obj_data_display = $dbh_pdo_statement->fetchObject('class_common_data', array());
    }
    catch(\PDOException $e)
    {
        die('Database error : '.$e->getMessage());
    }

    if($_obj_data_display)
    {
        $cause_of_incident = $_obj_data_display->get_label();
    }
	
	/* Party display. */
	$responsible_party = NULL;
	
	$sql_string = 'EXEC fire_alarm_responsible_party_display :id';	
	
    try
    {
        $dbh_pdo_statement = $dc_yukon_connection->get_member_connection()->prepare($sql_string);
        
        $dbh_pdo_statement->bindValue(':id', $_obj_data_main->get_fire(), \PDO::PARAM_INT);
        
        $_obj_data_display = $dbh_pdo_statement->execute();

        $_obj_data_display = $dbh_pdo_statement->fetchObject('class_common_data', array());
    }
    catch(\PDOException $e)
    {
        die('Database error : '.$e->getMessage());
    }

    if($_obj_data_display)
    {
        $responsible_party = $_obj_data_display->get_label();
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
            <h2>General</h2>
            <table class="table">
                <caption></caption>
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
                                                
                        <?php $obj_date_time = new DateTime($_obj_data_main->get_log_create()); ?>
                        
                        <td><?php echo date('Y-m-d H:i:s', $obj_date_time->getTimestamp()); ?></td>
                        
                    </tr>
                    
                    <tr>
                        <th>Created By</th>                                   
                        <td><?php echo $_obj_data_main->get_log_create_by(); //$lookup->get_account_data()->name_proper(); ?></td>
                    </tr>
                    
                    <tr>
                        <th>Last Update</th>    
                        
                        <?php $obj_date_time = new DateTime($_obj_data_main->get_log_update()); ?>
                        <td><?php echo date('Y-m-d H:i:s', $obj_date_time->getTimestamp()); ?></td>
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
            
            <h2>Alarm</h2>
            <table class="table">
                <caption></caption>
                <thead>
                </thead>
                <tfoot>
                </tfoot>
                <tbody> 
                    <tr>
                        <th>Time of Incident</th> 
                        
                        <?php $obj_date_time = new DateTime($_obj_data_main->get_time_reported()); ?>
                        <td><?php echo date('Y-m-d H:i:s', $obj_date_time->getTimestamp()); ?></td>
                    </tr>
                    <tr>
                        <th>Time Silenced</th>   
                        
                        <?php $obj_date_time = new DateTime($_obj_data_main->get_time_silenced()); ?>
                        <td><?php echo date('Y-m-d H:i:s', $obj_date_time->getTimestamp()); ?></td>
                    </tr>  
                    
                    <tr>
                        <th>Time Reset</th>
                        <?php $obj_date_time = new DateTime($_obj_data_main->get_time_reset()); ?>
                        <td><?php echo date('Y-m-d H:i:s', $obj_date_time->getTimestamp()); ?></td>
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
                                    <tr>
                                	    <th>Pull Station</th>
                                        <th>Sprinkler</th>
                                        <th>Smoke Detector</th>
                                        <th>Stove Supression</th>
                                    </tr>
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
            
            <h2>Incident</h2>
            
            <table class="table">
                <caption></caption>
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
            
            <?php echo $obj_navigation_main->get_markup_footer(); ?>
        </div><!--container-->  
        
        <script
			  src="https://code.jquery.com/jquery-3.5.1.min.js"
			  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
			  crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    
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