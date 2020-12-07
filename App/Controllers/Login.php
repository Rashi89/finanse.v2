<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;
use \App\Models\Income;
use \App\Models\Expense;

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
			//$_SESSION['last_income_id']=Income::getMax($_SESSION['user_id']);
			//$_SESSION['last_expense_id']=Expense::getMax($_SESSION['user_id']);
			
			//Flash::addMessage('Login successful!');
			

			//pokazuje strone glowna index.html

			//$this->redirect(Auth::getReturnToPage());

			//Auth::getSumExpenses();
			//User::sumExpenses($user);
			//pokazuje strone glowna index.html

			//$this->redirect(Auth::getReturnToPage());
			//View::renderTemplate('Profile/show.html');
			$this->redirect('/profile/show');

			
		} else {
			
			Flash::addMessage('Niepoprawny login lub hasło!', Flash::WARNING);

			View::renderTemplate('Home/index.html',[
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