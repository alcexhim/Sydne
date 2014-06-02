<?php
	namespace Sydne\PublicWebsite\MasterPages;
	
	use WebFX\System;
	use WebFX\WebStyleSheet;
	
	use WebFX\Controls\Disclosure;
	
	class WebPage extends \WebFX\WebPage
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->StyleSheets[] = new WebStyleSheet("~/StyleSheets/Main.css");
		}
		
		protected function BeforeContent()
		{
			?>
			<div class="Header">
				Not logged in. <a href="#">Sign in</a> or <a href="#">become a customer</a>!
			</div>
			<div class="Sidebar">
				<div class="Panel">
					<div class="Content" style="text-align: center;">
						<img src="<?php echo(System::ExpandRelativePath("~/Images/Logo.png")); ?>" alt="Sullen Studio" />
					</div>
				</div>
				<div class="Panel">
					<div class="Title">Find me on</div>
					<div class="Content">
						<div class="IconList">
							<!-- <a href="#"><img src="<?php echo(System::ExpandRelativePath("~/Images/Buttons/SocialNetworks/Facebook.png")); ?>" /></a> -->
							<a href="http://sullenswordsman.deviantart.com/"><img src="<?php echo(System::ExpandRelativePath("~/Images/Icons/SocialNetworks/1.png")); ?>" /></a>
							<a href="http://sullenswordsart.tumblr.com/"><img src="<?php echo(System::ExpandRelativePath("~/Images/Icons/SocialNetworks/2.png")); ?>" /></a>
						</div>
					</div>
				</div>
				<div class="Panel">
					<div class="Title">I do commissions, too!</div>
					<div class="Content">
						<a target="_blank" href="http://sullenswordsman.deviantart.com/art/Commissions-are-OPEN-414448693"><img src="http://th03.deviantart.net/fs71/PRE/i/2014/109/8/6/commissions_are_open__by_sullenswordsman-d6ur2np.png"  style="width: 300px;" /></a>
						<!--
						<table style="width: 100%;">
							<tr>
								<th>Type</th>
								<th>Sketch</th>
								<th>Black &amp; white</th>
								<th>Full color</th>
							</tr>
							<tr>
								<td>Bust</td>
								<td>$5</td>
								<td>$10</td>
								<td>$15</td>
							</tr>
							<tr>
								<td>Chibi</td>
								<td>$8</td>
								<td>$13</td>
								<td>$18</td>
							</tr>
							<tr>
								<td>Regular</td>
								<td>$10</td>
								<td>$15</td>
								<td>$20</td>
							</tr>
						</table>
						-->
						<p style="border-top: solid 1px #5F2364; padding-top: 4px; margin-top: 4px;">
							Rules and restrictions apply. Prices are subject to vary. See <a target="_blank" href="http://sullenswordsman.deviantart.com/art/Commissions-are-OPEN-414448693">my post on DeviantART</a> for more details.
						</p>
					</div>
				</div>
			</div>
			<div class="Content">
			<?php
		}
		protected function AfterContent()
		{
			?>
			</div>
			<?php
		}
	}
?>