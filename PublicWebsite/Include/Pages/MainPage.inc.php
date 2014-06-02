<?php
	namespace Sydne\PublicWebsite\Pages;
	
	use Sydne\PublicWebsite\MasterPages\WebPage;
	
	use WebFX\Controls\ImageGallery;
	
	class MainPage extends WebPage
	{
		protected function RenderContent()
		{
			?>
			<p>
				Hi. My name's Denys, and welcome to my website!
			</p>
			<p>
				I'm an artist that draws mostly for enjoyment. As a result, a lot of what I do tends to be fanart, though I occasionally dip into
				original stuff from time to time. I also like games, manga, and cartoons from pretty much all sides of the globe.
			</p>
			<?php
			
			$gallery = new ImageGallery();
			$gallery->Render();
			
		}
	}
?>