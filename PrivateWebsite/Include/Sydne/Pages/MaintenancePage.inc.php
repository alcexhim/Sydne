<?php
	namespace Sydne\Pages
	{
		use WebFramework\Controls\ButtonGroup;
		use WebFramework\Controls\ButtonGroupButton;
		use WebFramework\Controls\ButtonGroupButtonAlignment;
		
		use Sydne\Objects\Employee;
		use Sydne\Objects\Privilege;
		use Sydne\Objects\PrivilegeType;
		
		class MaintenancePage extends SydnePage
		{
			protected function Initialize()
			{
				parent::Initialize();
				$this->Title = "Maintenance";
			}
			protected function RenderContent()
			{
				$CurrentEmployee = Employee::GetCurrent();
				
				$btng = new ButtonGroup("btngMaintenance");
				$btng->ButtonAlignment = ButtonGroupButtonAlignment::Center;
				
				$btng->Items[] = new ButtonGroupButton("btnOpenCashDrawer", "Open Cash Drawer", "", "~/Images/Buttons/CashDrawer.png", null, "Sydne.CashDrawer.Open();");
				$btng->Items[] = new ButtonGroupButton("btnPriceLookup", "Price Lookup", "", "~/Images/Buttons/PriceLookup.png", "~/PriceLookup.php", "return false;");
				$btng->Render();
			}
		}
		
		\System::$VirtualPaths[] = new \VirtualPath("Maintenance", function($path)
		{
			$page = new MaintenancePage();
			$page->Render();
			return true;
		});
	}
?>