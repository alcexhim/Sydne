<?php
	class System
	{
		public static $Configuration;
		public static $VirtualPaths;
		
		public static function Redirect($path)
		{
			header("Location: " . System::ExpandRelativePath($path));
			return;
		}
		public static function ExpandRelativePath($path, $includeServerInfo = false)
		{
			$retval = str_replace("~", System::$Configuration["Application.BasePath"], $path);
			if ($includeServerInfo)
			{
				// from http://stackoverflow.com/questions/6768793/php-get-the-full-url
				$sp = strtolower($_SERVER["SERVER_PROTOCOL"]);
				$protocol = substr($sp, 0, strpos($sp, "/")) . $s;
				$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
				$serverPath = $protocol . "://" . $_SERVER["SERVER_NAME"] . $port;
				$retval = $serverPath . $retval;
			}
			return $retval;
		}
		public static function RedirectToLoginPage()
		{
			System::Redirect("~/account/login");
			return;
		}
		public static function GetVirtualPath()
		{
			if (isset($_GET["virtualpath"]))
			{
				if ($_GET["virtualpath"] != null) return explode("/", $_GET["virtualpath"]);
			}
			return null;
		}
	}
	System::$VirtualPaths = array();
	
	class VirtualPath
	{
		public $PathName;
		public $UserFunction;
		
		public function __construct($pathName, $userFunction)
		{
			$this->PathName = $pathName;
			$this->UserFunction = $userFunction;
		}
		
		public function Execute($param)
		{
			return call_user_func($this->UserFunction, $param);
		}
	}
	
	System::$Configuration = array();
	
	require("Configuration.inc.php");
	require("CueCatDecoder4.inc.php");
	
	global $MySQL;
	$MySQL = new mysqli(\System::$Configuration["Database.ServerName"], \System::$Configuration["Database.UserName"], \System::$Configuration["Database.Password"], \System::$Configuration["Database.DatabaseName"]);
	if ($MySQL->connect_error)
	{
		// die('Connect Error (' . $MySQL->connect_errno . ') ' . $MySQL->connect_error);
	}
	
	require("WebFramework/WebFramework.inc.php");
	require("Sydne/Sydne.inc.php");
	
	session_start();
	
	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$loginSuccessful = false;
		if ($_POST["cn"] != null)
		{
			$_SESSION["CurrentCardNumber"] = $_POST["cn"];
			$loginSuccessful = true;
		}
		else if ($_POST["un"] != null && $_POST["pw"] != null)
		{
			$_SESSION["CurrentUserName"] = $_POST["un"];
			$_SESSION["CurrentPassword"] = $_POST["pw"];
			$loginSuccessful = true;
		}
		
		if ($loginSuccessful)
		{
			$CurrentEmployee = Sydne\Objects\Employee::GetCurrent();
			if ($CurrentEmployee != null)
			{
				$CurrentEmployee->LogIn();
			}
			
			/*
			if ($_SESSION["LoginRedirect"] == null)
			{
				\System::Redirect("~/");
			}
			else
			{
				\System::Redirect($_SESSION["LoginRedirect"]);
			}
			*/
		}
	}
	
	if (!$InhibitLoginCheck)
	{
		$CurrentEmployee = Sydne\Objects\Employee::GetCurrent();
		if ($CurrentEmployee == null)
		{
			if ($_POST["Format"] == "JSON" || $_GET["Format"] == "JSON")
			{
				echo("{ \"Success\": false, \"ErrorMessage\": \"You are not logged in. Please log in to continue.\", \"Remedy\": \"login\" }");
				exit();
			}
			else
			{
				$_SESSION["LoginRedirect"] = $_SERVER["PHP_SELF"];
				
				$page = new Sydne\Pages\LoginPage();
				$page->LoginInvalid = ($_SERVER["REQUEST_METHOD"] == "POST");
				$page->Render();
				exit();
			}
		}
	}
	
	$path = System::GetVirtualPath();
?>