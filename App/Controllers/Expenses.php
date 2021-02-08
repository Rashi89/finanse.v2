<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\RememberedLogin;
use \App\Models\Expense;
use \Core\View;
use \App\Auth;
use \App\Flash;

class Expenses extends Authenticated
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

		$category =User::getAllExpenses($_SESSION['user_id']);
		$payMethod=User::getAllPayMethod($_SESSION['user_id']);
		View::renderTemplate('Expenses/add.html',[
			'user' => $this->user,
			'category' => $category,
			'payMethod'=>$payMethod
		]);	

		}
	
	}
	
	public function createAction()
	{
		$expense= new Expense($_POST);
		$expense->saveExpense();
		
		$_SESSION['last_expense_id']=Expense::getMax($_SESSION['user_id']);
		
		$category =User::getAllExpenses($_SESSION['user_id']);
		$payMethod=User::getAllPayMethod($_SESSION['user_id']);
		$this->redirect('/profile/show');
	}
	
	public function showAction()
	{
		if(isset($_SESSION['user_id']))
		{
				$category =User::getAllExpenses($_SESSION['user_id']);
				View::renderTemplate('Options/removeexpense.html',[
				'user' => $this->user,
				'category' => $category	
					]);	
		}

	}
	
	public function removeAction()
	{
		$expense= new Expense($_POST);
		
		if($expense->isInne()==true)
		{
			Flash::addMessage('Wybrana kategoria nie została usunięta!');
		}
		else
		{
		$expense->removeLimit();
		$expense->removeExpense();
		Flash::addMessage('Wybrana kategoria została usunięta!');
		
		}
		$this->redirect('/expenses/show');
	}
	
	public function showpaymentAction()
	{
		if(isset($_SESSION['user_id']))
		{
				$category =User::getAllPayMethod($_SESSION['user_id']);
				View::renderTemplate('Options/removepayment.html',[
				'user' => $this->user,
				'category' => $category
			
					]);	
		}
	}
	public function removepaymentAction()
	{
		$expense= new Expense($_POST);
		
		if($expense->isPaymentInne()==true)
		{
		Flash::addMessage('Wybrana kategoria nie została usunięta!');
		}
		else
		{
		$expense->removePayment();
		Flash::addMessage('Wybrana kategoria została usunięta!');
		}
		$this->redirect('/expenses/showpayment');
	}
	
	public function limitAction()
	{
		if(isset($_POST['dane'])){
			$category_name = $_POST['dane'];
			$user_id=$_SESSION['user_id'];
			$id_kategory =Expense::getCategoryExpense($user_id,$category_name);
			$kwota = Expense::getLimit($user_id,$id_kategory); 
			if($kwota == false) echo "Brak";
			else 
			{
				$kwota=round($kwota,2);
				echo $kwota;
			}

		}
	}
	
		public function sumaAction()
	{
		if(isset($_POST['dane'])){
			$category_name = $_POST['dane'];
			$user_id=$_SESSION['user_id'];
			$id_kategory =Expense::getCategoryExpense($user_id,$category_name);
			$month=$_POST['month'];
			$year=$_POST['year'];
		    $amount = $_POST['amount'];
			$kwota = Expense::getSumLimit($user_id,$id_kategory,$month,$year);
			$kwota +=$amount;
			if($kwota == false) echo 0;
			else
			{
				$kwota =round($kwota,2);
				echo $kwota;
			}
		}
	}
	
	public function commentAction()
	{
		if(isset($_POST['dane'])){
			$category_name = $_POST['dane'];
			$user_id=$_SESSION['user_id'];
			$id_kategory =Expense::getCategoryExpense($user_id,$category_name);
			$month=$_POST['month'];
			$year=$_POST['year'];
		    $amount = $_POST['amount'];
			$kwota = Expense::getSumLimit($user_id,$id_kategory,$month,$year);
			$kwota +=$amount;
			$limit = Expense::getLimit($user_id,$id_kategory);
			if($limit !=false)
			{
				$roznica = $limit - $kwota;
				$roznica=round($roznica,2);
					echo $roznica;
			}
			else echo "Brak";

		}
	}
	public function spendInfoAction()
	{
		if(isset($_POST['dane'])){
			$category_name = $_POST['dane'];
			$user_id=$_SESSION['user_id'];
			$id_kategory =Expense::getCategoryExpense($user_id,$category_name);
			$month=$_POST['month'];
			$year=$_POST['year'];
		    $amount = $_POST['amount'];
			$kwota = Expense::getSumLimit($user_id,$id_kategory,$month,$year);
			$limit = Expense::getLimit($user_id,$id_kategory);
			if($limit !=false)
			{
				$roznica = $limit - $kwota;
				$roznica=round($roznica,2);
					echo $roznica;
			}
			else echo "Brak";

		}
	}
	public function addlimitsAction()
	{
		if(isset($_SESSION['user_id']))
		{
			$category =User::getOtherExpense($_SESSION['user_id']);
			//if(isset($_POST['myData']))
			//{
				//$user_id=$_SESSION['user_id'];
				//$category_name = $_POST['myData'];
				//$id_kategory =Expense::getCategoryExpense($user_id,$category_name);
				//$limit = Expense::getLimit($user_id,$id_kategory);
				//if($limit!=0){
				//View::renderTemplate('Expenses/addlimits.html',[
				//'user' => $this->user,
				//'category' => $category
					//]);	
				//}
			//}
			if(empty($category))
					{
						$info ='Wszystkie kategorie mają ustalone limity!';
						View::renderTemplate('Expenses/info.html',[
						'user' => $this->user,
						'info'=>$info
							]);	
					}
			else{
						View::renderTemplate('Expenses/addlimits.html',[
						'user' => $this->user,
						'category' => $category
							]);	
						}
			
		}
	}
	public function addlimitAction()
	{
		if(isset($_SESSION['user_id']))
		{
		
		$expense= new Expense($_POST);
		$expense->addLimits();
		Flash::addMessage('Wybrany limit został dodany!');
		$this->redirect('/expenses/addlimits');

		}
	}
	public function deletelimitsAction()
	{
		if(isset($_SESSION['user_id']))
		{
			$category =User::getAllExpensesLimit($_SESSION['user_id']);
			/*if(isset($_POST['myData']))
			{
				$user_id=$_SESSION['user_id'];
				$category_name = $_POST['myData'];
				$id_kategory =Expense::getCategoryExpense($user_id,$category_name);
				$limit = Expense::getLimit($user_id,$id_kategory);
				if($limit!=0){
				View::renderTemplate('Expenses/deletelimits.html',[
				'user' => $this->user,
				'category' => $category
					]);	
				}				
			}*/
			if(empty($category))
					{
						$info ='Żadna kategoria nie ma limitu!';
						View::renderTemplate('Expenses/info.html',[
						'user' => $this->user,
						'info'=>$info
							]);	
					}
			else{
					View::renderTemplate('Expenses/deletelimits.html',[
					'user' => $this->user,
					'category' => $category
						]);	
				}
		}
	}
		public function deleteLimitAction()
	{
		if(isset($_SESSION['user_id']))
		{
			$expense= new Expense($_POST);
			$expense->removeLimit();
			Flash::addMessage('Wybrany limit został usunięty!');
			$this->redirect('/expenses/deletelimits');

		}
	}
	
		public function editlimitsAction()
	{
		if(isset($_SESSION['user_id']))
		{
			$category =User::getAllExpensesLimit($_SESSION['user_id']);
			/*if(isset($_POST['myData']))
			{
				$user_id=$_SESSION['user_id'];
				$category_name = $_POST['myData'];
				$id_kategory =Expense::getCategoryExpense($user_id,$category_name);
				$limit = Expense::getLimit($user_id,$id_kategory);
				if($limit!=0){
				View::renderTemplate('Expenses/editlimits.html',[
				'user' => $this->user,
				'category' => $category
					]);	
				}			
			}*/
			if(empty($category))
					{
						$info ='Żadna kategoria nie ma limitu!';
						View::renderTemplate('Expenses/info.html',[
						'user' => $this->user,
						'info'=>$info
							]);	
					}
			else
					{
					View::renderTemplate('Expenses/editlimits.html',[
					'user' => $this->user,
					'category' => $category,
						]);	
					}
			
		}
	}
	public function editlimitAction()
	{
		if(isset($_SESSION['user_id']))
		{
		
		$expense= new Expense($_POST);
		$expense->addLimits();
		Flash::addMessage('Wybrany limit został edytowany!');
		$category =User::getAllExpensesLimit($_SESSION['user_id']);
		View::renderTemplate('Expenses/editlimits.html',[
			'user' => $this->user,
			'category' => $category
		]);	

		}
	}
}