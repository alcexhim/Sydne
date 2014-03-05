<?php
	namespace Sydne\Pages;
	
	use WebFX\System;
	use WebFX\ModulePage;
	
	class LoginPage extends SydnePage
	{
		public $LoginInvalid;
		public $CredentialsPrompt;
		public $TouchSwipePrompt;
		public $ProcessingLoginPrompt;
		
		public $InvalidCredentialsMessage;
		
		public function __construct()
		{
			$this->LoginInvalid = false;
			
			$this->InvalidCredentialsMessage = "Invalid user name or password entered. Please try again.";
			$this->CredentialsPrompt = "Please enter your login information";
			$this->TouchSwipePrompt = "Please touch/swipe your employee card or enter your login information";
			$this->ProcessingLoginPrompt = "Processing login, please wait...";
		}
		
		protected function Initialize()
		{
			parent::Initialize();
			$this->CssClass = "LoginPage";
		}
		protected function BeforeContent()
		{
			// do nothing, overriding the parent class BeforeContent
		}
		protected function RenderContent()
		{
			?>
				<form method="POST" id="frmLogin" onsubmit="DisplayWaitingText('<?php echo($this->ProcessingLoginPrompt); ?>');">
					<input type="hidden" id="txtCardNumber" name="cn" />
					<table class="LoginForm" style="margin-top: 128px; width: 500px; margin-left: auto; margin-right: auto;">
						<tr>
							<td colspan="2" style="text-align: center; padding-bottom: 16px;">
								<img src="<?php echo(System::ExpandRelativePath("~/Images/Logo.png")); ?>" alt="Log in to Sydne" />
							</td>
						</tr>
						<tr id="trUserName">
							<td><label for="txtUserName">User <u>n</u>ame:</label></td>
							<td><input type="text" id="txtUserName" name="un" value="<?php if (isset($_POST["un"])) { echo($_POST["un"]); } ?>" accesskey="n" /></td>
						</tr>
						<tr id="trPassword">
							<td><label for="txtPassword"><u>P</u>assword:</label></td>
							<td><input type="password" id="txtPassword" name="pw" accesskey="P" /></td>
						</tr>
						<tr>
							<td colspan="2" style="text-align: center; padding-top: 16px; padding-bottom: 16px; display: none;" id="tdWaiting">
								<img src="<?php echo(System::ExpandRelativePath("~/Images/Waiting.gif")); ?>" />
							</td>
						</tr>
						<tr>
							<td colspan="2" style="text-align: center; padding-top: 16px; padding-bottom: 16px;" id="tdPrompt">
							<?php
								if ($this->LoginInvalid)
								{
									echo($this->InvalidCredentialsMessage);
								}
								else
								{
									echo($this->CredentialsPrompt);
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
						
						DisplayWaitingText("<?php echo($this->ProcessingLoginPrompt); ?>");
						
						txtCardNumber.value = e.content;
						frmLogin.submit();
					}
					function DisplayWaitingText(text)
					{
						var trUserName = document.getElementById("trUserName");
						var trPassword = document.getElementById("trPassword");
						var tdPrompt = document.getElementById("tdPrompt");
						var tdWaiting = document.getElementById("tdWaiting");
						
						trUserName.style.display = "none";
						trPassword.style.display = "none";
						tdPrompt.innerHTML = text;
						tdWaiting.style.display = "table-cell";
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
					
					var loginInvalid = <?php
					if ($this->LoginInvalid)
					{
						echo ("true");
					}
					else
					{
						echo("false");
					} ?>;
					
					var tdPrompt = document.getElementById("tdPrompt");
					if (!Sydne.Available)
					{
						if (loginInvalid)
						{
							tdPrompt.innerHTML = "<?php echo($this->InvalidCredentialsMessage); ?>";
							document.getElementById("txtPassword").focus();
						}
						else
						{
							tdPrompt.innerHTML = "<?php echo($this->CredentialsPrompt); ?>";
						}
					}
					else
					{
						window.setTimeout(function()
						{
							Sydne.ExternalDisplay.LogoText("Register Closed");
						}, 500);
						
						if (loginInvalid)
						{
							tdPrompt.innerHTML = "<?php echo($this->InvalidCredentialsMessage); ?>";
							document.getElementById("txtPassword").focus();
						}
						else
						{
							tdPrompt.innerHTML = "<?php echo($this->TouchSwipePrompt); ?>";
						}
						tryLogin();
					}
				</script>
			<?php
		}
		protected function AfterContent()
		{
		?>
		<div style="padding-top: 48px; color: #5F2364; text-align: center; font-size: 9pt;">sydne version 0.3a &bullet; licensed to <?php echo(System::$Configuration["Company.Title"]); ?> &bullet; Written by Michael Becker</div>
		<?php
		}
	}
?>