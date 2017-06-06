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
		
		$mediaFile = $this->pictureService->getMediaFile($mediaId);
		header("Content-type: " . $mediaFile[1]);
		echo $mediaFile[0];		
	}
}