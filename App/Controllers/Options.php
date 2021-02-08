<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\RememberedLogin;
use \App\Models\Option;
use \App\Models\Income;
use \App\Models\Expense;
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
				Flash::addMessage('Taka kategoria już istnieje!',Flash::WARNING);
			}
			else
			{
				$options->addCategoryPayment();
				Flash::addMessage('Kategoria dodana poprawnie!');
	
			}
				View::renderTemplate('Options/addpayment.html',[
				'user' => $this->user
			
					]);
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
				Flash::addMessage('Taka kategoria już istnieje!',Flash::WARNING);
			}
			else
			{
				$options->addCategoryIncome();
				Flash::addMessage('Kategoria dodana poprawnie!');
			}
				View::renderTemplate('Options/addincome.html',[
				'user' => $this->user
			
					]);	
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
				Flash::addMessage('Taka kategoria już istnieje!',Flash::WARNING);
			}
			else
			{
				$options->addCategoryExpense();
				if(isset($_POST['kwota']))
				{
				$options->addLimit();
				Flash::addMessage('Kategoria została dodana!');
				}
			}
				View::renderTemplate('Options/addexpense.html',[
				'user' => $this->user
					]);	

		}
	}
	public function showcorrectincomeAction()
	{
		$exist=Income::existIncome($_SESSION['user_id']);
		if($exist==true){
				$max_id=Income::getMax($_SESSION['user_id']);
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
			$info='Nie ma żadnego wpisu w wydatkach!';
			View::renderTemplate('Incomes/info.html',[
				'user' => $this->user,
				'info'=>$info
					]);	
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
	public function showcorrectexpenseAction()
	{
		$exist=Expense::existExpense($_SESSION['user_id']);
		
		if($exist==true)
		{		
				$max_id=Expense::getMax($_SESSION['user_id']);
				$presentCategoryID=Expense::getCategory($_SESSION['user_id'],$max_id);
				$namePresentCategory=Expense::getName($_SESSION['user_id'],$presentCategoryID);
				$prize=Expense::getPrize($_SESSION['user_id'],$max_id);
				$date=Expense::getData($_SESSION['user_id'],$max_id);
				$comment=Expense::getComment($_SESSION['user_id'],$max_id);
				$category =User::getAllExpenses($_SESSION['user_id']);
				$presentCategoryPaymentID=Expense::getPresentCategoryPayment($_SESSION['user_id'],$max_id);
				$namePresentCategoryPayment=Expense::getNamePayment($_SESSION['user_id'],$presentCategoryPaymentID);
				$payment=User::getAllPayMethod($_SESSION['user_id']);
				View::renderTemplate('Options/correctexpense.html',[
				'user' => $this->user,
				'name'=>$namePresentCategory,
				'prize'=>$prize,
				'date'=>$date,
				'comment'=>$comment,
				'category' => $category,
				'category_payment'=>$payment,
				'namepayment'=>$namePresentCategoryPayment
					]);	
		}
		else
		{
			$info='Nie ma żadnego wpisu w wydatkach!';
			View::renderTemplate('Expenses/info.html',[
				'user' => $this->user,
				'info' =>$info
					]);	
		}
	}
	public function correctexpenseAction()
	{
		if(isset($_SESSION['user_id']))
		{		
				$options = new Option($_POST);
				$options -> saveExpense();
				Flash::addMessage('Wpis został poprawiony!');
				$this->redirect('/options/showcorrectexpense');
		}
	}
	public function showdeleteincomeAction()
	{
		$exist=Income::existIncome($_SESSION['user_id']);
		
		if($exist==true&&isset($_SESSION['last_income_id']))
		{		
				$max_id=Income::getMax($_SESSION['user_id']);
				if($max_id==$_SESSION['last_income_id'])
				{
				$presentCategoryID=Income::getCategory($_SESSION['user_id'],$max_id);
				$namePresentCategory=Income::getName($_SESSION['user_id'],$presentCategoryID);
				$prize=Income::getPrize($_SESSION['user_id'],$max_id);
				$date=Income::getData($_SESSION['user_id'],$max_id);
				$comment=Income::getComment($_SESSION['user_id'],$max_id);

				View::renderTemplate('Options/deletelastincome.html',[
				'user' => $this->user,
				'name'=>$namePresentCategory,
				'prize'=>$prize,
				'date'=>$date,
				'comment'=>$comment
			
		
					]);	
				}
				else
				{
					$info ="Ostatnio nie dodałeś żadnego przychodu!";
					View::renderTemplate('Incomes/info.html',[
					'user' => $this->user,
					'info'=>$info
					]);	
				}
		}
		else
				{
					$info ="Ostatnio nie dodałeś żadnego przychodu!";
					View::renderTemplate('Incomes/info.html',[
					'user' => $this->user,
					'info'=>$info
					]);	
				}
	}
	public function deleteincomeAction()
	{
			
				$max_id=Income::getMax($_SESSION['user_id']);
				$user_id=$_SESSION['user_id'];
				if(Option::deleteIncome($user_id,$max_id))
				{
					$info="Wpis został usunięty!";
				}
				else
				{
					$info="Niestety coś poszło nie tak! Wpis nie został usunięty!";
				}
				View::renderTemplate('Incomes/info.html',[
						'user' => $this->user,
						'info' =>$info
							]);
	}
	public function showdeleteexpenseAction()
	{
		$exist=Expense::existExpense($_SESSION['user_id']);
		
		if($exist==true&&isset($_SESSION['last_expense_id']))
		{		
				$max_id=Expense::getMax($_SESSION['user_id']);
				if($max_id==$_SESSION['last_expense_id'])
				{
				$presentCategoryID=Expense::getCategory($_SESSION['user_id'],$max_id);
				$namePresentCategory=Expense::getName($_SESSION['user_id'],$presentCategoryID);
				$prize=Expense::getPrize($_SESSION['user_id'],$max_id);
				$date=Expense::getData($_SESSION['user_id'],$max_id);
				$comment=Expense::getComment($_SESSION['user_id'],$max_id);
				$category =User::getAllExpenses($_SESSION['user_id']);
				$presentCategoryPaymentID=Expense::getPresentCategoryPayment($_SESSION['user_id'],$max_id);
				$namePresentCategoryPayment=Expense::getNamePayment($_SESSION['user_id'],$presentCategoryPaymentID);
				$payment=User::getAllPayMethod($_SESSION['user_id']);
				View::renderTemplate('Options/deletelastexpense.html',[
				'user' => $this->user,
				'name'=>$namePresentCategory,
				'prize'=>$prize,
				'date'=>$date,
				'comment'=>$comment,
				'category' => $category,
				'category_payment'=>$payment,
				'namepayment'=>$namePresentCategoryPayment
					]);	
				}
				else
				{
					$info ="Ostatnio nie dodałeś żadnego wydatku!";
					View::renderTemplate('Expenses/info.html',[
					'user' => $this->user,
					'info'=>$info
					]);	
				}
		}
		else
				{
					$info ="Ostatnio nie dodałeś żadnego wydatku!";
					View::renderTemplate('Expenses/info.html',[
					'user' => $this->user,
					'info'=>$info
					]);	
				}
	}
	
	public function deleteexpenseAction()
	{
			
				$max_id=Expense::getMax($_SESSION['user_id']);
				$user_id=$_SESSION['user_id'];
				if(Option::deleteExpense($user_id,$max_id))
				{
					$info="Wpis został usunięty!";
				}
				else
				{
					$info="Niestety coś poszło nie tak! Wpis nie został usunięty!";
				}
					View::renderTemplate('Expenses/info.html',[
					'user' => $this->user,
					'info' =>$info
				
					]);	
	}
}