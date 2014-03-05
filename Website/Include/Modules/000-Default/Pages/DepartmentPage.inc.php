<?php
	namespace Sydne\Pages;
	
	use WebFX\System;
	
	use Sydne\Objects\Employee;
	use Sydne\Objects\Privilege;
	use Sydne\Objects\PrivilegeType;
	
	class DepartmentListPage extends SydnePage
	{
		protected function Initialize()
		{
			parent::Initialize();
			$this->Title = "Modify Departments";
		}
		
		protected function RenderContent()
		{
			$CurrentEmployee = Employee::GetCurrent();
			
		}
	}
	
	System::$VirtualPaths[] = new \VirtualPath("Departments", function($path)
	{
		$CurrentEmployee = Employee::GetCurrent();
		if ($CurrentEmployee != null)
		{
			if ($CurrentEmployee->HasPrivilege(Privilege::GetByID(PrivilegeType::DepartmentModify)))
			{
				if (count($path) == 0 || $path[0] == "")
				{
					$page = new DepartmentListPage();
					$page->Render();
					return true;
				}
			}
		}
		return false;
	});
?>