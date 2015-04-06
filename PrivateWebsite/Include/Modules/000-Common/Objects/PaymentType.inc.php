<?php
	namespace Sydne\Objects
	{
		class PaymentType
		{
			public $ID;
			public $Title;
			public $Enabled;
			
			public $UseCashDrawer;
			
			public static function GetByAssoc($values)
			{
				$item = new PaymentType();
				$item->ID = $values["ID"];
				$item->Title = $values["Title"];
				$item->Enabled = ($values["Enabled"] == 1);
				$item->UseCashDrawer = ($values["UseCashDrawer"] == 1);
				return $item;
			}
			public static function Get($max = null)
			{
				global $MySQL;
				$retval = array();
				$query = "SELECT * FROM " . \System::$Configuration["Database.TablePrefix"] . "PaymentTypes";
				if (is_numeric($max)) $query .= " LIMIT " . $max;
				$result = $MySQL->query($query);
				if ($result === false) return $retval;
				$count = $result->num_rows;
				for ($i = 0; $i < $count; $i++)
				{
					$values = $result->fetch_assoc();
					$retval[] = PaymentType::GetByAssoc($values);
				}
				return $retval;
			}
			public static function GetByID($id)
			{
				global $MySQL;
				if (!is_numeric($id)) return null;
				$query = "SELECT * FROM " . \System::$Configuration["Database.TablePrefix"] . "PaymentTypes WHERE ID = " . $id;
				$result = $MySQL->query($query);
				if ($result === false) return null;
				$count = $result->num_rows;
				if ($count == 0) return null;
				$values = $result->fetch_assoc();
				return PaymentType::GetByAssoc($values);
			}
		}
	}
?>