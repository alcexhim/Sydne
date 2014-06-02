<?php
	namespace Sydne\Pages;
	
	use WebFX\System;
	
	use WebFX\Controls\ButtonGroup;
	use WebFX\Controls\ButtonGroupButton;
	use WebFX\Controls\ButtonGroupButtonAlignment;
	
	use Sydne\Objects\Employee;
	use Sydne\Objects\Privilege;
	use Sydne\Objects\PrivilegeType;
	
	class ConfigurationPage extends SydnePage
	{
		protected function Initialize()
		{
			parent::Initialize();
			$this->Title = "Configuration";
		}
		
		protected function RenderContent()
		{
			$CurrentEmployee = Employee::GetCurrent();
			
			$btng = new ButtonGroup("btngAdministration");
			$btng->ButtonAlignment = ButtonGroupButtonAlignment::Center;
			if ($CurrentEmployee->HasPrivilege(Privilege::GetByID(PrivilegeType::CompanyModify)))
			{
				$btng->Items[] = new ButtonGroupButton("btnCompany", "Company", "", "~/Images/Buttons/Company.png", "~/Configuration/Company");
			}
			if ($CurrentEmployee->HasPrivilege(Privilege::GetByID(PrivilegeType::DepartmentModify)))
			{
				$btng->Items[] = new ButtonGroupButton("btnDepartments", "Departments", "", "~/Images/Buttons/Departments.png", "~/Departments");
			}
			if ($CurrentEmployee->HasPrivilege(Privilege::GetByID(PrivilegeType::EmployeeModify)))
			{
				$btng->Items[] = new ButtonGroupButton("btnEmployees", "Employees", "", "~/Images/Buttons/Employees.png", "~/Employees");
			}
			if ($CurrentEmployee->HasPrivilege(Privilege::GetByID(PrivilegeType::ProductModify)))
			{
				$btng->Items[] = new ButtonGroupButton("btnProducts", "Products", "", "~/Images/Buttons/Products.png", "~/Configuration/Products");
			}
			if ($CurrentEmployee->HasPrivilege(Privilege::GetByID(PrivilegeType::SaleModify)))
			{
				$btng->Items[] = new ButtonGroupButton("btnSales", "Sales", "", "~/Images/Buttons/Sales.png", "~/Sales");
			}
			if ($CurrentEmployee->HasPrivilege(Privilege::GetByID(PrivilegeType::CustomerModify)))
			{
				$btng->Items[] = new ButtonGroupButton("btnCustomers", "Customers", "", "~/Images/Buttons/Customers.png", "~/Customers");
			}
			if ($CurrentEmployee->HasPrivilege(Privilege::GetByID(PrivilegeType::EventModify)))
			{
				$btng->Items[] = new ButtonGroupButton("btnEvents", "Events", "", "~/Images/Buttons/Events.png", "~/Events");
			}
			$btng->Render();
		}
	}
?>