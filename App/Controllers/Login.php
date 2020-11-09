<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;

class Login extends \Core\Controller
{
	public function newAction()
	{
		View::renderTemplate('Login/new.html');
	}
	
	public function createAction()
	{
		$user = User::authenticate($_POST['email'],$_POST['password']);
		
		$remember_me = isset($_POST['remember_me']);
		
		if($user){
			
			Auth::login($user,$remember_me);
			
			Flash::addMessage('Login successful!');
			
<<<<<<< HEAD
			//pokazuje strone glowna index.html

			$this->redirect(Auth::getReturnToPage());
=======
			//Auth::getSumExpenses();
			//User::sumExpenses($user);
			//pokazuje strone glowna index.html

			//$this->redirect(Auth::getReturnToPage());
			View::renderTemplate('Profile/show.html');
>>>>>>> feature/login_signup
			
		} else {
			
			Flash::addMessage('Login unsuccessful, please try again!', Flash::WARNING);
<<<<<<< HEAD
			View::renderTemplate('Login/new.html',[
=======
			View::renderTemplate('Home/index.html',[
>>>>>>> feature/login_signup
						'email'=>$_POST['email'],
						'remember_me'=>$remember_me
			]);
		}
	}
	
	public function destroyAction()
	{
		Auth::logout();
		$this->redirect('/login/show-logout-message');
		

	}
	
	public function showLogoutMessageAction()
	{
		Flash::addMessage('Logout successful!');
		
		$this->redirect('/');
	}
}