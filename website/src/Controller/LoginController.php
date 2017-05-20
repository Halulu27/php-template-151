<?php

namespace halulu27\Controller;

use halulu27\Service\Login\LoginService;

class LoginController 
{
  private $template;
  private $loginService;

  public function __construct(\Twig_Environment $template, LoginService $loginService)
  {
     $this->template = $template;
     $this->loginService = $loginService;
  }
  
  public function showRegister($email = "", $username = "", $errormessage = "", $confirmation = false)
  {
  	$csrf = $this->generateCsrf("register");
  	$user = $this->getUser();
  	echo $this->template->render("register.html.twig", ["user" => $user, "registercsrf" => $csrf, "email" => $email, "username" => $username, "errormessage" => $errormessage, "confirm" => $confirmation]);
  }
  
  public function showLogin($username = "", $errormessage = "")
  {
  	session_regenerate_id();
  	$csrf = $this->generateCsrf("login");
  	$user = $this->getUser();
  	echo $this->template->render("login.html.twig", ["user" => $user, "logincsrf" => $csrf, "username" => $username, "errormessage" => $errormessage]);
  }
  
  public function showPassword($reset = false)
  {
  	$csrf = $this->generateCsrf("password");
  	$user = $this->getUser();
  	echo $this->template->render("password.html.twig", ["user" => $user, "passwordcsrf" => $csrf, "reset" => $reset]);
  }
  
  public function showResetPassword($resetString1, $resetString2, $errormessage = "")
  {
  	$user = $this->getUser();
  	echo $this->template->render("resetpassword.html.twig", ["user" => $user, "resetString1" => $resetString1, "resetString2" => $resetString2, "errormessage" => $errormessage]);
  }
  
  private function getUser()
  {
  	$user = array();
  	$user["loggedIn"] = false;
  	if (isset($_SESSION["username"]))
  	{
  		$user["loggedIn"] = true;
  		$user["username"] = $_SESSION["username"];
  	}
  	return $user;
  }
  
  public function generateCsrf($csrfName)
  {
  	$csrf = $this->generateString(50);
  	$_SESSION[$csrfName . "csrf"] = $csrf;
  	return $csrf;
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
  		return true;
  	}
  	else
  	{
  		return false;
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
  
  public function resetPassword($email, array $data, $resetString1, $resetString2)
  {
  	if (!array_key_exists("password", $data) OR !array_key_exists("confirmpassword", $data))
  	{
  		$this->showResetPassword($resetString1, $resetString2);
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
  		return true;
  	}
  	else
  	{
  		$this->showResetPassword($resetString1, $resetString2, "");
  		return false;
  	}
  }
  
  public function register(array $data)
  {
  	if (!array_key_exists("registercsrf", $_POST) && !isset($_POST["registercsrf"]) && trim($_POST["registercsrf"]) == '' && $_SESSION["registercsrf"] != $_POST["registercsrf"])
  	{
  		$cnt->showRegister();
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
  		return $message;
  	}
  	$errormessage["email"] = "Failed hard";
  	$this->showRegister($data["email"], $data["username"], $errormessage);
  	return;
  }
  
  public function login(array $data)
  {
  	if (!array_key_exists("logincsrf", $_POST) && !isset($_POST["logincsrf"]) && trim($_POST["logincsrf"]) == '' && $_SESSION["logincsrf"] != $_POST["logincsrf"])
  	{
  		$cnt->showLogin();
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
	  		header("Location: /");
	  		return;
  		}
  	}
  	$this->showLogin($data["username"], "Login failed");
  }
  
  public function logout(array $data)
  {
  	// implement csrf security
  	session_destroy();
  }
  
  public function password(array $data)
  {
	if (!array_key_exists("passwordcsrf", $_POST) && !isset($_POST["passwordcsrf"]) && trim($_POST["passwordcsrf"]) == '' && $_SESSION["passwordcsrf"] != $_POST["passwordcsrf"])
	{
		$cnt->showPassword();		
	}
	
  	if (!array_key_exists("username", $data))
  	{
  		$this->showPassword();
  		return;
  	}
  	// Check if form is filled out.
  	if (!isset($data["username"]) || trim($data["username"]) == '')
  	{
  		$this->showPassword();
  		return;
  	}  	

  	$resetString1 = $this->generateLink();
  	$resetString2 = $this->generateLink();
  	if (($email = $this->loginService->resetPassword($data["username"])) != false)
  	{
  		$result = array();
  		$result["email"] = $email;
  		$link = "http://localhost/reset/password/" . $resetString1 . "/" . $resetString2;
  		$result["message"] = '<h1>Hi</h1><div><p>Please use the following link to reset your account.
							If you have reset your password you can ignore this email.</p>
							<a href="' . $link . '">' . $link .'</a></div>';
  		return $result;
  	}
  	$this->showPassword();
  }

  public function generateString($length)
  {
  	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  	$charactersLength = strlen($characters);
  	$randomString = '';
  	for ($i = 0; $i < $length; $i++)
  	{
  		$randomString .= $characters[rand(0, $charactersLength - 1)];
  	}
  	return $randomString;
  }
  
  public function generateLink($length = 25)
  {
  	$randomString = '';
  	do
  	{
  		$randomString = $this->generateString($length);
  		$result = $this->loginService->linkExists($randomString);
  	} while ($result == true);
  	return $randomString;
  }
}





