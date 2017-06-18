<?php 

namespace halulu27\Service\Hashtag;

interface HashtagService
{
	public function getHashtagId($hashtagName);
	
	public function getPostIds($hashtagId);
	
	public function getAllPosts($allIds);
	
	public function getUsername($userId);
	
	public function getHashtagIds($postId);
	
	public function getHashtagName($hashtagId);
	
	public function getLikesNumber($postId);
	
	public function getLiked($postId, $userId);
}