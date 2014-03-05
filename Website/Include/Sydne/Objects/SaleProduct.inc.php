<?php
	namespace Sydne\Objects
	{
		class SaleProduct
		{
			public $Sale;
			public $Product;
			public $Quantity;
			
			public function __construct($sale, $product, $quantity = 1)
			{
				$this->Sale = $sale;
				$this->Product = $product;
				$this->Quantity = $quantity;
			}
			
			public static function GetByAssoc($values)
			{
				$item = new SaleProduct();
				$item->Sale = Sale::GetByID($values["SaleID"]);
				$item->Product = Product::GetByID($values["ProductID"]);
				$item->Quantity = $values["Quantity"];
				return $item;
			}
		}
	}
?>