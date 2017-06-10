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
		$factory->getIndexController()->homepage();
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
		if ($_SERVER["REQUEST_METHOD"] === "POST")
		{
			$factory->getLoginController()->logout($_POST);
		}
		header("Location: /");
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
		
	case "/addpost":
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
			$cnt->password($_POST);
		}
		else
		{
			$cnt->showPassword();
		}
		break;
		
	case "/updateSubscription":		
		if ($_SERVER["REQUEST_METHOD"] === "POST")
		{
			$factory->getSubscriptionController()->updateSubscription($_POST);
		}
		else
		{
			header("Location: /");
		}
		break;
		
		
	case "/updateprofilepicture":		
		if ($_SERVER["REQUEST_METHOD"] === "POST")
		{
			$factory->getProfileController()->updateProfilePicture($_POST);
			
		}
		else
		{
			header("Location: /");
		}
		break;
		
	case "/search":
		if ($_SERVER["REQUEST_METHOD"] === "GET")
		{
			$factory->getIndexController()->searchUsernames($_GET);
			break;
		}
		header("Location: /");
		break;
		
	case "/feed":
		if ($_SERVER["REQUEST_METHOD"] === "GET")
		{
			$factory->getFeedController()->showFeed();
			break;
		}
		header("Location: /");
		break;
			
	default:
		$matches = [];
		
		// Activate Account
		if (preg_match("|^/activate/account/(.+)/(.+)$|", $_SERVER["REQUEST_URI"], $matches))
		{
			$cnt = $factory->getLoginController()->activateAccount($matches[1], $matches[2]);
			break;
		}
		
		// Reset password
		if (preg_match("|^/reset/password/(.+)/(.+)$|", $_SERVER["REQUEST_URI"], $matches))
		{
			$cnt = $factory->getLoginController();
			if ($_SERVER["REQUEST_METHOD"] === "GET")
			{
				$cnt->showResetPassword($matches[1], $matches[2]);
			}
			else
			{
				$cnt->resetPassword($_POST, $matches[1], $matches[2]);
			}
			header("Location: /");
			break;
		}
		
		// media file
		if (preg_match("|^/media/(.*)/file$|", $_SERVER["REQUEST_URI"], $matches))
		{
			$cnt = $factory->getPictureController()->renderPicture($matches[1]);
			break;
		}		
		
		// find profile of user
		if (preg_match("|^/(.+)$|", $_SERVER["REQUEST_URI"], $matches))
		{
			if (preg_match('/[A-Za-z0-9._]/', $matches[1]))
			{
				$factory->getProfileController()->showProfile($matches[1]);
				break;
			}
			header("Location: /");
			break;
		}
		
		$factory->getIndexController()->homepage();
}











