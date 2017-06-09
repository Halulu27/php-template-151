<?php

namespace halulu27\Controller;

use halulu27\Service\Picture\PictureService;

class PictureController
{
	private $template;
	private $pictureService;
	
	public function __construct(\Twig_Environment $template, PictureService $pictureService)
	{
		$this->template = $template;
		$this->pictureService = $pictureService;
	}
	
	public function renderPicture($mediaId)
	{
		// Only if you are logged in you are allowed to use Socialize!
		/*
		if (isset($_SESSION["isLoggedIn"]))
		{
			if ($_SESSION["isLoggedIn"] == false || !isset($_SESSION["username"]))
			{
				header("Location: /");
				return;				
			}
		}
		else {
			http_response_code(404);
			header("Location: /");
			return;
		}*/
		
		if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			if(strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) < time() - 600) {
				header('HTTP/1.1 304 Not Modified');
				exit;
			}
		}
		
		$mediaFile = $this->pictureService->getMediaFile($mediaId);
		header("Content-type: " . $mediaFile[1]);
		
		echo $mediaFile[0];
	}
}