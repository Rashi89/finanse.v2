<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\RememberedLogin;
use \App\Models\Option;
use \Core\View;
use \App\Auth;
use \App\Flash;

class Options extends Authenticated
{
	protected function before()
	{
		parent::before();
		$this->user = Auth::getUser();
	}
	
	public function addpaymentAction()
	{
		if(isset($_SESSION['user_id']))
		{
				View::renderTemplate('Options/addpayment.html',[
				'user' => $this->user
			
					]);	
		}

	}
	public function showpaymentAction()
	{
		if(isset($_SESSION['user_id']))
		{
			$options = new Option($_POST);
			$options->existCategory();
			if($options->existCategory() == false)
			{
				Flash::addMessage('Taka kategoria już istnieje!');
				View::renderTemplate('Options/addpayment.html',[
				'user' => $this->user
			
					]);	
			}
			else
			{
				$options->addCategoryPayment();
				Flash::addMessage('Kategoria dodana poprawnie!');
				View::renderTemplate('Options/addpayment.html',[
				'user' => $this->user
			
					]);	
			}

		}
	}
	
	
	public function addincomeAction()
	{
		if(isset($_SESSION['user_id']))
		{
				View::renderTemplate('Options/addincome.html',[
				'user' => $this->user
			
					]);	
		}

	}
	public function showincomeAction()
	{
		if(isset($_SESSION['user_id']))
		{
			$options = new Option($_POST);
			$options->existCategoryIncome();
			if($options->existCategoryIncome() == false)
			{
				Flash::addMessage('Taka kategoria już istnieje!');
				View::renderTemplate('Options/addincome.html',[
				'user' => $this->user
			
					]);	
			}
			else
			{
				$options->addCategoryIncome();
				Flash::addMessage('Kategoria dodana poprawnie!');
				View::renderTemplate('Options/addincome.html',[
				'user' => $this->user
			
					]);	
			}

		}
	}
	
	public function addexpenseAction()
	{
		if(isset($_SESSION['user_id']))
		{
				View::renderTemplate('Options/addexpense.html',[
				'user' => $this->user
			
					]);	
		}

	}
	public function showexpenseAction()
	{
		if(isset($_SESSION['user_id']))
		{
			$options = new Option($_POST);
			$options->existCategoryExpense();
			if($options->existCategoryExpense() == false)
			{
				Flash::addMessage('Taka kategoria już istnieje!');
				View::renderTemplate('Options/addexpense.html',[
				'user' => $this->user
			
					]);	
			}
			else
			{
				$options->addCategoryExpense();
				Flash::addMessage('Kategoria dodana poprawnie!');
				View::renderTemplate('Options/addexpense.html',[
				'user' => $this->user
			
					]);	
			}

		}
	}
	
	
}