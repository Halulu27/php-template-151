<?php

namespace halulu27\Controller;

use halulu27\Service\Hashtag\HashtagService;

class HashtagController 
{
  private $template;
  private $hashtagService;
  
  public function __construct(\Twig_Environment $template, HashtagService $hashtagService)
  {
     $this->template = $template;
     $this->hashtagService = $hashtagService;
  }
  
  public function showHashtag($hashtagName)
  {
	if (!isset($_SESSION["isLoggedIn"]))
	{
		header("Location: /");
		return;
	}
	
  	$hashtagId = $this->hashtagService->getHashtagId($hashtagName);
  	$allPostIds = $this->hashtagService->getPostIds($hashtagId);
  	if ($allPostIds != false)
  	{
  		$endPosts = array();
  		$postIds = "Id = " .$allPostIds[0][0];
  		for ($i = 1; $i < count($allPostIds); $i++)
  		{
  			$postIds .= " OR userId = " . $allPostIds[$i][0];
  		}
  		$allPosts = $this->hashtagService->getAllPosts($postIds);
  		
  		if ($allPosts != false)
  		{
  			for ($i = 0; $i < count($allPosts); $i++)
  			{
  				$allPosts[$i]["username"] = $this->feedService->getUsername($allPosts[$i]["userId"]);
  				$allHashtagIds = $this->feedService->getHashtagIds($allPosts[$i]["Id"]);
  				if ($allHashtagIds != false)
  				{
  					for ($i = 0; $i < count($allHashtagIds); $i++)
  					{
  						$allPosts[$i]["hashtags"][$i] = $this->feedService->getHashtagName($allHashtagIds[$i][0]);
  					}
  				}
  			}
  			echo $this->template->render("hashtag.html.twig", ["hashtag" => $hashtagName, "posts" => $allPosts]);
  			return;
  		}
  	}
  }
}