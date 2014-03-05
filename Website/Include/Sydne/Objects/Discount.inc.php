<?php
	namespace Sydne\Objects
	{
		class Discount
		{
			public $ID;
			public $Title;
			public $PercentChange;
			public $AmountChange;
			public $BeginDate;
			public $EndDate;
			
			public static function GetByAssoc($values)
			{
				$item = new Discount();
				$item->ID = $values["ID"];
				$item->Title = $values["Title"];
				$item->PercentChange = $values["PercentChange"];
				$item->AmountChange = $values["AmountChange"];
				$item->BeginDate = $values["BeginDate"];
				$item->EndDate = $values["EndDate"];
				return $item;
			}
			public static function Get($max = null, $includeExpired = false)
			{
				global $MySQL;
				$retval = array();
				$query = "SELECT * FROM " . \System::$Configuration["Database.TablePrefix"] . "Discounts";
				if (!$includeExpired)
				{
					$query .= " WHERE BeginDate >= NOW() AND EndDate <= NOW()";
				}
				if (is_numeric($max)) $query .= " LIMIT " . $max;
				$result = $MySQL->query($query);
				if ($result === false) return $retval;
				$count = $result->num_rows;
				for ($i = 0; $i < $count; $i++)
				{
					$values = $result->fetch_assoc();
					$retval[] = Discount::GetByAssoc($values);
				}
				return $retval;
			}
			public static function GetByID($id)
			{
				if (!is_numeric($id)) return null;
				global $MySQL;
				$query = "SELECT * FROM " . \System::$Configuration["Database.TablePrefix"] . "Discounts WHERE ID = " . $id;
				$result = $MySQL->query($query);
				if ($result === false) return null;
				$count = $result->num_rows;
				if ($count == 0) return null;
				$values = $result->fetch_assoc();
				return Discount::GetByAssoc($values);
			}
		}
	}
?>