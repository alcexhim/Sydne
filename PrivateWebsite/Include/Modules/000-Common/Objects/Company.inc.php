<?php
	namespace Sydne\Objects
	{
		class Company
		{
			public $ID;
			public $Title;
			public $Location;
			
			public function AddProduct($product)
			{
				global $MySQL;
				$retval = array();
				$query = "INSERT INTO " . \System::$Configuration["Database.TablePrefix"] . "CompanyProducts (CompanyID, ProductID) VALUES (" . $this->ID . ", " . $product->ID . ")";
				$result = $MySQL->query($query);
				if ($result === false) return false;
				return true;
			}
			public function GetProducts($max = null)
			{
				global $MySQL;
				$retval = array();
				$query = "SELECT * FROM " . \System::$Configuration["Database.TablePrefix"] . "CompanyProducts WHERE CompanyID = " . $this->ID;
				if (is_numeric($max)) $query .= " LIMIT " . $max;
				$result = $MySQL->query($query);
				if ($result === false) return $retval;
				
				$count = $result->num_rows;
				for ($i = 0; $i < $count; $i++)
				{
					$values = $result->fetch_assoc();
					$retval[] = Product::GetByID($values["ProductID"]);
				}
				return $retval;
			}
			public function GetEmployees($max = null)
			{
				global $MySQL;
				$retval = array();
				$query = "SELECT " . \System::$Configuration["Database.TablePrefix"] . "Employees.*, " . \System::$Configuration["Database.TablePrefix"] . "CompanyEmployees.CompanyID, " . \System::$Configuration["Database.TablePrefix"] . "CompanyEmployees.EmployeeID FROM " . \System::$Configuration["Database.TablePrefix"] . "Employees, " . \System::$Configuration["Database.TablePrefix"] . "CompanyEmployees WHERE (" . \System::$Configuration["Database.TablePrefix"] . "CompanyEmployees.CompanyID = " . $this->ID . ") AND (" . \System::$Configuration["Database.TablePrefix"] . "Employees.ID = " . \System::$Configuration["Database.TablePrefix"] . "CompanyEmployees.EmployeeID)";
				
				if (is_numeric($max)) $query .= " LIMIT " . $max;
				$result = $MySQL->query($query);
				if ($result === false) return $retval;
				
				$count = $result->num_rows;
				for ($i = 0; $i < $count; $i++)
				{
					$values = $result->fetch_assoc();
					$retval[] = Employee::GetByAssoc($values);
				}
				return $retval;
			}
			
			public static function GetByAssoc($values)
			{
				$item = new Company();
				$item->ID = $values["ID"];
				$item->Title = $values["Title"];
				$item->Location = Location::GetByID($values["LocationID"]);
				return $item;
			}
			public static function Get($max = null)
			{
				global $MySQL;
				$retval = array();
				$query = "SELECT * FROM " . \System::$Configuration["Database.TablePrefix"] . "Companies";
				if (is_numeric($max)) $query .= " LIMIT " . $max;
				$result = $MySQL->query($query);
				if ($result === false) return $retval;
				
				$count = $result->num_rows;
				for ($i = 0; $i < $count; $i++)
				{
					$values = $result->fetch_assoc();
					$retval[] = Company::GetByAssoc($values);
				}
				return $retval;
			}
			public static function GetByID($id)
			{
				if (!is_numeric($id)) return null;
				global $MySQL;
				$query = "SELECT * FROM " . \System::$Configuration["Database.TablePrefix"] . "Companies WHERE ID = " . $id;
				$result = $MySQL->query($query);
				if ($result === false) return null;
				
				$count = $result->num_rows;
				if ($count < 1) return null;
				
				$values = $result->fetch_assoc();
				return Company::GetByAssoc($values);
			}
			public static function GetCurrent()
			{
				return Company::GetByID(1);
			}
		}
	}
?>