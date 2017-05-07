<?php 

namespace halulu27\Service\Login;

class LoginPdoService implements LoginService
{
	private $pdo;
	public function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
	}
	
	// Checks if username already exists in the database. Used with the AJAX
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
	
	// Checks if Email already exists in the database. Used with the AJAX
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
	
	// Checks if user is activated. Otherwise he is not able to login till he 
	// used the activation email.
	private function userActivated($user)
	{
		$stmt = $this->pdo->prepare("SELECT * FROM user WHERE email=? OR username=? AND active=1");
		$stmt->bindValue(1, $user);
		$stmt->bindValue(2, $user);
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
	
	private function generateActivationString($length = 25)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		$stmt = $this->pdo->prepare("SELECT * FROM user WHERE activationString1=? OR activationString2=? OR resetString1=? OR resetString2=? ;");
		do 
		{
			$randomString = '';
			for ($i = 0; $i < $length; $i++) 
			{
				$randomString .= $characters[rand(0, $charactersLength - 1)];
			}
			$stmt->bindValue(1, $randomString);
			$stmt->bindValue(2, $randomString);
			$stmt->bindValue(3, $randomString);
			$stmt->bindValue(4, $randomString);
			$stmt->execute();
		} while ($stmt->rowCount() != 0);
		return $randomString;
	}
		
	public function activationStringsCorrect($activationString1, $activationString2)
	{
		$stmt = $this->pdo->prepare("SELECT * FROM user WHERE active=0 AND activationString1=? AND activationString2=?;");
		$stmt->bindValue(1, $activationString1);
		$stmt->bindValue(2, $activationString2);
		$stmt->execute();
		
		if ($stmt->rowCount() == 1)
		{
			// Set user to active
			$result = $stmt->fetch();
			$stmt = $this->pdo->prepare("UPDATE user SET active=1, activationString1=null, activationString2=null WHERE Id=?;");
			$stmt->bindValue(1, $result["Id"]);
			$stmt->execute();
			return true;
		}
		else
		{
			return false;
		}		
	}
	
	public function resetStringCorrect($resetString1, $resetString2)
	{
		$stmt = $this->pdo->prepare("SELECT * FROM user WHERE resetString1=? AND resetString2=?;");
		$stmt->bindValue(1, $resetString1);
		$stmt->bindValue(2, $resetString2);
		$stmt->execute();
		
		if ($stmt->rowCount() == 1)
		{
			$result = $stmt->fetch();
			return $result["email"];
		}
		return false;
	}
	
	public function resetPassword($username)
	{
		$resetString1 = $this->generateActivationString();
		$resetString2 = $this->generateActivationString();
		$stmt = $this->pdo->prepare("UPDATE user SET resetString1=?, resetString2=? WHERE username=? OR email=?;");
		$stmt->bindValue(1, $resetString1);
		$stmt->bindValue(2, $resetString2);
		$stmt->bindValue(3, $username);
		$stmt->bindValue(4, $username);
		if ($stmt->execute())
		{
			$stmt = $this->pdo->prepare("SELECT * FROM user WHERE username=? OR email=?;");
			$stmt->bindValue(1, $username);
			$stmt->bindValue(2, $username);
			if ($stmt->execute() AND $stmt->rowCount() == 1)
			{
				$result = $stmt->fetch();
				$link = array();
				$link[0] = $result["email"];
				$link[1] = "http://localhost/reset/password/" . $resetString1 . "/" . $resetString2;
				return $link;
			}
		}
		else
		{
			return false;
		}
	}
	
	public function renewPassword($email, $password)
	{
		$stmt = $this->pdo->prepare("UPDATE user SET resetString1=null, resetString2=null, password=? WHERE email=?;");
		$stmt->bindValue(1, $password);
		$stmt->bindValue(2, $email);
		if ($stmt->execute())
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
		$activationString1 = $this->generateActivationString();
		$activationString2 = $this->generateActivationString();
		$stmt = $this->pdo->prepare("INSERT INTO user (username, email, password, activationString1, activationString2) VALUES (?, ?, ?, ?, ?);");
		$stmt->bindValue(1, $username);
		$stmt->bindValue(2, $email);
		$stmt->bindValue(3, $password);
		$stmt->bindValue(4, $activationString1);
		$stmt->bindValue(5, $activationString2);
		if ($stmt->execute())
		{
			$link = "http://localhost/activate/account/" . $activationString1 . "/" . $activationString2;
			return $link;
		}
		else
		{
			return false;
		}
	}
	
	public function authenticate($user, $password)
	{
		// Checks if users exists and is activated.
		if (!$this->emailExists($user) AND !$this->usernameExists($user))
		{
			return false;
		}
		
		$stmt = $this->pdo->prepare("SELECT * FROM user WHERE (username=? OR email=?) AND password=?");
		$stmt->bindValue(1, $user);
		$stmt->bindValue(2, $user);
		$stmt->bindValue(3, $password);
		$stmt->execute();
		
		if ($stmt->rowCount() == 1)
		{
			$result = $stmt->fetch();
			$_SESSION["email"] = $result["email"];
			$_SESSION["username"] = $result["username"];
			return true;
		}
		else 
		{
			return false;
		}
	}
}



