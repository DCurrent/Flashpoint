<?php 	
	require(__DIR__.'/source/main.php');	 

	$dialog = NULL;
	
	/* Page caching. */
	$page_obj = new \dc\Prudhoe\PageCache();

    /* 
    * Set up access control, then get and verify 
    * log in status. Needs fix now.
	*/
    $access_obj_process = new \dc\stoeckl\process();
	$access_obj_process->get_member_config()->set_authenticate_url(APPLICATION_SETTINGS::AUTHENTICATE_URL);
	$access_obj_process->get_member_config()->set_use_local(FALSE);
	$access_obj_process->process_control();
		
    $access_obj = new \dc\stoeckl\status();
	$access_obj->get_member_config()->set_authenticate_url(APPLICATION_SETTINGS::AUTHENTICATE_URL);	
	$access_obj->verify();    
    $access_obj->action();
				
	/* Main navigaiton. */
	$obj_navigation_main = new Navigation();
	$obj_navigation_main->generate_markup_nav_public();
	$obj_navigation_main->generate_markup_footer();	
				
	/* Record navigation. */
	$obj_navigation_rec = new dc\record_navigation\RecordMenu();	
	
	/* Prepare redirect url with variables. */
	$url_query	= new dc\fraser\URLFix();
	$url_query->set_data('action', $obj_navigation_rec->get_action());
	$url_query->set_data('id', $obj_navigation_rec->get_id());
	
	/* 
    * Initialize our data objects. This is just in case there is no table
	* data for any of the navigation queries to find, we are making new
	* records, or copies of records. It also has the side effect of enabling 
	* IDE type hinting.
	*/
    $_main_data = new data_fire_alarm();
		
	/* Ensure the main data ID member is same as navigation object ID. */
	$_main_data->set_id($obj_navigation_rec->get_id());
			
	switch($obj_navigation_rec->get_action())
	{		
	
		default:		
		case dc\record_navigation\RECORD_NAV_COMMANDS::NEW_BLANK:
		
			$_main_data->set_status(1);
			break;
			
		case dc\record_navigation\RECORD_NAV_COMMANDS::NEW_COPY:			
			
			/* Populate the object from post values. */
			$_main_data->populate_from_request();			
			break;
			
		case dc\record_navigation\RECORD_NAV_COMMANDS::LISTING:
			
			/* Direct to listing. */				
			header('Location: alarm_list.php');
			break;
			
		case dc\record_navigation\RECORD_NAV_COMMANDS::DELETE:						
			
			/* Populate the object from post values. */
			$_main_data->populate_from_request();
				
			/* Call and execute delete SP. */
			$query->set_sql('{call fire_alarm_delete(@id = ?)}');			
			
			$query->set_params(array(array($_main_data->get_id(), SQLSRV_PARAM_IN)));
			$query->query();
			
			/* Refrsh page to the previous record. */
			header('Location: '.$_SERVER['PHP_SELF']);			
				
			break;				
					
		case dc\record_navigation\RECORD_NAV_COMMANDS::SAVE:
			
			/* Stop errors in case someone tries a direct command link. */
			if($obj_navigation_rec->get_command() != dc\record_navigation\RECORD_NAV_COMMANDS::SAVE) break;
			
			$file_name = NULL;
			
			/* 
            * Save the record. Saving main record is straight forward. We’ll run the populate method on our 
			* main data object which will gather up post values. Then we can run a query to merge the values into 
			* database table. We’ll then get the id from saved record (since we are using a surrogate key, the ID
			* should remain static unless this is a brand new record). 
			*
			* If necessary we will then save any sub records (see each for details).
			*
			* Finally, we redirect to the current page using the freshly acquired id. That will ensure we have 
			* always an up to date ID for our forms and navigation system.			
		    */
            
			/* Populate the object from post values. */
			$_main_data->populate_from_request();
			
			$_main_data_label = $_main_data->get_label(); 
		
			/* Call update stored procedure. */
			$query->set_sql('{call fire_alarm_update(@id 			= ?,														 
													@label 			= ?,
													@details 		= ?,
													@log_update 	= ?, 
													@log_update_by 	= ?, 
													@log_update_ip 	= ?,
													@building_code 	= ?,
													@room_code 		= ?,																										 
													@time_reported 	= ?,
													@time_silenced	= ?,
													@time_reset		= ?,
													@report_device_pull 		= ?,
													@report_device_sprinkler 	= ?,
													@report_device_smoke 		= ?,
													@report_device_stove		= ?,
                                                    @report_device_911          = ?,
													@cause			= ?,
													@occupied		= ?,
													@evacuated		= ?,
													@notified		= ?,
													@fire			= ?,
													@extinguisher	= ?,
													@injuries		= ?,
													@fatalities		= ?,
													@injury_desc	= ?,
													@property_damage = ?,
													@responsible_party = ?,
													@public_details = ?,
													@status			= ?)}');
						
												
			$params = array(array($_main_data->get_id(), 		SQLSRV_PARAM_IN),
						array($_main_data->get_label(), 		SQLSRV_PARAM_IN),						
						array($_main_data->get_details(), 		SQLSRV_PARAM_IN),
						array(date('Y-m-d H:i:s'), 					SQLSRV_PARAM_IN),
						array($access_obj->get_account(), 		SQLSRV_PARAM_IN),
						array($access_obj->get_ip(), 			SQLSRV_PARAM_IN),
						array($_main_data->get_building_code(), SQLSRV_PARAM_IN),
						array($_main_data->get_room_code(), 	SQLSRV_PARAM_IN),
						array($_main_data->get_time_reported(),	SQLSRV_PARAM_IN),
						array($_main_data->get_time_silenced(),	SQLSRV_PARAM_IN),
						array($_main_data->get_time_reset(),	SQLSRV_PARAM_IN),
						array($_main_data->get_report_device_pull(),		SQLSRV_PARAM_IN),
						array($_main_data->get_report_device_sprinkler(),	SQLSRV_PARAM_IN),
						array($_main_data->get_report_device_smoke(), 		SQLSRV_PARAM_IN),
						array($_main_data->get_report_device_stove(),		SQLSRV_PARAM_IN),
                        array($_main_data->get_report_device_911(),         SQLSRV_PARAM_IN),
						array($_main_data->get_cause(),			SQLSRV_PARAM_IN),
						array($_main_data->get_occupied(),		SQLSRV_PARAM_IN),
						array($_main_data->get_evacuated(),		SQLSRV_PARAM_IN),
						array($_main_data->get_notified(),		SQLSRV_PARAM_IN),
						array($_main_data->get_fire(),			SQLSRV_PARAM_IN),
						array($_main_data->get_extinguisher(),	SQLSRV_PARAM_IN),
						array($_main_data->get_injuries(),		SQLSRV_PARAM_IN),
						array($_main_data->get_fatalities(),	SQLSRV_PARAM_IN),
						array($_main_data->get_injury_desc(),	SQLSRV_PARAM_IN),
						array($_main_data->get_property_damage(),	SQLSRV_PARAM_IN),
						array($_main_data->get_responsible_party(),	SQLSRV_PARAM_IN),
						array($_main_data->get_public_details(),	SQLSRV_PARAM_IN),
						array($_main_data->get_status(),		SQLSRV_PARAM_IN));
			
			//var_dump($params);
			
			/* Let's do some validation before we execute the query. */
			$dialog = NULL;
			$valid	= TRUE;
			
			$date = DateTime::createFromFormat('Y-m-d H:i', $_main_data->get_time_reported());
			$date_errors = DateTime::getLastErrors();
			if ($date_errors['warning_count'] + $date_errors['error_count'] > 0) 
			{
				$valid 	= FALSE;				
				$dialog .= '<p class="alert alert-danger">Time Reported is not a valid date/time. Please enter the date and time as yyyy-mm-dd hh:mm (ex. 2015-01-23 23:45).</p>';
			}
			
			$date = DateTime::createFromFormat('Y-m-d H:i', $_main_data->get_time_silenced());
			$date_errors = DateTime::getLastErrors();
			if ($date_errors['warning_count'] + $date_errors['error_count'] > 0) 
			{
				$valid 	= FALSE;	
				$dialog .= '<p class="alert alert-danger">Time Silenced is not a valid date/time. Please enter the date and time as yyyy-mm-dd hh:mm (ex. 2015-01-23 23:45).</p>';
			}
			
			$date = DateTime::createFromFormat('Y-m-d H:i', $_main_data->get_time_reset());
			$date_errors = DateTime::getLastErrors();
			if ($date_errors['warning_count'] + $date_errors['error_count'] > 0) 
			{
				$valid 	= FALSE;	
				$dialog .= '<p class="alert alert-danger">Time Reset is not a valid date/time. Please enter the date and time as yyyy-mm-dd hh:mm (ex. 2015-01-23 23:45).</p>';
			}
			
			if (!$_main_data->get_room_code() || $_main_data->get_room_code() == '') 
			{
				$valid 	= FALSE;	
				$dialog .= '<p class="alert alert-danger">You must include the location. Please select a facility and area.</p>';
			}
			
			/* Did all data verify? */
			if($valid === TRUE)
			{
				$query->set_params($params);			
				$query->query();
				
				$query->get_line_params()->set_class_name('class_fire_alarm_data');
				$_main_data = $query->get_line_object();
				
				$dialog .= '<p class="alert alert-success">Your incident report was successfully entered. You may enter another report below or leave this page.</p>';
			
				/* 
                * Set up and send an email alert.
				*/
                
                $address  = 'dvcask2@uky.edu, kjcoom0@email.uky.edu, jdel222@uky.edu, jwmonr1@email.uky.edu, richard.peddicord@ky.gov, ggwill2@email.uky.edu, seberr0@email.uky.edu, pjmerr0@email.uky.edu, tross@email.uky.edu, rob.turner@uky.edu, ska248@uky.edu, lee.poore@uky.edu';
													
				$subject = MAILING::SUBJECT;
				$body = 'An incident has been created or updated. <a href="http://ehs.uky.edu/apps/flashpoint/alarm_list_detail.php?id='.$_main_data->get_id().'">Click here</a> to view details.';
						
				$headers   = array();
				$headers[] = "MIME-Version: 1.0";
				$headers[] = "Content-type: text/html; charset=iso-8859-1";
				if(MAILING::FROM)	$headers[] = "From: ".MAILING::FROM;
				if(MAILING::BCC)	$headers[] = "Bcc: ".MAILING::BCC;
				if(MAILING::CC) 	$headers[] = "Cc: ".MAILING::CC;	

				mail($address, MAILING::SUBJECT.' - Incident Alert', $body, implode("\r\n", $headers));
			}
			
			break;			
	}
		
	
	/* 
    * Datalist list generation.
	*/
    
    $_obj_data_list_cause_list = $dc_yukon_connection->get_row_object_list('{call fire_alarm_cause_list}', 'class_common_data');
    $_obj_data_list_party_list = $dc_yukon_connection->get_row_object_list('{call fire_alarm_party_list}', 'class_common_data');
	
	/* Type. */
	$_obj_data_list_type_list = $dc_yukon_connection->get_row_object_list('{call dc_flashpoint_fire_alarm_type_list}', 'class_common_data');
	
	/* Generate navigation buttons. */
	$obj_navigation_rec->generate_button_list();
	
