<?php 

namespace halulu27\Service\Subscription;

interface SubscriptionService
{
	public function getUserId($username);
	
	public function findSubscription($followerId, $userId);
	
	public function addSubscription($followerId, $userId);
	
	public function removeSubscription($subscriptionId);
}