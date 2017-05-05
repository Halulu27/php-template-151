<?php 

namespace halulu27\Service\Login;

interface LoginService
{
	public function authenticate($username, $password);
	
	public function registration($username, $email, $password);
	
	public function emailExists($email);
	
	public function usernameExists($username);
}