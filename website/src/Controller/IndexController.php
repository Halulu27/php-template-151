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
  	echo $this->template->render("index.html.twig", ["session" => $_SESSION["username"]]);
  }

  public function greet($name) 
  {
  	echo $this->template->render("hello.html.twig", ["name" => $name]);
  }
  
  public function showIndex()
  {
  	echo $this->template->render("index.html.twig", []);
  }
}
