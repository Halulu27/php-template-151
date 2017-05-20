<?php

namespace halulu27\Controller;

class IndexController 
{
  private $template;
  
  public function __construct(\Twig_Environment $template)
  {
     $this->template = $template;
  }

  public function homepage() 
  {
  	$user = $this->getUser();
  	echo $this->template->render("index.html.twig", ["user" => $user]);
  }

  public function greet($name) 
  {
  	echo $this->template->render("hello.html.twig", ["name" => $name]);
  }
  
  public function showIndex()
  {
  	//echo $this->template->render("index.html.twig", []);
  	$this->homepage();
  }
}
