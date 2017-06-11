<?php 

namespace halulu27\Service\Search;

interface SearchService
{
	public function getMatchingUsernames($username);
	
	public function getMatchingHashtag($hastag);
}