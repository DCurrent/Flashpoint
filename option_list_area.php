<?php	
		
	require(__DIR__.'/source/main.php');

	/*
	facility
	Damon Vaughn Caskey
	2014-07-16
	
	Output facility options. Used to generate facility drop list contents.
	*/
	
    class request_data
    {
        private $filter_building_code;
        private $selected = NULL;
        
        public function __construct() 
		{		
			$this->populate_from_request();	
	 	}
        
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
        
        public function set_building_code($value)
        {
            $this->filter_building_code = $value;
        }
        
        public function get_filter_building_code()
        {
            return $this->filter_building_code;
        }
        
        public function get_selected()
        {
            return $this->selected;
        }
        
        /* From options update script. */
        public function set_value_current($value)
        {
            $this->selected = $value;
        }
    }

    class area_data
    {
        private $row_id = NULL;
        private $area_id = NULL;
        private $barcode = NULL;
        private $useage_id = NULL;
        private $useage_desc = NULL;
        private $building_code = NULL;
        private $area_floor = NULL;
        
        
        public function get_row_id()
        {
            return $this->row_id;
        }
        
        public function get_building_name()
        {
            return $this->building_name;
        }
        
        public function set_building_name($value)
        {
            $this->building_name = $value;
        }
        
        public function get_area_id()
        {
            return $this->area_id;
        }
        
        public function set_area_id($value)
        {
            $this->area_id = $value;
        }
        
        public function set_barcode($value)
        {
            $this->barcode = $value;
        }
        
        public function get_barcode()
        {
            return $this->barcode;
        }
        
        public function get_useage_id()
        {
            return $this->useage_id;
        }
        
        public function set_useage_desc($value)
        {
            $this->useage_desc = $value;
        }
        
        public function get_useage_desc()
        {
            return $this->useage_desc;
        }
        
        public function get_building_code()
        {
            return $this->building_code;
        }
        
        public function set_area_floor($value)
        {
            $this->area_floor = $value;
        }
        
        public function get_area_floor()
        {
            return $this->area_floor;
        }
        
        
    }
		
	$request_data = new request_data();
	
	 
        
    try
    {  
        /* 
        * Floor list. We only need to query the floor
        * list one time, so we'll go ahead and execute
        * it here.
        */
        $sql_string_floor = 'EXEC dc_flashpoint_floor_list_simple :filter_building_code';    
    
        $dbh_pdo_statement_floor_list = $dc_yukon_connection->get_member_connection()->prepare($sql_string_floor);
		
	    $dbh_pdo_statement_floor_list->bindValue(':filter_building_code', $request_data->get_filter_building_code(), \PDO::PARAM_STR);		
        $dbh_pdo_statement_floor_list->execute();
        
        /* 
        * Room list. We're building option markup that 
        * rooms by floor, so we need to run this query
        * once for every floor. We'll prepare the query
        * here and bind the floor parameter to it for
        * exeuction as we go through the floor query 
        * results.
        */
        $sql_string_room = 'EXEC dc_flashpoint_area_list_simple :filter_building_code, :filter_building_floor';
    
        $filter_building_floor = '';
        
        $dbh_pdo_statement_room_list = $dc_yukon_connection->get_member_connection()->prepare($sql_string_room);
		
        /* 
        * Building code is always the same. We can bind 
        * its VALUE now. We only need to bind a PARAMETER
        * for the floor.
        */
        $dbh_pdo_statement_room_list->bindValue(':filter_building_code', $request_data->get_filter_building_code(), \PDO::PARAM_STR);
	    $dbh_pdo_statement_room_list->bindParam(':filter_building_floor', $filter_building_floor, \PDO::PARAM_STR);
        
    }
    catch(\PDOException $e)
    {
        die('Database error : '.$e->getMessage());
    }
    
    /* 
    * Set up the object variables we'll use to hold
    * query results.
    */
    $_row_object_floor = NULL;
    $_row_obj_floor_list = new \SplDoublyLinkedList();
    
    $_row_object_room = NULL;
    $_row_obj_room_list = new \SplDoublyLinkedList();

    /*
    * Manually add objects that we need and don't
    * appear in database.
    */
    $_row_object_floor = NULL;
    $_row_object_floor = new area_data();
    $_row_object_floor->set_area_floor('Other');
    $_row_obj_floor_list->push($_row_object_floor);

    $_row_object_room = new area_data();
    $_row_object_room->set_barcode(-1);
    $_row_object_room->set_area_id('NA');
    $_row_object_room->set_useage_desc('Outside of Building');
    $_row_obj_room_list->push($_row_object_room);


    /* Build list of row objects for floor. */
    while($_row_object_floor = $dbh_pdo_statement_floor_list->fetchObject('area_data', array()))
    {       
        $_row_obj_floor_list->push($_row_object_floor);
    }

    /*
    * Go through list of floor objects we just built. 
    * Create the option group markup for floor, and
    * update the floor parameter bound to room query.
    * Then we execute the room query to get a list
    * of rooms for current floor in loop.
    */
    if(is_object($_row_obj_floor_list) === TRUE)
    { 
        for($_row_obj_floor_list->rewind(); $_row_obj_floor_list->valid(); $_row_obj_floor_list->next())
        {            
            $_row_object_floor = $_row_obj_floor_list->current();
            
            /* Build option group markup. */
            ?>
            <optgroup label="Floor <?php echo $_row_object_floor->get_area_floor(); ?>">
            <?php
            
            /* 
            * Update bound floor parameter and execute
            * the room list query.
            */
            $filter_building_floor = $_row_object_floor->get_area_floor();
            $dbh_pdo_statement_room_list->execute();            
                        
            /* Build list of room objects. */
            while($_row_object_room = $dbh_pdo_statement_room_list->fetchObject('area_data', array()))
            {       
                $_row_obj_room_list->push($_row_object_room);
            }
            
            /* Build markup option for each room in list. */
            if(is_object($_row_obj_room_list) === TRUE)
            { 
                for($_row_obj_room_list->rewind(); $_row_obj_room_list->valid(); $_row_obj_room_list->next())
                {            
                    $_row_object_room = $_row_obj_room_list->current();
                    
                    
                    /* 
                    * We may already have a selection. If so 
                    * and the value matches value in this loop 
                    * iteration, let's generate the markup to 
                    * pre-select option in the broswer.
                    */
                    $selected_markup = NULL;

                    if($_row_object_room->get_barcode() == $request_data->get_selected()) 
                    {
                        $selected_markup = 'selected';
                    }
                    
                    ?>
                    <option value = "<?php echo $_row_object_room->get_barcode(); ?>" <?php echo $selected_markup; ?>><?php 
            echo $_row_object_room->get_area_id().' - '.ucwords(strtolower($_row_object_room->get_useage_desc())); ?></option>
                    <?php
                }
            }
            
            /* Close option group markup. */
            ?>
            </optgroup>
            <?php
        }
    }
?>