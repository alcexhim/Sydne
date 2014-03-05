<?php
	namespace Sydne\Controls
	{
		use WebFramework\WebControl;
		
		use WebFramework\Controls\ButtonGroup;
		use WebFramework\Controls\ButtonGroupButton;
		use WebFramework\Controls\ButtonGroupButtonAlignment;
		use WebFramework\Controls\ButtonGroupOrientation;
		
		\Enum::Create("Sydne\\Controls\\KeypadCharacterSet", "Numeric", "Alphanumeric");
		
		class Keypad extends WebControl
		{
			public $AllowDecimal;
			public $ValuePrefix;
			public $ValueSuffix;
			
			public function __construct($id)
			{
				parent::__construct($id);
				$this->AllowDecimal = true;
				$this->CharacterSet = KeypadCharacterSet::Numeric;
			}
			protected function RenderContent()
			{
			?>
			<form id="Keypad_<?php echo($this->ID); ?>_Form">
				<input type="hidden" name="Keypad_<?php echo($this->ID); ?>_Value" id="Keypad_<?php echo($this->ID); ?>_Value" />
			</form>
			<table style="margin-left: auto; margin-right: auto;">
				<tr>
					<td colspan="3">
						<span style="background-color: #FFFFFF; padding: 8px; font-weight: bold; width: 100%; text-align: right; display: block;"><span style="padding-right: 8px; font-size: 18pt;"><?php echo($this->ValuePrefix); ?></span><span id="Keypad_<?php echo($this->ID); ?>_ValueText" style="font-size: 18pt;"></span><span style="padding-left: 8px; font-size: 18pt;"><?php echo($this->ValueSuffix); ?></span></span>
					</td>
				</tr>
				<tr>
					<td>
					<?php
						$btng = new ButtonGroup("btngKeypad_" . $this->ID . "_123");
						$btng->ButtonSize = 64;
						$btng->ButtonAlignment = ButtonGroupButtonAlignment::Center;
						$btng->Items[] = new ButtonGroupButton("btngKeypad_" . $this->ID . "_123_1", "1", null, null, null, $this->ID . ".Press(1);");
						$btng->Items[] = new ButtonGroupButton("btngKeypad_" . $this->ID . "_123_2", "2", null, null, null, $this->ID . ".Press(2);");
						$btng->Items[] = new ButtonGroupButton("btngKeypad_" . $this->ID . "_123_3", "3", null, null, null, $this->ID . ".Press(3);");
						$btng->Render();
						
						$btng = new ButtonGroup("btngKeypad_" . $this->ID . "_456");
						$btng->ButtonSize = 64;
						$btng->ButtonAlignment = ButtonGroupButtonAlignment::Center;
						$btng->Items[] = new ButtonGroupButton("btngKeypad_" . $this->ID . "_456_4", "4", null, null, null, $this->ID . ".Press(4);");
						$btng->Items[] = new ButtonGroupButton("btngKeypad_" . $this->ID . "_456_5", "5", null, null, null, $this->ID . ".Press(5);");
						$btng->Items[] = new ButtonGroupButton("btngKeypad_" . $this->ID . "_456_6", "6", null, null, null, $this->ID . ".Press(6);");
						$btng->Render();
						
						$btng = new ButtonGroup("btngKeypad_" . $this->ID . "_789");
						$btng->ButtonSize = 64;
						$btng->ButtonAlignment = ButtonGroupButtonAlignment::Center;
						$btng->Items[] = new ButtonGroupButton("btngKeypad_" . $this->ID . "_789_7", "7", null, null, null, $this->ID . ".Press(7);");
						$btng->Items[] = new ButtonGroupButton("btngKeypad_" . $this->ID . "_789_8", "8", null, null, null, $this->ID . ".Press(8);");
						$btng->Items[] = new ButtonGroupButton("btngKeypad_" . $this->ID . "_789_9", "9", null, null, null, $this->ID . ".Press(9);");
						$btng->Render();
						
						$btng = new ButtonGroup("btngKeypad_" . $this->ID . "_x0x");
						$btng->ButtonSize = 64;
						$btng->ButtonAlignment = ButtonGroupButtonAlignment::Center;
						$btng->Items[] = new ButtonGroupButton("btngKeypad_" . $this->ID . "_x0x_Backspace", "&lt;- Erase", null, null, null, $this->ID . ".Backspace();");
						$btng->Items[] = new ButtonGroupButton("btngKeypad_" . $this->ID . "_x0x_0", "0", null, null, null, $this->ID . ".Press(0);");
						$btng->Items[] = new ButtonGroupButton("btngKeypad_" . $this->ID . "_x0x_Decimal", ".", null, null, null, $this->ID . ".Decimal();");
						if (!$this->AllowDecimal)
						{
							$btng->Items[2]->Visible = false;
						}
						
						$btng->Render();
					?>
					</td>
					<td>
					<?php
						$btng = new ButtonGroup("btngPaymentTypeCashQuickPresets");
						$btng->ButtonWidth = 128;
						$btng->ButtonHeight = 64;
						$btng->Orientation = ButtonGroupOrientation::Vertical;
						$btng->Items[] = new ButtonGroupButton("btngKeypad_QuickPresets_1", "1", null, null, null, $this->ID . ".QuickPreset(1);");
						$btng->Items[] = new ButtonGroupButton("btngKeypad_QuickPresets_5", "5", null, null, null, $this->ID . ".QuickPreset(5);");
						$btng->Items[] = new ButtonGroupButton("btngKeypad_QuickPresets_10", "10", null, null, null, $this->ID . ".QuickPreset(10);");
						$btng->Items[] = new ButtonGroupButton("btngKeypad_QuickPresets_20", "20", null, null, null, $this->ID . ".QuickPreset(20);");
						$btng->Render();
					?>
					</td>
				</tr>
			</table>
			<script type="text/javascript">
				var <?php echo($this->ID); ?> = new Keypad("<?php echo($this->ID); ?>");
			</script>
			<?php
			}
		}
	}
?>