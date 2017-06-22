<?php

namespace halulu27\Controller;

use halulu27\Service\Search\SearchService;

class IndexController 
{
  private $template;
  private $searchService;
  private $factory;
  
  public function __construct(\Twig_Environment $template, SearchService $searchService, $factory)
  {
     $this->template = $template;
     $this->searchService = $searchService;
     $this->factory = $factory;
  }

  public function homepage($usernames = "", $hashtags = "") 
  {
  	$this->factory->generateCsrf("searchcsrf");
  	echo $this->template->render("index.html.twig", ["usernames" => $usernames, "hashtags" => $hashtags]);
  }
  
  public function search($getData)
  {
	if (!isset($_SESSION["isLoggedIn"]))
	{
		$this->homepage();
		return;
	}
	if (!array_key_exists("searchcsrf", $getData) && !isset($getData["searchcsrf"]) && trim($getData["searchcsrf"]) == '' && $_SESSION["searchcsrf"] != $getData["searchcsrf"])
	{
		$this->homepage();
		return;
	}
	if (isset($getData["searchusername"]) AND !preg_match('|[^A-Za-z0-9._]$|', $getData["searchusername"]))
	{
		$usernames = $this->searchService->getMatchingUsernames($getData["searchusername"]);
	  	$this->homepage($usernames);		
		return;
	}
	else if (isset($getData["searchhashtag"]) AND !preg_match('|#[^A-Za-z0-9._]$|', $getData["searchhashtag"]))
	{
		$matches = array();
		if (preg_match("|#([A-Za-z0-9._]+)$|", $getData["searchhashtag"], $matches));
		{
			if (!isset($matches[1]))
			{
				$matches[1] = "";
			}
			$hashtags = $this->searchService->getMatchingHashtag($matches[1]);
			$this->homepage("", $hashtags);
			return;			
		}
	}
	$this->homepage();
  }
}
