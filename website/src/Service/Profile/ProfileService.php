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
}