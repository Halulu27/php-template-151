<?php

namespace halulu27\Controller;

use halulu27\Service\Login\LoginService;
use Swift_Message;

class LoginController 
{
  private $template;
  private $loginService;
  private $factory;

  public function __construct(\Twig_Environment $template, LoginService $loginService, $factory)
  {
    $this->template = $template;
    $this->loginService = $loginService;
  	$this->factory = $factory;
  }
  
  public function showRegister($email = "", $username = "", $errormessage = "", $confirmation = false)
  {
	if (isset($_SESSION["isLoggedIn"]))
	{
		header("Location: /");
		return;
	}	
  	$csrf = $this->factory->generateCsrf("registercsrf");
  	echo $this->template->render("register.html.twig", ["registercsrf" => $csrf, "email" => $email, "username" => $username, "errormessage" => $errormessage, "confirm" => $confirmation]);
  }
  
  public function showLogin($username = "", $errormessage = "")
  {
	if (isset($_SESSION["isLoggedIn"]))
	{
		header("Location: /");
		return;
	}
  	
  	session_regenerate_id();
  	$csrf = $this->factory->generateCsrf("logincsrf");
  	echo $this->template->render("login.html.twig", ["logincsrf" => $csrf, "username" => $username, "errormessage" => $errormessage]);
  }
  
  public function showPassword($reset = false)
  {
  	$csrf = $this->factory->generateCsrf("passwordcsrf");
  	echo $this->template->render("password.html.twig", ["passwordcsrf" => $csrf, "reset" => $reset]);
  }
  
  public function showResetPassword($resetString1, $resetString2, $errormessage = "")
  {
  	$csrf = $this->factory->generateCsrf("showpasswordcsrf");
  	echo $this->template->render("resetpassword.html.twig", ["resetString1" => $resetString1, "resetString2" => $resetString2, "errormessage" => $errormessage, "showpasswordcsrf" => $csrf]);
  }
  
  public function checkEmail($email)
  {
  	if ($this->loginService->emailExists($email))
  	{
  		echo true;
  	}
  	else
  	{
  		echo false;
  	}
  }
  
  public function checkUsername($username)
  {
  	if ($this->loginService->usernameExists($username))
  	{
  		echo true;
  	}
  	else
  	{
  		echo false;
  	}
  }
  
  public function activateAccount($activationString1, $activationString2)
  {
  	if ($this->loginService->userActivation($activationString1, $activationString2))
  	{
  		$this->showLogin();
  	}
  	else
  	{
  		header("Location: /");
  	}
  }

  public function checkResetString($resetString1, $resetString2)
  {
  	 if (($email = $this->loginService->resetStringCorrect($resetString1, $resetString2)) != false)
  	 {
  	 	return $email;
  	 }
  	 else
  	 {
  	 	return false;
  	 }
  }
  
  public function resetPassword(array $data, $resetString1, $resetString2)
  {
  	$email = $cnt->checkResetString($resetString1, $resetString2);
  	if ($email == false)
  	{
  		header("Location: /");
  		return;
  	}
  	
  	if (!array_key_exists("password", $data) OR !array_key_exists("confirmpassword", $data))
  	{
  		$this->showResetPassword($resetString1, $resetString2);
  		return;
  	}
  	if (!array_key_exists("showpasswordcsrf", $data) && !isset($data["showpasswordcsrf"]) && trim($data["showpasswordcsrf"]) == '' && $_SESSION["showpasswordcsrf"] != $data["showpasswordcsrf"])
  	{
  		$this->homepage();
  		return;
  	}
  	
  	if (!isset($data["password"]) || trim($data["password"]) == '')
  	{
  		$this->showResetPassword($resetString1, $resetString2, "password is invalid");
  		return;
  	}
  	elseif (!isset($data["confirmpassword"]) || trim($data["confirmpassword"]) == '')
  	{
  		$this->showResetPassword($resetString1, $resetString2, "password is invalid");
  		return;
  	}
  	elseif ($data["password"] != $data["confirmpassword"])
  	{
  		$this->showResetPassword($resetString1, $resetString2, "password is invalid");
  		return;
  	}
  	
  	$passwordhash = password_hash($data["password"], PASSWORD_DEFAULT);
  	if ($this->loginService->renewPassword($email, $passwordhash) == true)
  	{
  		$this->showLogin();
  	}
  	else
  	{
  		$this->showResetPassword($resetString1, $resetString2, "");
  		header("Location: /");
  	}
  }
  
