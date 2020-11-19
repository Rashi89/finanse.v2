<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\RememberedLogin;
use \App\Models\Expense;
use \Core\View;
use \App\Auth;

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
		$income= new Expense($_POST);
		$income->saveExpense();
		$category =User::getAllExpenses($_SESSION['user_id']);
		$payMethod=User::getAllPayMethod($_SESSION['user_id']);
		View::renderTemplate('Expenses/add.html',[
			'user' => $this->user,
			'category' => $category,
			'payMethod'=>$payMethod
		]);	
	}
}