<?php 

namespace halulu27;

class Factory
{
	private $config;
	public static function createFromInitFile($filename)
	{
		return new Factory(parse_ini_file($filename, true));
	}
	
	private function getTwigEngine()
	{
		$loader = new \Twig_Loader_Filesystem(__DIR__ . "/../templates/");
		$twig = new \Twig_Environment($loader);
		$twig->addGlobal('SESSION', $_SESSION);
		return $twig;
	}
	
	public function __construct(array $config)
	{
		$this->config = $config;
	}
	
	public function getIndexController()
	{
		return new Controller\IndexController($this->getTwigEngine(), $this->getSearchService(), $this);
	}
	
	public function getLoginController()
	{
		return new Controller\LoginController($this->getTwigEngine(), $this->getLoginService(), $this);
	}
	
	public function getProfileController()
	{
		return new Controller\ProfileController($this->getTwigEngine(), $this->getProfileService(), $this);
	}
	
	public function getPostController()
	{
		return new Controller\PostController($this->getTwigEngine(), $this->getPostService(), $this);
	}
	
	public function getPictureController()
	{
		return new Controller\PictureController($this->getTwigEngine(), $this->getPictureService());
	}
	
	public function getSubscriptionController()
	{
		return new Controller\SubscriptionController($this->getTwigEngine(), $this->getSubscriptionService());
	}
	
	public function getFeedController()
	{
		return new Controller\FeedController($this->getTwigEngine(), $this->getFeedService(), $this);
	}
	
	public function getHashtagController()
	{
		return new Controller\HashtagController($this->getTwigEngine(), $this->getHashtagService(), $this);
	}
	
	public function getMailer()
	{
		return \Swift_Mailer::newInstance(
				\Swift_SmtpTransport::newInstance("smtp.gmail.com", 465, "ssl")
				->setUsername("socializeag@gmail.com")
				->setPassword("socializ")
				);
	}
	
	public function getPdo()
	{
		return new \PDO(
				"mysql:host=mariadb;dbname=socialize;charset=utf8",
				$this->config["database"]["user"],
				"my-secret-pw",
				[\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
				);
	}
	
	public function getLoginService()
	{
		return new Service\Login\LoginPdoService($this->getPdo());
	}
	
	public function getProfileService()
	{
		return new Service\Profile\ProfilePdoService($this->getPdo());
	}
	
	public function getPostService()
	{
		return new Service\Post\PostPdoService($this->getPdo());
	}
	
	public function getPictureService()
	{
		return new Service\Picture\PicturePdoService($this->getPdo());
	}
	
	public function getSubscriptionService()
	{
		return new Service\Subscription\SubscriptionPdoService($this->getPdo());
	}
	
	public function getSearchService()
	{
		return new Service\Search\SearchPdoService($this->getPdo());
	}
	
	public function getFeedService()
	{
		return new Service\Feed\FeedPdoService($this->getPdo());
	}
	
	public function getHashtagService()
	{
		return new Service\Hashtag\HashtagPdoService($this->getPdo());
	}

	public function generateCsrf($csrfName)
	{
		$csrf = $this->generateString(50);
		$_SESSION[$csrfName] = $csrf;
		return $csrf;
	}
	
	public function generateString($length)
	{
	  	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	  	$charactersLength = strlen($characters);
	  	$randomString = '';
	  	for ($i = 0; $i < $length; $i++)
	  	{
	  		$randomString .= $characters[rand(0, $charactersLength - 1)];
	  	}
	  	return $randomString;
	}
}