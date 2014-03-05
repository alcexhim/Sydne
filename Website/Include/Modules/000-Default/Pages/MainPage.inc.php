<?php
	namespace Sydne\Pages;
	
	use WebFX\System;
	use WebFX\ModulePage;
	
	use WebFX\Controls\ButtonGroup;
	use WebFX\Controls\ButtonGroupButton;
	use WebFX\Controls\ButtonGroupButtonAlignment;
	
	use Sydne\Objects\Employee;
	use Sydne\Objects\Privilege;
	use Sydne\Objects\PrivilegeType;
	
	class MainPage extends SydnePage
	{
		protected function Initialize()
		{
			parent::Initialize();
			$this->Title = "Main Menu";
			$this->ReturnButtonVisible = false;
		}
		protected function RenderContent()
		{
			$CurrentEmployee = Employee::GetCurrent();
		?>
		<script type="text/javascript">
			Sydne.ExternalDisplay.LogoText("Register Closed");
		</script>
		<?php
			
			if ($CurrentEmployee->ClockedIn)
			{
				$btng = new ButtonGroup("btngSale");
				$btng->ButtonAlignment = ButtonGroupButtonAlignment::Center;
				
				if ($CurrentEmployee->HasPrivilege(Privilege::GetByID(PrivilegeType::SaleCreate)))
				{
					$btng->Items[] = new ButtonGroupButton("btnSaleCreate", "Make a Sale", "", "~/Images/Buttons/SaleCreate.png", "~/Sales/Create.page");
				}
				if ($CurrentEmployee->HasPrivilege(Privilege::GetByID(PrivilegeType::RefundCreate)))
				{
					$btng->Items[] = new ButtonGroupButton("btnRefundCreate", "Refund Purchase", "", "~/Images/Buttons/Refund.png", "~/Refunds/Create.page");
				}
				
				// Event Management
				if ($CurrentEmployee->HasPrivilege(Privilege::GetByID(PrivilegeType::EventGuestAccept)))
				{
					$btng->Items[] = new ButtonGroupButton("btnAcceptEventGuest", "Accept Guest", "", "~/Images/Buttons/EventManagement/GuestAccept.png", "~/Events/1/Guests/Accept");
				}
				if ($CurrentEmployee->HasPrivilege(Privilege::GetByID(PrivilegeType::EventGuestDismiss)))
				{
					$btng->Items[] = new ButtonGroupButton("btnDismissEventGuest", "Dismiss Guest", "", "~/Images/Buttons/EventManagement/GuestReject.png", "~/Events/1/Guests/Dismiss");
				}
				
				$btng->Render();
				?><br /><?php
			}
			
			if ($_SESSION["KioskMode.Enabled"] != "1")
			{
				$btng = new ButtonGroup("btngEmployee");
				$btng->ButtonAlignment = ButtonGroupButtonAlignment::Center;
				
				$btng->Items[] = new ButtonGroupButton("btnEmployeeTimesheet", "Timesheet", "", "~/Images/Buttons/Timesheet.png", "~/Timesheet");
				$btng->Items[] = new ButtonGroupButton("btnMaintenance", "Maintenance", "", "~/Images/Buttons/Maintenance.png", "~/Maintenance");
				$btng->Items[] = new ButtonGroupButton("btnConfiguration", "Configuration", "", "~/Images/Buttons/Configuration.png", "~/Configuration");
				$btng->Render();
			}
		}
	}
	
	System::$Modules["Sydne.DefaultModule"]->Pages[] = new ModulePage("", function($path)
	{
		$page = new MainPage();
		$page->Render();
		return true;
	});
?>