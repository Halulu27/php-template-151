<?php 

namespace halulu27\Service\Login;

class LoginPdoService implements LoginService
{
	private $pdo;
	public function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
	}
	
	public function usernameExists($username)
	{
		$stmt = $this->pdo->prepare("SELECT * FROM user WHERE username=?");
		$stmt->bindValue(1, $username);
		$stmt->execute();
		
		if ($stmt->rowCount() > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function emailExists($email)
	{
		$stmt = $this->pdo->prepare("SELECT * FROM user WHERE email=?");
		$stmt->bindValue(1, $email);
		$stmt->execute();
		
		if ($stmt->rowCount() > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function registration($username, $email, $password)
	{		
		$stmt = $this->pdo->prepare("INSERT INTO user (username, email, password) VALUES (?, ?, ?);");
		$stmt->bindValue(1, $username);
		$stmt->bindValue(2, $email);
		$stmt->bindValue(3, $password);
		if ($stmt->execute())
		{
			$_SESSION["email"] = $email;
			$_SESSION["username"] = $username;
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function authenticate($username, $password)
	{
		$statement = "";
		if (!$this->emailExists($username) AND !$this->usernameExists($username))
		{
			return false;
		}
		else 
		{
			if ($this->usernameExists($username))
			{
				$statement = "SELECT * FROM user WHERE username=? AND password=?";
			}
			else 
			{
				$statement = "SELECT * FROM user WHERE email=? AND password=?";
			}
		}
		
		$stmt = $this->pdo->prepare($statement);
		$stmt->bindValue(1, $username);
		$stmt->bindValue(2, $password);
		$stmt->execute();
		
		if ($stmt->rowCount() == 1)
		{
			$result = $stmt->fetch();
			$_SESSION["email"] = $result["email"][0];
			$_SESSION["username"] = $result["username"][0];
			return true;
		}
		else {
			return false;
		}
	}
}



