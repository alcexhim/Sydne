<?php
	namespace Sydne\Objects;
	
	class Employee
	{
		public $ID;
		public $UserName;
		public $DisplayName;
		public $ClockedIn;
		public $OnBreak;
		
		public static function GetByAssoc($values)
		{
			$item = new Employee();
			$item->ID = $values["ID"];
			$item->UserName = $values["UserName"];
			$item->DisplayName = $values["DisplayName"];
			$item->ClockedIn = false;
			
			global $MySQL;
			$query = "SELECT TimeclockStampID FROM " . \System::$Configuration["Database.TablePrefix"] . "Employees WHERE ID = " . $item->ID;
			$result = $MySQL->query($query);
			if ($result !== false)
			{
				$values = $result->fetch_assoc();
				$item->ClockedIn = ($values["TimeclockStampID"] != null);
			}
			
			$query = "SELECT BreakStampID FROM " . \System::$Configuration["Database.TablePrefix"] . "Employees WHERE ID = " . $item->ID;
			$result = $MySQL->query($query);
			if ($result !== false)
			{
				$values = $result->fetch_assoc();
				$item->OnBreak = ($values["BreakStampID"] != null);
			}
			
			return $item;
		}
		public static function Get($max = null)
		{
			global $MySQL;
			$retval = array();
			$query = "SELECT * FROM " . \System::$Configuration["Database.TablePrefix"] . "Employees";
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
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			global $MySQL;
			$query = "SELECT * FROM " . \System::$Configuration["Database.TablePrefix"] . "Employees WHERE ID = " . $id;
			$result = $MySQL->query($query);
			if ($result === false) return null;
			$count = $result->num_rows;
			if ($count == 0) return null;
			$values = $result->fetch_assoc();
			return Employee::GetByAssoc($values);
		}
		public static function GetByCardNumber($cardnumber)
		{
			global $MySQL;
			$query = "SELECT * FROM " . \System::$Configuration["Database.TablePrefix"] . "Employees WHERE IdentityCardNumber = '" . $MySQL->real_escape_string($cardnumber) . "'";
			$result = $MySQL->query($query);
			if ($result === false) return null;
			$count = $result->num_rows;
			if ($count == 0) return null;
			$values = $result->fetch_assoc();
			return Employee::GetByAssoc($values);
		}
		public static function GetByCredentials($username, $password)
		{
			global $MySQL;
			$query = "SELECT * FROM " . \System::$Configuration["Database.TablePrefix"] . "Employees WHERE UserName = '" . $MySQL->real_escape_string($username) . "' AND Password = '" . hash("sha512", $password) . "'";
			$result = $MySQL->query($query);
			if ($result === false) return null;
			$count = $result->num_rows;
			if ($count == 0) return null;
			$values = $result->fetch_assoc();
			return Employee::GetByAssoc($values);
		}
		public static function GetCurrent()
		{
			if ($_SESSION["CurrentCardNumber"] != null)
			{
				return Employee::GetByCardNumber($_SESSION["CurrentCardNumber"]);
			}
			else if ($_SESSION["CurrentUserName"] != null && $_SESSION["CurrentPassword"] != null)
			{
				return Employee::GetByCredentials($_SESSION["CurrentUserName"], $_SESSION["CurrentPassword"]);
			}
			return null;
		}
		
		public function GetProductsSold()
		{
			global $MySQL;
			$retval = array();
			$query = "SELECT " . \System::$Configuration["Database.TablePrefix"] . "SaleProducts.* FROM " . \System::$Configuration["Database.TablePrefix"] . "SaleProducts, Sales WHERE " . \System::$Configuration["Database.TablePrefix"] . "SaleProducts.SaleID = " . \System::$Configuration["Database.TablePrefix"] . "Sales.ID AND " . \System::$Configuration["Database.TablePrefix"] . "Sales.EmployeeID = " . $this->ID;
			$result = $MySQL->query($query);
			if ($result === false) return 0;
			
			$count = $result->num_rows;
			for($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = SaleProduct::GetByAssoc($values);
			}
			return $retval;
		}
		public function CountProductsSold()
		{
			global $MySQL;
			$products = $this->GetProductsSold();
			$i = 0;
			foreach ($products as $product)
			{
				$i += $product->Quantity;
			}
			return $i;
		}
		
		public function LogIn()
		{
			global $MySQL;
			$query = "INSERT INTO " . \System::$Configuration["Database.TablePrefix"] . "EmployeeLogins (EmployeeID, TimestampBegin, TimestampEnd, IPAddress) VALUES (" . $this->ID . ", NOW(), NULL, '" . $_SERVER["REMOTE_ADDR"] . "')";
			$result = $MySQL->query($query);
			if ($result === false) return false;
			
			$stampID = $MySQL->insert_id;
			
			$query = "UPDATE " . \System::$Configuration["Database.TablePrefix"] . "Employees SET LoginStampID = " . $stampID . " WHERE ID = " . $this->ID;
			$result = $MySQL->query($query);
			if ($result === false) return false;
			return true;
		}
		public function LogOut()
		{
			global $MySQL;
			$query = "SELECT LoginStampID FROM " . \System::$Configuration["Database.TablePrefix"] . "Employees WHERE ID = " . $this->ID;
			$result = $MySQL->query($query);
			if ($result === false)
			{
				return false;
			}
			
			$values = $result->fetch_assoc();
			$stampID = $values["LoginStampID"];
			if (!is_numeric($stampID))
			{
				return false;
			}
			$query = "UPDATE " . \System::$Configuration["Database.TablePrefix"] . "EmployeeLogins SET TimestampEnd = NOW() WHERE ID = " . $stampID;
			$result = $MySQL->query($query);
			if ($result === false)
			{
				return false;
			}
			$query = "UPDATE " . \System::$Configuration["Database.TablePrefix"] . "Employees SET LoginStampID = NULL WHERE ID = " . $this->ID;
			$result = $MySQL->query($query);
			if ($result === false)
			{
				return false;
			}
			return true;
		}
		
		public function ClockIn()
		{
			global $MySQL;
			$query = "INSERT INTO " . \System::$Configuration["Database.TablePrefix"] . "EmployeeTimeclockEntries (EmployeeID, TimestampBegin, TimestampEnd) VALUES (" . $this->ID . ", NOW(), NULL)";
			$result = $MySQL->query($query);
			if ($result === false) return false;
			
			$stampID = $MySQL->insert_id;
			
			$query = "UPDATE " . \System::$Configuration["Database.TablePrefix"] . "Employees SET TimeclockStampID = " . $stampID . " WHERE ID = " . $this->ID;
			$result = $MySQL->query($query);
			if ($result === false) return false;
			return true;
		}
		public function ClockOut()
		{
			global $MySQL;
			$query = "SELECT TimeclockStampID FROM " . \System::$Configuration["Database.TablePrefix"] . "Employees WHERE ID = " . $this->ID;
			$result = $MySQL->query($query);
			if ($result === false)
			{
				return false;
			}
			
			$values = $result->fetch_assoc();
			$stampID = $values["TimeclockStampID"];
			if (!is_numeric($stampID))
			{
				return false;
			}
			$query = "UPDATE " . \System::$Configuration["Database.TablePrefix"] . "EmployeeTimeclockEntries SET TimestampEnd = NOW() WHERE ID = " . $stampID;
			$result = $MySQL->query($query);
			if ($result === false)
			{
				return false;
			}
			$query = "UPDATE " . \System::$Configuration["Database.TablePrefix"] . "Employees SET TimeclockStampID = NULL WHERE ID = " . $this->ID;
			$result = $MySQL->query($query);
			if ($result === false)
			{
				return false;
			}
			return true;
		}
		
		public function BreakIn($classification, $comments = null)
		{
			if (!is_numeric($classification->ID)) return false;
			
			global $MySQL;
			$query = "INSERT INTO " . \System::$Configuration["Database.TablePrefix"] . "EmployeeBreakEntries (EmployeeID, TimestampBegin, ClassificationID, Comments) VALUES (" . $this->ID . ", NOW(), " . $classification->ID . ", " . ($comments == null ? "NULL" : ("'" . $comments . "'")) . ")";
			$result = $MySQL->query($query);
			if ($result === false) return false;
			
			$stampID = $MySQL->insert_id;
			
			$query = "UPDATE " . \System::$Configuration["Database.TablePrefix"] . "Employees SET BreakStampID = " . $stampID . " WHERE ID = " . $this->ID;
			$result = $MySQL->query($query);
			if ($result === false) return false;
			return true;
		}
		public function BreakOut()
		{
			global $MySQL;
			$query = "SELECT BreakStampID FROM " . \System::$Configuration["Database.TablePrefix"] . "Employees WHERE ID = " . $this->ID;
			$result = $MySQL->query($query);
			if ($result === false)
			{
				return false;
			}
			
			$values = $result->fetch_assoc();
			$stampID = $values["BreakStampID"];
			if (!is_numeric($stampID))
			{
				return false;
			}
			$query = "UPDATE " . \System::$Configuration["Database.TablePrefix"] . "EmployeeBreakEntries SET TimestampEnd = NOW() WHERE ID = " . $stampID;
			$result = $MySQL->query($query);
			if ($result === false)
			{
				return false;
			}
			$query = "UPDATE " . \System::$Configuration["Database.TablePrefix"] . "Employees SET BreakStampID = NULL WHERE ID = " . $this->ID;
			$result = $MySQL->query($query);
			if ($result === false)
			{
				return false;
			}
			return true;
		}
		
		public function CalculateTotalProfit()
		{
			global $MySQL;
			$query = "SELECT " . \System::$Configuration["Database.TablePrefix"] . "SaleProducts.Quantity, SUM((" . \System::$Configuration["Database.TablePrefix"] . "Products.UnitPrice * " . \System::$Configuration["Database.TablePrefix"] . "SaleProducts.Quantity) - IFNULL(" . \System::$Configuration["Database.TablePrefix"] . "Products.ManufacturingCost, 0)) AS TotalProfit FROM " .
				\System::$Configuration["Database.TablePrefix"] . "Products, " . \System::$Configuration["Database.TablePrefix"] . "Sales, " . \System::$Configuration["Database.TablePrefix"] . "SaleProducts WHERE " .
				\System::$Configuration["Database.TablePrefix"] . "Sales.EmployeeID = " . $this->ID . " AND " . \System::$Configuration["Database.TablePrefix"] . "Sales.ID = " . \System::$Configuration["Database.TablePrefix"] . "SaleProducts.SaleID AND " . \System::$Configuration["Database.TablePrefix"] . "Products.ID = " . \System::$Configuration["Database.TablePrefix"] . "SaleProducts.ProductID";
			
			$result = $MySQL->query($query);
			if ($result === false) return 0.00;
			
			$values = $result->fetch_assoc();
			return $values["TotalProfit"];
		}
		
		public function HasPrivilege($privilege)
		{
			global $MySQL;
			if (!is_numeric($privilege->ID)) return false;
			$query = "SELECT COUNT(*) FROM " . \System::$Configuration["Database.TablePrefix"] . "EmployeePrivileges WHERE EmployeeID = " . $this->ID . " AND PrivilegeID = " . $privilege->ID;
			$result = $MySQL->query($query);
			if ($result === false) return false;
			$retval = $result->fetch_array();
			return (is_numeric($retval[0]) && $retval[0] > 0);
		}
	}
?>