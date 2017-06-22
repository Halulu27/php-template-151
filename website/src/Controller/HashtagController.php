<?php

namespace halulu27\Controller;

use halulu27\Service\Hashtag\HashtagService;

class HashtagController 
{
  private $template;
  private $hashtagService;
  private $factory;
  
  public function __construct(\Twig_Environment $template, HashtagService $hashtagService, $factory)
  {
     $this->template = $template;
     $this->hashtagService = $hashtagService;
     $this->factory = $factory;
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
  			$postIds .= " OR Id = " . $allPostIds[$i][0];
  		}
  		$allPosts = $this->hashtagService->getAllPosts($postIds);
  		
  		if ($allPosts != false)
  		{
  			for ($i = 0; $i < count($allPosts); $i++)
  			{
  				$allPosts[$i]["likes"] = $this->hashtagService->getLikesNumber($allPosts[$i]["Id"]);
  				$allPosts[$i]["liked"] = $this->hashtagService->getLiked($allPosts[$i]["Id"], $_SESSION["Id"]);
  				$allPosts[$i]["csrf"] = $this->factory->generateCsrf("like" . $allPosts[$i]["Id"] . "csrf");
  				$allPosts[$i]["username"] = $this->hashtagService->getUsername($allPosts[$i]["userId"]);
  				$allHashtagIds = $this->hashtagService->getHashtagIds($allPosts[$i]["Id"]);
  				if ($allHashtagIds != false)
  				{
  					for ($e = 0; $e < count($allHashtagIds); $e++)
  					{
  						$allPosts[$i]["hashtags"][$e] = $this->hashtagService->getHashtagName($allHashtagIds[$e][0]);
  					}
  				}
  			}
  			echo $this->template->render("hashtag.html.twig", ["hashtag" => $hashtagName, "posts" => $allPosts]);
  			return;
  		}
  	}
  	header("Location: /");
  }
}