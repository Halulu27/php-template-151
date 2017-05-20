<?php
error_reporting(E_ALL);
use halulu27\Factory;
session_start();

require_once("../vendor/autoload.php");
$factory = halulu27\Factory::createFromInitFile(__DIR__. "/../config.ini");

$pdo = $factory->getPdo();
$loginService = $factory->getLoginService();

switch(strtok($_SERVER["REQUEST_URI"],'?')) {
	case "/":
		$factory->getIndexController()->homepage();
		break;
		
	//case "/email":
		//$factory->getMailer()->send(
			//	Swift_Message::newInstance("Subject")
				//->setFrom(["gibz.module.151@gmail.com" => "Your Name"])
				//->setTo(["simon.odermatt@hotmail.ch" => "Foos Name"])
				//->setBody("Here is the message itself")
				//);
 		//break;
		
	case "/index":
		$factory->getIndexController()->showIndex();
		break;
		
	case "/login":
		$cnt = $factory->getLoginController();
		if ($_SERVER["REQUEST_METHOD"] === "POST")
		{	
			$cnt->login($_POST);
		}
		else 
		{
			$cnt->showLogin();
		}
		break;
		
	case "/logout":
		$cnt = $factory->getLoginController();
		if ($_SERVER["REQUEST_METHOD"] === "POST")
		{
			$cnt->logout($_POST);
		}
		header("Location: /");
		break;
		
	case "/register":
		$cnt = $factory->getLoginController();
		if ($_SERVER["REQUEST_METHOD"] === "POST")
		{
			$message = $cnt->register($_POST);
			if (isset($message) AND !empty($message))
			{
				// send email and redirect to page where is said that the user have to activate his account with his email.
				$factory->getMailer()->send(
						Swift_Message::newInstance("Socialize - Activate your account")
						->setFrom(["gibz.module.151@gmail.com" => "Socialize"])
						->setTo($_POST["email"])
						->setContentType("text/html")
						->setBody($message)
						);
				$cnt->showRegister("", "", "", true);
			}
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
		//brea;
		
		case "/register/checkemail":
			return $factory->getLoginController()->checkEmail($_REQUEST["email"]);
			break;
		
		case "/register/checkusername":
			return $factory->getLoginController()->checkUsername($_REQUEST["username"]);
			break;
			
		case "/password":
			$cnt = $factory->getLoginController();
			if ($_SERVER["REQUEST_METHOD"] === "POST")
			{
				$result = $cnt->password($_POST);
				if (isset($message) AND !empty($message))
				{
					// send email and redirect to page where is said that the user have to check his email to reset his password.
					$factory->getMailer()->send(
							Swift_Message::newInstance("Instafornotrich - Reset your password")
							->setFrom(["gibz.module.151@gmail.com" => "Instafornotrich"])
							->setTo($result["email"])
							->setContentType("text/html")
							->setBody($result["message"])
							);
					$cnt->showPassword(true);
				}
			}
			else
			{
				$cnt->showPassword();
			}
			break;
			
	default:
		$matches = [];
		if (preg_match("|^/hello/(.+)$|", $_SERVER["REQUEST_URI"], $matches)) {
			$factory->getIndexController()->greet($matches[1]);
			break;
		}
		
		// Activate Account
		if (preg_match("|^/activate/account/(.+)/(.+)$|", $_SERVER["REQUEST_URI"], $matches))
		{
			$cnt = $factory->getLoginController();
			if ($cnt->activateAccount($matches[1], $matches[2]))
			{
				$cnt->showLogin();
			}
			else 
			{
				$factory->getIndexController()->homepage();
			}
			break;
		}
		
		// Reset password
		if (preg_match("|^/reset/password/(.+)/(.+)$|", $_SERVER["REQUEST_URI"], $matches))
		{
			$cnt = $factory->getLoginController();
			if (($email = $cnt->checkResetString($matches[1], $matches[2])) != false)
			{
				if ($_SERVER["REQUEST_METHOD"] === "GET")
				{
					$cnt->showResetPassword($matches[1], $matches[2]);
				}
				else 
				{
					if ($cnt->resetPassword($email, $_POST, $matches[1], $matches[2]) == true)
					{
						$cnt->showLogin();
					}
					else
					{
						$cnt->showLogin("", "fatal error; was not able to reset password");
					}
				}
				break;
			}
		}
		echo "Not Found";
}











