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
		$category =User::getAllIncomes($_SESSION['user_id']);
		View::renderTemplate('Incomes/add.html',[
			'user' => $this->user,
			'category' => $category
		]);	
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
		$income->removeIncome();
		Flash::addMessage('Wybrana kategoria została usunięta!');
		$this->redirect('/expenses/showpayment');
	}
}