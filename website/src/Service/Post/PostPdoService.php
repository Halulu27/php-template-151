<?php

namespace halulu27\Service\Post;

class PostPdoService implements PostService
{
	private $pdo;
	
	public function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
	}
	
	public function addPost()
	{
		return false;
	}
}