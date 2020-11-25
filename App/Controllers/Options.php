<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\RememberedLogin;
use \App\Models\Option;
use \App\Models\Income;
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
	public function showcorrectincomeAction()
	{
		$max_id=Income::getMax($_SESSION['user_id']);
		if(isset($_SESSION['user_id'])&&$max_id>0)
		{		
				//$max_id=Income::getMax($_SESSION['user_id']);
				$presentCategoryID=Income::getCategory($_SESSION['user_id'],$max_id);
				$namePresentCategory=Income::getName($_SESSION['user_id'],$presentCategoryID);
				$prize=Income::getPrize($_SESSION['user_id'],$max_id);
				$date=Income::getData($_SESSION['user_id'],$max_id);
				$comment=Income::getComment($_SESSION['user_id'],$max_id);
				$category =User::getAllIncomes($_SESSION['user_id']);
				View::renderTemplate('Options/correctincome.html',[
				'user' => $this->user,
				'max_id'=>$max_id,
				'present'=>$presentCategoryID,
				'name'=>$namePresentCategory,
				'prize'=>$prize,
				'date'=>$date,
				'comment'=>$comment,
				'category' => $category
					]);	
		}
		else
		{
			$this->redirect('/profile/show');
		}
	}
	public function correctincomeAction()
	{
		if(isset($_SESSION['user_id']))
		{		
				$options = new Option($_POST);
				$options -> saveIncome();
				Flash::addMessage('Wpis został poprawiony!');
				$this->redirect('/options/showcorrectincome');
		}
	}
	
}