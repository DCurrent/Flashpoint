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
        
        public function get_area_floor()
        {
            return $this->area_floor;
        }
        
        
    }
		
	$request_data = new request_data();
	
	 
    $sql_string = 'EXEC dc_flashpoint_area_list_simple :filter_building_code';	
	
    try
    {   
        $dbh_pdo_statement = $dc_yukon_connection->get_member_connection()->prepare($sql_string);
		
	    $dbh_pdo_statement->bindValue(':filter_building_code', $request_data->get_filter_building_code(), \PDO::PARAM_STR);		
        $dbh_pdo_statement->execute();
    }
    catch(\PDOException $e)
    {
        die('Database error : '.$e->getMessage());
    }
    
    /* 
    * Get every row as an object and 
    * push it into a double linked
    * list.
    */
    
    $_row_object = NULL;
    $_row_obj_list = new \SplDoublyLinkedList();	// Linked list object.

    $_row_object = new area_data();
    $_row_object->set_barcode(ROOM_SELECT::OUTSIDE);
    $_row_object->set_area_id('NA');
    $_row_object->set_useage_desc('Outside');
    $_row_obj_list->push($_row_object);

    while($_row_object = $dbh_pdo_statement->fetchObject('area_data', array()))
    {       
        $_row_obj_list->push($_row_object);
    }
    
    if(is_object($_row_obj_list) === TRUE)
    { 
        for($_row_obj_list->rewind(); $_row_obj_list->valid(); $_row_obj_list->next())
        {            
            $_row_object = $_row_obj_list->current();
            
            /* 
            * We may already have a selection. If so 
            * and the value matches value in this loop 
            * iteration, let's generate the markup to 
            * pre-select option in the broswer.
            */
            $selected_markup = NULL;
            
            if($_row_object->get_building_code() == $request_data->get_selected()) 
            {
                $selected_markup = 'selected';
            }
            
            ?>
            <option value="<?php echo $_row_object->get_barcode(); ?>"><?php 
            echo $_row_object->get_area_id().' - '.ucwords(strtolower($_row_object->get_useage_desc()));?></option>
            <?php 
        }
    }

?>