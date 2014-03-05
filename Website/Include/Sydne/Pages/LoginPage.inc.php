<?php
	namespace Sydne\Pages
	{
		use WebFramework\WebPage;
		
		class LoginPage extends SydnePage
		{
			public $LoginInvalid;
			
			protected function Initialize()
			{
				parent::Initialize();
				$this->CssClass = "LoginPage";
				$this->LoginInvalid = false;
			}
			protected function BeforeContent()
			{
				// do nothing, overriding the parent class BeforeContent
			}
			protected function RenderContent()
			{
				?>
					<form method="POST" id="frmLogin">
						<input type="hidden" id="txtCardNumber" name="cn" />
						<table class="LoginForm" style="margin-top: 128px; width: 500px; margin-left: auto; margin-right: auto;">
							<tr>
								<td colspan="2" style="text-align: center; padding-bottom: 16px;">
									<img src="<?php echo(\System::ExpandRelativePath("~/Images/Logo.png")); ?>" alt="Log in to Sydne" />
								</td>
							</tr>
							<tr id="trUserName">
								<td><label for="txtUserName">User <u>n</u>ame:</label></td>
								<td><input type="text" id="txtUserName" name="un" value="<?php echo($_POST["un"]); ?>" /></td>
							</tr>
							<tr id="trPassword">
								<td><label for="txtPassword"><u>P</u>assword:</label></td>
								<td><input type="password" id="txtPassword" name="pw" /></td>
							</tr>
							<tr>
								<td colspan="2" style="text-align: center; padding-top: 16px; padding-bottom: 16px;" id="tdPrompt">
								<?php
									if ($this->LoginInvalid)
									{
										echo("Invalid user name or password entered. Please try again.");
									}
									else
									{
										echo("Please enter your login information");
									}
								?>
								</td>
							</tr>
							<tr>
								<td colspan="2" style="text-align: right; padding-top: 16px; display: none;">
									<input type="submit" value="Login" />
								</td>
							</tr>
						</table>
					</form>
					<script type="text/javascript">
						var loginSuccessful = false;
						function processLogin(e)
						{
							if (!e) return;
							
							if (e.remedy == "ConnectDevice")
							{
								loginSuccessful = true;
								return;
							}
							if (!e.content)
							{
								console.log("not a credential packet");
								return;
							}
							
							loginSuccessful = true;
							
							var txtUserName = document.getElementById("txtUserName");
							var txtPassword = document.getElementById("txtPassword");
							var txtCardNumber = document.getElementById("txtCardNumber");
							var trUserName = document.getElementById("trUserName");
							var trPassword = document.getElementById("trPassword");
							var tdPrompt = document.getElementById("tdPrompt");
							
							trUserName.style.display = "none";
							trPassword.style.display = "none";
							tdPrompt.innerHTML = "Processing login, please wait...";
							
							txtCardNumber.value = e.content;
							frmLogin.submit();
						}
						function tryLogin()
						{
							var script = document.createElement("SCRIPT");
							script.setAttribute("type", "text/javascript");
							script.setAttribute("src", "http://localhost:27248/Mifare/Scan?jsonp=processLogin");
							script.onload = function()
							{
								if (!loginSuccessful) tryLogin();
							};
							document.body.appendChild(script);
						}
						
						
						var trUserName = document.getElementById("trUserName");
						var trPassword = document.getElementById("trPassword");
						document.getElementById("txtUserName").focus();
						
						if (!Sydne.Available)
						{
							var tdPrompt = document.getElementById("tdPrompt");
							tdPrompt.innerHTML = "Please enter your login information";
						}
						else
						{
							window.setTimeout(function()
							{
								Sydne.ExternalDisplay.LogoText("Register Closed");
							}, 500);
							
							var tdPrompt = document.getElementById("tdPrompt");
							tdPrompt.innerHTML = "Please touch/swipe your employee card or enter your login information";
							tryLogin();
						}
					</script>
				<?php
			}
			protected function AfterContent()
			{
			?>
			<div style="padding-top: 48px; color: #5F2364; text-align: center; font-size: 9pt;">sydne version 0.3a &bullet; licensed to <?php echo(\System::$Configuration["Company.Title"]); ?> &bullet; Written by Michael Becker</div>
			<?php
			}
		}
	}
?>