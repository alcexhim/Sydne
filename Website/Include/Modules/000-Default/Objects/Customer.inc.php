<?php
	namespace Sydne\Objects;
	
	use WebFX\System;
	
	class Customer
	{
		public $ID;
		public $GivenName;
		public $FamilyName;
		public $BillingLocation;
		public $ShippingLocation;
		
		public static function GetByAssoc($values)
		{
			$item = new Customer();
			$item->ID = $values["ID"];
			$item->GivenName = $values["GivenName"];
			$item->FamilyName = $values["FamilyName"];
			$item->BillingLocation = Location::GetByID($values["BillingLocationID"]);
			$item->ShippingLocation = Location::GetByID($values["ShippingLocationID"]);
		}
		public static function Get($max = null)
		{
			global $MySQL;
			$retval = array();
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Customers";
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
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Customers WHERE ID = " . $id;
			$result = $MySQL->query($query);
			if ($result === false) return null;
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			return Customer::GetByAssoc($values);
		}
		public static function GetByCardNumber($cardNumber)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Customers WHERE IdentityCardNumber = '" . $MySQL->real_escape_string($cardNumber) . "'";
			$result = $MySQL->query($query);
			if ($result === false) return null;
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			return Customer::GetByAssoc($values);
		}
	}
?>