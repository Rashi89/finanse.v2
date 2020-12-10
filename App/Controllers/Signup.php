<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

class Signup extends \Core\Controller
{
	public function newAction()
	{
		View::renderTemplate('Signup/new.html');
	}
	
	public function createAction()
	{
		$user= new User($_POST);
		if($user->save())
		{
			/*aktywacja mailem
			$user->sendActivationEmail();
			$user->upgradeBase();
			$this->redirect('/signup/success');
			//$this->redirect('/signup/activated');*/
			/*bez aktywacji*/
			$user->upgradeBase();
			$this->redirect('/signup/activated');
		}
		else
		{
			View::renderTemplate('Signup/new.html',[
			'user'=>$user
			]);
		}
	}
	
	public function successAction()
	{
		View::renderTemplate('Signup/success.html');
	}
	
	public function activateAction()
	{
		User::activate($this->route_params['token']);
		$this->redirect('/signup/activated');
	}
	
	public function activatedAction()
	{
		View::renderTemplate('Signup/activated.html');
	}
}