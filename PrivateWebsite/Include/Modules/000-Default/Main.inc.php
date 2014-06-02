<?php
	// use Sydne\Pages\ConfigurationPage;
	
	use WebFX\System;
	use WebFX\Module;
	use WebFX\ModulePage;
	
	/*
	
	require("Pages/SydnePage.inc.php");
	require("Pages/ErrorPage.inc.php");
	require("Pages/LoginPage.inc.php");
	
	require("Pages/DepartmentPage.inc.php");
	require("Pages/EmployeePage.inc.php");
	require("Pages/ProductPage.inc.php");
	require("Pages/SalePage.inc.php");
	require("Pages/TimesheetPage.inc.php");
	
	*/
	
	require_once("Controls/Keypad.inc.php");
	
	require_once("Objects/Company.inc.php");
	require_once("Objects/Customer.inc.php");
	require_once("Objects/Discount.inc.php");
	require_once("Objects/Employee.inc.php");
	require_once("Objects/Event.inc.php");
	require_once("Objects/Location.inc.php");
	require_once("Objects/PaymentType.inc.php");
	require_once("Objects/Privilege.inc.php");
	require_once("Objects/Product.inc.php");
	
	
	require_once("Pages/SydnePage.inc.php");
	
	require_once("Pages/LoginPage.inc.php");
	require_once("Pages/MainPage.inc.php");
	require_once("Pages/MaintenancePage.inc.php");
	require_once("Pages/CompanyPage.inc.php");
	require_once("Pages/ConfigurationPage.inc.php");
	
	require_once("Pages/ProductPage.inc.php");
	
	use Sydne\Pages\MainPage;
	use Sydne\Pages\SydnePage;
	use Sydne\Pages\LoginPage;
	use Sydne\Pages\MaintenancePage;
	use Sydne\Pages\CompanyPage;
	use Sydne\Pages\ConfigurationPage;
	
	use Sydne\Pages\ProductListPage;
	use Sydne\Pages\ProductDetailPage;
	
	use Sydne\Objects\Employee;
	
	System::$Modules[] = new Module("Sydne Default Module", array
	(
		new ModulePage("", function($path)
		{
			$page = new MainPage();
			$page->Render();
			return true;
		}),
		new ModulePage("Account", array
		(
			new ModulePage("", function($path)
			{
				$page = new SydnePage();
				$page->Title = "Account Settings";
				$page->BeginContent();
				$page->EndContent();
			}),
			new ModulePage("Login.page", function($path)
			{
				if (isset($_POST["un"]) && isset($_POST["pw"]))
				{
					$user = Employee::GetByCredentials($_POST["un"], $_POST["pw"]);
					if ($user == null)
					{
						$page = new LoginPage();
						$page->LoginInvalid = true;
						$page->Render();
					}
					else
					{
						$_SESSION["CurrentUserName"] = $_POST["un"];
						$_SESSION["CurrentPassword"] = $_POST["pw"];
						System::Redirect("~/");
					}
				}
				else if (isset($_SESSION["CurrentUserName"]) && isset($_SESSION["CurrentPassword"]))
				{
					System::Redirect("~/");
				}
				else
				{
					$page = new LoginPage();
					$page->Render();
				}
				return true;
			}),
			new ModulePage("Logout.page", function($path)
			{
				/*
				$CurrentEmployee = Sydne\Objects\Employee::GetCurrent();
				if ($CurrentEmployee != null)
				{
					$CurrentEmployee->LogOut();
				}
				*/
				
				unset($_SESSION["CurrentCardNumber"]);
				unset($_SESSION["CurrentUserName"]);
				unset($_SESSION["CurrentPassword"]);
				
				System::Redirect("~/");
				return true;
			})
		)),
		new ModulePage("Configuration", array
		(
			new ModulePage("", function($path)
			{
				$page = new ConfigurationPage();
				$page->Render();
				return true;
			}),
			new ModulePage("Company", function($vpath)
			{
				$page = new CompanyPage();
				$page->Render();
				return true;
			}),
			new ModulePage("Products", function($vpath)
			{
				$page = new ProductListPage();
				$page->Render();
				return true;
			})
		)),
		new ModulePage("Maintenance", function($vpath)
		{
			$page = new MaintenancePage();
			$page->Render();
			return true;
		})
	));
?>