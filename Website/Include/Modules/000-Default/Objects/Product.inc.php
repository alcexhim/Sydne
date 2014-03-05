<?php
	namespace Sydne\Objects;
	
	use WebFX\System;
	
	class Product
	{
		public $ID;
		public $Title;
		public $Description;
		public $UnitPrice;
		public $QuantityInStock;
		public $Barcode;
		
		public static function GetByAssoc($values)
		{
			$item = new Product();
			$item->ID = $values["ID"];
			$item->Title = $values["Title"];
			$item->Description = $values["Description"];
			$item->UnitPrice = $values["UnitPrice"];
			$item->QuantityInStock = $values["QuantityInStock"];
			$item->Barcode = $values["Barcode"];
			return $item;
		}
		public static function Get($max = null, $includeOutOfStock = true)
		{
			global $MySQL;
			$retval = array();
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Products";
			if (!$includeOutOfStock)
			{
				$query .= " WHERE (QuantityInStock IS NULL OR QuantityInStock > 0)";
			}
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			$result = $MySQL->query($query);
			if ($result === false) return $retval;
			$count = $result->num_rows;
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$item = Product::GetByAssoc($values);
				if ($item == null) continue;
				
				$retval[] = $item;
			}
			return $retval;
		}
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Products WHERE ID = " . $id;
			$result = $MySQL->query($query);
			if ($result === false) return null;
			$count = $result->num_rows;
			if ($count == 0) return null;
			$values = $result->fetch_assoc();
			return Product::GetByAssoc($values);
		}
		public static function GetByBarcode($barcode)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Products WHERE Barcode = '" . $MySQL->real_escape_string($barcode) . "'";
			$result = $MySQL->query($query);
			if ($result === false) return null;
			$count = $result->num_rows;
			if ($count == 0) return null;
			$values = $result->fetch_assoc();
			return Product::GetByAssoc($values);
		}
		
		public function Update()
		{
			global $MySQL;
			if ($this->ID != null)
			{
				$query = "UPDATE " . System::$Configuration["Database.TablePrefix"] . "Products SET " .
					"Title = '" . $MySQL->real_escape_string($this->Title) . "', " .
					"Description = " . ($this->Description == null ? "NULL" : ("'" . $MySQL->real_escape_string($this->Description) . "'")) . ", " .
					"UnitPrice = " . $this->UnitPrice . ", " .
					"QuantityInStock = " . $this->QuantityInStock . ", " .
					"Barcode = " . ($this->Barcode != null ? ("'" . $MySQL->real_escape_string($this->Barcode) . "'") : "NULL") . " " .
					"WHERE ID = " . $this->ID;
					
				$result = $MySQL->query($query);
				if ($result === false) return false;
			}
			else
			{
				$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "Products (Title, Description, UnitPrice, QuantityInStock, Barcode) VALUES (" .
					"'" . $MySQL->real_escape_string($this->Title) . "', " .
					($this->Description == null ? "NULL" : ("'" . $MySQL->real_escape_string($this->Description) . "'")) . ", " .
					$this->UnitPrice . ", " .
					$this->QuantityInStock . ", " .
					($this->Barcode != null ? ("'" . $MySQL->real_escape_string($this->Barcode) . "'") : "NULL") .
				")";
					
				$result = $MySQL->query($query);
				if ($result === false) return false;
				
				$this->ID = $MySQL->insert_id;
				
				$CurrentCompany = Company::GetCurrent();
				if ($CurrentCompany != null)
				{
					$CurrentCompany->AddProduct($this);
				}
			}
			return true;
		}
	}
?>