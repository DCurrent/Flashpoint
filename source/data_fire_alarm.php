<?php

    require('data_common.php');

    class data_fire_alarm extends class_common_data
	{
		private	$details			      = NULL;
		private	$building_code		      = NULL;
		private	$building_name		      = NULL;
		private	$room_code			      = NULL;
		private	$room_id			      = NULL;
		private	$time_reported		      = NULL;
		private	$time_silenced			  = NULL;
		private	$time_reset				  = NULL;
		private	$report_device_pull       = NULL;
		private	$report_device_sprinkler  = NULL;
		private	$report_device_smoke	  = NULL;
		private	$report_device_stove	  = NULL;	
        private $report_device_911        = NULL;
		private	$cause				= NULL;
		private	$occupied			= NULL;
		private	$evacuated			= NULL;
		private	$notified			= NULL;
		private	$fire				= NULL;
		private	$extinguisher		= NULL;
		private	$injuries			= NULL;
		private	$fatalities			= NULL;
		private	$injury_desc		= NULL;
		private	$property_damage	= NULL;
		private	$responsible_party	= NULL;
		private	$public_details		= NULL;
		private	$status				= NULL;
			
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
        
        public function get_report_device_911()
		{
			return $this->report_device_911;
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
        
        public function set_report_device_911($value)
		{
			$this->report_device_911 = $value;
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