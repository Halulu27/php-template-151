<?php

namespace halulu27\Controller;

use halulu27\Service\Profile\ProfileService;

class ProfileController
{
	private $template;
	private $profileService;
	
	public function __construct(\Twig_Environment $template, ProfileService $profileService)
	{
		$this->template = $template;
		$this->profileService = $profileService;
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
		$account["Posts"] = $this->profileService->getPosts($userId);
		$account["PostNumber"] = $this->profileService->getPostNumber($userId);
		$account["FollowerNumber"] = $this->profileService->getFollowerNumber($userId);
		$account["SubscriberNumber"] = $this->profileService->getSubscriberNumber($userId);
		
		echo $this->template->render("profile.html.twig", ["account" => $account]);
	}
}