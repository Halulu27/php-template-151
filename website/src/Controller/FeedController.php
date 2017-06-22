<?php

namespace halulu27\Controller;

use halulu27\Service\Feed\FeedService;

class FeedController 
{
  private $template;
  private $feedService;
  private $factory;
  
  public function __construct(\Twig_Environment $template, FeedService $feedService, $factory)
  {
     $this->template = $template;
     $this->feedService = $feedService;
     $this->factory = $factory;
  }
  
  public function showFeed()
  {
	if (!isset($_SESSION["isLoggedIn"]))
	{
		header("Location: /");
		return;
	}
	
	$followerIds = $this->feedService->getSubscriptions($_SESSION["Id"]);
	
	if ($followerIds != false)
	{
		$endPosts = array();
		$allFollowerIds = "userId = " .$followerIds[0][0];
		for ($i = 1; $i < count($followerIds); $i++)
		{
			$allFollowerIds .= " OR userId = " . $followerIds[$i][0];
		}
		
		$allPosts = $this->feedService->getPosts($allFollowerIds);
		if ($allPosts != false)
		{
			for ($i = 0; $i < count($allPosts); $i++)
			{
				$allPosts[$i]["csrf"] = $this->factory->generateCsrf("like" . $allPosts[$i]["Id"] . "csrf");
				$allPosts[$i]["username"] = $this->feedService->getUsername($allPosts[$i]["userId"]);
				$allPosts[$i]["likes"] = $this->feedService->getLikesNumber($allPosts[$i]["Id"]);
				$allPosts[$i]["liked"] = $this->feedService->getLiked($allPosts[$i]["Id"], $_SESSION["Id"]);
				$allHashtagIds = $this->feedService->getHashtagIds($allPosts[$i]["Id"]);
				if ($allHashtagIds != false)
				{
					$hashtags = array();
					for ($e = 0; $e < count($allHashtagIds); $e++)
					{
						$hashtags[$e]["name"] = $this->feedService->getHashtagName($allHashtagIds[$e][0]);						
					}
					$allPosts[$i]["hashtags"] = $hashtags;
				}
			}
			echo $this->template->render("feed.html.twig", ["feed" => $allPosts]);
			return;			
		}
	}
	echo $this->template->render("feed.html.twig");
  }
}