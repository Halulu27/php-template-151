<?php

namespace halulu27\Controller;

use halulu27\Service\Post\PostService;

class PostController
{
	private $template;
	private $postService;
	private $factory;
	
	public function __construct(\Twig_Environment $template, PostService $postService, $factory)
	{
		$this->template = $template;
		$this->postService = $postService;
		$this->factory = $factory;
	}
	
	public function showAddPost($comment = "", $hashtag = "", $errormessage = "")
	{
		$csrf = $this->factory->generateCsrf("addpost");
		echo $this->template->render("addpost.html.twig", ["addpostcsrf" => $csrf, "comment" => $comment, "hashtag" => $hashtag, "errormessage" => $errormessage]);
	}
	
	public function savePost(array $data)
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
		
	  	if (!array_key_exists("addpostcsrf", $_POST) && !isset($_POST["addpostcsrf"]) && trim($_POST["addpostcsrf"]) == '' && $_SESSION["addpostcsrf"] != $_POST["addpostcsrf"])
	  	{
	  		$this->showAddPost("failed");
	  		return;
	  	}
	  	/*if (!array_key_exists("comment", $data) OR !array_key_exists("file", $data) OR !array_key_exists("hashtag", $data))
	  	{
	  		$this->showAddPost();
	  		return;
	  	}*/
	  	
	  	// check if all needed things are filled out
	  	$errormessage = array();
	  	/*if (!isset($data["file"]))
	  	{
	  		$errormessage["file"] = "Upload your image or video";
	  	}*/
	  	if ($_FILES['file']['type'] != "image/jpg" && $_FILES['file']['type'] != "image/png" && $_FILES['file']['type'] != "image/jpeg" && $_FILES['file']['type'] != "image/gif"/* && $_FILES['file']['type'] != "file/mp4"*/)
	  	{
	  		$errormessage["file"] = "Only JPG, JPEG, PNG, GIF and MP4 files are allowed!";
	  	}
	  	if (isset($errormessage["file"]))
	  	{
	  		$this->showAddPost($data["comment"], $data["hashtag"], $errormessage);
	  		return;
	  	}
	  	// Save Hashtags
	  	$hashtagId = array();
	  	if ($data["hashtag"] != "")
	  	{
	  		$hashtagId = array();
	  		$hashtag = explode(" ", $data["hashtag"]);
	  		$hashtagresult = [];
	  		for ($i = 0; $i < count($hashtag); $i++)
	  		{
	  			if (preg_match("|#([A-Za-z0-9._]+)$|", $hashtag[$i], $hashtagresult))
	  			{
	  				// hashtag matches the hashtag pattern	  				
	  				if ($this->postService->findHashtagId($hashtagresult[0]) == false)
	  				{
	  					// If the Hashtag does not exist
	  					$this->postService->saveHashtag($hashtagresult[0]);
	  				}
	  				$hashtagId[$i] = $this->postService->findHashtagId($hashtagresult[0]);
	  			}
	  		}
	  	}
	  	
	  	// Save new Post
	  	$content = file_get_contents($_FILES['file']['tmp_name']);
	  	
	  	$timeStamp = date('Y-m-d H:i:s');
	  	if ($this->postService->saveMedia($content, $_FILES["file"]["type"], $timeStamp, $_SESSION["Id"]) != false)
	  	{
	  		$mediaId = $this->postService->getMediaId($_SESSION["Id"], $timeStamp);
	  		
	  		if ($this->postService->savePost($_SESSION["Id"], $mediaId, $data["comment"]) != false)
	  		{
	  			// Save PostHashtag
	  			$postId = $this->postService->getPostId($mediaId, $_SESSION["Id"]);
	  			for ($i = 0; $i < count($hashtagId); $i++)
	  			{
	  				$this->postService->saveHashtagPost($hashtagId[$i], $postId);
	  			}
		  		header("Location: /" . $_SESSION["username"]);
		  		return;	  			
	  		}
	  	}
	  	$this->showAddPost($data["comment"], $data["hashtag"]);
	}
	
	public function removePost($postId)
	{
		/* if (!array_key_exists("addpostcsrf", $_POST) && !isset($_POST["addpostcsrf"]) && trim($_POST["addpostcsrf"]) == '' && $_SESSION["addpostcsrf"] != $_POST["addpostcsrf"])
		{
			$this->showAddPost();
			return;
		} */
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
		
		if (!isset($postId))
		{
			header("Location: /");
			return;
		}
		
	}
}






