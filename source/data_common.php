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

	class class_fire_alarm_data extends class_common_data
	{
		private			
			$details			= NULL,
			$building_code		= NULL,
			$building_name		= NULL,
			$room_code			= NULL,
			$room_id			= NULL,
			$time_reported		= NULL,
			$time_silenced			= NULL,
			$time_reset				= NULL,
			$report_device_pull		= NULL,
			$report_device_sprinkler	= NULL,
			$report_device_smoke	= NULL,
			$report_device_stove	= NULL,			
			$cause				= NULL,
			$occupied			= NULL,
			$evacuated			= NULL,
			$notified			= NULL,
			$fire				= NULL,
			$extinguisher		= NULL,
			$injuries			= NULL,
			$fatalities			= NULL,
			$injury_desc		= NULL,
			$property_damage	= NULL,
			$responsible_party	= NULL,
			$public_details		= NULL,
			$status				= NULL;
			
		public function __construct()
		{
			$this->set_defaults();
		}
		
		public function set_defaults()
		{
			if($this->injuries === NULL) $this->injuries = 0;
			if($this->fatalities === NULL) $this->fatalities = 0;
			if($this->property_damage === NULL) $this->property_damage = 0.0;
		}
			
		// Accessors
		public function get_details()
		{
			return $this->details;
		}
		
		public function get_building_code()
		{
			return $this->building_code;
		}
		
		public function get_building_name()
		{
			return $this->building_name;
		}
		
		public function get_room_code()
		{
			return $this->room_code;
		}
		
		public function get_room_id()
		{
			return $this->room_id;
		}
		
		public function get_time_reported()
		{
			return $this->time_reported;
		}
		
		public function get_time_silenced()
		{
			return $this->time_silenced;
		}
		
		public function get_time_reset()
		{
			return $this->time_reset;
		}
				
		public function get_report_device_pull()
		{
			return $this->report_device_pull;
		}
		
		public function get_report_device_sprinkler()
		{
			return $this->report_device_sprinkler;
		}
		
		public function get_report_device_smoke()
		{
			return $this->report_device_smoke;
		}
		
		public function get_report_device_stove()
		{
			return $this->report_device_stove;
		}
		
		public function get_cause()
		{
			return $this->cause;
		}
		
		public function get_occupied()
		{
			return $this->occupied;
		}
		
		public function get_evacuated()
		{
			return $this->evacuated;
		}
		
		public function get_notified()
		{
			return $this->notified;
		}
		
		public function get_fire()
		{
			return $this->fire;
		}
		
		public function get_extinguisher()
		{
			return $this->extinguisher;
		}
		
		public function get_injuries()
		{
			return $this->injuries;
		}
		
		public function get_fatalities()
		{
			return $this->fatalities;
		}
		
		public function get_injury_desc()
		{
			return $this->injury_desc;
		}
		
		public function get_property_damage()
		{
			return $this->property_damage;
		}
		
		public function get_responsible_party()
		{
			return $this->responsible_party;
		}
		
		public function get_public_details()
		{
			return $this->public_details;
		}
		
		public function get_status()
		{
			return $this->status;
		}
		
		// Mutators		
		public function set_details($value)
		{
			$this->details = $value;
		}		
		
		public function set_building_code($value)
		{
			$this->building_code = $value;	
		}
				
		public function set_room_code($value)
		{
			$this->room_code = $value;
		}
		
		public function set_time_reported($value)
		{
			if($value)
			{
				$this->time_reported = $value;
			}
			else
			{
				$this->time_reported = NULL;
			}
		}				
		
		public function set_time_silenced($value)
		{
			if($value)
			{
				$this->time_silenced = $value;
			}
			else
			{
				$this->time_silenced = NULL;
			}
		}				
		
		public function set_time_reset($value)
		{
			if($value)
			{
				$this->time_reset = $value;
			}
			else
			{
				$this->time_reset = NULL;
			}
		}
		
		
		public function set_report_device_pull($value)
		{
			$this->report_device_pull = $value;
		}
		
		public function set_report_device_sprinkler($value)
		{
			$this->report_device_sprinkler = $value;
		}
		
		public function set_report_device_smoke($value)
		{
			$this->report_device_smoke = $value;
		}
		
		public function set_report_device_stove($value)
		{
			$this->report_device_stove = $value;
		}
		
		public function set_cause($value)
		{
			$this->cause = $value;
		}
		
		public function set_occupied($value)
		{
			$this->occupied = $value;
		}
		
		public function set_evacuated($value)
		{
			$this->evacuated = $value;
		}
		
		public function set_fire($value)
		{
			$this->fire = $value;
		}
		
		public function set_notified($value)
		{
			$this->notified = $value;
		}
		
		public function set_extinguisher($value)
		{
			$this->extinguisher = $value;
		}
		
		public function set_injuries($value)
		{
			$this->injuries = $value;
		}
		
		public function set_fatalities($value)
		{
			$this->fatalities = $value;
		}
		
		public function set_injury_desc($value)
		{
			$this->injury_desc = $value;
		}
		
		public function set_responsible_party($value)
		{
			$this->responsible_party = $value;
		}
		
		public function set_property_damage($value)
		{
			$this->property_damage = $value;
		}
		
		public function set_public_details($value)
		{
			$this->public_details = $value;
		}
		
		public function set_status($value)
		{
			$this->status = $value;
		}
		
	}

?>