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
	
	public function showPost()
	{
		$csrf = $this->factory->generateCsrf("addpost");
		echo $this->template->render("post.html.twig", ["addpostcsrf" => $csrf]);
	}
	
	public function addPost(array $data)
	{
		
	}
}