<?php
	global $RootPath;
	$RootPath = dirname(__FILE__) . "/../";
	
	require("WebFX/WebFX.inc.php");
	
	use WebFX\System;
?>
var System =
{
	"ExpandRelativePath": function(path)
	{
		var basepath = "<?php echo(System::$Configuration["Application.BasePath"]); ?>";
		var retpath = path.replace(/~\//g, basepath + "/");
		return retpath;
	},
	"Redirect": function(url)
	{
		window.location.href = System.ExpandRelativePath(url);
	}
};