<?php
	namespace Sydne\Objects;
	
	use WebFX\System;
	
	class EmployeeBreakClassification
	{
		public $ID;
		public $Title;
		
		public static function GetByAssoc($values)
		{
			$item = new EmployeeBreakClassification();
			$item->ID = $values["ID"];
			$item->Title = $values["Title"];
			return $item;
		}
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "EmployeeBreakClassifications WHERE ID = " . $id;
			$result = $MySQL->query($query);
			if ($result === false) return null;
			
			$count = $result->num_rows;
			if ($count < 1) return null;
			
			$values = $result->fetch_assoc();
			return EmployeeBreakClassification::GetByAssoc($values);
		}
		public static function Get($max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "EmployeeBreakClassifications";
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			$result = $MySQL->query($query);
			$retval = array();
			if ($result === false) return $retval;
			
			$count = $result->num_rows;
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = EmployeeBreakClassification::GetByAssoc($values);
			}
			return $retval;
		}
	}
?>