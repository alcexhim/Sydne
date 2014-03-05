<?php
	namespace Sydne\Pages;
	
	use WebFX\System;
	
	class ErrorPage extends SydnePage
	{
		public $Message;
		public $ReturnButtonURL;
		public $ReturnButtonText;
		
		public function __construct()
		{
			parent::__construct();
			$this->Message = "An error has occurred.";
			$this->ReturnButtonURL = "javascript:history.back();";
			$this->ReturnButtonText = "Return to Previous Page";
		}
		
		protected function RenderContent()
		{
		?>
			<table class="LoginForm" style="margin-top: 128px; width: 500px; margin-left: auto; margin-right: auto;">
				<tr>
					<td style="text-align: center; padding-bottom: 16px;">
						<?php echo($this->Message); ?>
					</td>
				</tr>
				<tr>
					<td style="text-align: center; padding-bottom: 16px;">
						<a href="<?php echo(System::ExpandRelativePath($this->ReturnButtonURL)); ?>"><?php echo($this->ReturnButtonText); ?></a>
					</td>
				</tr>
			</table>
		<?php
		}
	}
?>