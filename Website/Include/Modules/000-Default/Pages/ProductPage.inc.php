<?php
	namespace Sydne\Pages;
	
	use Enum;
	
	use WebFX\System;
	
	use WebFX\HorizontalAlignment;
	use WebFX\VerticalAlignment;
	
	use WebFX\Controls\ButtonGroupButton;
	
	use WebFX\Controls\ListView;
	use WebFX\Controls\ListViewColumn;
	use WebFX\Controls\ListViewItem;
	use WebFX\Controls\ListViewItemColumn;
	
	use WebFX\Controls\Window;
	
	use Sydne\Objects\Company;
	use Sydne\Objects\Product;
	
	Enum::Create("Sydne\\Pages\\ProductDetailPageMode", "Create", "Modify", "Detail");
	
	class ProductListPage extends SydnePage
	{
		protected function Initialize()
		{
			parent::Initialize();
			$this->ToolbarLeftItems[] = new ButtonGroupButton("btnCreateProduct", "Add Product", null, null, "~/Products/Create");
			$this->ToolbarLeftItems[] = new ButtonGroupButton("btnSearch", "Scan Barcode", null, "~/Images/Buttons/BarcodeSearch.png", "~/Products/Search", "wndSearch.Show(); return false;");
			
			$this->ReturnButtonText = "Configuration";
			$this->ReturnButtonURL = "~/Configuration";
		}
		protected function RenderContent()
		{
			$company = Company::GetCurrent();
			$products = $company->GetProducts();
			
			$wndSearch = new Window("wndSearch");
			$wndSearch->Title = "Scan Barcode";
			$wndSearch->Visible = false;
			$wndSearch->BeginContent();
		?>
		<form method="POST" action="<?php echo(System::ExpandRelativePath("~/Products/Search/Barcode")); ?>">
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
			
			$lv = new ListView("lvProducts");
			$lv->Width = "100%";
			$lv->Columns[] = new ListViewColumn("chTitle", "Title");
			$lv->Columns[] = new ListViewColumn("chUnitPrice", "Unit Price");
			$lv->Columns[] = new ListViewColumn("chQuantityInStock", "Quantity in Stock");
			
			foreach ($products as $product)
			{
				$lvi = new ListViewItem();
				$lvi->Columns[] = new ListViewItemColumn("chTitle", $product->Title);
				$lvi->Columns[] = new ListViewItemColumn("chUnitPrice", $product->UnitPrice);
				$lvi->Columns[] = new ListViewItemColumn("chQuantityInStock", $product->QuantityInStock);
				$lvi->NavigateURL = "~/Products/Modify/" . $product->ID;
				$lv->Items[] = $lvi;
			}
			
			$lv->Render();
		}
	}
	class ProductDetailPage extends SydnePage
	{
		public $Product;
		public $Mode;
		
		public function __construct()
		{
			parent::__construct();
			$this->Mode = ProductDetailPageMode::Detail;
		}
		protected function Initialize()
		{
			parent::Initialize();
			
			if ($this->Mode == ProductDetailPageMode::Modify)
			{
				$this->ToolbarLeftItems[] = new ButtonGroupButton("btnDelete", "Delete", null, "~/Images/Buttons/Delete.png", "~/Products/Delete/" . $this->Product->ID);
			}
			
			$this->ToolbarLeftItems[] = new ButtonGroupButton("btnSaveChanges", "Save Changes", null, "~/Images/Buttons/Accept.png", null, "document.getElementById('frmMain').submit();");
			$this->ToolbarLeftItems[] = new ButtonGroupButton("btnDiscardChanges", "Cancel", null, "~/Images/Buttons/Cancel.png", "~/Products");
		}
		protected function RenderContent()
		{
			$wndProductDetails = new Window("wndProductDetails", "Product Details");
			$wndProductDetails->HorizontalAlignment = HorizontalAlignment::Center;
			$wndProductDetails->VerticalAlignment = VerticalAlignment::Middle;
			$wndProductDetails->BeginContent();
		?>
		<form id="frmMain" method="POST">
			<table style="width: 100%;">
				<tr>
					<td><label for="txtProductTitle">Product <u>t</u>itle:</label></td>
					<td><input type="text" name="Title" id="txtProductTitle" accesskey="t" value="<?php if ($this->Product != null) echo ($this->Product->Title); ?>" /></td>
				</tr>
				<tr>
					<td><label for="txtUnitPrice">Unit <u>p</u>rice:</label></td>
					<td><input type="number" name="UnitPrice" id="txtUnitPrice" accesskey="p" value="<?php if ($this->Product != null) echo ($this->Product->UnitPrice); ?>" /></td>
				</tr>
				<tr>
					<td><label for="txtQuantityInStock"><u>Q</u>uantity in Stock:</label></td>
					<td><input type="number" name="QuantityInStock" id="txtQuantityInStock" accesskey="Q" value="<?php if ($this->Product != null) echo ($this->Product->QuantityInStock); ?>" /></td>
				</tr>
				<tr>
					<td><label for="txtBarcode"><u>B</u>arcode:</label></td>
					<td><input type="text" name="Barcode" id="txtBarcode" accesskey="B" value="<?php if ($this->Product != null) echo ($this->Product->Barcode); ?>" /></td>
				</tr>
			</table>
		</form>
		<?php
			$wndProductDetails->EndContent();
		}
	}
?>