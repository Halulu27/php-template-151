<?php
error_reporting(E_ALL);
use halulu27\Factory;
session_start();

require_once("../vendor/autoload.php");
$factory = halulu27\Factory::createFromInitFile(__DIR__. "/../config.ini");
$tmpl = $factory->getTemplateEngine();

$pdo = $factory->getPdo();
$loginService = $factory->getLoginService();



switch(strtok($_SERVER["REQUEST_URI"],'?')) {
	case "/":
		$factory->getIndexController()->homepage();
	//->	//$factory->getMailer()->send(
			//	Swift_Message::newInstance("Subject")
				//->setFrom(["gibz.module.151@gmail.com" => "Your Name"])
				//->setTo(["foobar@gmail.com" => "Foos Name"])
				//->setBody("Here is the message itself")
				//);
		break;
	case "/register/test":
		echo true;
		return true;
		break;
	case "/login":
		$cnt = $factory->getLoginController();
		if ($_SERVER["REQUEST_METHOD"] === "GET")
		{
			$cnt->showLogin();
		}
		else 
		{
			$cnt->login($_POST);
		}
		break;
	case "/register":
		$cnt = $factory->getLoginController();
		if ($_SERVER["REQUEST_METHOD"] === "POST")
		{
			$cnt->register($_POST);
		}
		else
		{
			$cnt->showRegister();
		}
		break;		
	//case "/test/upload":
		//if(file_put_contents(__DIR__ . "/../../upload/test.txt", "Mein erster Upload")) {
			//echo "It worked";
		//} else {
			//echo "Error happened";
		//}
		//break;	
		
	case "/register/checkemail":
		return $factory->getLoginController()->checkEmail($_REQUEST["email"]);
		break;
		
	case "/register/checkusername":
		return $factory->getLoginController()->checkUsername($_REQUEST["username"]);
		break;
		
	default:
		$matches = [];		
		if(preg_match("|^/hello/(.+)$|", $_SERVER["REQUEST_URI"], $matches)) {
			$factory->getIndexController()->greet($matches[1]);
			break;
		}
		echo "Not Found";
}

