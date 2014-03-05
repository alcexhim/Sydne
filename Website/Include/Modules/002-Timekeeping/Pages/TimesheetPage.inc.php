<?php
	namespace Sydne\Pages
	{
		use WebFX\System;
		
		use WebFX\Controls\ButtonGroup;
		use WebFX\Controls\ButtonGroupButton;
		use WebFX\Controls\ButtonGroupButtonAlignment;
		
		use WebFX\WebPageVariable;
		
		use Sydne\Objects\Employee;
		use Sydne\Objects\EmployeeBreakClassification;
		use Sydne\Objects\Privilege;
		use Sydne\Objects\PrivilegeType;
		
		class TimesheetPage extends SydnePage
		{
			protected function Initialize()
			{
				parent::Initialize();
				$this->Title = "Timekeeping";
			}
			protected function RenderContent()
			{
				$CurrentEmployee = Employee::GetCurrent();
				
				$btng = new ButtonGroup("btngTimesheet");
				$btng->ButtonAlignment = ButtonGroupButtonAlignment::Center;
				
				if ($CurrentEmployee->ClockedIn)
				{
					if ($CurrentEmployee->OnBreak)
					{
						$btng->Items[] = new ButtonGroupButton("btnTimesheetBreak", "Return from Break", "", "~/Images/Buttons/Break.png", "~/Timesheet/BreakOut");
					}
					else
					{
						$btng->Items[] = new ButtonGroupButton("btnTimesheetClockOut", "Clock Out", "", "~/Images/Buttons/ClockIn.png", "~/Timesheet/ClockOut");
						$btng->Items[] = new ButtonGroupButton("btnTimesheetBreak", "Go on Break", "", "~/Images/Buttons/Break.png", "~/Timesheet/BreakIn");
					}
				}
				else
				{
					$btng->Items[] = new ButtonGroupButton("btnTimesheetClockIn", "Clock In", "", "~/Images/Buttons/ClockIn.png", "~/Timesheet/ClockIn");
				}
				$btng->Items[] = new ButtonGroupButton("btnTimesheetView", "View/Print Timesheet", "", "~/Images/Buttons/TimesheetView.png", "~/Timesheet/View");
				
				$btng->Render();
			}
		}
		class TimesheetBreakPage extends SydnePage
		{
			protected function Initialize()
			{
				parent::Initialize();
				$this->Title = "Go on Break";
			}
			protected function BeforeConstruct()
			{
				$this->Variables[] = new WebPageVariable("ClassificationID");
				$this->Variables[] = new WebPageVariable("Comments");
				$this->Variables[] = new WebPageVariable("BreakFinalized");
			}
			protected function RenderContent()
			{
				$CurrentEmployee = Employee::GetCurrent();
				
				if (!$this->IsVariableSet("ClassificationID"))
				{
					$btng = new ButtonGroup("btngClassification");
					$btng->ButtonAlignment = ButtonGroupButtonAlignment::Center;
					$classifications = EmployeeBreakClassification::Get();
					$count = count($classifications);
					for ($i = 0; $i < $count; $i++)
					{
						$classification = $classifications[$i];
						$btng->Items[] = new ButtonGroupButton("btngClassification" . $classification->ID, $classification->Title, null, "~/Images/BreakClassifications/" . $classification->ID . ".png", null, "WebPage.SetVariableValue('ClassificationID', " . $classification->ID . ");");
					}
					$btng->Render();
				}
				else
				{
				?>
				<table style="margin-right: auto; margin-left: auto;">
					<tr>
						<td style="vertical-align: top;"><label for="txtComments">Comments:</label></td>
						<td><textarea cols="50" rows="5" id="txtComments" name="Comments"></textarea></td>
					</tr>
					<tr>
						<td colspan="2" style="text-align: center;">
							<?php
								$btng = new ButtonGroup();
								$btng->ButtonAlignment = ButtonGroupButtonAlignment::Center;
								$btng->Items[] = new ButtonGroupButton("btnAccept", "Save Changes", null, "~/Images/Buttons/Accept.png", null, "WebPage.SetVariableValue('Comments', document.getElementById('txtComments').value, false); WebPage.SetVariableValue('BreakFinalized', '1'); return false;");
								$btng->Render();
							?>
						</td>
					</tr>
				</table>
			<?php
				}
			}
		}
	}
?>