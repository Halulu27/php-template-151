<?php

namespace halulu27\Controller;

use halulu27\Service\Feed\FeedService;

class FeedController 
{
  private $template;
  private $feedService;
  
  public function __construct(\Twig_Environment $template, FeedService $feedService)
  {
     $this->template = $template;
     $this->feedService = $feedService;
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
				$allPosts[$i]["username"] = $this->feedService->getUsername($allPosts[$i]["userId"]);
				$allHashtagIds = $this->feedService->getHashtagIds($allPosts[$i]["Id"]);
				if ($allHashtagIds != false)
				{
					for ($e = 0; $e < count($allHashtagIds); $e++)
					{
						$allPosts[$i]["hashtags"][$e] = $this->feedService->getHashtagName($allHashtagIds[$e][0]);						
					}
				}
			}
			echo $this->template->render("feed.html.twig", ["feed" => $allPosts]);
			return;			
		}
	}
	echo $this->template->render("feed.html.twig");
  }
}