  public function register(array $data)
  {
	if (isset($_SESSION["isLoggedIn"]))
	{
		header("Location: /");
		return;
	}
	
  	if (!array_key_exists("registercsrf", $data) && !isset($data["registercsrf"]) && trim($data["registercsrf"]) == '' && $_SESSION["registercsrf"] != $data["registercsrf"])
  	{
  		$this->showRegister();
  		return;
  	}
  	
  	if (!array_key_exists("email", $data) OR !array_key_exists("password", $data) OR !array_key_exists("username", $data))
  	{
  		$this->showRegister();
  		return;
  	}
  	
	// Check if form is filled out.
  	$errormessage = array();
  	if (!isset($data["email"]) || trim($data["email"]) == '')
  	{
  		$errormessage["email"] = "Please enter your email."; 
  	}
  	if (!isset($data["username"]) || trim($data["username"]) == '')
  	{
  		$errormessage["username"] = "\nPlease enter your username";
  	}
  	if (!isset($data["password"]) || trim($data["password"]) == '')
  	{
  		$errormessage["password"] = "Please enter your password";
  	}
  	elseif (strlen($data["password"]) < 8)
  	{
  		$errormessage["password"] = "Enter minimum 8 characters.";
  	}
  	elseif (!preg_match('/\d/', $data["password"]))
  	{
  		$errormessage["password"] = "Enter minimum 1 number.";
  	}
  	elseif (!preg_match('/[a-z]/', $data["password"]))
  	{
  		$errormessage["password"] = "Enter minimum 1 lower letter a-z.";
  	}
  	if ($this->loginService->usernameExists($data["username"]))
  	{
  		$errormessage["username"] = "Username existiert bereits.";
  	}
  	elseif (preg_match('/[^A-Za-z0-9._]/', $data["username"]))
  	{
  		$errormessage["username"] = "Username can only contain numbers, digits, . and _";
  	}
  	// some usernames cannot be used e.g. "index", "login", etc.
  	elseif ($data["username"] == "index" OR $data["username"] == "login" OR $data["username"] == "logout" OR $data["username"] == "register" OR $data["username"] == "addpost" OR $data["username"] == "password" OR $data["username"] == "updateSubscription" OR $data["username"] == "password" OR $data["username"] == "updateprofilepicture" OR $data["username"] == "search" OR $data["username"] == "feed" OR $data["username"] == "like" OR $data["username"] == "editPost" OR $data["username"] == "deletePost")
  	{
  		$errormessage["username"] = "This username cannot be used!";
  	}
  	if ($this->loginService->emailExists($data["email"]))
  	{
  		$errormessage["email"] = "Email existiert bereits";
  	}
  	elseif (filter_var($data["email"], FILTER_VALIDATE_EMAIL) == false)
  	{
  		$errormessage["email"] = "Email is invalid";
  	}
  	if (isset($errormessage["username"]) OR isset($errormessage["email"]) OR isset($errormessage["password"]))
  	{
	  	$this->showRegister($data["email"], $data["username"], $errormessage);
	  	return;  		
  	}

  	$passwordhash = password_hash($data["password"], PASSWORD_DEFAULT);
  	$activationString1 = $this->generateLink();
  	$activationString2 = $this->generateLink();  	
  	if ($this->loginService->registration($data["username"], $data["email"], $passwordhash, $activationString1, $activationString2) == true)
  	{
  		$link = "http://localhost/activate/account/" . $activationString1 . "/" . $activationString2;
  		$message = "<h1>Hi " . $data["username"] . '</h1><div><p>Please use the following link to activate your account.
			If you have not created a new account you can ignore this email.</p>
					<a href="' . $link . '">' . $link .'</a></div>';
  		$this->sendEmail("Activate your account", $data["email"], $message);
  		$this->showRegister("", "", "", true);
  	}
  	$errormessage["email"] = "Failed hard";
  	$this->showRegister($data["email"], $data["username"], $errormessage);
  	return;
  }
  
