<?php 

namespace halulu27\Controller;

use halulu27\Service\Subscription\SubscriptionService;

class SubscriptionController
{
	private $template;
	private $subscriptionService;
	
	public function __construct(\Twig_Environment $template, SubscriptionService $subscriptionService)
	{
		$this->template = $template;
		$this->subscriptionService = $subscriptionService;
	}
	
	public function updateSubscription($data)
	{
		// Only if you are logged in you are allowed to use Socialize!
		if (!isset($_SESSION["isLoggedIn"]))
		{
			header("Location: /");
			return;
		}
		
		if (!array_key_exists("addSubscriptioncsrf", $data) && !isset($data["addSubscriptioncsrf"]) && trim($data["addSubscriptioncsrf"]) == '' && $_SESSION["addSubscriptioncsrf"] != $data["addSubscriptioncsrf"])
		{
			header("Location: /");
			return;
		}
		
		if (!array_key_exists("target", $data))
		{
			header("Location: /");
			return;
		}
		
		$targetId = $this->subscriptionService->getUserId($data["target"]);
		if ($targetId == $_SESSION["Id"])
		{
			// It is not allowed to follow yourself!
			header("Location: /" . $data["target"] . "/");
			return;
		}
		//echo $this->subscriptionService->findSubscription($targetId, $_SESSION["Id"]);
		if (($subscriptionId = $this->subscriptionService->findSubscription($targetId, $_SESSION["Id"])) != false)
		{
			// The user is already subscribed and wanted to unsubscribe!
			$this->subscriptionService->removeSubscription($subscriptionId);
		}
		else 
		{
			$this->subscriptionService->addSubscription($targetId, $_SESSION["Id"]);
		}
		header("Location: /" . $data["target"]);
	}
}