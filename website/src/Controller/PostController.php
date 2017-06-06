<?php

namespace halulu27\Controller;

use halulu27\Service\Post\PostService;

class PostController
{
	private $template;
	private $postService;
	
	public function __construct(\Twig_Environment $template, PostService $postService)
	{
		$this->template = $template;
		$this->postService = $postService;
	}
	
	public function showAddPost($comment = "", $hashtag = "", $errormessage = "")
	{
		$csrf = $this->factory->generateCsrf("addpost");
		echo $this->template->render("addpost.html.twig", ["addpostcsrf" => $csrf, "comment" => $comment, "hashtag" => $hashtag, "errormessage" => $errormessage]);
	}
	
	public function savePost(array $data)
	{
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
	  	/*if ($_FILES['file']['type'] != "file/jpg" && $_FILES['file']['type'] != "file/png" && $_FILES['file']['type'] != "file/jpeg" && $_FILES['file']['type'] != "file/gif" && $_FILES['file']['type'] != "file/mp4")
	  	{
	  		$errormessage["file"] = "Only JPG, JPEG, PNG, GIF and MP4 files are allowed!";
	  	}*/
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
	  	$tmpName  = $_FILES['file']['tmp_name'];
	  	$fp = fopen($tmpName, 'r');
	  	$content = fread($fp, filesize($tmpName));
	  	$content = addslashes($content);
	  	fclose($fp);
	  	
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
		
		if (!isset($postId))
		{
			header("Location: /");
			return;
		}
		
	}
}






