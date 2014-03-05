<?php
	namespace Sydne\Modules\DefaultModule;
	
	// use Sydne\Pages\ConfigurationPage;
	
	use WebFX\System;
	use WebFX\Module;
	use WebFX\ModulePage;
	
	/*
	
	require("Pages/SydnePage.inc.php");
	require("Pages/ErrorPage.inc.php");
	require("Pages/LoginPage.inc.php");
	
	require("Pages/CompanyPage.inc.php");
	require("Pages/DepartmentPage.inc.php");
	require("Pages/EmployeePage.inc.php");
	require("Pages/SalePage.inc.php");
	require("Pages/MaintenancePage.inc.php");
	
	*/
	require_once("Objects/EmployeeBreakClassification.inc.php");
	
	require_once("Pages/TimesheetPage.inc.php");
	
	use Sydne\Pages\TimesheetPage;
	use Sydne\Pages\TimesheetBreakPage;
	
	use Sydne\Objects\Employee;
	use Sydne\Objects\EmployeeBreakClassification;
	
	System::$Modules[] = new Module("Timekeeping", array
	(
		new ModulePage("Timesheet", function($path)
		{
			switch ($path[0])
			{
				case "ClockIn":
				{
					$employee = Employee::GetCurrent();
					$employee->ClockIn();
					System::Redirect("~/Timesheet");
					return true;
				}
				case "ClockOut":
				{
					$employee = Employee::GetCurrent();
					$employee->ClockOut();
					System::Redirect("~/Timesheet");
					return true;
				}
				case "BreakIn":
				{
					$page = new TimesheetBreakPage();
					if ($page->GetVariableValue("BreakFinalized") == "1")
					{
						$employee = Employee::GetCurrent();
						$employee->BreakIn(EmployeeBreakClassification::GetByID($page->GetVariableValue("ClassificationID")), $page->GetVariableValue("Comments"));
						System::Redirect("~/Timesheet");
					}
					else
					{
						$page->Render();
					}
					return true;
				}
				case "BreakOut":
				{
					$employee = Employee::GetCurrent();
					$employee->BreakOut();
					System::Redirect("~/Timesheet");
					return true;
				}
				case "":
				{
					$page = new TimesheetPage();
					$page->Render();
					return true;
				}
			}
			return false;
		})
	));
?>