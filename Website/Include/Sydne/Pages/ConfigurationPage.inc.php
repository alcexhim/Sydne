<?php
	namespace Sydne\Pages
	{
		use WebFramework\Controls\ButtonGroup;
		use WebFramework\Controls\ButtonGroupButton;
		use WebFramework\Controls\ButtonGroupButtonAlignment;
		
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
					$btng->Items[] = new ButtonGroupButton("btnCompany", "Company", "", "~/Images/Buttons/Company.png", "~/Company");
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
					$btng->Items[] = new ButtonGroupButton("btnProducts", "Products", "", "~/Images/Buttons/Products.png", "~/Products");
				}
				if ($CurrentEmployee->HasPrivilege(Privilege::GetByID(PrivilegeType::SaleModify)))
				{
					$btng->Items[] = new ButtonGroupButton("btnSales", "Sales", "", "~/Images/Buttons/Sales.png", "~/Sales");
				}
				$btng->Render();
			}
		}
		
		\System::$VirtualPaths[] = new \VirtualPath("Configuration", function($path)
		{
			$page = new ConfigurationPage();
			$page->Render();
			return true;
		});
	}
?>