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
		if (!isset($_SESSION["isLoggedIn"]))
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
		$account["addSubscriptioncsrf"] = $this->factory->generateCsrf("addSubscriptioncsrf");
		$account["removePostcsrf"] = $this->factory->generateCsrf("removePostcsrf");
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
			$account["removeProfilePicturecsrf"] = $this->factory->generateCsrf("removeProfilePicturecsrf");
		}
		$account["changeProfilePicturecsrf"] = $this->factory->generateCsrf("changeProfilePicture");
		// csrf for adding your profileimage
		$account["addPicturecsrf"] = $this->factory->generateCsrf("addPicturecsrf");

		$account["PostNumber"] = $this->profileService->getPostNumber($userId);
		$account["FollowerNumber"] = $this->profileService->getFollowerNumber($userId);
		$account["SubscriberNumber"] = $this->profileService->getSubscriberNumber($userId);
		
		if ($account["PostNumber"] > 0)
		{
			$allPosts = $this->profileService->getPosts($userId);
			$singlePost = array();
			for ($i = 0; $i < $account["PostNumber"]; $i++)
			{
				$singlePost["Id"] = $allPosts[$i]["Id"];
				$singlePost["mediaId"] = $allPosts[$i]["mediaId"];
				$singlePost["comment"] = $allPosts[$i]["comment"];
				$singlePost["uploadTime"] = $allPosts[$i]["uploadTime"];
				$singlePost["likes"] = $this->profileService->getLikesNumber($allPosts[$i]["Id"]);
				$singlePost["liked"] = $this->profileService->getLiked($singlePost["Id"], $_SESSION["Id"]);
				$allHashtagIds = $this->profileService->getHashtagIds($singlePost["Id"]);
				if ($allHashtagIds != false)
				{
					for ($e = 0; $e < count($allHashtagIds); $e++)
					{
						$singlePost["hashtags"][$e]["name"] = $this->profileService->getHashtagName($allHashtagIds[$e][0]);
					}
				}
				for ($x = 0; $x < count($allHashtagIds); $x++)
				{
					$allHashtagIds[$x] = "0";					
				}
				$account["Posts"][$i] = $singlePost;
			}			
		}
		
		echo $this->template->render("profile.html.twig", ["account" => $account]);
	}
	
	public function updateProfilePicture($data)
	{
		// Only if you are logged in you are allowed to use Socialize!
		if (!isset($_SESSION["isLoggedIn"]))
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