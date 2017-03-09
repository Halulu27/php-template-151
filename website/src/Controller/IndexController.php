<?php

namespace halulu27\Controller;

use halulu27\SimpleTemplateEngine;

class IndexController 
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

  public function homepage() {
    echo "INDEX";
  }

  public function greet($name) {
  	echo $this->template->render("hello.html.php", ["name" => $name]);
  }
}
