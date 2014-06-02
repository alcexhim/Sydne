<?php
	use WebFX\System;
	use WebFX\Module;
	use WebFX\ModulePage;
	
	use Sydne\PublicWebsite\Pages\MainPage;
	
	System::$Modules[] = new Module("com.sydne.PublicWebsite.Default", array
	(
		new ModulePage("", function($path)
		{
			$page = new MainPage();
			$page->Render();
		})
	));
?>