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
			$zakres=$bilans->getOptionName();
			if($bilans->showOption()==4&&!Date::goodRange($_POST))
			{
				Flash::addMessage('Zły zakres dat!');
				$this->redirect('/Bilans/selected');
			}
			else if($bilans->showOption()==5&&!Date::goodRange($_POST))
			{
			$option_number=1;
			$zakres="Bieżący miesiąc";
			Flash::addMessage('Zły zakres dat!');
			}
			else
			{
			$option_number=$bilans->showOption();	
			}	
			$array = Income::getArray($_SESSION['user_id'],$option_number);
			$sum = Income::getSumAllIncomes($_SESSION['user_id'],$option_number);
			$array_expense = Expense::getArray($_SESSION['user_id'],$option_number);
			$sum_expenses = Expense::getSumAllExpense($_SESSION['user_id'],$option_number);
			$tablica_nazw =  Expense::getExpenseBilans($_SESSION['user_id'],$option_number);
			$tablica_wartosci = Expense::getExpenseSum($_SESSION['user_id'],$option_number);
			$allBilans=$sum-$sum_expenses;
			$info = Bilanses::infoBilans($this->user,$allBilans);
			$allBilans=$allBilans.' zł';
				View::renderTemplate('Bilans/show.html',[
				'user' => $this->user,
				'array'=>$array,
				'sum_incomes'=>$sum,
				'sum_expenses'=>$sum_expenses,
				'array_expense'=>$array_expense,
				'allBilans'=>$allBilans,
				'info'=>$info,
				'nazwy'=>$tablica_nazw,
				'wartosci'=>$tablica_wartosci,
				'zakres'=>$zakres		
			]);
		}
	
	}
}