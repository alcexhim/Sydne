<?php
	namespace Sydne\Objects
	{
		class Sale
		{
			public $ID;
			public $Company;
			public $Employee;
			public $Customer;
			public $TimestampOrdered;
			public $TimestampPaid;
			public $PaymentType;
			public $PaymentAmount;
			
			public $Products;
			public function __construct()
			{
				$this->Products = array();
			}
			
			public static function GetByAssoc($values)
			{
				$item = new Sale();
				$item->ID = $values["ID"];
				$item->Company = Company::GetByID($values["CompanyID"]);
				$item->Employee = Employee::GetByID($values["EmployeeID"]);
				$item->Customer = Customer::GetByID($values["CustomerID"]);
				$item->TimestampOrdered = $values["TimestampOrdered"];
				$item->TimestampPaid = $values["TimestampPaid"];
				$item->PaymentType = PaymentType::GetByID($values["PaymentTypeID"]);
				$item->PaymentAmount = $values["PaymentAmount"];
				return $item;
			}
			public static function Get($max = null)
			{
				global $MySQL;
				$retval = array();
				$query = "SELECT * FROM " . \System::$Configuration["Database.TablePrefix"] . "Sales";
				$query .= " ORDER BY TimestampOrdered DESC";
				
				if (is_numeric($max)) $query .= " LIMIT " . $max;
				$result = $MySQL->query($query);
				if ($result === false) return $retval;
				
				$count = $result->num_rows;
				
				for ($i = 0; $i < $count; $i++)
				{
					$values = $result->fetch_assoc();
					$retval[] = Sale::GetByAssoc($values);
				}
				
				return $retval;
			}
			public static function GetByID($id)
			{
				if (!is_numeric($id)) return null;
				global $MySQL;
				$query = "SELECT * FROM " . \System::$Configuration["Database.TablePrefix"] . "Sales WHERE ID = " . $id;
				$result = $MySQL->query($query);
				if ($result === false) return null;
				$count = $result->num_rows;
				if ($count == 0) return null;
				$values = $result->fetch_assoc();
				return Sale::GetByAssoc($values);
			}
			
			public function GetProducts($max = null)
			{
				global $MySQL;
				$retval = array();
				$query = "SELECT " . \System::$Configuration["Database.TablePrefix"] . "SaleProducts.* FROM " . \System::$Configuration["Database.TablePrefix"] . "SaleProducts WHERE SaleID = " . $this->ID;
				
				if (is_numeric($max)) $query .= " LIMIT " . $max;
				$result = $MySQL->query($query);
				if ($result === false) return $retval;
				$count = $result->num_rows;
				for ($i = 0; $i < $count; $i++)
				{
					$values = $result->fetch_assoc();
					$retval[] = SaleProduct::GetByAssoc($values);
				}
				return $retval;
			}
			
			public function Update()
			{
				global $MySQL;
				
				$company = $this->Company;
				if ($company == null) $company = Company::GetCurrent();
				$employee = $this->Employee;
				if ($employee == null) $employee = Employee::GetCurrent();
				
				if ($this->ID == null)
				{
					$query = "INSERT INTO " . \System::$Configuration["Database.TablePrefix"] . "Sales (CompanyID, EmployeeID, CustomerID, TimestampOrdered, TimestampPaid, PaymentTypeID, PaymentAmount) VALUES (" .
						$company->ID . ", " .
						$employee->ID . ", " .
						($this->Customer == null ? "NULL" : $this->Customer->ID) . ", " .
						($this->TimestampOrdered == null ? "NOW()" : $this->TimestampOrdered) . ", " .
						($this->TimestampPaid == null ? "NOW()" : $this->TimestampPaid) . ", " .
						$this->PaymentType->ID . ", " .
						$this->PaymentAmount .
					")";
					
					$result = $MySQL->query($query);
					if ($result === false) return false;
					
					$this->ID = $MySQL->insert_id;
				}
				else
				{
					// Sales are read-only, you cannot update them through the Web interface.
					return false;
				
					// Clear out all the associated products
					$query = "DELETE FROM " . \System::$Configuration["Database.TablePrefix"] . "SaleProducts WHERE SaleID = " . $this->ID;
					$result = $MySQL->query($query);
					if ($result === false) return false;
				}
				
				// Associate the new products
				$i = 0; $count = count($this->Products);
				if ($count > 0)
				{
					$query = "INSERT INTO " . \System::$Configuration["Database.TablePrefix"] . "SaleProducts (SaleID, ProductID, Quantity) VALUES ";
					for ($i = 0; $i < $count; $i++)
					{
						$product = $this->Products[$i];
						$query .= "(" . $this->ID . ", " . $product->Product->ID . ", " . $product->Quantity . ")";
						if ($i < $count - 1) $query .= ", ";
					}
					$result = $MySQL->query($query);
					
					if ($result === false) return false;
				}
				
				return true;
			}
		}
	}
?>