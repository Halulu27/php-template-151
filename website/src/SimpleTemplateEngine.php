<?php

namespace halulu27;

/**
 * Simple Template-Engine which provides arguments into a template,
 * captures the output and returns it to the caller.
 */
class SimpleTemplateEngine 
{
  /**
   * @var string Location of the directory containing the templates
   */
  private $templatePath;
  
  /**
   * @param string $templatePath 
   */
  public function __construct($templatePath) 
  {
    $this->templatePath = $templatePath;
  }
  
  /**
   * Renders a *.html.php file inside the template path
   * @return string
   */
  public function render($template, array $arguments = []) 
  {
    extract($arguments);
    ob_start();
    require($this->templatePath.$template);
    return ob_get_clean();
  }
}
