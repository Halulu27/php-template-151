<?php 

namespace halulu27\Service\Login;

interface LoginService
{
	public function authenticate($username, $password);
	
	public function registration($username, $email, $password);
	
	public function emailExists($email);
	
	public function usernameExists($username);
	
	public function activationStringsCorrect($activationString1, $activationString2);
	
	public function resetStringCorrect($resetString1, $resetString2);
	
	public function resetPassword($username);
	
	public function renewPassword($email, $password);
	
	public function generateString($length = 25);
}