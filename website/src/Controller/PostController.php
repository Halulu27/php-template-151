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
		if (!isset($_SESSION["isLoggedIn"]))
		{
			header("Location: /");
			return;
		}
		
		$this->factory->generateCsrf("addpostcsrf");
		echo $this->template->render("addpost.html.twig", ["comment" => $comment, "hashtag" => $hashtag, "errormessage" => $errormessage]);
	}
	
	public function editPost($postId)
	{
		return;
	}
	
	public function like(array $data)
	{
		if (!isset($_SESSION["isLoggedIn"]))
		{
			header("Location: /");
			return;
		}
		
		if (!array_key_exists("postId", $data) && !isset($data["postId"]))
		{
			header("Location: /");
			return;
		}
		$csrfName = "like" . $data["postId"] . "csrf";
		if (!array_key_exists($csrfName, $data) && !isset($data[$csrfName]) && trim($data[$csrfName]) == '' && $_SESSION[$csrfName] != $data[$csrfName])
		{
			header("Location: /");
			return;
		}
		if (!array_key_exists("returnUrl", $data))
		{
			header("Location: /");
			return;
		}
		
		$likeId = $this->postService->findLike($data["postId"], $_SESSION["Id"]);
		if ($likeId != false)
		{
			$this->postService->deleteLike($likeId);
		}
		else 
		{
			$this->postService->saveLike($data["postId"], $_SESSION["Id"]);
		}
		header("Location: /" . $data["returnUrl"]);
	}
	
	public function savePost(array $data)
	{
		// Only if you are logged in you are allowed to use Socialize!
		if (!isset($_SESSION["isLoggedIn"]))
		{
			header("Location: /");
			return;
		}
		
	  	if (!array_key_exists("addpostcsrf", $_POST) && !isset($_POST["addpostcsrf"]) && trim($_POST["addpostcsrf"]) == '' && $_SESSION["addpostcsrf"] != $_POST["addpostcsrf"])
	  	{
	  		$this->showAddPost("failed");
	  		return;
	  	}
	  	
	  	// check if all needed things are filled out
	  	$errormessage = array();
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
	  				if ($this->postService->findHashtagId($hashtagresult[1]) == false)
	  				{
	  					// If the Hashtag does not exist
	  					$this->postService->saveHashtag($hashtagresult[1]);
	  				}
	  				$hashtagId[$i] = $this->postService->findHashtagId($hashtagresult[1]);
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
	
	public function removePost($data, $postId)
	{
		if (!array_key_exists("removePostcsrf", $data) && !isset($data["removePostcsrf"]) && trim($data["removePostcsrf"]) == '' && $_SESSION["removePostcsrf"] != $data["removePostcsrf"])
		{
			header("Location: /");
			return;
		}
		// Only if you are logged in you are allowed to use Socialize!
		if (!isset($_SESSION["isLoggedIn"]))
		{
			header("Location: /");
			return;
		}
		
		if (!isset($postId))
		{
			header("Location: /");
			return;
		}
		
		$hashtagPostIds = $this->postService->findHashtagPosts($postId);
		for ($i = 0; $i < count($hashtagPostIds); $i++)
		{
			$this->postService->deleteHashtagPost($hashtagPostIds[$i], $postId);			
		}
		$this->postService->deleteMedia($this->postService->getPostMediaId($postId));
		$this->postService->deleteLikes($postId);
		$this->postService->deletePost($postId);
		header("Location: /" . $_SESSION["username"]);
		return;
	}
}






