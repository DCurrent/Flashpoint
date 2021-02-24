<?php	
		
	require(__DIR__.'/source/main.php');

	/*
	facility
	Damon Vaughn Caskey
	2014-07-16
	
	Output facility options. Used to generate facility drop list contents.
	*/
	
	abstract class FACILITY_COL_ORDER
	{
		const CODE_FIRST	= 0;
		const ADDRESS_FIRST	= 1;
        const CODE_NAME_ADDRESS = 2;
	}

    class request_data
    {
        private $filter_like;
        
        public function __construct() 
		{		
			/* Iterate through each class variable. */
       		foreach($this as $key => $value) 
			{			
				/* 
                * If we can find a matching a post var with key matching
				* key of current object var, set object var to the post value. 
				*/
                if(isset($_POST[$key]))
				{					
					$this->$key = $_POST[$key];           						
				}
			}	
	 	}
        
        public function get_filter_like()
        {
            return $this->filter_like;
        }
    }

    class building_data
    {
        private $building_code = NULL;
        private $building_name = NULL;
        private $address_street = NULL;
        private $address_city = NULL;
        private $address_zip = NULL;
        private $address_zip_sort = NULL;
        
        public function get_building_code()
        {
            return $this->building_code;
        }
        
        public function get_building_name()
        {
            return $this->building_name;
        }
        
        public function get_address_street()
        {
            return $this->address_street;
        }
        
        public function get_address_city()
        {
            return $this->address_city;
        }
        
        public function get_address_zip()
        {
            return $this->address_zip;
        }
        
        public function get_address_zip_sort()
        {
            return $this->address_zip_sort;
        }
    }
		
	$request_data = new request_data();
	
	 
    $sql_string = 'EXEC dc_flashpoint_building_list_simple :filter_like';	
	
    try
    {   
        $dbh_pdo_statement = $dc_yukon_connection->get_member_connection()->prepare($sql_string);
		
	    $dbh_pdo_statement->bindValue(':filter_like', 'uk', \PDO::PARAM_STR);		
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

    while($_row_object = $dbh_pdo_statement->fetchObject('building_data', array()))
    {       
        $_row_obj_list->push($_row_object);
    }
    
    if(is_object($_row_obj_list) === TRUE)
    { 
        for($_row_obj_list->rewind(); $_row_obj_list->valid(); $_row_obj_list->next())
        {            
            $_row_object = $_row_obj_list->current();
            
            ?>
            <option value="<?php echo $_row_object->get_building_code(); ?>"><?php 
            echo $_row_object->get_building_code().' - '.ucwords(strtolower($_row_object->get_building_name().' | '.$_row_object->get_address_street())).'&nbsp;'.$_row_object->get_address_zip(); ?></option>
            <?php 
        }
    }

?>