<?php

namespace halulu27\Controller;

use halulu27\Service\Search\SearchService;

class IndexController 
{
  private $template;
  private $searchService;
  
  public function __construct(\Twig_Environment $template, SearchService $searchService)
  {
     $this->template = $template;
     $this->searchService = $searchService;
  }

  public function homepage($usernames = "") 
  {
  	echo $this->template->render("index.html.twig", ["usernames" => $usernames]);
  }
  
  public function searchUsernames($getData)
  {
	if (!isset($_SESSION["isLoggedIn"]))
	{
		$this->homepage();
		return;
	}
	
	if (!isset($getData["username"]) OR preg_match('|[^A-Za-z0-9._]$|', $getData["username"]))
	{
		$this->homepage();
		return;
	}
	
	$usernames = $this->searchService->getMatchingUsernames($getData["username"]);
  	$this->homepage($usernames);
  }
}
