<?php 

namespace halulu27\Service\Profile;

class ProfilePdoService implements ProfileService
{
	private $pdo;
	
	public function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
	}
	
	public function getPosts($userId)
	{
		$stmt = $this->pdo->prepare("SELECT * FROM post WHERE userId=?;");
		$stmt->bindValue(1, $userId);
		$stmt->execute();		
		return $stmt->fetch();
	}
	
	public function getUserId($username)
	{
		$stmt = $this->pdo->prepare("SELECT Id FROM user WHERE username=?;");
		$stmt->bindValue(1, $username);
		$stmt->execute();
		if ($stmt->rowCount() == 1)
		{
			$result = $stmt->fetch();
			return $result["Id"];
		}
		return false;
	}
	
	public function getUser($userId)
	{
		$stmt = $this->pdo->prepare("SELECT username FROM user WHERE Id=?;");
		$stmt->bindValue(1, $userId);
		$stmt->execute();
		if ($stmt->rowCount() == 1)
		{
			return $stmt->fetch();
		}
	}
	
	public function getFollowerNumber($userId)
	{
		$stmt = $this->pdo->prepare("SELECT COUNT(followerId) FROM subscription WHERE followerId=?");
		$stmt->bindValue(1, $userId);
		$stmt->execute();
		if ($stmt->rowCount() == 1)
		{
			$result = $stmt->fetch();
			return $result[0];
		}		
	}
	
	public function getSubscriberNumber($userId)
	{
		$stmt = $this->pdo->prepare("SELECT COUNT(userId) FROM subscription WHERE userId=?");
		$stmt->bindValue(1, $userId);
		$stmt->execute();
		if ($stmt->rowCount() == 1)
		{
			$result = $stmt->fetch();
			return $result[0];
		}		
	}
	
	public function getPostNumber($userId)
	{
		$stmt = $this->pdo->prepare("SELECT COUNT(userId) FROM post WHERE userId=?");
		$stmt->bindValue(1, $userId);
		$stmt->execute();
		if ($stmt->rowCount() == 1)
		{
			$result = $stmt->fetch();
			return $result[0];
		}
	}
}