  public function login(array $data)
  {
	if (isset($_SESSION["isLoggedIn"]))
	{
		header("Location: /");
		return;
	}
	
  	if (!array_key_exists("logincsrf", $data) && !isset($data["logincsrf"]) && trim($data["logincsrf"]) == '' && $_SESSION["logincsrf"] != $data["logincsrf"])
  	{
  		$this->showLogin();
  		return;
  	}
  	
  	if (!array_key_exists("username", $data) OR !array_key_exists("password", $data))
  	{
  		$this->showLogin();
  		return;
  	}
  	
  	// Is the user activated?
  	if (!$this->loginService->userActivated($data["username"]))
  	{
  		$this->showLogin();
  		return;
  	}
  	
	// Check if form is filled out.
  	$errormessage = "";
  	if (!isset($data["username"]) || trim($data["username"]) == '')
  	{
  		$errormessage = "Please enter your username or email";
  	}
  	if (!isset($data["password"]) || trim($data["password"]) == '')
  	{
  		$errormessage .= "<br>Please enter your password";
  	}
  	if (!$this->loginService->emailExists($data["username"]) AND !$this->loginService->usernameExists($data["username"]))
  	{
  		$errormessage .= "Username or password is wrong!";
  	}
  	if ($errormessage != "")
  	{
	  	$this->showLogin($data["username"], $errormessage);
	  	return;  		
  	}  	
  	
  	if (($result = $this->loginService->authenticate($data["username"], $data["password"])) != false)
  	{
  		if (password_verify($data["password"], $result["password"]))
  		{
	  		session_regenerate_id();
			$_SESSION["email"] = $result["email"];
			$_SESSION["username"] = $result["username"];
			$_SESSION["Id"] = $result["Id"];
			$_SESSION["isLoggedIn"] = true;
	  		header("Location: /");
	  		return;
  		}
  	}
  	$this->showLogin($data["username"], "Login failed");
  }
  
  public function logout(array $data)
  {
  	// implement csrf security
	if (!isset($_SESSION["isLoggedIn"]))
	{
		header("Location: /");
		return;
	}  	
  	session_destroy();
	header("Location: /");
  }
  
  public function password(array $data)
  {
	if (!array_key_exists("passwordcsrf", $data) && !isset($data["passwordcsrf"]) && trim($data["passwordcsrf"]) == '' && $_SESSION["passwordcsrf"] != $data["passwordcsrf"])
	{
		return false;
	}	
  	else if (!array_key_exists("username", $data))
  	{
  		return false;
  	}
  	// Check if form is filled out.
  	else if (!isset($data["username"]) || trim($data["username"]) == '')
  	{
  		return false;
  	}  	

  	$resetString1 = $this->generateLink();
  	$resetString2 = $this->generateLink();
  	if (($email = $this->loginService->resetPassword($data["username"], $resetString1, $resetString2)) != false)
  	{
  		$link = "http://localhost/reset/password/" . $resetString1 . "/" . $resetString2;
  		$message = '<h1>Hi</h1><div><p>Please use the following link to reset your account.
							If you have reset your password you can ignore this email.</p>
							<a href="' . $link . '">' . $link .'</a></div>';
  		$this->sendEmail("Reset password", $email, $message);
  		$this->showPassword(true);
  	}
  	$this->showPassword();
  }
	  
  public function generateLink($length = 25)
  {
	  $randomString = '';
	  do
	  {
	  	$randomString = $this->factory->generateString($length);
	  	$result = $this->loginService->linkExists($randomString);
	  } while ($result == true);
	  return $randomString;
  }
  
  private function sendEmail($subject, $email, $message)
  {
  	$this->factory->getMailer()->send(
  			Swift_Message::newInstance("Socialize - " . $subject)
  			->setFrom(["socializeag@gmail.com" => "Socialize"])
  			->setTo($email)
  			->setContentType("text/html")
  			->setBody($message)
  			);
  }
}





