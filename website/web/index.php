<?php

error_reporting(E_ALL);
session_start();


require_once("../vendor/autoload.php");
$tmpl = new halulu27\SimpleTemplateEngine(__DIR__ . "/../templates/");
$pdo = new \PDO(
		"mysql:host=mariadb;dbname=app;charset=utf8",
		"root",
		"my-secret-pw",
		[\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
		);

switch($_SERVER["REQUEST_URI"]) {
	case "/":
		(new halulu27\Controller\IndexController($tmpl))->homepage();
		break;
	case "/login":
		$cnt = (new halulu27\Controller\LoginController($tmpl, $pdo));
		if ($_SERVER["REQUEST_METHOD"] === "GET")
		{
			$cnt->showLogin();
		}
		else 
		{
			$cnt->login($_POST);
		}
		break;
	//case "/test/upload":
		//if(file_put_contents(__DIR__ . "/../../upload/test.txt", "Mein erster Upload")) {
			//echo "It worked";
		//} else {
			//echo "Error happened";
		//}
		//break;	
	default:
		$matches = [];
		if(preg_match("|^/hello/(.+)$|", $_SERVER["REQUEST_URI"], $matches)) {
			(new halulu27\Controller\IndexController($tmpl))->greet($matches[1]);
			break;
		}
		echo "Not Found";
}

