<?php

namespace halulu27\Controller;

use halulu27\SimpleTemplateEngine;
use halulu27\Service\Login\LoginService;

class LoginController 
{
  /**
   * @var halulu27\SimpleTemplateEngine Template engines to render output
   */
  private $template;
  private $loginService;
  
  /**
   * @param halulu27\SimpleTemplateEngine
   */
  public function __construct(SimpleTemplateEngine $template, LoginService $loginService)
  {
     $this->template = $template;
     $this->loginService = $loginService;
  }
  
  public function showLogin()
  {
  	echo $this->template->render("login.html.php");
  }
  
  public function login(array $data)
  {
  	if(!array_key_exists("email", $data) OR !array_key_exists("password", $data))
  	{
  		$this->showLogin();
  		return;
  	} 	
  	
  	//$stmt = $this->pdo->prepare("SELECT * FROM user WHERE email=? AND password=?");
  	//$stmt->bindValue(1, $data["email"]);
  	//$stmt->bindValue(2, $data["password"]);
  	//$stmt->execute();
  	
  	if($this->loginService->authenticate($data["email"], $data["password"]))
  	{
  		//$_SESSION["email"] = $data["email"];
  		header("Location: /");
  	}
  	else {
  		echo $this->template->render("login.html.php", ["email" => $data["email"]]);
  		echo "Login failed";
  	}
  }
}