?>
<!DOCtype html>
    <html lang="en">
    <head>
        <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1" />
        <title><?php echo APPLICATION_SETTINGS::NAME; ?>, Alarm Entry</title>        
        
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
        <link rel="stylesheet" href="source/css/style.css" />
        <link rel="stylesheet" href="source/css/print.css" media="print" />
        
        <script src="../../libraries/javascript/options_update.js"></script>
    
        
    	<style>
			ul.checkbox  { 
				
			 	-webkit-column-count: auto;  				
				-moz-column-count: auto;				
			  column-count: auto;			 
			  margin: 0; 
			  padding: 0; 
			  margin-left: 20px; 
			  list-style: none;			  
			} 
			
			ul.checkbox li input { 
			  margin-right: .25em; 
			  cursor:pointer;
			} 
			
			ul.checkbox li { 
			  border: 1px transparent solid; 
			  display:inline-block;
			  width:12em;			  
			} 
		</style>
    </head>
    
    <body>    
        <div id="container" class="container">            
            <?php echo $obj_navigation_main->get_markup_nav(); ?>                                                                                
            <div class="page-header">           
                <h1>Alarm Entry</h1>
                <p>Fill out the form below and save to create a drill or incident report.</p>
            </div>
            
            <?php echo $dialog; ?>
            
            <form class="" role="form" method="post" enctype="multipart/form-data">           
           		<input type="hidden" name="account" id="account" value="<?php echo $_main_data->get_log_update_by(); ?>" />
                
				<?php //echo $obj_navigation_rec->get_markup(); ?>         
          
          		<?php
					$lookup = new \dc\stoeckl\status;
				echo $_main_data->get_log_update_by();
					if($_main_data->get_log_update_by())
					{
						//$lookup->lookup($_main_data->get_log_update_by());
					}
					else
					{
						//$lookup->lookup($access_obj->get_account());
					}
                ?>         		
          
          		<div class="form-group row">
                	<label class="col-sm-2" for="account_dsp">Created by</label>
                	<div class="col-sm-10">
                		<input type="text" class="form-control"  name="account_dsp" id="account_dsp" placeholder="Person creating ticket." 
                        value="<?php echo $lookup->get_name_f().' '.$lookup->get_name_l(); ?>" 
						readonly>
                	</div>
                </div>
                
                <?php 
				
					// Super crude code at work here, but in a big hurry. Will revisit.
					
					$building_code_display 	= NULL;
					$room_id_display		= NULL;
					
					if($_main_data->get_room_id())
					{
						switch($_main_data->get_room_id())
						{
							case ROOM_SELECT::OUTSIDE:
								$room_id_display = 'Outside';	
								break;
							default:
								$room_id_display = trim($_main_data->get_room_id());
						}
					}
					else
					{
						$room_id_display = 'Unknown';	
					}
										
					
					if($_main_data->get_building_code())
					{
						$building_code_display = $room_id_display.', '. $_main_data->get_building_code().' - '.$_main_data->get_building_name(); 
					}
				
				?>
                
                <div class="form-group row">
                	<label class="col-sm-2" for="label">Title of Entry</label>
                	<div class="col-sm-10">
                		<input type="text" class="form-control"  name="label" id="label" placeholder="Title of entry." value="<?php echo $_main_data->get_label(); ?>">
                	</div>
                </div>
                
                <fieldset id="fs_location">
                	<legend>Location</legend>
                    
                    <div class="form-group row">
                        <label class="col-sm-2" for="building_code">Facility</label>
                        <div class="col-sm-10">
                            <select name="building_code" 
                                id="building_code" 
                                data-current="<?php //echo $post->get_facility(); ?>" 
                                data-source-url="../../libraries/inserts/facility.php" 
                                data-extra-options='<option value="">Select Facility</option>'
                                data-col_order="<?php echo 2; //FACILITY_COL_ORDER::CODE_NAME_ADDRESS; ?>"
                                data-grouped="1"
                                class="room_search form-control">
                                    <!--This option is for valid HTML5; it is overwritten on load.--> 
                                    <option value="">Select Facility</option>                                    
                                    <!--Options will be populated on load via jquery.-->                                 
                            </select>
                        </div>
                    </div> 
                                   
                    <div class="form-group row">
                        <label class="col-sm-2" for="room_code">Area</label>
                        <div class="col-sm-10">
                            <select name="room_code" 
                                id="room_code" 
                                data-current="<?php //echo $post->get_area(); ?>" 
                                data-source-url="../../libraries/inserts/room.php" 
                                data-grouped="1" 
                                data-extra-options='<option value="">Select Room/Area/Lab</option><option value="<?php echo ROOM_SELECT::OUTSIDE; ?>">Outside</option>' 
                                class="room_code_search disable form-control" 
                                disabled>                                        
                                    <!--Options will be populated/replaced on load via jquery.-->
                                    <option value="">Select Room/Area/Lab</option>                                  							
                            </select> 
                        </div>                                   
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-2" for="occupied">Building Occupied</label>
                        <div class="col-sm-10">
                            
                            <!--Occupied: <?php echo $_main_data->get_occupied(); ?>-->
                            
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="occupied" id="occupied_0" value="0" <?php if(!$_main_data->get_occupied()) echo ' checked ';?> required>
                                <label class="form-check-label" for="occupied_0">No</label>
                            </div>
                            
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="occupied" id="occupied_1" value="0" <?php if($_main_data->get_occupied()) echo ' checked ';?> required>
                                <label class="form-check-label" for="occupied_1">Yes</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-2" for="evacuated">Building Evacuated</label>
                        <div class="col-sm-10">
                            <!--Evacuated: <?php echo $_main_data->get_evacuated(); ?>-->
                        
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="evacuated" id="evacuated_0" value="0" <?php if(!$_main_data->get_evacuated()) echo ' checked ';?> required>
                                <label class="form-check-label" for="evacuated_0">No</label>
                            </div>
                            
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="evacuated" id="evacuated_1" value="0" <?php if($_main_data->get_evacuated()) echo ' checked ';?> required>
                                <label class="form-check-label" for="evacuated_1">Yes</label>
                            </div>
                        </div>
                    </div>
                    
              	</fieldset>                
                
                <fieldset id="fs_alarm">
                	<legend>Alarm</legend>
                    
                    <div class="form-group row">
                        <label class="col-sm-2" for="time_reported">Time of Incident</label>
                        <div class="col-sm-10">
                            <input type="text"
                            	class	="form-control"  
                                name	="time_reported" 
                                id		="time_reported"
                                placeholder	="yyyy-mm-dd hh:mm" 
                            	value="<?php echo $_main_data->get_time_reported(); ?>"
                                required>
                        </div>                        
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-2" for="time_silenced">Time Silenced</label>
                        <div class="col-sm-10">                            
                            <input type="text"
                            	class	="form-control"  
                                name	="time_silenced" 
                                id		="time_silenced"
                                placeholder	="yyyy-mm-dd hh:mm" 
                            	value="<?php echo $_main_data->get_time_silenced(); ?>"
                                required>
                        </div>                        
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-2" for="time_reset">Time Reset</label>
                        <div class="col-sm-10">                            
                            <input type="text"
                            	class	="form-control"  
                                name	="time_reset" 
                                id		="time_reset"
                                placeholder	="yyyy-mm-dd hh:mm" 
                            	value="<?php echo $_main_data->get_time_reset(); ?>"
                                required>
                        </div>                        
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-2" for="devices_activated">Devices Activated</label>
                        <div class="col-sm-10" id="devices_activated">
                            <div class="form-check form-check">
                                <input class="form-check-input" type="checkbox" name="report_device_pull" id="report_device_pull" value="1" <?php if($_main_data->get_report_device_pull() == TRUE) echo ' checked '; ?>>
                                <label class="form-check-label" for="report_device_pull">Pull Station</label>
                            </div>

                            <div class="form-check form-check">
                                <input class="form-check-input" type="checkbox" name="report_device_sprinkler" id="report_device_sprinkler" value="1" <?php if($_main_data->get_report_device_sprinkler() == TRUE) echo ' checked '; ?>>
                                <label class="form-check-label" for="report_device_sprinkler">Sprinkler Activation</label>
                            </div>

                            <div class="form-check form-check">
                                <input class="form-check-input" type="checkbox" name="report_device_smoke" id="report_device_smoke" value="1" <?php if($_main_data->get_report_device_smoke() == TRUE) echo ' checked '; ?>>
                                <label class="form-check-label" for="report_device_smoke">Smoke/Heat Detector</label>
                            </div>

                            <div class="form-check form-check">
                                <input class="form-check-input" type="checkbox" name="report_device_stove" id="report_device_stove" value="1" <?php if($_main_data->get_report_device_stove() == TRUE) echo ' checked '; ?>>
                                <label class="form-check-label" for="report_device_stove">Alternate Suppression</label>
                            </div>
                        </div>
                    </div>              
                    
                    <div class="form-group row">
                        <label class="col-sm-2" for="notified">Notified</label>
                        <div class="col-sm-10">
                            <!--Notified: <?php echo $_main_data->get_notified(); ?>-->
                        
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="notified" id="notified_0" value="0" <?php if(!$_main_data->get_notified()) echo ' checked ';?> required>
                                <label class="form-check-label" for="notified_0">No</label>
                            </div>
                            
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="notified" id="notified_1" value="0" <?php if($_main_data->get_notified()) echo ' checked ';?> required>
                                <label class="form-check-label" for="notified_1">Yes</label>
                            </div>
                        </div>
                    </div>                                 
                </fieldset>
                        
                <fieldset id="incident">
                	<legend>Incident</legend>
                    
                        <div class="form-group row">
                            <label class="col-sm-2" for="">Type of Incident</label>
                            <div class="col-sm-10">
                                <!--Fire (type of incident): <?php echo $_main_data->get_fire(); ?>-->

                                <?php
                                    if(is_object($_obj_data_list_type_list) === TRUE)
                                    {
                                        for($_obj_data_list_type_list->rewind(); $_obj_data_list_type_list->valid(); $_obj_data_list_type_list->next())
                                        {						
                                            $_obj_data_list_type = $_obj_data_list_type_list->current();

                                            /*
                                            * Populate selected if the ID of current element
                                            * in loop matches the underlying data value or if
                                            * there's no underlying data value at all and current
                                            * element ID matches a predetermined default.                                            
                                            */
                                            $selected = NULL;
                                                                                        
                                            if($_obj_data_list_type->get_id() == $_main_data->get_fire() || !$_main_data->get_fire() && $_obj_data_list_type->get_id() == 6)
                                            {
                                                $selected = ' checked ';
                                            }
                                            ?>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="fire" id="fire_<?php echo $_obj_data_list_type->get_id(); ?>" value="<?php echo $_obj_data_list_type->get_id(); ?>" <?php $selected; ?> required>
                                                    <label class="form-check-label" for="fire_<?php echo $_obj_data_list_type->get_id(); ?>"><?php echo $_obj_data_list_type->get_label(); ?></label>
                                                </div>                                          
                                            <?php										
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                    
                    	<div class="form-group row">
                            <label class="col-sm-2" for="fire">Type of Incident</label>
                            <div class="col-sm-10">
                                <!--Fire (type of incident): <?php echo $_main_data->get_fire(); ?>-->
                                <div>
                                	<?php
										if(is_object($_obj_data_list_type_list) === TRUE)
										{
											for($_obj_data_list_type_list->rewind(); $_obj_data_list_type_list->valid(); $_obj_data_list_type_list->next())
											{						
												$_obj_data_list_type = $_obj_data_list_type_list->current();
												
												$selected = NULL;
												
												if($_obj_data_list_type->get_id() == $_main_data->get_fire())
												{
													$selected = ' checked ';
												}
												?>
                                                	<label class="radio-inline">
                                                        <input 
                                                        	type	="radio" 
                                                            name	="fire" 
                                                            value	="<?php echo $_obj_data_list_type->get_id(); ?>" <?php echo $selected;?> 
                                                            required>&nbsp;<?php echo $_obj_data_list_type->get_label(); ?>
                                                    </label>                                                    
												<?php										
											}
										}
									?>                                               	
                                </div>
                            </div>
                        </div>
                    
                    	<div class="form-group row">
                            <label class="col-sm-2" for="cause">Cause of Incident</label>
                            <div class="col-sm-10"> 
                                <select class		= "form-control"
                                        name		= "cause" 
                                        id			= "cause" required>
                                        <option value="">Select Cause</option>
                                    <?php
                                        if(is_object($_obj_data_list_cause_list) === TRUE)
                                        {
                                            for($_obj_data_list_cause_list->rewind(); $_obj_data_list_cause_list->valid(); $_obj_data_list_cause_list->next())
                                            {						
                                                $_obj_data_list_cause = $_obj_data_list_cause_list->current();
                                                
                                                $selected = NULL;
                                                
                                                if($_obj_data_list_cause->get_id() == $_main_data->get_cause())
                                                {
                                                    $selected = ' selected ';
                                                }
                                                ?>
                                                    <option value="<?php echo $_obj_data_list_cause->get_id(); ?>" <?php echo $selected; ?>><?php echo $_obj_data_list_cause->get_label(); ?></option>
                                                <?php										
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                    	</div>
                        
                        <div class="form-group row">
                            <label class="col-sm-2" for="cause">Responsible Party</label>
                            <div class="col-sm-10"> 
                                <select class		= "form-control"
                                        name		= "responsible_party" 
                                        id			= "responsible_party" required>
                                        <option value="">Select Party</option>
                                    <?php
                                        if(is_object($_obj_data_list_party_list) === TRUE)
                                        {
                                            for($_obj_data_list_party_list->rewind(); $_obj_data_list_party_list->valid(); $_obj_data_list_party_list->next())
                                            {						
                                                $_obj_data_list_party = $_obj_data_list_party_list->current();
                                                
                                                $selected = NULL;
                                                
                                                if($_obj_data_list_party->get_id() == $_main_data->get_responsible_party())
                                                {
                                                    $selected = ' selected ';
                                                }
                                                ?>
                                                    <option value="<?php echo $_obj_data_list_party->get_id(); ?>" <?php echo $selected; ?>><?php echo $_obj_data_list_party->get_label(); ?></option>
                                                <?php										
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-sm-2" for="extinguisher">Fire Extinguisher Used</label>
                            <div class="col-sm-10">
                                <!--Occupied: <?php echo $_main_data->get_extinguisher(); ?>-->
                            
                                <div>
                                    <label class="radio-inline">
                                        <input type="radio" name="extinguisher" value="0" <?php if(!$_main_data->get_extinguisher()) echo ' checked ';?> required>No
                                    </label>
                                    
                                    <label class="radio-inline">
                                        <input type="radio" name="extinguisher" value="1" <?php if($_main_data->get_extinguisher() == 1) echo ' checked ';?> required>Yes
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-sm-2" for="label">Injuries</label>
                            <div class="col-sm-3">
                                <input type="number" min="0" step="1" class="form-control"  name="injuries" id="injuries" value="<?php echo $_main_data->get_injuries(); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-sm-2" for="label">Fatalities</label>
                            <div class="col-sm-3">
                                <!--<?php echo $_main_data->get_fatalities(); ?>-->
                                <input type="number" min="0" step="1" class="form-control"  name="fatalities" id="fatalities" value="<?php echo $_main_data->get_fatalities(); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-sm-2" for="injury_desc">Casualty Description</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" rows="5" name="injury_desc" id="injury_desc"><?php echo $_main_data->get_injury_desc(); ?></textarea>
                            </div>
                        </div> 
                        
                        <div class="form-group row">
                            <label class="col-sm-2" for="label">Property Damage</label>
                            <div class="col-sm-3">
                                <!--<?php echo $_main_data->get_property_damage(); ?>-->
                                <input type="text" class="form-control"  name="property_damage" id="property_damage" placeholder="0.0" value="<?php echo $_main_data->get_property_damage(); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-sm-2" for="details">Details</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" rows="5" name="details" id="details"><?php echo $_main_data->get_details(); ?></textarea>
                            </div>
                        </div>
                </fieldset>
                                        
                <hr />
                <div class="form-group">
                	<div class="col-sm-12">
                		<?php echo $obj_navigation_rec->get_markup_cmd_save_block(); ?>
                	</div>
                </div> 
                             
            </form>
            
            <?php echo $obj_navigation_main->get_markup_footer(); ?>
        </div><!--container-->    
        
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
        
    <script>
  $('.room_search').change(function(event)
    {	
		//options_update(event, null, '#room_code');	
	});
  
  $(document).ready(function(event)
    {
    	$('[data-toggle="tooltip"]').tooltip();
	
		//options_update(event, null, '#building_code');
	
	});        
    
</script>

</body>
</html>

<?php
	/* Collect and output page markup. */
	$page_obj->markup_and_flush();	
	$page_obj->output_markup();
?>