<?php
	namespace Sydne\Pages
	{
		use WebFramework\Controls\ButtonGroup;
		use WebFramework\Controls\ButtonGroupButton;
		use WebFramework\Controls\ButtonGroupButtonAlignment;
		
		use Sydne\Objects\Company;
		use Sydne\Objects\Location;
		
		class CompanyPage extends SydnePage
		{
			protected function Initialize()
			{
				parent::Initialize();
				$this->ToolbarLeftItems[] = new ButtonGroupButton("btnSaveChanges", "Save Changes", null, "~/Images/Buttons/Accept.png", null, "document.getElementById('frmMain').submit();");
				$this->ToolbarLeftItems[] = new ButtonGroupButton("btnDiscardChanges", "Cancel", null, "~/Images/Buttons/Cancel.png", "~/");
				$this->ReturnButtonVisible = false;
			}
			protected function RenderContent()
			{
				$company = Company::GetCurrent();
			?>
				<form method="POST" id="frmMain">
					<table style="margin-left: auto; margin-right: auto;">
						<tr>
							<td>Title:</td>
							<td><input type="text" value="<?php echo($company->Title); ?>" /></td>
						</tr>
						<tr>
							<td>Location:</td>
							<td>
								<select id="cboLocation" name="LocationID">
									<option value="">(unspecified)</option>
								<?php
									$locations = Location::Get();
									foreach ($locations as $location)
									{
										?><option <?php if ($company->Location->ID == $location->ID) echo("selected=\"selected\""); ?> value="<?php echo($location->ID); ?>"><?php echo($location->Title); ?></option><?php
									}
								?>
								</select>
							</td>
						</tr>
					</table>
				</form>
			<?php
			}
		}
	
		\System::$VirtualPaths[] = new \VirtualPath("Company", function($vpath)
		{
			$page = new CompanyPage();
			$page->Render();
		});
	}
?>