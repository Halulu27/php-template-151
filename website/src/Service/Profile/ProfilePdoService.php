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
		$stmt = $this->pdo->prepare("SELECT Id, mediaId, comment, uploadTime FROM post WHERE userId=? ORDER BY uploadTime DESC;");
		$stmt->bindValue(1, $userId);
		$stmt->execute();
		if ($stmt->rowCount() > 0)
		{
			$data = array();
			$i = 0;
			while ($row = $stmt->fetch($this->pdo::FETCH_NUM, $this->pdo::FETCH_ORI_NEXT)) 
			{
				$data[$i]["Id"] = $row[0];
				$data[$i]["mediaId"] = $row[1];
				$data[$i]["comment"] = $row[2];
				$data[$i]["uploadTime"] = $row[3];
				$i++;
			}
			return $data;
		}
		return false;
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
		$stmt = $this->pdo->prepare("SELECT username, mediaId FROM user WHERE Id=?;");
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
	
	public function getMedia($mediaId)
	{
		$stmt = $this->pdo->prepare("SELECT uploadTime, type, content FROM media WHERE Id=?;");
		$stmt->bindValue(1, $mediaId);
		$stmt->execute();
		if ($stmt->rowCount() == 1)
		{
			$result = $stmt->fetch();
			return $result;
		}
		return false;
	}
	
	public function isSubscribed($targetId, $userId)
	{
		$stmt = $this->pdo->prepare("SELECT Id FROM subscription WHERE followerId=? AND userId=?;");
		$stmt->bindValue(1, $targetId);
		$stmt->bindValue(2, $userId);
		$stmt->execute();
		if ($stmt->rowCount() == 1)
		{
			return 1;
		}
		return 0;
	}
	
	public function addProfilMediaId($userId, $mediaId)
	{
		$stmt = $this->pdo->prepare("UPDATE user SET mediaId=? WHERE Id=?;");
		$stmt->bindValue(1, $mediaId);
		$stmt->bindValue(2, $userId);
		return $stmt->execute();
	}
	
	public function deleteMedia($mediaId)
	{
		$stmt = $this->pdo->prepare("DELETE FROM media WHERE Id=?;");
		$stmt->bindValue(1, $mediaId);
		return $stmt->execute();
	}
	
	public function saveMedia($content, $type, $timeStamp, $userId)
	{
		$stmt = $this->pdo->prepare("INSERT INTO media (userId, uploadTime, type, content) VALUES (?, ?, ?, ?);");
		$stmt->bindValue(1, $userId);
		$stmt->bindValue(2, $timeStamp);
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