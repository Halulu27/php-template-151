<?php 

namespace halulu27\Service\Profile;

interface ProfileService
{
	public function getPosts($userId);
	
	public function getUserId($username);
	
	public function getFollowerNumber($userId);
	
	public function getSubscriberNumber($userId);
	
	public function getPostNumber($userId);
	
	public function getUser($userId);
	
	public function getMedia($mediaId);
	
	public function isSubscribed($targetId, $userId);
	
	public function addProfilMediaId($userId, $mediaId);
	
	public function deleteMedia($mediaId);
	
	public function saveMedia($content, $type, $timeStamp, $userId);
	
	public function getMediaId($userId, $timeStamp);
	
	public function getHashtagIds($postId);
	
	public function getHashtagName($hashtagId);
	
	public function getLikesNumber($postId);
	
	public function getLiked($postId, $userId);
}