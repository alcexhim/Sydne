<?php
	namespace Sydne\Objects
	{
		class PrivilegeType
		{
			const CompanyModify = 1;
			const EmployeeModify = 2;
			const ProductModify = 3;
			const SaleModify = 4;
			const SaleCreate = 5;
			const RefundCreate = 6;
			const DepartmentModify = 7;
		}
		class Privilege
		{
			public $ID;
			public $Title;
			
			public static function GetByAssoc($values)
			{
				$item = new Privilege();
				$item->ID = $values["ID"];
				$item->Title = $values["Title"];
				return $item;
			}
			public static function GetByID($id)
			{
				if (!is_numeric($id)) return null;
				
				global $MySQL;
				
				$query = "SELECT * FROM " . \System::$Configuration["Database.TablePrefix"] . "Privileges WHERE ID = " . $id;
				$result = $MySQL->query($query);
				if ($result === false) return null;
				
				$count = $result->num_rows;
				if ($count == 0) return null;
				
				$values = $result->fetch_assoc();
				return Privilege::GetByAssoc($values);
			}
			public static function Get($max = null)
			{
				global $MySQL;
				
				$query = "SELECT * FROM " . \System::$Configuration["Database.TablePrefix"] . "Privileges";
				if (is_numeric($max)) $query .= " LIMIT " . $max;
				
				$retval = array();
				$result = $MySQL->query($query);
				if ($result === false) return $retval;
				
				$count = $result->num_rows;
				for ($i = 0; $i < $count; $i++)
				{
					$values = $result->fetch_assoc();
					$retval[] = Privilege::GetByAssoc($values);
				}
				return $retval;
			}
		}
	}
?>