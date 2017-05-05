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
  
  public function showRegister($email = "", $username = "", $errormessage = "")
  {
  	echo $this->template->render("register.html.twig", ["email" => $email, "username" => $username, "errormessage" => $errormessage]);
  }
  
  public function showLogin($username = "", $errormessage = "")
  {
  	echo $this->template->render("login.html.twig", ["username" => $username, "errormessage" => $errormessage]);
  }
  
  public function checkEmail($email)
  {
  	if ($this->loginService->emailExists($email))
  	{
  		echo 1;
  	}
  	else
  	{
  		echo 0;
  	}
  }
  
  public function checkUsername($username)
  {
  	if ($this->loginService->usernameExists($username))
  	{
  		echo 1;
  	}
  	else
  	{
  		echo 0;
  	}
  }
  
  public function register(array $data)
  {
  	if (!array_key_exists("email", $data) OR !array_key_exists("password", $data) OR !array_key_exists("username", $data))
  	{
  		$this->showRegister();
  		return;
  	}
  	
  	// Kompakter machen  	
  	if ($this->loginService->usernameExists($data["username"]))
  	{
  		$this->showRegister($data["email"], $data["username"], "Username existiert bereits.");
  		return;
  	}
  	
  	if ($this->loginService->emailExists($data["email"]))
  	{
  		$this->showRegister($data["email"], $data["username"], "Email existiert bereits");
  		return;
  	}
  	
  	if ($this->loginService->registration($data["username"], $data["email"], $data["password"]))
  	{
  		header("Location: /");
  	}
  	else {
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
}




