<?php

namespace halulu27\Service\Post;

class PostPdoService implements PostService
{
	private $pdo;
	
	public function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
	}
	
	public function savePost($userId, $mediaId, $comment)
	{
		$timestamp = date('Y-m-d H:i:s');
		$stmt = $this->pdo->prepare("INSERT INTO post (userId, mediaId, comment, uploadTime) VALUES (?, ?, ?, ?);");
		$stmt->bindValue(1, $userId);
		$stmt->bindValue(2, $mediaId);
		$stmt->bindValue(3, $comment);
		$stmt->bindValue(4, $timestamp);
		return $stmt->execute();
	}
	
	public function getPostId($mediaId, $userId)
	{
		$stmt = $this->pdo->prepare("SELECT Id FROM post WHERE mediaId=? AND userId=?;");
		$stmt->bindValue(1, $mediaId);
		$stmt->bindValue(2, $userId);
		$stmt->execute();
		if ($stmt->rowCount() == 1)
		{
			$result = $stmt->fetch();
			return $result["Id"];
		}
		return false;
	}
	
	public function getPostMediaId($postId)
	{
		$stmt = $this->pdo->prepare("SELECT mediaId FROM post WHERE Id=?;");
		$stmt->bindValue(1, $postId);
		$stmt->execute();
		if ($stmt->rowCount() == 1)
		{
			$result = $stmt->fetch();
			return $result[0][0];
		}
		return false;		
	}
	
	public function deleteLikes($postId)
	{
		$stmt = $this->pdo->prepare("DELETE FROM `like` WHERE postId=?;");
		$stmt->bindValue(1, $postId);
		return $stmt->execute();
	}
	
	public function deletePost($Id)
	{
		$stmt = $this->pdo->prepare("DELETE FROM post WHERE Id=?;");
		$stmt->bindValue(1, $Id);
		if ($stmt->execute())
		{
			return true;
		}
		return false;
	}
	
	public function saveMedia($content, $type, $mediaTimeStamp, $userId)
	{
		$stmt = $this->pdo->prepare("INSERT INTO media (userId, uploadTime, type, content) VALUES (?, ?, ?, ?);");
		$stmt->bindValue(1, $userId);
		$stmt->bindValue(2, $mediaTimeStamp);
		$stmt->bindValue(3, $type);
		$stmt->bindValue(4, $content);
		return $stmt->execute();
	}
	
	public function getMediaId($userId, $timeStamp)
	{
		$stmt = $this->pdo->prepare("SELECT Id FROM media WHERE uploadTime=? AND userId=?;");
		$stmt->bindValue(1, $timeStamp);
		$stmt->bindValue(2, $userId);
		$stmt->execute();
		if ($stmt->rowCount() == 1)
		{
			$result = $stmt->fetch();
			return $result["Id"];			
		}
		return false;
	}
	
	public function deleteMedia($Id)
	{
		$stmt = $this->pdo->prepare("DELETE FROM media WHERE Id=?;");
		$stmt->bindValue(1, $Id);
		if ($stmt->execute())
		{
			return true;
		}
		return false;
	}
	
	public function saveHashtag($name)
	{
		$stmt = $this->pdo->prepare("INSERT INTO hashtag (name) VALUES (?);");
		$stmt->bindValue(1, $name);
		$stmt->execute();
		if ($stmt->rowCount() == 1)
		{
			return true;
		}
		return false;
	}
	
	public function findHashtagId($name)
	{
		$stmt = $this->pdo->prepare("SELECT * FROM hashtag WHERE name=?;");
		$stmt->bindValue(1, $name);
		$stmt->execute();
		if ($stmt->rowCount() == 1)
		{
			$result = $stmt->fetch();
			return $result["Id"];
		}
		return false;
	}
	
	public function findHashtagName($Id)
	{
		$stmt = $this->pdo->prepare("SELECT Id FROM hashtag WHERE Id=?;");
		$stmt->bindValue(1, $Id);
		$stmt->execute();
		if ($stmt->rowCount() == 1)
		{
			$result = $stmt->fetch();
			return $result["name"];
		}
		return false;
	}
	
	public function saveHashtagPost($hashtagId, $postId)
	{
		$stmt = $this->pdo->prepare("INSERT INTO posthashtag (postId, hashtagId) VALUES (?, ?);");
		$stmt->bindValue(1, $postId);
		$stmt->bindValue(2, $hashtagId);
		if ($stmt->execute())
		{
			return true;
		}
		return false;
	}
	
	public function deleteHashtagPost($hashtagId, $postId)
	{
		$stmt = $this->pdo->prepare("DELETE FROM posthashtag WHERE postId=? AND hashtagId=?;");
		$stmt->bindValue(1, $postId);
		$stmt->bindValue(2, $hashtagId);
		if ($stmt->execute())
		{
			return true;
		}
		return false;
	}
	
	public function findHashtagPosts($postId)
	{
		$stmt = $this->pdo->prepare("SELECT * FROM posthashtag WHERE postId=?;");
		$stmt->bindValue(1, $postId);
		$stmt->execute();
		if ($stmt->rowCount() > 0)
		{
			$result = $stmt->fetch();
			return $result;
		}
		return false;
	}
	
	public function findLike($postId, $userId)
	{
		$stmt = $this->pdo->prepare("SELECT Id FROM `like` WHERE postId=? AND userId=?;");
		$stmt->bindValue(1, $postId);
		$stmt->bindValue(2, $userId);
		$stmt->execute();
		if ($stmt->rowCount() == 1)
		{
			$result = $stmt->fetch();
			return $result[0][0];
		}
		return false;
	}
	
	public function deleteLike($likeId)
	{
		$stmt = $this->pdo->prepare("DELETE FROM `like` WHERE Id=?;");
		$stmt->bindValue(1, $likeId);
		if ($stmt->execute())
		{
			return true;
		}
		return false;
	}
	
	public function saveLike($postId, $userId)
	{
		$stmt = $this->pdo->prepare("INSERT INTO `like` (postId, userId) VALUES (?, ?);");
		$stmt->bindValue(1, $postId);
		$stmt->bindValue(2, $userId);
		return $stmt->execute();
	}
}