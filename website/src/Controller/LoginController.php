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
  
  public function showLogin($email = "")
  {
  	echo $this->template->render("login.html.twig", ["email" => $email]);
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
  	if(!array_key_exists("email", $data) OR !array_key_exists("password", $data) OR !array_key_exists("username", $data))
  	{
  		$this->showRegister($data["email"], $data["username"], "Bitte alles ausfüllen");
  		return;
  	}
  	
  	if ($this->loginService->usernameExists($data["username"]))
  	{
  		$this->showRegister($data["email"], $data["username"], "Username existiert bereits.");
  		return;
  	}
  	
  	if ($this->loginService->emailExists($data["email"]))
  	{
  		$this->showRegister($data["email"], $data["username"], "email existiert bereits");
  		return;
  	}
  	
  	if ($this->loginService->registration($data["username"], $data["email"], $data["password"]))
  	{
  		if ($this->login($data))
  		{
  			return true;
  		}
  		else 
  		{
  			return false;
  		}
  			header("Location: /");
  	}
  	else {
  		echo $this->template->render("register.html.twig", ["email" => $data["email"], "username" => $data["username"]]);
  		echo "Registration failed";
  	}
  }
  
  public function login(array $data)
  {
  	if(!array_key_exists("email", $data) OR !array_key_exists("password", $data))
  	{
  		$this->showLogin($data["email"]);
  		return;
  	}
  	
  	if($this->loginService->authenticate($data["email"], $data["password"]))
  	{
  		header("Location: /");
  	}  	
  	else 
  	{
  		echo $this->template->render("login.html.twig", ["email" => $data["email"]]);
  		echo "Login failed";
  	}
  }
}




