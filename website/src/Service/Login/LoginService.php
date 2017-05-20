<?php 

namespace halulu27\Service\Login;

interface LoginService
{
	public function authenticate($username, $password);
	
	public function registration($username, $email, $password, $activationString1, $activationString2);
	
	public function emailExists($email);
	
	public function usernameExists($username);
	
	public function userActivated($user);
	
	public function userActivation($activationString1, $activationString2);
	
	public function resetStringCorrect($resetString1, $resetString2);
	
	public function resetPassword($username, $resetString1, $resetString2);
	
	public function renewPassword($email, $password);
	
	public function linkExists($randomString);
}