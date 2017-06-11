<?php 

namespace halulu27\Service\Search;

class SearchPdoService implements SearchService
{
	private $pdo;
	
	public function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
	}
	
	public function getMatchingUsernames($username)
	{
		$stmt = $this->pdo->prepare("SELECT username FROM user WHERE username LIKE ?;");
		$stmt->bindValue(1, $username . '%');
		$stmt->execute();
		if ($stmt->rowCount() > 0)
		{
			$data = array();
			$i = 0;
			while ($row = $stmt->fetch($this->pdo::FETCH_NUM, $this->pdo::FETCH_ORI_NEXT))
			{
				$data[$i] = $row[0];
				$i++;
			}
			return $data;
		}
		return false;
	}
	
	public function getMatchingHashtag($hastag)
	{
		$stmt = $this->pdo->prepare("SELECT name FROM hashtag WHERE name LIKE ?;");
		$stmt->bindValue(1, $hastag . '%');
		$stmt->execute();
		if ($stmt->rowCount() > 0)
		{
			$data = array();
			$i = 0;
			while ($row = $stmt->fetch($this->pdo::FETCH_NUM, $this->pdo::FETCH_ORI_NEXT))
			{
				$data[$i] = $row[0];
				$i++;
			}
			return $data;
		}
		return false;
	}
}