<?php

namespace halulu27\Controller;

use halulu27\Service\Profile\ProfileService;

class ProfileController
{
	private $template;
	private $profileService;
	private $factory;
	
	public function __construct(\Twig_Environment $template, ProfileService $profileService, $factory)
	{
		$this->template = $template;
		$this->profileService = $profileService;
		$this->factory = $factory;
	}
	
	public function showProfile($username)
	{
		// Only if you are logged in you are allowed to use Socialize!
		if (isset($_SESSION["isLoggedIn"]))
		{
			if ($_SESSION["isLoggedIn"] == false || !isset($_SESSION["username"]))
			{
				header("Location: /");
				return;				
			}
		}
		
		$userId = $this->profileService->getUserId($username);
		if ($userId == false)
		{
			header("Location: /");
			return;
		}
		$account["user"] = $this->profileService->getUser($userId);
		
		$account["addSubscriptioncsrf"] = $this->factory->generateCsrf("addSubscription");
		if ($username != $_SESSION["username"])
		{
			$account["subscribed"] = $this->profileService->isSubscribed($userId, $_SESSION["Id"]);
		}

		$account["PostNumber"] = $this->profileService->getPostNumber($userId);
		$account["FollowerNumber"] = $this->profileService->getFollowerNumber($userId);
		$account["SubscriberNumber"] = $this->profileService->getSubscriberNumber($userId);
		
		if ($account["PostNumber"] > 0)
		{
			$allPosts = $this->profileService->getPosts($userId);		
			$singlePost = array();
			for ($i = 0; $i < $account["PostNumber"]; $i++)
			{
				$singlePost["Id"] = $allPosts[$i][0];
				$singlePost["comment"] = $allPosts[$i][2];
				$mediaFile = $this->profileService->getMedia($allPosts[$i][1]);
				$singlePost["uploadTime"] = $mediaFile["uploadTime"];
				$singlePost["type"] = $mediaFile["type"];
				$singlePost["content"] = base64_encode($mediaFile["content"]);
				$account["Posts"][$i] = $singlePost;
			}			
		}
		
		echo $this->template->render("profile.html.twig", ["account" => $account]);
	}
}