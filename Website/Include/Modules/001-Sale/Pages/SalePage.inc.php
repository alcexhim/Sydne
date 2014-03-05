<?php
	namespace Sydne\Pages;
	
	use WebFX\System;
	use WebFX\ModulePage;
	
	use WebFX\HorizontalAlignment;
	use WebFX\VerticalAlignment;
	
	use WebFX\Controls\ButtonGroup;
	use WebFX\Controls\ButtonGroupButton;
	use WebFX\Controls\ButtonGroupButtonAlignment;
	use WebFX\Controls\ButtonGroupOrientation;
	
	use WebFX\Controls\ListView;
	use WebFX\Controls\ListViewColumn;
	use WebFX\Controls\ListViewItem;
	use WebFX\Controls\ListViewItemColumn;
	
	use WebFX\Controls\Window;
	
	use WebFX\WebPageVariable;
	
	use Sydne\Objects\Company;
	use Sydne\Objects\PaymentType;
	use Sydne\Objects\Product;
	use Sydne\Objects\Employee;
	use Sydne\Objects\Sale;
	use Sydne\Objects\SaleProduct;
	
	use Sydne\Controls\Keypad;
	use Sydne\Controls\KeypadCharacterSet;
	
	class SaleListPage extends SydnePage
	{
		protected function RenderContent()
		{
			$sales = Sale::Get();
			
			$lvSales = new ListView();
			$lvSales->Columns = array
			(
				new ListViewColumn("chTimestampOrdered", "Date Taken"),
				// new ListViewColumn("chCustomer", "Customer"),
				new ListViewColumn("chEmployee", "Employee"),
				new ListViewColumn("chAmountPaid", "Amount Paid"),
				new ListViewColumn("chPaymentType", "Payment Type")
			);
			
			foreach ($sales as $sale)
			{
				$lvi = new ListViewItem(array
				(
					new ListViewItemColumn("chTimestampOrdered", $sale->TimestampOrdered),
					// new ListViewItemColumn("chCustomer", "Customer"),
					new ListViewItemColumn("chEmployee", $sale->Employee->DisplayName),
					new ListViewItemColumn("chAmountPaid", $sale->PaymentAmount),
					new ListViewItemColumn("chPaymentType", $sale->PaymentType->Title)
				));
				$lvi->NavigateURL = "~/Sales/Detail/" . $sale->ID;
				$lvSales->Items[] = $lvi;
			}
			
			$lvSales->Render();
		}
	}
	class SaleDetailPage extends SydnePage
	{
		public $Sale;
		protected function Initialize()
		{
			parent::Initialize();
			$this->ToolbarRightItems[] = new ButtonGroupButton("btnReturnToSales", "Sales List", null, null, "~/Sales");
		}
		
		protected function RenderContent()
		{
			$CurrentCompany = Company::GetCurrent();
			
		?>
			<div class="Receipt" style="width: 400px; margin-left: auto; margin-right: auto; height: auto; border: inset 1px; padding: 8px;">
				<table style="width: 100%;">
					<tr>
						<td colspan="2" style="text-align: center; display: none;"><img src="<?php echo(System::ExpandRelativePath("~/Images/Companies/" . $CurrentCompany->ID . "/Logo.png")); ?>" /></td>
					<tr>
						<td colspan="2" style="font-size: 16pt; font-weight: bold;">Products</td>
					</tr>
					<?php
					$products = $this->Sale->GetProducts();
					$TotalCost = 0.00;
					foreach ($products as $product)
					{
						echo("<tr><td style=\"width: 48px;\">" . $product->Quantity . "</td><td>" . $product->Product->Title . "</td><td style=\"width: 96px;\"><span style=\"padding-right: 8px;\">$</span>" . number_format($product->Quantity * $product->Product->UnitPrice, 2) . "</td></tr>");
						$TotalCost += ($product->Quantity * $product->Product->UnitPrice);
					}
					$TotalCost = number_format($TotalCost, 2);
					?>
					<tr>
						<td colspan="2" style="font-size: 16pt; font-weight: bold;">Total Cost</td>
						<td><span style="padding-right: 8px;">$</span><?php echo($TotalCost); ?></td>
					</tr>
					<tr>
						<td colspan="2" style="font-size: 16pt; font-weight: bold;">Payment Tendered</td>
						<td><span style="padding-right: 8px;">$</span><?php
							echo(number_format($this->Sale->PaymentAmount, 2));
						?></td>
					</tr>
					<tr>
						<td colspan="2" style="font-size: 16pt; font-weight: bold;">Change</td>
						<td style="font-size: 14pt; font-weight: bold;"><span style="padding-right: 8px;">$</span><?php echo(number_format(floatval($this->Sale->PaymentAmount) - floatval($TotalCost), 2)); ?></td>
					</tr>
				</table>
			</div>
<?php
		}
	}
	class SaleModifyPage extends SydnePage
	{
		protected function Initialize()
		{
			parent::Initialize();
			
			$this->Variables[] = new WebPageVariable("CustomerID");
			$this->Variables[] = new WebPageVariable("ProductIDs");
			$this->Variables[] = new WebPageVariable("ProductQuantities");
			$this->Variables[] = new WebPageVariable("PaymentTypeID");
			$this->Variables[] = new WebPageVariable("PaymentValue");
			$this->Variables[] = new WebPageVariable("IsSaleFinalized", "false");
			$this->Variables[] = new WebPageVariable("CustomerPaymentAmount", "0.00");
			
			$this->Title = "Make a Sale";
			
			$this->ReturnButtonVisible = false;
		}
		protected function AfterVariablesInitialize()
		{
			if (!$this->IsVariableSet("CustomerID"))
			{
				$this->Title = "Select Customer";
			}
			else if ($this->GetVariableValue("IsSaleFinalized") != "true")
			{
				$this->Title = "Select Product";
			}
			else if (!$this->IsVariableSet("PaymentTypeID"))
			{
				$this->Title = "Select Payment Type";
			}
			else if (!$this->IsVariableSet("CustomerPaymentAmount"))
			{
				$this->Title = "Enter Payment Amount";
			}
			
			if (!$this->IsVariableSet("CustomerPaymentAmount"))
			{
				if (!$this->IsVariableSet("CustomerID"))
				{
					$this->ToolbarRightItems[] = new ButtonGroupButton("btngCancelSale", "Main Menu", null, "~/Images/Buttons/Return.png", "~/");
				}
				else
				{
					$this->ToolbarRightItems[] = new ButtonGroupButton("btngCancelSale", "Cancel Sale", null, "~/Images/Buttons/Cancel.png", "~/Sales/Create.page");
				}
			}
			else
			{
				$this->ToolbarRightItems[] = new ButtonGroupButton("btngNewSale", "New Sale", null, "~/Images/Buttons/SaleCreate.png", "~/Sales/Create.page");
				$this->ToolbarRightItems[] = new ButtonGroupButton("btngMainMenu", "Main Menu", null, "~/Images/Buttons/Return.png", "~/");
			}
			
			if (!$this->IsVariableSet("CustomerID"))
			{
			}
			else if ($this->GetVariableValue("IsSaleFinalized") != "true")
			{
				$this->ToolbarLeftItems[] = new ButtonGroupButton("btngProductEntry_SearchByBarcode", "Touch/Swipe", null, "~/Images/Buttons/BarcodeSearch.png", null, "cmdProductEntrySearchBarcode_Click();");
				$this->ToolbarLeftItems[] = new ButtonGroupButton("btngProductEntry_FinalizeSale", "Finalize Sale", null, "~/Images/Buttons/Accept.png", null, "cmdProductEntryFinalizeSale_Click();");
				array_unshift($this->ToolbarRightItems, new ButtonGroupButton("btngProductEntryReturn", "Customer", null, "~/Images/Buttons/Return.png", null, "cmdProductEntryReturn_Click();"));
			}
			else if (!$this->IsVariableSet("PaymentTypeID"))
			{
				array_unshift($this->ToolbarRightItems, new ButtonGroupButton("cmdPaymentTypeReturn", "Product", null, "~/Images/Buttons/Return.png", null, "cmdPaymentTypeReturn_Click();"));
			}
			else if (!$this->IsVariableSet("CustomerPaymentAmount"))
			{
				array_unshift($this->ToolbarRightItems, new ButtonGroupButton("btngPaymentControlReturn", " Payment Type", null, "~/Images/Buttons/Return.png", null, "cmdPaymentControlReturn_Click();"));
			}
		}
		
		protected function RenderContent()
		{
			global $MySQL;
			$CurrentCompany = Company::GetCurrent();
			$CurrentEmployee = Employee::GetCurrent();
			
			if (!$this->IsVariableSet("CustomerID"))
			{
			?>
			<script type="text/javascript">
			Sydne.ExternalDisplay.LogoText("Welcome!");
			
			function cmdCustomerTypeUnspecified_Click()
			{
				WebPage.SetVariableValue("CustomerID", "0");
			}
			function cmdCustomerTypeSelect_Click()
			{
			}
			function cmdCustomerTypeEntry_Click()
			{
				Sydne.ExternalDisplay.LogoText("Hello, Michael!");
			}
			
			</script>
			<?php
				$btng = new ButtonGroup("btngCustomerType");
				$btng->ButtonAlignment = ButtonGroupButtonAlignment::Center;
				$btng->Items[] = new ButtonGroupButton("btngCustomerType_Unspecified", "Unspecified", null, "~/Images/Buttons/CustomerType/Unspecified.png", null, "cmdCustomerTypeUnspecified_Click();");
				$btng->Items[] = new ButtonGroupButton("btngCustomerType_Select", "Pick from List", null, null, null, "cmdCustomerTypeSelect_Click();");
				$btng->Items[] = new ButtonGroupButton("btngCustomerType_Entry", "Touch/Swipe Entry", null, "~/Images/Buttons/CustomerType/Entry.png", null, "cmdCustomerTypeEntry_Click();");
				$btng->Render();
			}
			else if ($this->GetVariableValue("IsSaleFinalized") != "true")
			{
		?>
		<script type="text/javascript">
			function cmdProductEntryFinalizeSale_Click()
			{
				var productIDs = WebPage.GetVariableValue("ProductIDs");
				WebPage.SetVariableValue("ProductIDs", productIDs, false);
				WebPage.SetVariableValue("IsSaleFinalized", "true");
			}
			function cmdProductEntryReturn_Click()
			{
				WebPage.ClearVariableValue("CustomerID");
			}
			
			function AddProduct(product, quantity)
			{
				if (!product) return;
				
				if (!quantity)
				{
					wndAddProduct.Product = product;
					wndAddProduct.Show();
					return;
				}
				
				var productIDs = WebPage.GetVariableValue("ProductIDs");
				var productQuantities = WebPage.GetVariableValue("ProductQuantities");
				
				var aryProductIDs = [];
				var aryProductQuantities = [];
				
				if (productIDs != "")
				{
					aryProductIDs = productIDs.split(",");
					aryProductQuantities = productQuantities.split(",");
				}
				
				var duplicate = false;
				for (var i = 0; i < aryProductIDs.length; i++)
				{
					if (aryProductIDs[i] == product.ID)
					{
						aryProductQuantities[i] = parseInt(aryProductQuantities[i]) + parseInt(quantity);
						duplicate = true;
						break;
					}
				}
				
				if (!duplicate)
				{
					aryProductIDs.push(product.ID);
					aryProductQuantities.push(quantity);
					productIDs = aryProductIDs.join(",");
					
					WebPage.SetVariableValue("ProductIDs", productIDs, false);
				}
				
				productQuantities = aryProductQuantities.join(",");
				WebPage.SetVariableValue("ProductQuantities", productQuantities, false);
				RefreshReceipt();
			}
			function RefreshReceipt()
			{
				var productIDs = WebPage.GetVariableValue("ProductIDs");
				var productQuantities = WebPage.GetVariableValue("ProductQuantities");
				
				var aryProductIDs = productIDs.split(",");
				var aryProductQuantities = productQuantities.split(",");
				
				var total = 0.00;
				var html = "";
				var text = "";
				for (var i = 0; i < aryProductIDs.length; i++)
				{
					var id = aryProductIDs[i];
					var quantity = aryProductQuantities[i];
					
					var product = Product.GetByID(id);
					total += (product.UnitPrice * quantity);
					html += "<a href=\"#\" onclick=\"RemoveProductByIndex(" + i + ");\"><span style=\"padding-right: 8px;\">" + quantity + "</span> " + product.Title + "</a>";
					
					text += quantity + "\t" + product.Title + "\t" + (product.UnitPrice * quantity) + "\n";
				}
				Sydne.ExternalDisplay.SaleDetail(text);
				
				var pnlReceipt = document.getElementById("pnlReceipt");
				pnlReceipt.innerHTML = html;
				
				var taxRate = 0.07;
				var lblReceiptSubTotal = document.getElementById("lblReceiptSubTotal");
				var lblReceiptTax = document.getElementById("lblReceiptTax");
				var lblReceiptTotal = document.getElementById("lblReceiptTotal");
				
				lblReceiptSubTotal.innerHTML = total.toFixed(2);
				lblReceiptTax.innerHTML = (total * taxRate).toFixed(2);
				lblReceiptTotal.innerHTML = (total + (total * taxRate)).toFixed(2);
			}
			
			function txtBarcode_KeyDown(event)
			{
				if (event.keyCode == 13)
				{
					var txtBarcode = document.getElementById("txtBarcode");
					wndSearchByBarcode.Hide();
					
					AddProduct(Product.GetByBarcode(txtBarcode.value), 1);
					if (wndSearchByBarcode.StayOpen) wndSearchByBarcode.Show();
					
					return false;
				}
				return true;
			}
		</script>
		
		<?php
			$wndSearchByBarcode = new Window("wndSearchByBarcode");
			$wndSearchByBarcode->Title = "Scan Barcode";
			$wndSearchByBarcode->Visible = false;
			$wndSearchByBarcode->BeginContent();
		?>
		<table style="width: 400px;">
			<tr>
				<td>Barcode:</td>
				<td><input type="text" name="BarcodeValue" id="txtBarcode" style="width: 100%;" onkeydown="return txtBarcode_KeyDown(event);" /></td>
			</tr>
			<tr>
				<td colspan="2" style="text-align: right;">
				<?php
					$btng = new ButtonGroup("btngSearchByBarcodeButtons");
					$btng->Items[] = new ButtonGroupButton("btngSearchByBarcodeButtonsClose", "Close", null, "~/Images/Buttons/Cancel.png", null, "wndSearchByBarcode.Hide();");
					$btng->ButtonSize = 64;
					$btng->Render();
				?>
				</td>
			</tr>
		</table>
		<?php
			$wndSearchByBarcode->EndContent();
		?>
		<?php
			$wndAddProduct = new Window("wndAddProduct");
			$wndAddProduct->Title = "Add Product";
			$wndAddProduct->Visible = false;
			$wndAddProduct->BeginContent();
		?>
		<table style="width: 600px;">
			<tr>
				<td>Product:</td>
				<td><span id="lblAddProduct_ProductTitle">&nbsp;</span></td>
			</tr>
			<tr>
				<td colspan="2">
				<?php
					$keypad = new Keypad("keypadProductQuantity");
					$keypad->AllowDecimal = false;
					$keypad->Render();
				?>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align: right;">
					<a class="Button" href="#" onclick="wndAddProduct.Accept(); return false;">Add Product</a>
					<a class="Button" href="#" onclick="wndAddProduct.Hide(); return false;">Close</a>
				</td>
			</tr>
		</table>
		<?php
			$wndAddProduct->EndContent();
		?>
		
		<script type="text/javascript">
		wndAddProduct.Accept = function()
		{
			var quantity = keypadProductQuantity.GetValue();
			if (quantity == "") quantity = 1;
			
			AddProduct(wndAddProduct.Product, quantity);
			wndAddProduct.Hide();
			
			if (wndSearchByBarcode.StayOpen) wndSearchByBarcode.Show();
		};
		
		wndAddProduct.Opened.Add(function(e)
		{
			wndAddProduct.SetHorizontalAlignment(HorizontalAlignment.Center);
			wndAddProduct.SetVerticalAlignment(VerticalAlignment.Middle);
			
			var lblAddProduct_ProductTitle = document.getElementById("lblAddProduct_ProductTitle");
			lblAddProduct_ProductTitle.innerHTML = wndAddProduct.Product.Title;
			
			keypadProductQuantity.SetValue("");
		});
		
		function cmdProductEntrySearchBarcode_Click()
		{
			wndSearchByBarcode.Show();
		}
		wndSearchByBarcode.Opened.Add(function()
		{
			wndSearchByBarcode.SetHorizontalAlignment(HorizontalAlignment.Center);
			wndSearchByBarcode.SetTop(96);
			
			var txtBarcode = document.getElementById("txtBarcode");
			txtBarcode.focus();
		});
		</script>
		
		<table style="width: 100%">
			<tr>
				<td style="vertical-align: top;">
				<?php
					$lv = new ListView("lvProducts");
					$lv->Width = "100%";
					$lv->Columns[] = new ListViewColumn("chTitle", "Title");
					$lv->Columns[] = new ListViewColumn("chUnitPrice", "Unit Price", null, "100px");
					
					$products = Product::Get();
					foreach ($products as $product)
					{
						$lv->Items[] = new ListViewItem(array
						(
							new ListViewItemColumn("chTitle", "<a class=\"Wrapper\" href=\"#\" onclick=\"AddProduct(Product.GetByID(" . $product->ID . "));\">" . $product->Title . "</a>", $product->Title),
							new ListViewItemColumn("chUnitPrice", $product->UnitPrice)
						));
					}
					
					$lv->Render();
				?>
				</td>
				<td style="vertical-align: top; width: 300px;">
					<div class="Window">
						<div class="TitleBar"><span class="Text">Current Sale</span></div>
						<div class="Content" style="padding: 4px; width: 300px;">
							<div class="Receipt" style="width: 100%;">
								<div id="pnlReceipt">
								<?php
									$total = 0.00;
									$totalText = "";
									
									$productIDs = $this->GetVariableValue("ProductIDs");
									$productQuantities = $this->GetVariableValue("ProductQuantities");
									$productIDs = explode(",", $productIDs);
									$productQuantities = explode(",", $productQuantities);
									
									$count = count($productIDs);
									for ($i = 0; $i < $count; $i++)
									{
										$quantity = $productQuantities[$i];
										$product = Product::GetByID($productIDs[$i]);
										$subtotal += ($product->UnitPrice * $quantity);
										
										$totalText .= ($quantity . "\\t" . $product->Title . "\\t" . ($product->UnitPrice * $quantity)) . "\\n";
										
										?><a href="#"><span style="padding-right: 8px;"><?php echo($quantity); ?></span> <?php echo($product->Title); ?></a><?php
									}
									
									$taxRate = 0.07;
									$tax = $taxRate * $subtotal;
									
									$total = $tax + $subtotal;
								?>
								</div>
								<script type="text/javascript">
									Sydne.ExternalDisplay.SaleDetail("<?php echo($totalText); ?>");
								</script>
								<div class="ReceiptTotal">
									<div style="font-weight: normal;">
										Subtotal: <span id="lblReceiptSubTotal"><?php echo(number_format($subtotal, 2)); ?></span>
									</div>
									<div style="font-weight: normal;">
										Tax: <span id="lblReceiptTax"><?php echo(number_format($tax, 2)); ?></span>
									</div>
									<div>
										Total: <span id="lblReceiptTotal"><?php echo(number_format($total, 2)); ?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</td>
			</tr>
		</table>
		<script type="text/javascript">
			wndSearchByBarcode.StayOpen = true;
			wndSearchByBarcode.Show();
		</script>
		<?php
			}
			else if (!$this->IsVariableSet("PaymentTypeID"))
			{
		?>
		<script type="text/javascript">
		function cmdPaymentType_Click(id)
		{
			WebPage.SetVariableValue("PaymentTypeID", id);
		}
		function cmdPaymentTypeReturn_Click()
		{
			WebPage.ClearVariableValue("IsSaleFinalized");
		}
		</script>
		<?php
				$btng = new ButtonGroup("btngPaymentType");
				$btng->ButtonAlignment = ButtonGroupButtonAlignment::Center;
				
				$PaymentTypes = PaymentType::Get();
				foreach ($PaymentTypes as $PaymentType)
				{
					if (!$PaymentType->Enabled) continue;
					$btng->Items[] = new ButtonGroupButton("btngPaymentType" . $PaymentType->ID, $PaymentType->Title, null, "~/Images/Buttons/PaymentType/" . $PaymentType->ID . ".png", null, "cmdPaymentType_Click(" . $PaymentType->ID . ");");
				}
				$btng->Render();
			}
			else if (!$this->IsVariableSet("CustomerPaymentAmount"))
			{
				$PaymentType = PaymentType::GetByID($this->GetVariableValue("PaymentTypeID"));
				?>
				<script type="text/javascript">
					var bDecimal = false;
					function cmdPaymentTypeCashNumeric_Click(value)
					{
						var iAmount = document.getElementById("iAmount");
						if (iAmount.innerHTML.indexOf(".") != -1 && iAmount.innerHTML.substring(iAmount.innerHTML.indexOf(".") + 1).length == 2) return;
						iAmount.innerHTML += value.toString();
					}
					function cmdPaymentTypeCashPreset_Click(value)
					{
						var iAmount = document.getElementById("iAmount");
						if (iAmount.innerHTML == "")
						{
							iAmount.innerHTML = "0";
						}
						var dAmount = parseFloat(iAmount.innerHTML);
						dAmount += value;
						iAmount.innerHTML = dAmount.toFixed(2);
					}
					function cmdPaymentTypeCashDecimal_Click()
					{
						var iAmount = document.getElementById("iAmount");
						if (iAmount.innerHTML == "")
						{
							iAmount.innerHTML += "0.";
						}
						else if (iAmount.innerHTML.indexOf(".") == -1)
						{
							iAmount.innerHTML += ".";
						}
					}
					
					function cmdPaymentTypeCashBackspace_Click()
					{
						var iAmount = document.getElementById("iAmount");
						iAmount.innerHTML = iAmount.innerHTML.substring(0, iAmount.innerHTML.length - 1);
					}
				</script>
				<table style="margin-left: auto; margin-right: auto;">
					<tr>
						<td colspan="3">
							<span style="background-color: #FFFFFF; padding: 8px; font-weight: bold; width: 100%; text-align: right; display: block;"><span style="padding-right: 8px; font-size: 18pt;">Total: $</span><span style="font-size: 18pt;"><?php
							
								$productIDs = $this->GetVariableValue("ProductIDs");
								$productIDs = explode(",", $productIDs);
								$productQuantities = $this->GetVariableValue("ProductQuantities");
								$productQuantities = explode(",", $productQuantities);
								
								$Subtotal = 0.00;
								$count = count($productIDs);
								for ($i = 0; $i < $count; $i++)
								{
									$productID = $productIDs[$i];
									$quantity = $productQuantities[$i];
									
									$product = Product::GetByID($productID);
									$sale->Products[] = new SaleProduct($sale, $product, $quantity);
									$product->QuantityInStock -= $quantity;
									$product->Update();
									
									if ($MySQL->errno != 0) die($MySQL->error);
									
									$Subtotal += ($product->UnitPrice * $quantity);
								}
								$TaxDecimal = 0.07;
								$Tax = $TaxDecimal * $Subtotal;
								$Total = $Subtotal + $Tax;
								
								echo(number_format($Total, 2)); ?></span></span>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<span style="background-color: #FFFFFF; padding: 8px; font-weight: bold; width: 100%; text-align: right; display: block;"><span style="padding-right: 8px; font-size: 18pt;">$</span><span id="iAmount" style="font-size: 18pt;"></span></span>
						</td>
					</tr>
					<tr>
						<td>
						<?php
							
							$btng = new ButtonGroup("btngPaymentTypeCash123");
							$btng->ButtonSize = 64;
							$btng->ButtonAlignment = ButtonGroupButtonAlignment::Center;
							$btng->Items[] = new ButtonGroupButton("btngPaymentTypeCash1", "1", null, null, null, "cmdPaymentTypeCashNumeric_Click(1);");
							$btng->Items[] = new ButtonGroupButton("btngPaymentTypeCash2", "2", null, null, null, "cmdPaymentTypeCashNumeric_Click(2);");
							$btng->Items[] = new ButtonGroupButton("btngPaymentTypeCash3", "3", null, null, null, "cmdPaymentTypeCashNumeric_Click(3);");
							$btng->Render();
							
							$btng = new ButtonGroup("btngPaymentTypeCash456");
							$btng->ButtonSize = 64;
							$btng->ButtonAlignment = ButtonGroupButtonAlignment::Center;
							$btng->Items[] = new ButtonGroupButton("btngPaymentTypeCash4", "4", null, null, null, "cmdPaymentTypeCashNumeric_Click(4);");
							$btng->Items[] = new ButtonGroupButton("btngPaymentTypeCash5", "5", null, null, null, "cmdPaymentTypeCashNumeric_Click(5);");
							$btng->Items[] = new ButtonGroupButton("btngPaymentTypeCash6", "6", null, null, null, "cmdPaymentTypeCashNumeric_Click(6);");
							$btng->Render();
							
							$btng = new ButtonGroup("btngPaymentTypeCash789");
							$btng->ButtonSize = 64;
							$btng->ButtonAlignment = ButtonGroupButtonAlignment::Center;
							$btng->Items[] = new ButtonGroupButton("btngPaymentTypeCash7", "7", null, null, null, "cmdPaymentTypeCashNumeric_Click(7);");
							$btng->Items[] = new ButtonGroupButton("btngPaymentTypeCash8", "8", null, null, null, "cmdPaymentTypeCashNumeric_Click(8);");
							$btng->Items[] = new ButtonGroupButton("btngPaymentTypeCash9", "9", null, null, null, "cmdPaymentTypeCashNumeric_Click(9);");
							$btng->Render();
							
							$btng = new ButtonGroup("btngPaymentTypeCashx0x");
							$btng->ButtonSize = 64;
							$btng->ButtonAlignment = ButtonGroupButtonAlignment::Center;
							$btng->Items[] = new ButtonGroupButton("btngPaymentTypeCashBackspace", "&lt;- Erase", null, null, null, "cmdPaymentTypeCashBackspace_Click();");
							$btng->Items[] = new ButtonGroupButton("btngPaymentTypeCash0", "0", null, null, null, "cmdPaymentTypeCashNumeric_Click(0);");
							$btng->Items[] = new ButtonGroupButton("btngPaymentTypeCashDecimal", ".", null, null, null, "cmdPaymentTypeCashDecimal_Click();");
							$btng->Render();
							
						?>
						</td>
						<td>
						<?php
							$btng = new ButtonGroup("btngPaymentTypeCashQuickPresets");
							$btng->ButtonWidth = 128;
							$btng->ButtonHeight = 64;
							$btng->Orientation = ButtonGroupOrientation::Vertical;
							$btng->Items[] = new ButtonGroupButton("btngPaymentTypeCashQuickPresets1", "1", null, null, null, "cmdPaymentTypeCashPreset_Click(1);");
							$btng->Items[] = new ButtonGroupButton("btngPaymentTypeCashQuickPresets5", "5", null, null, null, "cmdPaymentTypeCashPreset_Click(5);");
							$btng->Items[] = new ButtonGroupButton("btngPaymentTypeCashQuickPresets10", "10", null, null, null, "cmdPaymentTypeCashPreset_Click(10);");
							$btng->Items[] = new ButtonGroupButton("btngPaymentTypeCashQuickPresets20", "20", null, null, null, "cmdPaymentTypeCashPreset_Click(20);");
							$btng->Render();
						?>
						</td>
						<td style="vertical-align: top;">
						<?php
							$btng = new ButtonGroup("btngPaymentControlBox");
							$btng->Orientation = ButtonGroupOrientation::Vertical;
							$btng->Items[] = new ButtonGroupButton("btngPaymentControlFinalize", "Finalize Payment", null, "~/Images/Buttons/Accept.png", null, "cmdPaymentControlFinalize_Click();");
							$btng->Render();
						?>
						</td>
					</tr>
				</table>
				<script type="text/javascript">
					function cmdPaymentControlFinalize_Click()
					{
						window.onbeforeunload = null;
						
						var iAmount = document.getElementById("iAmount");
						WebPage.SetVariableValue("CustomerPaymentAmount", iAmount.innerHTML);
					}
					function cmdPaymentControlReturn_Click()
					{
						WebPage.ClearVariableValue("PaymentTypeID");
					}
				</script>
				<script type="text/javascript">
					window.onbeforeunload = function()
					{
						return "A sale is still in progress. Do you wish to exit?";
					};
				</script>
				<?php
			}
			else
			{
				$PaymentType = PaymentType::GetByID($this->GetVariableValue("PaymentTypeID"));
				if ($PaymentType->UseCashDrawer)
				{
					?><script type="text/javascript">Sydne.CashDrawer.Open();</script><?php
				}
				
				$sale = new Sale();
				$sale->Employee = $CurrentEmployee;
				$sale->TimestampOrdered = date();
				$sale->TimestampPaid = date();
				$sale->PaymentType = PaymentType::GetByID($this->GetVariableValue("PaymentTypeID"));
				$sale->PaymentAmount = $this->GetVariableValue("CustomerPaymentAmount");
				
				$receiptText = "";
				?>
				<div class="Receipt" style="width: 400px; margin-left: auto; margin-right: auto; height: auto; border: inset 1px; padding: 8px;">
					<table style="width: 100%;">
						<tr>
							<td colspan="3" style="text-align: center; display: none;"><img src="<?php echo(System::ExpandRelativePath("~/Images/Companies/" . $CurrentCompany->ID . "/Logo.png")); ?>" /></td>
						<tr>
							<td colspan="3" style="font-size: 16pt; font-weight: bold;">Products</td>
						</tr>
						<?php
						$productIDs = $this->GetVariableValue("ProductIDs");
						$productIDs = explode(",", $productIDs);
						$productQuantities = $this->GetVariableValue("ProductQuantities");
						$productQuantities = explode(",", $productQuantities);
						
						$Subtotal = 0.00;
						$count = count($productIDs);
						for ($i = 0; $i < $count; $i++)
						{
							$productID = $productIDs[$i];
							$quantity = $productQuantities[$i];
							
							$product = Product::GetByID($productID);
							$sale->Products[] = new SaleProduct($sale, $product, $quantity);
							$product->QuantityInStock -= $quantity;
							$product->Update();
							
							if ($MySQL->errno != 0) die($MySQL->error);
							
							echo("<tr><td style=\"width: 48px;\">" . $quantity . "</td><td>" . $product->Title . "</td><td>" . number_format($product->UnitPrice * $quantity, 2) . "</td></tr>");
							
							$receiptText .= $quantity . "\\t" . $product->Title . "\\t" . $product->UnitPrice . "\\n";
							
							$Subtotal += ($product->UnitPrice * $quantity);
						}
						$Subtotal = number_format($Subtotal, 2);
						?>
						<tr>
							<td colspan="2" style="font-size: 16pt; font-weight: bold;">Subtotal</td>
							<td><?php echo($Subtotal); ?></td>
						</tr>
						<tr>
							<td colspan="2" style="font-size: 16pt; font-weight: bold;">Tax</td>
							<td><?php
								$TaxDecimal = 0.07;
								$Tax = $TaxDecimal * $Subtotal;
								echo(number_format($Tax, 2));
							?></td>
						</tr>
						<tr>
							<td colspan="2" style="font-size: 16pt; font-weight: bold;">Total</td>
							<td><?php
								$Total = $Subtotal + $Tax;
								echo(number_format($Total, 2));
							?></td>
						</tr>
						<tr>
							<td colspan="2" style="font-size: 16pt; font-weight: bold;">Payment Tendered</td>
							<td><?php
								$CustomerPayment = $this->GetVariableValue("CustomerPaymentAmount");
								$CustomerPayment = number_format($CustomerPayment, 2);
								echo($CustomerPayment);
							?></td>
						</tr>
						<tr>
							<td colspan="2" style="font-size: 16pt; font-weight: bold;">Change</td>
							<td colspan="2" style="font-size: 14pt; font-weight: bold;"><?php echo(number_format(floatval($CustomerPayment) - floatval($Total), 2)); ?></td>
						</tr>
					</table>
					<script type="text/javascript">
						Sydne.ExternalDisplay.SaleFinal(<?php echo($Subtotal); ?>, <?php echo($Tax); ?>, <?php echo($CustomerPayment); ?>);
						Sydne.ReceiptPrinter.Print("<?php echo($receiptText); ?>", <?php echo(number_format($CustomerPayment, 2)); ?>);
					</script>
				</div>
				<?php
				$sale->Update();
				if ($MySQL->errno != 0) die($MySQL->error);
			}
		}
	}
?>