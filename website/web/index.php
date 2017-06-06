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
		
	case "/index":
		$factory->getIndexController()->showIndex();
		break;
		
	case "/login":
		if (isset($_SESSION["isLoggedIn"]))
		{
			if ($_SESSION["isLoggedIn"] == true)
			{
				header("Location: /index");
				break;
			}
		}
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
		if (isset($_SESSION["isLoggedIn"]))
		{
			if ($_SESSION["isLoggedIn"] == true)
			{
				header("Location: /index");
				break;
			}
		}
		$cnt = $factory->getLoginController();
		if ($_SERVER["REQUEST_METHOD"] === "POST")
		{
			$result = $cnt->register($_POST);
			if ($result == true)
			{
				$cnt->showRegister("", "", "", true);
			}
		}
		else
		{
			$cnt->showRegister();			
		}
		break;
		
	case "/addpost":
		if (isset($_SESSION["isLoggedIn"]))
		{
			if ($_SESSION["isLoggedIn"] == true && isset($_SESSION["username"]))
			{
				$cnt = $factory->getPostController();
				if ($_SERVER["REQUEST_METHOD"] === "POST")
				{
					$cnt->savePost($_POST);
				}
				else
				{
					$cnt->showAddPost();
				}
				break;
			}
		}
		header("Location: /");
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
				$worked = $cnt->password($_POST);
				$cnt->showPassword($worked);
			}
			else
			{
				$cnt->showPassword();
			}
			break;
			
		case "/updateSubscription":
			{
				if ($_SERVER["REQUEST_METHOD"] === "POST")
				{
					$cnt = $factory->getSubscriptionController();
					$cnt->updateSubscription($_POST);
					break;
				}
			}
			
		case "/updateprofilepicture":
			{
				if ($_SERVER["REQUEST_METHOD"] === "POST")
				{
					$cnt = $factory->getProfileController();
					$cnt->updateProfilePicture($_POST);
					break;
				}
			}
			
	default:
		$matches = [];
		if (preg_match("|^/hello/(.+)$|", $_SERVER["REQUEST_URI"], $matches)) 
		{
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
		
		// find profile of user
		if (preg_match("|^/(.+)$|", $_SERVER["REQUEST_URI"], $matches))
		{
			if (preg_match('/[A-Za-z0-9._]/', $matches[1]))
			{
				$cnt = $factory->getProfileController();
				$cnt->showProfile($matches[1]);
				break;			
			}
		}
		
		if (preg_match("|^/post/(\d+)/image$|", $_SERVER["REQUEST_URI"], $matches))
		{
			$cnt = $factory->getPictureController();
			$cnt->renderPicture($matches[1]);
			break;
		}
		
		$factory->getIndexController()->showIndex();
}











