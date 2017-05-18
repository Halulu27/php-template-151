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
  	echo $this->template->render("register.html.twig", ["registercsrf" => $csrf, "email" => $email, "username" => $username, "errormessage" => $errormessage, "confirm" => $confirmation]);
  }
  
  public function showLogin($username = "", $errormessage = "")
  {
  	$csrf = $this->generateCsrf("login");
  	echo $this->template->render("login.html.twig", ["logincsrf" => $csrf, "username" => $username, "errormessage" => $errormessage]);
  }
  
  public function showPassword($reset = false)
  {
  	$csrf = $this->generateCsrf("password");
  	echo $this->template->render("password.html.twig", ["passwordcsrf" => $csrf, "reset" => $reset]);
  }
  
  public function showResetPassword($resetString1, $resetString2, $errormessage = "")
  {
  	echo $this->template->render("resetpassword.html.twig", ["resetString1" => $resetString1, "resetString2" => $resetString2, "errormessage" => $errormessage]);
  }
  
  public function generateCsrf($csrfName)
  {
  	$csrf = $this->loginService->generateString(50);
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
  	if ($this->loginService->activationStringsCorrect($activationString1, $activationString2))
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
  	
  	if ($this->loginService->renewPassword($email, $data["password"]) == true)
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
  	if ($errormessage != "")
  	{
	  	$this->showRegister($data["email"], $data["username"], $errormessage);
	  	return;  		
  	}
  	
  	// Kompakter machen  	
  	if ($this->loginService->usernameExists($data["username"]))
  	{
  		$this->showRegister($data["email"], $data["username"], "Username existiert bereits.");
  		return;
  	}
  	elseif (preg_match('/[^A-Za-z0-9._]/', $data["username"]))
  	{
  		$this->showRegister($data["email"], $data["username"], "\r\nUsername can only contain numbers, digits, . and _");
  		return;
  	}
  	
  	if ($this->loginService->emailExists($data["email"]))
  	{
  		$this->showRegister($data["email"], $data["username"], "Email existiert bereits");
  		return;
  	}
  	elseif (filter_var($data["email"], FILTER_VALIDATE_EMAIL) === false)
  	{
  		$this->showRegister($data["email"], $data["username"], "\r\nEmail is invalid");
  		return;
  	}
  	
  	if (($link = $this->loginService->registration($data["username"], $data["email"], $data["password"])) != false)
  	{
  		$message = "<h1>Hi " . $data["username"] . '</h1><div><p>Please use the following link to activate your account.
			If you have not created a new account you can ignore this email.</p>
					<a href="' . $link . '">' . $link .'</a></div>';
  		return $message;
  	}
  	else
  	{
  		echo $this->template->render("register.html.twig", ["email" => $data["email"], "username" => $data["username"]]);
  		echo "Registration failed";
  	}
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
  	if ($errormessage != "")
  	{
	  	$this->showLogin($data["username"], $errormessage);
	  	return;  		
  	}
  	
  	if ($this->loginService->authenticate($data["username"], $data["password"]))
  	{
  		header("Location: /");
  	}  	
  	else 
  	{
  		$this->showLogin($data["username"], "Login failed");
  	}
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
  	
  	if (($link = $this->loginService->resetPassword($data["username"])) != false)
  	{
  		return '<h1>Hi</h1><div><p>Please use the following link to reset your account.
							If you have reset your password you can ignore this email.</p>
							<a href="' . $link[1] . '">' . $link[1] .'</a></div>';
  	}
  	$this->showPassword();
  }
}





