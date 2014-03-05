<?php
	namespace Sydne\Objects
	{
		class Location
		{
			public $ID;
			public $Title;
			public $BuildingTitle;
			public $AddressStreet;
			public $AddressCity;
			public $AddressState;
			public $AddressPostalCode;
			public $AddressPhoneNumber;
			
			public static function GetByAssoc($values)
			{
				$item = new Location();
				$item->ID = $values["ID"];
				$item->Title = $values["Title"];
				$item->BuildingTitle = $values["BuildingTitle"];
				$item->AddressStreet = $values["AddressStreet"];
				$item->AddressCity = $values["AddressCity"];
				$item->AddressState = $values["AddressState"];
				$item->AddressPostalCode = $values["AddressPostalCode"];
				$item->PhoneNumber = $values["PhoneNumber"];
				return $item;
			}
			public static function GetCurrent()
			{
				$company = Company::GetCurrent();
				if ($company == null) return null;
				return Location::GetByID($company->Location);
			}
			public static function GetByID($id)
			{
				if (!is_numeric($id)) return null;
				global $MySQL;
				$query = "SELECT * FROM " . \System::$Configuration["Database.TablePrefix"] . "Locations WHERE ID = " . $id;
				$result = $MySQL->query($query);
				if ($result === false) return null;
				$count = $result->num_rows;
				if ($count < 1) return null;
				$values = $result->fetch_assoc();
				return Location::GetByAssoc($values);
			}
			public static function Get($max = null)
			{
				global $MySQL;
				$retval = array();
				$query = "SELECT * FROM " . \System::$Configuration["Database.TablePrefix"] . "Locations";
				if (is_numeric($max)) $query .= " LIMIT " . $max;
				$result = $MySQL->query($query);
				if ($result === false) return $retval;
				$count = $result->num_rows;
				for ($i = 0; $i < $count; $i++)
				{
					$values = $result->fetch_assoc();
					$retval[] = Location::GetByAssoc($values);
				}
				return $retval;
			}
		}
	}
?>