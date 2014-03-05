<?php
	global $RootPath;
	$RootPath = dirname(__FILE__) . "/../";
	
	require("WebFX/WebFX.inc.php");
	
	use Sydne\Objects\Employee;
	
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
				
				$item = Employee::GetByID($id);
				if ($item == null)
				{
					echo("{ \"Success\": false, \"ErrorMessage\": \"Employee with ID " . $id . " does not exist\" }");
					return;
				}
				
				echo("{ \"Success\": true, \"Items\": [ ");
				OutputItem($item);
				echo(" ] }");
			}
			else
			{
				$items = Employee::Get();
				echo("{ \"Success\": true, \"Items\": [ ");
				$count = count($items);
				for ($i = 0; $i < $count; $i++)
				{
					$item = $items[$i];
					OutputItem($item);
					if ($i < $count - 1) echo(", ");
				}
				echo(" ] }");
			}
			return;
		}
	}
	
	echo("{ \"Success\": false, \"ErrorMessage\": \"Unknown action \"" . $_POST["Action"] . "\" }");
	
	function OutputItem($item)
	{
		echo("{ ");
		echo("\"ID\": " . $item->ID . ", ");
		echo("\"DisplayName\": \"" . $item->DisplayName . "\", ");
		echo("\"UserName\": \"" . $item->UserName . "\"");
		echo(" }");
	}
	
?>