<?php 

namespace halulu27\Service\Subscription;

class SubscriptionPdoService implements SubscriptionService
{
	private $pdo;
	
	public function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
	}
	
	public function getUserId($username)
	{
		$stmt = $this->pdo->prepare("SELECT Id FROM user WHERE username=?;");
		$stmt->bindValue(1, $username);
		$stmt->execute();
		if ($stmt->rowCount() == 1)
		{
			$result = $stmt->fetch();
			return $result[0][0];
		}
		return false;
	}
	
	public function findSubscription($followerId, $userId)
	{
		$stmt = $this->pdo->prepare("SELECT Id FROM subscription WHERE userId=? AND followerId=?");
		$stmt->bindValue(1, $userId);
		$stmt->bindValue(2, $followerId);
		$stmt->execute();
		if ($stmt->rowCount() >= 1)
		{
			$result = $stmt->fetch();
			return $result["Id"];
		}
		return false;
	}
	
	public function addSubscription($followerId, $userId)
	{
		$stmt = $this->pdo->prepare("INSERT INTO subscription (userId, followerId) VALUES (?, ?);");
		$stmt->bindValue(1, $userId);
		$stmt->bindValue(2, $followerId);
		if ($stmt->execute())
		{
			return true;
		}
		return false;
	}
	
	public function removeSubscription($subscriptionId)
	{
		$stmt = $this->pdo->prepare("DELETE FROM subscription WHERE Id=?;");
		$stmt->bindValue(1, $subscriptionId);
		if ($stmt->execute())
		{
			return true;
		}
		return false;
	}
}