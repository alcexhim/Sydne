<?php
	namespace Sydne\Pages;
	
	use Enum;

	use WebFX\System;
	
	use WebFX\HorizontalAlignment;
	use WebFX\VerticalAlignment;
	
	use WebFX\Controls\ButtonGroup;
	use WebFX\Controls\ButtonGroupButton;
	use WebFX\Controls\ButtonGroupButtonAlignment;
	
	use WebFX\Controls\ListView;
	use WebFX\Controls\ListViewColumn;
	use WebFX\Controls\ListViewItem;
	use WebFX\Controls\ListViewItemColumn;
	
	use WebFX\Controls\Window;
	
	use Sydne\Objects\Company;
	use Sydne\Objects\Employee;
	
	Enum::Create("Sydne\\Pages\\EmployeeDetailPageMode", "Create", "Modify", "Detail");
	
	class EmployeeListPage extends SydnePage
	{
		protected function Initialize()
		{
			parent::Initialize();
			$this->ToolbarLeftItems[] = new ButtonGroupButton("btnCreateEmployee", "Add Employee", null, null, "~/Employees/Create");
			$this->ToolbarLeftItems[] = new ButtonGroupButton("btnSearch", "Touch/Swipe", null, "~/Images/Buttons/BarcodeSearch.png", "~/Employees/Search", "wndSearch.Show(); return false;");
		}
		protected function RenderContent()
		{
			$company = Company::GetCurrent();
			$employees = $company->GetEmployees();
			
			$wndSearch = new Window("wndSearch");
			$wndSearch->Title = "Scan Barcode";
			$wndSearch->Visible = false;
			$wndSearch->BeginContent();
		?>
		<form method="POST" action="<?php echo(System::ExpandRelativePath("~/Employees/Search/Barcode")); ?>">
			<table style="width: 400px;">
				<tr>
					<td>Barcode:</td>
					<td><input type="text" name="BarcodeValue" id="txtBarcode" style="width: 100%;" /></td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: right;">
						<a class="Button" href="#" onclick="wndSearch.Hide(); return false;">Close</a>
					</td>
				</tr>
			</table>
		</form>
		<?php
			$wndSearch->EndContent();
		?>
		<script type="text/javascript">
			wndSearch.Opened.Add(function()
			{
				wndSearch.SetHorizontalAlignment(HorizontalAlignment.Center);
				wndSearch.SetTop(200);
				
				var txtBarcode = document.getElementById("txtBarcode");
				txtBarcode.focus();
			});
		</script>
		<?php
			
			$lv = new ListView("lvEmployees");
			$lv->Width = "100%";
			$lv->Columns[] = new ListViewColumn("chDisplayName", "Name");
			$lv->Columns[] = new ListViewColumn("chTotalProductsSold", "Products Sold");
			$lv->Columns[] = new ListViewColumn("chTotalProfit", "Profit Made");
			
			foreach ($employees as $employee)
			{
				$lvi = new ListViewItem();
				$lvi->Columns[] = new ListViewItemColumn("chDisplayName", $employee->DisplayName);
				$lvi->Columns[] = new ListViewItemColumn("chTotalProductsSold", $employee->CountProductsSold());
				$lvi->Columns[] = new ListViewItemColumn("chTotalProfit", "$ " . number_format($employee->CalculateTotalProfit(), 2));
				$lvi->NavigateURL = "~/Employees/Modify/" . $employee->ID;
				$lv->Items[] = $lvi;
			}
			
			$lv->Render();
		}
	}
	class EmployeeDetailPage extends SydnePage
	{
		public $Employee;
		public $Mode;
		
		public function __construct()
		{
			parent::__construct();
			$this->Mode = EmployeeDetailPageMode::Detail;
		}
		protected function Initialize()
		{
			parent::Initialize();
			
			if ($this->Mode == EmployeeDetailPageMode::Modify)
			{
				$this->ToolbarLeftItems[] = new ButtonGroupButton("btnDelete", "Delete", null, "~/Images/Buttons/Delete.png", "~/Employees/Delete/" . $this->Employee->ID);
			}
			
			$this->ToolbarLeftItems[] = new ButtonGroupButton("btnSaveChanges", "Save Changes", null, "~/Images/Buttons/Accept.png", null, "document.getElementById('frmMain').submit();");
			$this->ToolbarLeftItems[] = new ButtonGroupButton("btnDiscardChanges", "Cancel", null, "~/Images/Buttons/Cancel.png", "~/Employees");
		}
		protected function RenderContent()
		{
			$wndEmployeeDetails = new Window("wndEmployeeDetails", "Employee Details");
			$wndEmployeeDetails->HorizontalAlignment = HorizontalAlignment::Center;
			$wndEmployeeDetails->VerticalAlignment = VerticalAlignment::Middle;
			$wndEmployeeDetails->BeginContent();
		?>
		<form id="frmMain" method="POST">
			<table style="width: 100%;">
				<tr>
					<td><label for="txtEmployeeUserName"><u>U</u>ser name:</label></td>
					<td><input type="text" name="UserName" id="txtEmployeeUserName" accesskey="U" value="<?php if ($this->Employee != null) echo ($this->Employee->UserName); ?>" /></td>
				</tr>
				<tr>
					<td><label for="txtDisplayName"><u>D</u>isplay name:</label></td>
					<td><input type="number" name="DisplayName" id="txtDisplayName" accesskey="D" value="<?php if ($this->Employee != null) echo ($this->Employee->DisplayName); ?>" /></td>
				</tr>
			</table>
		</form>
		<?php
			$wndEmployeeDetails->EndContent();
			
			$btng = new ButtonGroup("btngEmployeeDetails");
			$btng->ButtonAlignment = ButtonGroupButtonAlignment::Center;
			$btng->Items[] = new ButtonGroupButton("btnTimesheet", "Timesheet", null, "~/Images/Buttons/Timesheet.png", "~/Employees/Modify/" . $this->Employee->ID . "/Timesheet");
			$btng->Render();
		}
	}
	
	System::$VirtualPaths[] = new \VirtualPath("Employees", function($path)
	{
		if ($path[0] == "")
		{
			$page = new EmployeeListPage();
			$page->Render();
			return;
		}
		else if ($path[0] == "Create")
		{
			if ($_SERVER["REQUEST_METHOD"] == "POST")
			{
				$product = new Employee();
				$product->Title = $_POST["Title"];
				$product->UnitPrice = $_POST["UnitPrice"];
				$product->QuantityInStock = $_POST["QuantityInStock"];
				$product->Barcode = $_POST["Barcode"];
				if ($product->Update())
				{
					System::Redirect("~/Employees");
				}
				else
				{
					global $MySQL;
					$page = new ErrorPage();
					$page->Message = "The data was not saved properly: " . $MySQL->error . " (" . $MySQL->errno . ")";
					$page->Render();
				}
			}
			else
			{
				$page = new EmployeeDetailPage();
				$page->Mode = EmployeeDetailPageMode::Create;
				$page->Employee = new Employee();
				$page->Render();
			}
			return;
		}
		else if ($path[0] == "Detail")
		{
			if (is_numeric($path[1]))
			{
				$product = Employee::GetByID($path[1]);
				if ($product != null)
				{
					$page = new EmployeeDetailPage();
					$page->Mode = EmployeeDetailPageMode::Detail;
					$page->Employee = $product;
					$page->Render();
				}
				else
				{
					$page = new ErrorPage();
					$page->Message = "The product you specified does not exist";
					$page->Render();
				}
			}
			else
			{
				$page = new ErrorPage();
				$page->Message = "Please select a product to edit from the list";
				$page->Render();
			}
			return;
		}
		else if ($path[0] == "Modify")
		{
			if (is_numeric($path[1]))
			{
				$product = Employee::GetByID($path[1]);
				if ($product != null)
				{
					if ($_SERVER["REQUEST_METHOD"] == "POST")
					{
						$product->Title = $_POST["Title"];
						$product->UnitPrice = $_POST["UnitPrice"];
						$product->QuantityInStock = $_POST["QuantityInStock"];
						$product->Barcode = $_POST["Barcode"];
						if ($product->Update())
						{
							System::Redirect("~/Employees");
						}
						else
						{
							$page = new ErrorPage();
							$page->Message = "The data was not saved properly";
							$page->Render();
						}
					}
					else
					{
						$page = new EmployeeDetailPage();
						$page->Mode = EmployeeDetailPageMode::Modify;
						$page->Employee = $product;
						$page->Render();
					}
				}
				else
				{
					$page = new ErrorPage();
					$page->Message = "The product you specified does not exist";
					$page->Render();
				}
			}
			else
			{
				$page = new ErrorPage();
				$page->Message = "Please select a product to edit from the list";
				$page->Render();
			}
			return;
		}
		else if ($path[0] == "Search")
		{
			if ($path[1] == "Barcode" && $path[2] == "")
			{
				if ($_POST["BarcodeValue"] != null)
				{
					$BarcodeValue = $_POST["BarcodeValue"];
					$CC4 = new \CueCatDecoder4();
					$BarcodeInfo = $CC4->TryParse($BarcodeValue);
					if ($BarcodeInfo !== false)
					{
						$BarcodeValue = $BarcodeInfo->Value;
					}
					System::Redirect("~/Employees/Search/Barcode/" . $_POST["BarcodeValue"]);
				}
				else
				{
				}
			}
			else if ($path[1] == "Barcode" && $path[2] != "")
			{
				$product = Employee::GetByBarcode($path[2]);
				if ($product == null)
				{
					$page = new ErrorPage();
					$page->Message = "The product with barcode '" . $path[2] . "' does not exist.";
					$page->Render();
				}
				else
				{
					System::Redirect("~/Employees/Detail/" . $product->ID);
				}
			}
			else
			{
			}
			return;
		}
		
		$page = new ErrorPage();
		$page->Message = "Invalid action: " . $path[0];
		$page->Render();
	});
?>