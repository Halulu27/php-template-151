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
		else if (!isset($_SESSION["isLoggedIn"]))
		{
			header("Location: /");
			return;
		}
		
		$userId = $this->profileService->getUserId($username);
		if ($userId == false)
		{
			header("Location: /");
			return;
		}
		// csrf for subscribing to another user
		$account["addSubscriptioncsrf"] = $this->factory->generateCsrf("addSubscription");
		if ($username != $_SESSION["username"])
		{
			$account["subscribed"] = $this->profileService->isSubscribed($userId, $_SESSION["Id"]);
		}
		$user = $this->profileService->getUser($userId);
		$account["user"] = $user;
		if (isset($user["mediaId"]))
		{
			// get image content;
			//$account[""]		
			// add removeCsrf
			$account["removeProfilePicturecsrf"] = $this->factory->generateCsrf("removeProfilePicture");
		}
		$account["changeProfilePicturecsrf"] = $this->factory->generateCsrf("changeProfilePicture");
		// csrf for adding your profileimage
		$account["addPicturecsrf"] = $this->factory->generateCsrf("addPicture");

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
				$singlePost["postId"] = $allPosts[$i][1];
				$account["Posts"][$i] = $singlePost;
			}			
		}
		
		echo $this->template->render("profile.html.twig", ["account" => $account]);
	}
	
	public function updateProfilePicture($data)
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
		else if (!isset($_SESSION["isLoggedIn"]))
		{
			header("Location: /");
			return;
		}
		
	  	if (!array_key_exists("removeProfilePicturecsrf", $data) && !isset($data["removeProfilePicturecsrf"]) && !array_key_exists("changeProfilePicturecsrf", $data) && !isset($data["changeProfilePicturecsrf"]))
	  	{
	  		header("Location: /");
	  		return;
	  	}
	  	else if (array_key_exists("removeProfilePicturecsrf", $data))
	  	{
	  		if (trim($data["removeProfilePicturecsrf"]) == '' && $_SESSION["removeProfilePicturecsrf"] != $data["removeProfilePicturecsrf"])
	  		{
	  			header("Location: /");
	  			return;
	  		}	  		
	  	}
	  	else if (array_key_exists("changeProfilePicturecsrf", $data))
	  	{
	  		if (trim($data["changeProfilePicturecsrf"]) == '' && $_SESSION["changeProfilePicturecsrf"] != $data["changeProfilePicturecsrf"])
	  		{
	  			header("Location: /");
	  			return;
	  		}  	 
		  	else if (!isset($_FILES["picture"]["type"]))
		  	{
		  		header("Location: /" . $_SESSION["username"]);
		  		return;
		  	}
		  	else if ($_FILES["picture"]["type"] != "image/jpg" && $_FILES["picture"]["type"] != "image/png" && $_FILES["picture"]["type"] != "image/jpeg")
		  	{
		  		header("Location: /" . $_SESSION["username"]);
		  		return;
		  	}
	  	}

	  	$profileMediaId = $this->profileService->getUser($_SESSION["Id"]);
	  	if (isset($profileMediaId[1]))
	  	{
	  		// referrence will be set to null automatically
			$this->profileService->deleteMedia($profileMediaId[1]);
	  	}
	  	if (isset($data["changeProfilePicturecsrf"])) 
	  	{
	  		// upload image and refer to it
	  		$content = file_get_contents($_FILES['picture']['tmp_name']);
	  		
	  		$timeStamp = date('Y-m-d H:i:s');
	  		if ($this->profileService->saveMedia($content, $_FILES["picture"]["type"], $timeStamp, $_SESSION["Id"]) != false)
	  		{
	  			$mediaId = $this->profileService->getMediaId($_SESSION["Id"], $timeStamp);
	  			$this->profileService->addProfilMediaId($_SESSION["Id"], $mediaId);
	  		}
	  	}
		header("Location: /" . $_SESSION["username"]);
		return;	
	}
}