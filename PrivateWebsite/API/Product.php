<?php
	global $RootPath;
	$RootPath = dirname(__FILE__) . "/../";
	
	require("WebFX/WebFX.inc.php");
	
	use Sydne\Objects\Product;
	
	switch ($_POST["Action"])
	{
		case "Retrieve":
		{
			if ($_POST["ID"] != null)
			{
				$id = $_POST["ID"];
				if (!is_numeric($id))
				{
					echo("{ \"Success\": false, \"ErrorMessage\": \"ID must be an integer\" }");
					return;
				}
				
				$product = Product::GetByID($id);
				if ($product == null)
				{
					echo("{ \"Success\": false, \"ErrorMessage\": \"Product with ID " . $id . " does not exist\" }");
					return;
				}
				
				echo("{ \"Success\": true, \"Items\": [ ");
				OutputProduct($product);
				echo(" ] }");
			}
			else if ($_POST["Barcode"] != null)
			{
				$barcode = $_POST["Barcode"];
				
				$product = Product::GetByBarcode($barcode);
				if ($product == null)
				{
					echo("{ \"Success\": false, \"ErrorMessage\": \"Product with barcode '" . $barcode . "' does not exist\" }");
					return;
				}
				
				echo("{ \"Success\": true, \"Items\": [ ");
				OutputProduct($product);
				echo(" ] }");
			}
			else
			{
				$products = Product::Get();
				echo("{ \"Success\": true, \"Items\": [ ");
				$count = count($products);
				for ($i = 0; $i < $count; $i++)
				{
					$product = $products[$i];
					OutputProduct($product);
					if ($i < $count - 1) echo(", ");
				}
				echo(" ] }");
			}
			return;
		}
	}
	
	echo("{ \"Success\": false, \"ErrorMessage\": \"Unknown action \"" . $_POST["Action"] . "\" }");
	
	function OutputProduct($product)
	{
		echo("{ ");
		echo("\"ID\": " . $product->ID . ", ");
		echo("\"Title\": \"" . $product->Title . "\", ");
		echo("\"UnitPrice\": " . $product->UnitPrice . ", ");
		echo("\"QuantityInStock\": " . $product->QuantityInStock);
		echo(" }");
	}
	
?>