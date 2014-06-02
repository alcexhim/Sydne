<?php
	namespace Sydne\Objects;
	
	use WebFX\System;
	
	class Event
	{
		public $ID;
		public $Title;
		public $TimestampOpen;
		public $TimestampBegin;
		public $TimestampEnd;
		
		public static function GetByAssoc($values)
		{
			$item = new Event();
			$item->ID = $values["ID"];
			$item->Title = $values["Title"];
			$item->TimestampOpen = $values["TimestampOpen"];
			$item->TimestampBegin = Location::GetByID($values["TimestampBegin"]);
			$item->TimestampEnd = Location::GetByID($values["TimestampEnd"]);
		}
		public static function Get($max = null)
		{
			global $MySQL;
			$retval = array();
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Events";
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			$result = $MySQL->query($query);
			if ($result === false) return $retval;
			$count = $result->num_rows;
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = Customer::GetByAssoc($values);
			}
			return $retval;
		}
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Events WHERE ID = " . $id;
			$result = $MySQL->query($query);
			if ($result === false) return null;
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			return Customer::GetByAssoc($values);
		}
	}
?>