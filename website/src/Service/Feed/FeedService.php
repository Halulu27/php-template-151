<?php 

namespace halulu27\Service\Feed;

interface FeedService
{
	public function getPosts($allUser);
	
	public function getUsername($userId);
	
	public function getSubscriptions($userId);
	
	public function getHashtagIds($postId);
	
	public function getHashtagName($hashtagId);
}