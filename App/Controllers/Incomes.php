<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\RememberedLogin;
use \App\Models\Income;
use \Core\View;
use \App\Auth;
use \App\Flash;

class Incomes extends Authenticated
{
	protected function before()
	{
		parent::before();
		$this->user = Auth::getUser();
	}
	
	public function addAction()
	{
		if(isset($_SESSION['user_id']))
		{

		$category =User::getAllIncomes($_SESSION['user_id']);
		View::renderTemplate('Incomes/add.html',[
			'user' => $this->user,
			'category' => $category
		]);	

		}
	}
	
	public function createAction()
	{
		$income= new Income($_POST);
		$income->saveIncome();
		
		$_SESSION['last_income_id']=Income::getMax($_SESSION['user_id']);
	
		$category =User::getAllIncomes($_SESSION['user_id']);
		$this->redirect('/profile/show');
		
	}
	
	public function showincomeAction()
	{
		if(isset($_SESSION['user_id']))
		{
				
				$category =User::getAllIncomes($_SESSION['user_id']);
				View::renderTemplate('Options/removeincome.html',[
				'user' => $this->user,
				'category' => $category
			
					]);	
		}

	}
	public function removeincomeAction()
	{
		$income= new Income($_POST);
		
		//$income->isInne();
		if($income->isInne()==true)
		{
		Flash::addMessage('Wybrana kategoria nie została usunięta!');
		$this->redirect('/incomes/showincome');
		}
		else{
		$income->removeIncome();
		Flash::addMessage('Wybrana kategoria została usunięta!');
		$this->redirect('/incomes/showincome');
		}
	}
	

}