<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\RememberedLogin;
use \App\Models\Income;
use \App\Models\Expense;
use \App\Models\Bilanses;
use \App\Models\Date;
use \Core\View;
use \App\Auth;
use \App\Flash;

class Bilans extends Authenticated
{
	public $errors=[];
	protected function before()
	{
		parent::before();
		$this->user = Auth::getUser();
	}
	
	public function selectedAction()
	{
		if(isset($_SESSION['user_id']))
		{
		View::renderTemplate('Bilans/selected.html',[
			'user' => $this->user
		]);	

		}
	}
	
	public function showAction()
	{
			if(isset($_SESSION['user_id']))
		{	
			$bilans =new Bilanses($_POST);
			$bilans->showOption();
			
			if($bilans->showOption()==4&&!Date::goodRange($_POST))
			{
				Flash::addMessage('Zły zakres dat!');
				$this->redirect('/Bilans/selected');
			}
			else if($bilans->showOption()==5&&!Date::goodRange($_POST))
			{
			$array = Income::getArray($_SESSION['user_id'],1);
			$sum = Income::getSumAllIncomes($_SESSION['user_id'],1);
			$array_expense = Expense::getArray($_SESSION['user_id'],1);
			$sum_expenses = Expense::getSumAllExpense($_SESSION['user_id'],1);
			$allBilans=$sum-$sum_expenses;
			$info = Bilanses::infoBilans($this->user,$allBilans);
			$allBilans=$allBilans.' zł';
			$tablica_nazw =  Expense::getExpenseBilans($_SESSION['user_id'],1);
			$tablica_wartosci = Expense::getExpenseSum($_SESSION['user_id'],1);
			Flash::addMessage('Zły zakres dat!');
			View::renderTemplate('Bilans/show.html',[
				'user' => $this->user,
				'array'=>$array,
				'sum_incomes'=>$sum,
				'sum_expenses'=>$sum_expenses,
				'array_expense'=>$array_expense,
				'allBilans'=>$allBilans,
				'info'=>$info,
				'nazwy'=>$tablica_nazw,
				'wartosci'=>$tablica_wartosci
				
			]);	
			}
			else
			{
			$array = Income::getArray($_SESSION['user_id'],$bilans->showOption());
			$sum = Income::getSumAllIncomes($_SESSION['user_id'],$bilans->showOption());
			$array_expense = Expense::getArray($_SESSION['user_id'],$bilans->showOption());
			$sum_expenses = Expense::getSumAllExpense($_SESSION['user_id'],$bilans->showOption());
			$allBilans=$sum-$sum_expenses;
			$info = Bilanses::infoBilans($this->user,$allBilans);
			$allBilans=$allBilans.' zł';
			$tablica_nazw =  Expense::getExpenseBilans($_SESSION['user_id'],$bilans->showOption());
			$tablica_wartosci = Expense::getExpenseSum($_SESSION['user_id'],$bilans->showOption());
			View::renderTemplate('Bilans/show.html',[
				'user' => $this->user,
				'array'=>$array,
				'sum_incomes'=>$sum,
				'sum_expenses'=>$sum_expenses,
				'array_expense'=>$array_expense,
				'allBilans'=>$allBilans,
				'info'=>$info,
				'nazwy'=>$tablica_nazw,
				'wartosci'=>$tablica_wartosci
				
			]);	

			}
		}
	
	}
}