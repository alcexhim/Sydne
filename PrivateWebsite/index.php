<?php
	global $RootPath;
	$RootPath = dirname(__FILE__);
	
	require_once("WebFX/WebFX.inc.php");
	use WebFX\System;
	
	use Sydne\Objects\Employee;
	
	System::$BeforeLaunchEventHandler = function()
	{
		$path = System::GetVirtualPath();
		
		$user = Employee::GetCurrent();
		if ($user == null)
		{
			if ($path[0] == "Account" && $path[1] == "Login.page")
			{
				return true;
			}
			else
			{
				System::Redirect("~/Account/Login.page");
				return false;
			}
		}
	};
	
	System::Launch();
?>