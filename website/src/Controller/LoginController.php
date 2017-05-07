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
  	echo $this->template->render("register.html.twig", ["email" => $email, "username" => $username, "errormessage" => $errormessage, "confirm" => $confirmation]);
  }
  
  public function showLogin($username = "", $errormessage = "")
  {
  	echo $this->template->render("login.html.twig", ["username" => $username, "errormessage" => $errormessage]);
  }
  
  public function showPassword($reset = false)
  {
  	echo $this->template->render("password.html.twig", ["reset" => $reset]);
  }
  
  public function showResetPassword($resetString1, $resetString2)
  {
  	echo $this->template->render("resetpassword.html.twig", ["resetString1" => $resetString1, "resetString2" => $resetString2]);
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
  		$this->showResetPassword($resetString1, $resetString2);
  		return;
  	}
  	elseif (!isset($data["confirmpassword"]) || trim($data["confirmpassword"]) == '')
  	{
  		$this->showResetPassword($resetString1, $resetString2);
  		return;
  	}
  	elseif ($data["password"] != $data["confirmpassword"])
  	{
  		$this->showResetPassword($resetString1, $resetString2);
  		return;
  	}
  	
  	if ($this->loginService->renewPassword($email, $data["password"]) == true)
  	{
  		return true;
  	}
  	else
  	{
  		$this->showResetPassword($resetString1, $resetString2);
  	}
  }
  
  public function register(array $data)
  {
  	if (!array_key_exists("email", $data) OR !array_key_exists("password", $data) OR !array_key_exists("username", $data))
  	{
  		$this->showRegister();
  		return;
  	}
  	
	// Check if form is filled out.
  	$errormessage = "";
  	if (!isset($data["email"]) || trim($data["email"]) == '')
  	{
  		$errormessage .= "Please enter your email."; 
  	}
  	if (!isset($data["username"]) || trim($data["username"]) == '')
  	{
  		$errormessage .= "\nPlease enter your username";
  	}
  	if (!isset($data["password"]) || trim($data["password"]) == '')
  	{
  		$errormessage .= "<br>Please enter your password";
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
  		return $link;
  	}
  	else
  	{
  		echo $this->template->render("register.html.twig", ["email" => $data["email"], "username" => $data["username"]]);
  		echo "Registration failed";
  	}
  }
  
  public function login(array $data)
  {
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
  
  public function password(array $data)
  {
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
  		return $link;
  	}
  	$this->showPassword();
  }
}





