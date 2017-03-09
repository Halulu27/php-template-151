<?php

namespace halulu27\Controller;

use halulu27\SimpleTemplateEngine;

class LoginController 
{
  /**
   * @var halulu27\SimpleTemplateEngine Template engines to render output
   */
  private $template;
  
  /**
   * @param halulu27\SimpleTemplateEngine
   */
  public function __construct(SimpleTemplateEngine $template)
  {
     $this->template = $template;
  }
  
  public function showLogin()
  {
  	echo $this->template->render("login.html.php");
  }
}
