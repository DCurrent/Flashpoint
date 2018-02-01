<?php

	abstract class TIME_LIST_TYPE
	{
		const 
			YEAR 	= 1,
			MONTH	= 2,
			DAY		= 3,
			HOUR	= 4,
			MINUTE	= 5;	
	}
	
	class class_time_list
	{
		
		private
			$type 		= TIME_LIST_TYPE::YEAR,
			$current	= NULL;
		
		public function set_current($value)
		{
			$this->current = $value;
		}
		
		public function set_type($value)
		{
			$this->type = $value;
		}
		
		public function build_list()
		{		
			$result		= NULL;
			$unit		= NULL;	
			$selected	= NULL;
			$range		= array();
			
			
			$range = $this->time_range();
				 
			foreach ($range as $unit)
			{
				if($this->current == $unit) 
				{
					$selected = ' selected';
				}
				else
				{
					$selected = NULL;
				}
				
				$result .= '<option value="'.$unit.'"'.$selected.'>'.$unit.'</option>'.PHP_EOL;
			}
			
			return $result;
		}
		
		private function time_range()
		{
			$result = array();
			
			switch($this->type)
			{
				default:
				case TIME_LIST_TYPE::YEAR:
				
					$result[] = 2014;
					$result[] = 2015; 
				
					break;
					
				case TIME_LIST_TYPE::MONTH:
					
					$result = range(1, 12);
					
					break;
					
				case TIME_LIST_TYPE::DAY:
				
					$result = range(1, 31);
					
					break;
					
				case TIME_LIST_TYPE::HOUR:
				
					$result = range(0, 23);
					
					break;
					
				case TIME_LIST_TYPE::MINUTE:
				
					$result = range(0, 59);
					
					break;
			}
			
			return $result;		
		}
	}
?>
