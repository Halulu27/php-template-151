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

  public function homepage($usernames = "", $hashtags = "") 
  {
  	echo $this->template->render("index.html.twig", ["usernames" => $usernames, "hashtags" => $hashtags]);
  }
  
  public function search($getData)
  {
	if (!isset($_SESSION["isLoggedIn"]))
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
		if (preg_match("|#([A-Za-z0-9._]+)$|", $getData["searchhashtag"]));
		{
			$hashtags = $this->searchService->getMatchingHashtag($getData["searchhashtag"]);
			$this->homepage("", $hashtags);
			return;			
		}
	}
	$this->homepage();
  }
}
