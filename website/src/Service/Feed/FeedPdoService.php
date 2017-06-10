<?php 

namespace halulu27\Service\Feed;

class FeedPdoService implements FeedService
{
	private $pdo;
	public function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
	}
	
	public function getPosts($allUser)
	{
		$stmt = $this->pdo->prepare("SELECT * FROM post WHERE " . $allUser . " ORDER BY uploadTime DESC;");
		$stmt->execute();
		if ($stmt->rowCount() > 0)
		{
			$data = array();
			$i = 0;
			while ($row = $stmt->fetch($this->pdo::FETCH_NUM, $this->pdo::FETCH_ORI_NEXT))
			{
				$data[$i]["Id"] = $row[0];
				$data[$i]["userId"] = $row[1];
				$data[$i]["mediaId"] = $row[2];
				$data[$i]["comment"] = $row[3];
				$data[$i]["uploadTime"] = $row[4];
				$i++;
			}
			return $data;
		}
		return false;
	}
	
	
	public function getUsername($userId)
	{
		$stmt = $this->pdo->prepare("SELECT username FROM user WHERE Id=?;");
		$stmt->bindValue(1, $userId);
		$stmt->execute();
		if ($stmt->rowCount() == 1)
		{
			$result = $stmt->fetch();
			return $result["username"];
		}
		return false;
	}
	
	public function getSubscriptions($userId)
	{
		$stmt = $this->pdo->prepare("SELECT followerId FROM subscription WHERE userid=?;");
		$stmt->bindValue(1, $userId);
		$stmt->execute();
		if ($stmt->rowCount() > 0)
		{
			$data = array();
			$i = 0;
			while ($row = $stmt->fetch($this->pdo::FETCH_NUM, $this->pdo::FETCH_ORI_NEXT))
			{
				$data[$i][0] = $row[0];
				$i++;
			}
			return $data;
		}
		return false;
	}
}