<?php
	// use Sydne\Pages\ConfigurationPage;
	
	use WebFX\System;
	use WebFX\Module;
	use WebFX\ModulePage;
	
	
	/*
	require("Controls/Keypad.inc.php");
	
	require("Pages/SydnePage.inc.php");
	require("Pages/ErrorPage.inc.php");
	require("Pages/LoginPage.inc.php");
	
	require("Pages/ConfigurationPage.inc.php");
	require("Pages/CompanyPage.inc.php");
	require("Pages/DepartmentPage.inc.php");
	require("Pages/EmployeePage.inc.php");
	require("Pages/ProductPage.inc.php");
	require("Pages/SalePage.inc.php");
	require("Pages/TimesheetPage.inc.php");
	require("Pages/MaintenancePage.inc.php");
	
	*/
	require("Objects/Sale.inc.php");
	require("Objects/SaleProduct.inc.php");
	
	require("Pages/SalePage.inc.php");
	require("Pages/RefundPage.inc.php");
	
	use Sydne\Objects\Employee;
	
	use Sydne\Pages\SaleListPage;
	use Sydne\Pages\SaleModifyPage;
	use Sydne\Pages\SaleDetailPage;
	
	use Sydne\Pages\RefundListPage;
	use Sydne\Pages\RefundModifyPage;
	use Sydne\Pages\RefundDetailPage;
	
	System::$Modules[] = new Module("Sales", array
	(
		new ModulePage("Sales", array
		(
			new ModulePage("", function($path)
			{
				$page = new SaleListPage();
				$page->Render();
				return true;
			}),
			new ModulePage("Create.page", function($path)
			{
				$page = new SaleModifyPage();
				$page->Render();
				return true;
			}),
			new ModulePage("Detail.page", function($path)
			{
				$page = new SaleDetailPage();
				$id = $path[1];
				if (!is_numeric($id))
				{
					return true;
				}
				$page->Sale = Sale::GetByID($id);
				$page->Render();
				return true;
			})
		)),
		new ModulePage("Refunds", array
		(
			new ModulePage("Create.page", function($path)
			{
				$page = new RefundModifyPage();
				$page->Render();
				return true;
			})
		))
	));
?>