<?php

	class class_common_data
	{
		protected
			$id					= NULL,
			$label				= NULL,
			$log_create			= NULL,
			$log_create_by		= NULL,
			$log_update			= NULL,
			$log_update_by		= NULL,
			$log_update_ip		= NULL,
			$log_version		= NULL,
			$record_deleted		= NULL;
		
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
		
		// Accessors
		public function get_id()
		{
			return $this->id;
		}
		
		public function get_label()
		{
			return $this->label;
		}
		
		public function get_log_create()
		{
			return $this->log_create;
		}
		
		public function get_log_create_by()
		{
			return $this->log_create_by;
		}
		
		public function get_log_update()
		{
			return $this->log_update;
		}
		
		public function get_log_update_by()
		{
			return $this->log_update_by;
		}
		
		public function get_log_update_ip()
		{
			return $this->log_update_ip;
		}
		
		public function get_record_deleted()
		{
			return $this->record_deleted;
		}
		
		// Mutators
		public function set_id($value)
		{
			$this->id = $value;
		}
		
		public function set_label($value)
		{
			$this->label = $value;
		}
		
		//public function set_log_create($value)
		//{
		//	$this->log_create = $value;
		//}
		
		public function set_log_update($value)
		{
			$this->log_update = $value;
		}
		
		public function set_log_update_by($value)
		{
			$this->log_update_by = $value;
		}
		
		public function set_log_update_ip($value)
		{
			$this->log_update_ip = $value;
		}
		
		public function set_record_deleted($value)
		{
			$this->record_deleted = $value;
		}
	}

	class class_facility_data
	{
		private
			$code		= NULL,
			$label		= NULL,
			$address	= NULL;
		
		public function get_code()
		{
			return $this->code;
		}
		
		public function get_label()
		{
			return $this->label;
		}
		
		public function get_address()
		{
			return $this->address;
		}
	}

	class class_facility_area_data
	{
		private
			$room_id		= NULL,
			$barcode		= NULL,
			$useage_desc	= NULL,
			$facility		= NULL,
			$floor			= NULL;
		
		public function get_room_id()
		{
			return $this->room_id;
		}
		
		public function get_barcode()
		{
			return $this->barcode;
		}
		
		public function get_useage_desc()
		{
			return $this->useage_desc;
		}
		
		public function get_facility()
		{
			return $this->facility;
		}
		
		public function get_floor()
		{
			return $this->floor;
		}
	}

?>