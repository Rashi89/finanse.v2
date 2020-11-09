<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\RememberedLogin;
use \Core\View;
use \App\Auth;

class Incomes extends Authenticated
{
	protected function before()
	{
		parent::before();
		$this->user = Auth::getUser();
	}
	
	public function addAction()
	{
		View::renderTemplate('Incomes/add.html',[
			'user' => $this->user
		]);		
	}
}