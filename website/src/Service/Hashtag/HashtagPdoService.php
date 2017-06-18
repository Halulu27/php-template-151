<?php 

namespace halulu27\Service\Hashtag;

class HashtagPdoService implements HashtagService
{
	private $pdo;
	public function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
	}
	
	public function getHashtagId($hashtagName)
	{
		$stmt = $this->pdo->prepare("SELECT Id FROM hashtag WHERE name=?;");
		$stmt->bindValue(1, $hashtagName);
		$stmt->execute();
		if ($stmt->rowCount() == 1)
		{
			$result = $stmt->fetch();
			return $result["Id"];
		}
		return false;
	}
	
	public function getPostIds($hashtagId)
	{
		$stmt = $this->pdo->prepare("SELECT postId FROM posthashtag WHERE hashtagId=?;");
		$stmt->bindValue(1, $hashtagId);
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
	
	public function getAllPosts($allIds)
	{
		$stmt = $this->pdo->prepare("SELECT * FROM post WHERE " . $allIds . " ORDER BY uploadTime DESC;");
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
	
	public function getHashtagIds($postId)
	{
		$stmt = $this->pdo->prepare("SELECT hashtagId FROM posthashtag WHERE postId=?;");
		$stmt->bindValue(1, $postId);
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
	
	public function getHashtagName($hashtagId)
	{
		$stmt = $this->pdo->prepare("SELECT name FROM hashtag WHERE Id=?;");
		$stmt->bindValue(1, $hashtagId);
		$stmt->execute();
		if ($stmt->rowCount() == 1)
		{
			$result = $stmt->fetch();
			return $result["name"];
		}
		return false;
	}
	
	public function getLikesNumber($postId)
	{
		$stmt = $this->pdo->prepare("SELECT COUNT(userId) FROM `like` WHERE postId=?;");
		$stmt->bindValue(1, $postId);
		$stmt->execute();
		if ($stmt->rowCount() == 1)
		{
			$result = $stmt->fetch();
			return $result[0];
		}
	}
	
	public function getLiked($postId, $userId)
	{
		$stmt = $this->pdo->prepare("SELECT Id FROM `like` WHERE postId=? AND userId=?;");
		$stmt->bindValue(1, $postId);
		$stmt->bindValue(2, $userId);
		$stmt->execute();
		if ($stmt->rowCount() == 1)
		{
			return true;
		}
		return false;
	}
}