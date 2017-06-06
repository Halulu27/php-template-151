<?php

namespace halulu27\Service\Picture;

class PicturePdoService implements PictureService
{
	private $pdo;
	
	public function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
	}
	 function getMediaFile($mediaId)
	 {
	 	$stmt = $this->pdo->prepare("SELECT content, type FROM media WHERE Id=?;");
	 	$stmt->bindValue(1, $mediaId);
	 	$stmt->execute();
	 	if ($stmt->rowCount() == 1) 
	 	{
	 		$result = $stmt->fetch();
	 		return $result;
	 	}
	 	return false;
	 }
}