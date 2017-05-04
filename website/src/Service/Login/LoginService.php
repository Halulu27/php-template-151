<?php 

namespace halulu27\Service\Login;

interface LoginService
{
	public function authenticate($username, $password);
}