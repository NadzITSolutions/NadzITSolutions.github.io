<?php
include('core/config.php');
include_once("controllers/DriverController.php");
include_once("controllers/UserController.php");
include_once("controllers/AdminController.php");
include_once("controllers/ValidationController.php");
include_once("controllers/MailController.php");

if (!isset($_SESSION)) {
  session_start();
}

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
if(substr($uri,0,strlen($apiurl))==$apiurl)
	{
		$request = substr($uri,strlen($apiurl));
	}
	else
	{
		header('HTTP/1.1 400 Bad Request');
		header('Content-Type: text/xml; charset=UTF-8');
		echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
		echo("<error>Bad Request</error>");
		return;
	}

$args = explode("/",$request);
$controller = $args[0];

if($controller=="driver")
{
	$driverCon = new DriverController($request,$method);
	$driverCon->resolve();
}
else if($controller=="user")
{
	
}	
else if($controller=="admin")
{
	$adminCon = new AdminController($request,$method);
	$adminCon->resolve();
}
else if($controller=="validate")
{
	$validCon = new ValidationController($request,$method);
	$validCon->resolve();
}
else if($controller=="mail")
{
	$mailCon = new MailController($request,$method);
	$mailCon->resolve();
}
else
{
	header('HTTP/1.1 400 Bad Request');
	header('Content-Type: text/xml; charset=UTF-8');
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
	echo("<error>\n");
	echo "<reason>Undefined API</reason>\n";
	echo "<description>Kindly refer Ride on Bike API documentation</description>\n";
	echo "</error>";
	return;
}

?>