<?php
	namespace Sydne\Pages;
	
	use WebFX\System;
	
	use WebFX\WebPage;
	use WebFX\WebScript;
	use WebFX\WebStyleSheet;
	
	use WebFX\HorizontalAlignment;
	use WebFX\VerticalAlignment;
	
	use WebFX\Controls\ButtonGroup;
	use WebFX\Controls\ButtonGroupButton;
	use WebFX\Controls\ButtonGroupButtonAlignment;
	
	use WebFX\Controls\Window;
	
	class SydnePage extends WebPage
	{
		public $ToolbarLeftItems;
		public $ToolbarCenterItems;
		public $ToolbarRightItems;
		
		public $ReturnButtonVisible;
		public $ReturnButtonText;
		public $ReturnButtonURL;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->ToolbarLeftItems = array();
			$this->ToolbarCenterItems = array();
			$this->ToolbarRightItems = array();
		}
		
		protected function Initialize()
		{
			parent::Initialize();
			$this->StyleSheets[] = new WebStyleSheet("~/StyleSheets/Main.css");
			$this->StyleSheets[] = new WebStyleSheet("~/StyleSheets/Purple/Main.css");
			
			$this->Scripts[] = new WebScript("http://localhost:27248/Sydne.js");
			$this->Scripts[] = new WebScript("http://localhost:27248/Mifare/Reset");
			
			$this->Scripts[] = new WebScript("~/Scripts/Sydne.js");
			
			$this->Scripts[] = new WebScript("~/Scripts/System.js.php");
			$this->Scripts[] = new WebScript("~/Scripts/Controls/Keypad.js");
			
			$this->Scripts[] = new WebScript("~/Scripts/Objects/Employee.js.php");
			$this->Scripts[] = new WebScript("~/Scripts/Objects/Product.js");
			
			$this->ReturnButtonVisible = true;
			$this->ReturnButtonText = "Main Menu";
			$this->ReturnButtonURL = "~/";
		}
		protected function BeforeContent()
		{
			$CurrentEmployee = \Sydne\Objects\Employee::GetCurrent();
			
			if ($_SESSION["KioskMode.Enabled"] != "1")
			{
		?>
			<table class="StatusBar" style="width: 100%;">
				<tr>
					<td style="text-align: left; width: 33%;">
						<span id="lblCurrentTime">&nbsp;</span>
					</td>
					<td style="text-align: center; width: 33%;">
						<?php echo($this->Title); ?>
					</td>
					<td style="text-align: right; width: 33%;">
						<table style="width: 100%;">
							<tr>
								<td style="text-align: right;"><?php echo($CurrentEmployee->DisplayName); ?></td>
								<td style="width: 50px;">
								<?php
								if ($CurrentEmployee->ClockedIn)
								{
									?><img src="<?php echo(System::ExpandRelativePath("~/Images/Icons/Timesheet/ClockIn.png")); ?>" alt="[CLK]" title="Clocked In" /><?php
								}
								if ($CurrentEmployee->OnBreak)
								{
									?><img src="<?php echo(System::ExpandRelativePath("~/Images/Icons/Timesheet/Break.png")); ?>" alt="[BRK]" title="On Break" /><?php
								}
								?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		<?php
			}
		?>
			<table style="width: 100%; padding-bottom: 24px;">
				<tr>
					<td style="width: 33%;">
					<?php
						$btngToolbarLeft = new ButtonGroup("btngToolbarLeft");
						$btngToolbarLeft->ButtonSize = 64;
						foreach ($this->ToolbarLeftItems as $item)
						{
							$btngToolbarLeft->Items[] = $item;
						}
						$btngToolbarLeft->ButtonAlignment = ButtonGroupButtonAlignment::Left;
						$btngToolbarLeft->Render();
					?>
					</td>
					<td style="width: 33%;">
					<?php
						$btngToolbarCenter = new ButtonGroup("btngToolbarCenter");
						$btngToolbarCenter->ButtonSize = 64;
						foreach ($this->ToolbarCenterItems as $item)
						{
							$btngToolbarCenter->Items[] = $item;
						}
						$btngToolbarCenter->ButtonAlignment = ButtonGroupButtonAlignment::Center;
						$btngToolbarCenter->Render();
					?>
					</td>
					<td style="width: 33%;">
					<?php
						$btngToolbarRight = new ButtonGroup("btngToolbarRight");
						$btngToolbarRight->ButtonSize = 64;
						foreach ($this->ToolbarRightItems as $item)
						{
							$btngToolbarRight->Items[] = $item;
						}
						if ($this->ReturnButtonVisible)
						{
							$btngToolbarRight->Items[] = new ButtonGroupButton("btnReturnToMenu", $this->ReturnButtonText, "", "~/Images/Buttons/Return.png", $this->ReturnButtonURL);
						}
						if ($_SESSION["KioskMode.Enabled"] != "1")
						{
							$btngToolbarRight->Items[] = new ButtonGroupButton("btnLogOut", "Log Out", "", "~/Images/Buttons/LogOut.png", "~/Account/Logout.page");
						}
						$btngToolbarRight->ButtonAlignment = ButtonGroupButtonAlignment::Right;
						$btngToolbarRight->Render();
					?>
					</td>
				</tr>
			</table>
		<?php
		}
		protected function AfterContent()
		{
			$CurrentCompany = \Sydne\Objects\Company::GetCurrent();
			/*
			$wndCashDrawerMonitor = new Window("wndCashDrawerMonitor");
			$wndCashDrawerMonitor->HorizontalAlignment = HorizontalAlignment::Right;
			$wndCashDrawerMonitor->VerticalAlignment = VerticalAlignment::Bottom;
			$wndCashDrawerMonitor->Title = "Cash Drawer Monitor";
			$wndCashDrawerMonitor->BeginContent();
			
			$btngCashDrawer = new ButtonGroup("btngCashDrawer");
			$btngCashDrawer->ButtonSize = 64;
			$btngCashDrawer->Items[] = new ButtonGroupButton("btnCashDrawer", "Open Cash Drawer", "", "~/Images/Buttons/CashDrawer.png", null, "Sydne.CashDrawer.Open();");
			$btngCashDrawer->ButtonAlignment = ButtonGroupButtonAlignment::Right;
			$btngCashDrawer->Render();
			
			$wndCashDrawerMonitor->EndContent();
			*/
		?>
		<script type="text/javascript">
		</script>
		<img class="CompanyLogo" src="<?php echo(System::ExpandRelativePath("~/Images/Companies/" . $CurrentCompany->ID . "/Logo.png")); ?>" />
		<?php
		}
	}
?>