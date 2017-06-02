<?php 

namespace halulu27\Service\Profile;

interface ProfileService
{
	public function getPosts($userId);
	
	public function getUserId($username);
	
	public function getFollowerNumber($userId);
	
	public function getSubscriberNumber($userId);
	
	public function getPostNumber($userId);
}