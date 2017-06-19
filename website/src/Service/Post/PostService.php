<?php

namespace halulu27\Service\Post;

interface  PostService
{
	public function savePost($userId, $mediaId, $comment);
	
	public function getPostId($mediaId, $userId);
	
	public function deletePost($Id);
	
	public function deleteLikes($postId);
	
	public function saveMedia($content, $type, $mediaTimeStamp, $userId);
	
	public function getMediaId($userId, $timeStamp);
	
	public function deleteMedia($Id);
	
	public function saveHashtag($name);
	
	public function findHashtagId($name);
	
	public function findHashtagName($Id);
	
	public function saveHashtagPost($hashtagId, $postId);
	
	public function deleteHashtagPost($hashtagId, $postId);
	
	public function findHashtagPosts($postId);
	
	public function getPostMediaId($postId);
	
	public function findLike($postId, $userId);
	
	public function saveLike($postId, $userId);
	
	public function deleteLike($likeId);
}