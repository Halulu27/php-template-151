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
		$factory->getIndexController()->showIndex($_SESSION["email"]);
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
			$link = $cnt->register($_POST);
			if (isset($link) AND !empty($link))
			{
				// send email and redirect to page where is said that the user have to activate his account with his email.
				$factory->getMailer()->send(
					Swift_Message::newInstance("Instafornotrich - Activate your account")
					->setFrom(["gibz.module.151@gmail.com" => "Instafornotrich"])
					->setTo($_POST["email"])
					->setContentType("text/html")
					->setBody("<h1>Hi " . $_POST["username"] . '</h1><div><p>Please use the following link to activate your account.
						If you have not created a new account you can ignore this email.</p>
						<a href="' . $link . '">' . $link .'</a></div>')
				);
				$cnt->showRegister("","", "", true);
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
			if ($_SERVER["REQUEST_METHOD"] === "GET")
			{
				$cnt->showPassword();
			}
			else
			{
				$link = $cnt->password($_POST);
				if (isset($link) AND !empty($link))
				{
					// send email and redirect to page where is said that the user have to check his email to reset his password.
					$factory->getMailer()->send(
							Swift_Message::newInstance("Instafornotrich - Reset your password")
							->setFrom(["gibz.module.151@gmail.com" => "Instafornotrich"])
							->setTo($link[0])
							->setContentType("text/html")
							->setBody('<h1>Hi</h1><div><p>Please use the following link to reset your account.
						If you have reset your password you can ignore this email.</p>
						<a href="' . $link[1] . '">' . $link[1] .'</a></div>')
							);
					$cnt->showPassword(true);
				}
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
			$factory->getIndexController()->homepage();
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
						$cnt->homepage();
					}
				}
			}
		}
		echo "Not Found";
}











