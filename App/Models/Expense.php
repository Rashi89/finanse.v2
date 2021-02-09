<?php

namespace App\Models;

use PDO;

class Expense extends \Core\Model
{
	public $errors=[];
	
	public function __construct($data = [])
	{
		foreach($data as $key => $value)
		{
			$this->$key = $value;
		};
	}
	
	public function saveExpense()
	{
		if(empty($this->errors))
		{
			$user_id =$_SESSION['user_id'];
			$max_id = static::getMaxID();
			$expense_category=static::getCategoryExpense($user_id,$this->wybor);
			$payment_category=static::getCategoryPayment($user_id,$this->wyborMethod);

		
		$sql = 'INSERT INTO expenses (id, user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment) VALUES (:id,:user_id,:expense_category_assigned_to_user_id,:payment_method_assigned_to_user_id,:amount, :date_of_expense, :expense_comment)';
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':id', $max_id,PDO::PARAM_INT);
		$stmt->bindValue(':user_id', $user_id,PDO::PARAM_INT);
		$stmt->bindValue(':expense_category_assigned_to_user_id',$expense_category,PDO::PARAM_INT);
		$stmt->bindValue(':payment_method_assigned_to_user_id',$payment_category,PDO::PARAM_INT);
		$stmt->bindValue(':amount', $this->kwota,PDO::PARAM_STR);
		$stmt->bindValue(':date_of_expense', $this->date ,PDO::PARAM_STR);
		$stmt->bindValue(':expense_comment', $this->komentarz ,PDO::PARAM_STR);
		
		return $stmt->execute();
		}
		return false;
	}
	
	
	public static function getCategoryExpense($user_id,$category_name)
	{
		$sql ='SELECT id FROM expenses_category_assigned_to_users WHERE user_id=:user_id AND name=:name';
		$db=static::getDB();
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue(':user_id', $user_id,PDO::PARAM_INT);
		$stmt->bindValue(':name', $category_name,PDO::PARAM_STR);
		$stmt->execute();
		$id_cat=$stmt->fetch(PDO::FETCH_ASSOC);
		$id_kategoria=$id_cat['id'];
		return $id_kategoria;
		
	}
	
	public static function getCategoryPayment($user_id,$category_name)
	{
		$sql ='SELECT id FROM payment_methods_assigned_to_users WHERE user_id=:user_id AND name=:name';
		
		$db=static::getDB();
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue(':user_id', $user_id,PDO::PARAM_INT);
		$stmt->bindValue(':name', $category_name,PDO::PARAM_STR);
		$stmt->execute();
		$id_cat=$stmt->fetch(PDO::FETCH_ASSOC);
		$id_kategoria=$id_cat['id'];
		return $id_kategoria;
		
	}
	
		public static function getMaxID()
	{
		$sql='SELECT COUNT(*) FROM expenses';
		$db=static::getDB();
		$stmt = $db->prepare($sql);
		
		//$stmt->bindValue(':id',$user->id,PDO::PARAM_INT);
		
		$stmt->execute();
		$incomes=$stmt->fetch(PDO::FETCH_ASSOC); 
		//$stmt->rowCount();
		$empty=$incomes['COUNT(*)'];
		if($empty==0)
		{
			//$stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());
			//$expense=$stmt->fetch(PDO::FETCH_ASSOC); 
			//$all_expense=$expense['SUM(amount)'];
			return 1;
		}
		else
		{
		$sql='SELECT MAX(id) FROM expenses';
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());
		$max=$stmt->fetch(PDO::FETCH_ASSOC); 
		$maxID=$max['MAX(id)'];
		return $maxID +1;
		}
	}
	
	public static function getLimitsMaxID()
	{
		$sql='SELECT COUNT(*) FROM limits';
		$db=static::getDB();
		$stmt = $db->prepare($sql);
		
		//$stmt->bindValue(':id',$user->id,PDO::PARAM_INT);
		
		$stmt->execute();
		$incomes=$stmt->fetch(PDO::FETCH_ASSOC); 
		//$stmt->rowCount();
		$empty=$incomes['COUNT(*)'];
		if($empty==0)
		{
			//$stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());
			//$expense=$stmt->fetch(PDO::FETCH_ASSOC); 
			//$all_expense=$expense['SUM(amount)'];
			return 1;
		}
		else
		{
		$sql='SELECT MAX(id) FROM limits';
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());
		$max=$stmt->fetch(PDO::FETCH_ASSOC); 
		$maxID=$max['MAX(id)'];
		return $maxID +1;
		}
	}
	
	public static function getExpenseBilans($user_id,$opcja)
	{
		if($opcja ==1)
		{
			$month=date('n');
			$sql ='SELECT expense_category_assigned_to_user_id,SUM(amount),expenses_category_assigned_to_users.name FROM `expenses` INNER JOIN expenses_category_assigned_to_users ON expenses.expense_category_assigned_to_user_id=expenses_category_assigned_to_users.id WHERE MONTH(date_of_expense)=:month AND expenses.user_id=:user_id GROUP BY expense_category_assigned_to_user_id';
			$db=static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':user_id', $user_id,PDO::PARAM_INT);
			$stmt->bindValue(':month', $month,PDO::PARAM_INT);
			$stmt->execute();
			
			$count = $stmt->rowCount();
			$tablica_nazw = array();
			$i=0;
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
													
													$tablica_nazw[$i] = $row['name'];
													
													$i++;
											}
			return $tablica_nazw;
		}
		else if($opcja==2)
		{
			$month=date('n')-1;
			$year = date('Y');
			if($month == 0)
			{
				$month = 12;
				$year = $year-1;
				
			}
			$sql ='SELECT expense_category_assigned_to_user_id,SUM(amount),expenses_category_assigned_to_users.name FROM `expenses` INNER JOIN expenses_category_assigned_to_users ON expenses.expense_category_assigned_to_user_id=expenses_category_assigned_to_users.id WHERE MONTH(date_of_expense)=:month AND YEAR(date_of_expense)=:year AND expenses.user_id=:user_id GROUP BY expense_category_assigned_to_user_id';
			$db=static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':user_id', $user_id,PDO::PARAM_INT);
			$stmt->bindValue(':month', $month,PDO::PARAM_INT);
			$stmt->bindValue(':year', $year,PDO::PARAM_INT);
			$stmt->execute();
			
			$count = $stmt->rowCount();
			$tablica_nazw = array();
			$i=0;
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
													
													$tablica_nazw[$i] = $row['name'];
													
													$i++;
											}
			return $tablica_nazw;
		}
		else if($opcja==3)
		{
			$year=date('Y');
			$sql ='SELECT expense_category_assigned_to_user_id,SUM(amount),expenses_category_assigned_to_users.name FROM `expenses` INNER JOIN expenses_category_assigned_to_users ON expenses.expense_category_assigned_to_user_id=expenses_category_assigned_to_users.id WHERE YEAR(date_of_expense)=:year AND expenses.user_id=:user_id GROUP BY expense_category_assigned_to_user_id';
			$db=static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':user_id', $user_id,PDO::PARAM_INT);
			$stmt->bindValue(':year', $year,PDO::PARAM_INT);
			$stmt->execute();
			
			$count = $stmt->rowCount();
			$tablica_nazw = array();
			$i=0;
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
													
													$tablica_nazw[$i] = $row['name'];
													
													$i++;
											}
			return $tablica_nazw;
		}
		else if($opcja ==4||$opcja==5)
		{
			$start_date=Date::getStartData($_POST);
			$end_date=Date::getEndData($_POST);
			
			$sql ='SELECT expense_category_assigned_to_user_id,SUM(amount),expenses_category_assigned_to_users.name FROM `expenses` INNER JOIN expenses_category_assigned_to_users ON expenses.expense_category_assigned_to_user_id=expenses_category_assigned_to_users.id WHERE date_of_expense BETWEEN :start_date AND :end_date AND expenses.user_id=:user_id GROUP BY expense_category_assigned_to_user_id';
			$db=static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':user_id', $user_id,PDO::PARAM_INT);
			$stmt->bindValue(':start_date', $start_date,PDO::PARAM_STR);
			$stmt->bindValue(':end_date', $end_date,PDO::PARAM_STR);
			$stmt->execute();
			
			$count = $stmt->rowCount();
			$tablica_nazw = array();
			$i=0;
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
													
													$tablica_nazw[$i] = $row['name'];
													
													$i++;
											}
			return $tablica_nazw;
		}
	
	}
	
	public static function getExpenseSum($user_id,$opcja)
	{
		if($opcja==1)
		{
			$month=date('n');
			$sql ='SELECT expense_category_assigned_to_user_id,SUM(amount),expenses_category_assigned_to_users.name FROM `expenses` INNER JOIN expenses_category_assigned_to_users ON expenses.expense_category_assigned_to_user_id=expenses_category_assigned_to_users.id WHERE MONTH(date_of_expense)=:month AND expenses.user_id=:user_id GROUP BY expense_category_assigned_to_user_id';
			$db=static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':user_id', $user_id,PDO::PARAM_INT);
			$stmt->bindValue(':month', $month,PDO::PARAM_INT);
			$stmt->execute();
			$count = $stmt->rowCount();
			$tablica_sum = array();
			$i=0;
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
													
													$tablica_sum[$i] = $row['SUM(amount)'];
													
													$i++;
											}
			return $tablica_sum;
		}
		else if($opcja==2)
		{
			$month=date('n')-1;
			$year = date('Y');
			if($month == 0)
			{
				$month = 12;
				$year = $year-1;
				
			}
			$sql ='SELECT expense_category_assigned_to_user_id,SUM(amount),expenses_category_assigned_to_users.name FROM `expenses` INNER JOIN expenses_category_assigned_to_users ON expenses.expense_category_assigned_to_user_id=expenses_category_assigned_to_users.id WHERE MONTH(date_of_expense)=:month AND YEAR(date_of_expense)=:year AND expenses.user_id=:user_id GROUP BY expense_category_assigned_to_user_id';
			$db=static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':user_id', $user_id,PDO::PARAM_INT);
			$stmt->bindValue(':month', $month,PDO::PARAM_INT);
			$stmt->bindValue(':year', $year,PDO::PARAM_INT);
			$stmt->execute();
			$count = $stmt->rowCount();
			$tablica_sum = array();
			$i=0;
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
													
													$tablica_sum[$i] = $row['SUM(amount)'];
													
													$i++;
											}
			return $tablica_sum;
		}
		else if($opcja ==3)
		{
			$year=date('Y');
			$sql ='SELECT expense_category_assigned_to_user_id,SUM(amount),expenses_category_assigned_to_users.name FROM `expenses` INNER JOIN expenses_category_assigned_to_users ON expenses.expense_category_assigned_to_user_id=expenses_category_assigned_to_users.id WHERE YEAR(date_of_expense)=:year AND expenses.user_id=:user_id GROUP BY expense_category_assigned_to_user_id';
			$db=static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':user_id', $user_id,PDO::PARAM_INT);
			$stmt->bindValue(':year', $year,PDO::PARAM_INT);
			$stmt->execute();
			$count = $stmt->rowCount();
			$tablica_sum = array();
			$i=0;
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
													
													$tablica_sum[$i] = $row['SUM(amount)'];
													
													$i++;
											}
			return $tablica_sum;
		}
		else if($opcja ==4||$opcja==5)
		{
			$start_date=Date::getStartData($_POST);
			$end_date=Date::getEndData($_POST);
			
			$sql ='SELECT expense_category_assigned_to_user_id,SUM(amount),expenses_category_assigned_to_users.name FROM `expenses` INNER JOIN expenses_category_assigned_to_users ON expenses.expense_category_assigned_to_user_id=expenses_category_assigned_to_users.id WHERE date_of_expense BETWEEN :start_date AND :end_date AND expenses.user_id=:user_id GROUP BY expense_category_assigned_to_user_id';
			$db=static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':user_id', $user_id,PDO::PARAM_INT);
			$stmt->bindValue(':start_date', $start_date,PDO::PARAM_STR);
			$stmt->bindValue(':end_date', $end_date,PDO::PARAM_STR);
			$stmt->execute();
			
			$count = $stmt->rowCount();
			$tablica_sum = array();
			$i=0;
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
													
													$tablica_sum[$i] = $row['SUM(amount)'];
													
													$i++;
											}
			return $tablica_sum;
		}
	}
	
	public static function getArray($user_id,$opcja)
	{
		$tablica_nazw =  static::getExpenseBilans($user_id,$opcja);
		$tablica_sum = static::getExpenseSum($user_id,$opcja);
		
		
		if(!empty($tablica_nazw)&&!empty($tablica_sum))
		{
		$tablica = array_combine($tablica_nazw, $tablica_sum);
		
		return $tablica;
		}
		else
		{
			$tablica_nazw=array();
			$tablica_sum=array();
			$tablica = array_combine($tablica_nazw, $tablica_sum);
		return $tablica;
		}
		
		
	}
	
	public static function getSumAllExpense($user_id,$opcja)
	{
		if($opcja==1)
		{
			$month=date('n');
			$sql ='SELECT expense_category_assigned_to_user_id,SUM(amount),expenses_category_assigned_to_users.name FROM `expenses` INNER JOIN expenses_category_assigned_to_users ON expenses.expense_category_assigned_to_user_id=expenses_category_assigned_to_users.id WHERE MONTH(date_of_expense)=:month AND expenses.user_id=:user_id GROUP BY expense_category_assigned_to_user_id';
			$db=static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':user_id', $user_id,PDO::PARAM_INT);
			$stmt->bindValue(':month', $month,PDO::PARAM_INT);
			$stmt->execute();
			
			$count = $stmt->rowCount();
			
			$tablica_sum = static::getExpenseSum($user_id,$opcja);
			
			$suma_wydatkow=0;
		if(!empty($tablica_sum)){
			for($k=0; $k<$count; $k++){
				$suma_wydatkow=$suma_wydatkow+$tablica_sum[$k];
		}
			$allSum=round($suma_wydatkow,2);
			return $allSum;
		}
		else return 0;
		}
		else if($opcja==2)
		{
			$month=date('n')-1;
			$year = date('Y');
			if($month == 0)
			{
				$month = 12;
				$year = $year-1;
				
			}
			$sql ='SELECT expense_category_assigned_to_user_id,SUM(amount),expenses_category_assigned_to_users.name FROM `expenses` INNER JOIN expenses_category_assigned_to_users ON expenses.expense_category_assigned_to_user_id=expenses_category_assigned_to_users.id WHERE MONTH(date_of_expense)=:month AND YEAR(date_of_expense)=:year AND expenses.user_id=:user_id GROUP BY expense_category_assigned_to_user_id';
			$db=static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':user_id', $user_id,PDO::PARAM_INT);
			$stmt->bindValue(':month', $month,PDO::PARAM_INT);
			$stmt->bindValue(':year', $year,PDO::PARAM_INT);
			$stmt->execute();
			
			$count = $stmt->rowCount();
			
			$tablica_sum = static::getExpenseSum($user_id,$opcja);
			
			$suma_wydatkow=0;
		if(!empty($tablica_sum)){
			for($k=0; $k<$count; $k++){
				$suma_wydatkow=$suma_wydatkow+$tablica_sum[$k];
		}
			$allSum=round($suma_wydatkow,2);
			return $allSum;
		}
		else return 0;
		}
		else if($opcja==3)
		{
			$year=date('Y');
			$sql ='SELECT expense_category_assigned_to_user_id,SUM(amount),expenses_category_assigned_to_users.name FROM `expenses` INNER JOIN expenses_category_assigned_to_users ON expenses.expense_category_assigned_to_user_id=expenses_category_assigned_to_users.id WHERE YEAR(date_of_expense)=:year AND expenses.user_id=:user_id GROUP BY expense_category_assigned_to_user_id';
			$db=static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':user_id', $user_id,PDO::PARAM_INT);
			$stmt->bindValue(':year', $year,PDO::PARAM_INT);
			$stmt->execute();
			$count = $stmt->rowCount();
			$tablica_sum = static::getExpenseSum($user_id,$opcja);
			
			$suma_wydatkow=0;
		if(!empty($tablica_sum)){
			for($k=0; $k<$count; $k++){
				$suma_wydatkow=$suma_wydatkow+$tablica_sum[$k];
		}
			$allSum=round($suma_wydatkow,2);
			return $allSum;
		}
		else return 0;
		}
		else if($opcja==4||$opcja==5)
		{
			$start_date=Date::getStartData($_POST);
			$end_date=Date::getEndData($_POST);
			
			$sql ='SELECT expense_category_assigned_to_user_id,SUM(amount),expenses_category_assigned_to_users.name FROM `expenses` INNER JOIN expenses_category_assigned_to_users ON expenses.expense_category_assigned_to_user_id=expenses_category_assigned_to_users.id WHERE date_of_expense BETWEEN :start_date AND :end_date AND expenses.user_id=:user_id GROUP BY expense_category_assigned_to_user_id';
			$db=static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':user_id', $user_id,PDO::PARAM_INT);
			$stmt->bindValue(':start_date', $start_date,PDO::PARAM_STR);
			$stmt->bindValue(':end_date', $end_date,PDO::PARAM_STR);
			$stmt->execute();
			
			$count = $stmt->rowCount();
			$tablica_sum = static::getExpenseSum($user_id,$opcja);
			
			$suma_wydatkow=0;
		if(!empty($tablica_sum)){
			for($k=0; $k<$count; $k++){
				$suma_wydatkow=$suma_wydatkow+$tablica_sum[$k];
		}
			$allSum=round($suma_wydatkow,2);
			return $allSum;
		}
		else return 0;
		}
	}
	
	public function removeExpense()
	{
		if(empty($this->errors))
		{
			$user_id =$_SESSION['user_id'];
			
			$expense_category=static::getCategoryExpense($user_id,$this->wybor);
			$name_expense =$this->wybor;
			
			$category_another_id=static::getCategoryExpense($user_id,"Inne");
		
		//$sql = 'DELETE FROM `expenses` WHERE user_id=:user_id AND expense_category_assigned_to_user_id=:expense_category_assigned_to_user_id;
		//DELETE FROM expenses_category_assigned_to_users WHERE user_id=:user_id AND id=:expense_category_assigned_to_user_id';
		$sql = 'UPDATE `expenses` SET expense_category_assigned_to_user_id=:category_another_id WHERE user_id=:user_id AND expense_category_assigned_to_user_id=:expense_category_assigned_to_user_id;
		DELETE FROM expenses_category_assigned_to_users WHERE user_id=:user_id AND id=:expense_category_assigned_to_user_id';
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':user_id', $user_id,PDO::PARAM_INT);
		$stmt->bindValue(':expense_category_assigned_to_user_id',$expense_category,PDO::PARAM_INT);
		$stmt->bindValue(':category_another_id',$category_another_id,PDO::PARAM_INT);
		
		return $stmt->execute();
		}
		else return false;
	}
	
	public function isInne()
	{
		if(empty($this->errors))
		{
		$user_id =$_SESSION['user_id'];
			
		$expense_category=static::getCategoryExpense($user_id,$this->wybor);
		$category_another_id=static::getCategoryExpense($user_id,"Inne");
		
		if($expense_category==$category_another_id)
		{
			return true;
		}
		else return false;
		}
		return false;
	}
	
	public function removePayment()
	{
		if(empty($this->errors))
		{
		$user_id =$_SESSION['user_id'];
		$payment_category=static::getCategoryPayment($user_id,$this->wybor);	
		if(static::existCategoryInne()==false)
		{
			static::addCategoryPayment();
		}
		$payment_category_inne=static::getCategoryPayment($user_id,"Inne");	
		$sql = 'UPDATE `expenses` SET payment_method_assigned_to_user_id=:payment_category_inne WHERE user_id=:user_id AND payment_method_assigned_to_user_id
		=:payment_category_assigned_to_user_id;
		DELETE FROM payment_methods_assigned_to_users WHERE user_id=:user_id AND id=:payment_category_assigned_to_user_id';
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':user_id', $user_id,PDO::PARAM_INT);
		$stmt->bindValue(':payment_category_assigned_to_user_id',$payment_category,PDO::PARAM_INT);
		$stmt->bindValue(':payment_category_inne',$payment_category_inne,PDO::PARAM_INT);

		
		return $stmt->execute();
		}
		return false;
	}
	
    public function itisInne()
	{
	    $user_id =$_SESSION['user_id'];
	    $payment_category=static::getCategoryPayment($user_id,$this->wybor);
	    $option = static::findCategoryPayment($user_id,"Inne");
	    if($option)
	    {
	        $payment_inne_id=static::getCategoryPayment($user_id,"Inne");
	        if($payment_category==$payment_inne_id) return true;
	        else return false;
	    }
	    else return false;
	    
	}
	
	public static function existCategoryInne()
	{
		$user_id =$_SESSION['user_id'];
	
			$option = static::findCategoryPayment($user_id,"Inne");
		
			if($option) return true;
			else return false;
		
	}
	

	
	public static function findCategoryPayment($user_id,$category)
	{
		$sql = "SELECT id FROM payment_methods_assigned_to_users WHERE name=:category AND user_id=:user_id";
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':category', $category ,PDO::PARAM_STR);
		$stmt->bindValue(':user_id', $user_id ,PDO::PARAM_INT);
		
		$stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());
		
		$stmt->execute();
		
		return $stmt->fetch();
		
	}
	public function isPaymentInne()
	{
		if(empty($this->errors))
		{
		$user_id =$_SESSION['user_id'];
			
		$payment_category=static::getCategoryPayment($user_id,$this->wybor);
		$category_another_id=static::getCategoryPayment($user_id,"Inne");
		
		if($payment_category==$category_another_id)
		{
			return true;
		}
		else return false;
		}
		return false;
	}
	public function addCategoryPayment()
	{
		$user_id =$_SESSION['user_id'];
		$category ="Inne";
		
		$sql = "INSERT INTO payment_methods_assigned_to_users VALUES (NULL,:user_id,:category)";
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':category', $category ,PDO::PARAM_STR);
		$stmt->bindValue(':user_id', $user_id ,PDO::PARAM_INT);
		
		return $stmt->execute();
	}
	
	public static function getMax($id)
	{
		$sql='SELECT COUNT(*) FROM expenses WHERE user_id=:id';
		$db=static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':id', $id,PDO::PARAM_INT);
		//$stmt->bindValue(':id',$user->id,PDO::PARAM_INT);
		
		$stmt->execute();
		$incomes=$stmt->fetch(PDO::FETCH_ASSOC); 
		//$stmt->rowCount();
		$empty=$incomes['COUNT(*)'];
		if($empty==0)
		{
			//$stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());
			//$expense=$stmt->fetch(PDO::FETCH_ASSOC); 
			//$all_expense=$expense['SUM(amount)'];
			return 0;
		}
		else
		{
		$sql='SELECT MAX(id) FROM expenses WHERE user_id=:id';
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':id', $id,PDO::PARAM_INT);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());
		$max=$stmt->fetch(PDO::FETCH_ASSOC); 
		$maxID=$max['MAX(id)'];
		return $maxID;
		}
	}
	public static function getCategory($id,$max_id)
	{
		$sql='SELECT expense_category_assigned_to_user_id  FROM expenses WHERE id=:max_id AND user_id=:id';
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':id', $id,PDO::PARAM_INT);
		$stmt->bindValue(':max_id', $max_id,PDO::PARAM_INT);
		$stmt->execute();
		$max=$stmt->fetch(PDO::FETCH_ASSOC); 
		$ID=$max['expense_category_assigned_to_user_id'];
		return $ID;
	}
	public static function getName($user_id,$cat_id)
	{
		$sql='SELECT name FROM expenses_category_assigned_to_users WHERE id=:cat_id AND user_id=:id';
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':id', $user_id,PDO::PARAM_INT);
		$stmt->bindValue(':cat_id', $cat_id,PDO::PARAM_INT);
		$stmt->execute();
		$max=$stmt->fetch(PDO::FETCH_ASSOC); 
		$name=$max['name'];
		return $name;
	}
	public static function getPrize($id,$max_id)
	{
		$sql='SELECT amount  FROM expenses WHERE id=:max_id AND user_id=:id';
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':id', $id,PDO::PARAM_INT);
		$stmt->bindValue(':max_id', $max_id,PDO::PARAM_INT);
		$stmt->execute();
		$max=$stmt->fetch(PDO::FETCH_ASSOC); 
		$prize=$max['amount'];
		return $prize;
	}
	public static function getData($id,$max_id)
	{
		$sql='SELECT date_of_expense  FROM expenses WHERE id=:max_id AND user_id=:id';
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':id', $id,PDO::PARAM_INT);
		$stmt->bindValue(':max_id', $max_id,PDO::PARAM_INT);
		$stmt->execute();
		$max=$stmt->fetch(PDO::FETCH_ASSOC); 
		$date=$max['date_of_expense'];
		return $date;
	}
	public static function getComment($id,$max_id)
	{
		$sql='SELECT expense_comment  FROM expenses WHERE id=:max_id AND user_id=:id';
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':id', $id,PDO::PARAM_INT);
		$stmt->bindValue(':max_id', $max_id,PDO::PARAM_INT);
		$stmt->execute();
		$max=$stmt->fetch(PDO::FETCH_ASSOC); 
		$comment=$max['expense_comment'];
		return $comment;
	}
	public static function getPresentCategoryPayment($id,$max_id)
	{
		$sql='SELECT payment_method_assigned_to_user_id  FROM expenses WHERE id=:max_id AND user_id=:id';
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':id', $id,PDO::PARAM_INT);
		$stmt->bindValue(':max_id', $max_id,PDO::PARAM_INT);
		$stmt->execute();
		$max=$stmt->fetch(PDO::FETCH_ASSOC); 
		$ID=$max['payment_method_assigned_to_user_id'];
		return $ID;
	}
	public static function getNamePayment($user_id,$cat_id)
	{
		$sql='SELECT name FROM payment_methods_assigned_to_users WHERE id=:cat_id AND user_id=:id';
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':id', $user_id,PDO::PARAM_INT);
		$stmt->bindValue(':cat_id', $cat_id,PDO::PARAM_INT);
		$stmt->execute();
		$max=$stmt->fetch(PDO::FETCH_ASSOC); 
		$name=$max['name'];
		return $name;
	}
	public static function existExpense($user_id)
	{
		$sql='SELECT COUNT(*) FROM expenses WHERE user_id=:user_id';
		$db=static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':user_id', $user_id,PDO::PARAM_INT);
		
		//$stmt->bindValue(':id',$user->id,PDO::PARAM_INT);
		
		$stmt->execute();
		$incomes=$stmt->fetch(PDO::FETCH_ASSOC); 
		//$stmt->rowCount();
		$empty=$incomes['COUNT(*)'];
		if($empty==0)
		{
			//$stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());
			//$expense=$stmt->fetch(PDO::FETCH_ASSOC); 
			//$all_expense=$expense['SUM(amount)'];
			return false;
		}
		else return true;
	}
	
	public function addLimits()
	{
		if(empty($this->errors))
		{
			$user_id =$_SESSION['user_id'];
		
			$expense_category=static::getCategoryExpense($user_id,$this->wybor);
			$max_id = static::getLimitsMaxID();

			$existLimit = static:: existLimits($user_id,$expense_category);
			if($existLimit == false)
			{
				$sql = 'INSERT INTO limits (id, users_id, expense_id, kwota) VALUES (:id,:user_id,:expense_category_assigned_to_user_id,:amount)';
				$db = static::getDB();
				
				$stmt = $db->prepare($sql);
				$stmt->bindValue(':id', $max_id,PDO::PARAM_INT);
				$stmt->bindValue(':user_id', $user_id,PDO::PARAM_INT);
				$stmt->bindValue(':expense_category_assigned_to_user_id',$expense_category,PDO::PARAM_INT);
				$stmt->bindValue(':amount', $this->kwota,PDO::PARAM_STR);
				
				return $stmt->execute();
		
			}
			else{
				$id=static::getlimitsID($user_id,$expense_category);
				$sql = 'UPDATE limits SET kwota=:amount WHERE id=:id';
				$db = static::getDB();
				
				$stmt = $db->prepare($sql);
				
				$stmt->bindValue(':id', $id,PDO::PARAM_STR);
				$stmt->bindValue(':amount', $this->kwota,PDO::PARAM_STR);
				
				return $stmt->execute();
			}
		}
		return false;
	}
	
	public static function getlimitsID($user_id,$expense_category)
	{
		$sql='SELECT id FROM limits WHERE expense_id=:cat_id AND users_id=:id';
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':id', $user_id,PDO::PARAM_INT);
		$stmt->bindValue(':cat_id', $expense_category,PDO::PARAM_INT);
		$stmt->execute();
		$limit=$stmt->fetch(PDO::FETCH_ASSOC); 
		$empty=$limit['id'];
		return $empty;
	}
	
	public static function existLimits($user_id,$cat_id)
	{
		$sql='SELECT COUNT(*) FROM limits WHERE expense_id=:cat_id AND users_id=:id';
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':id', $user_id,PDO::PARAM_INT);
		$stmt->bindValue(':cat_id', $cat_id,PDO::PARAM_INT);
		$stmt->execute();
		$limit=$stmt->fetch(PDO::FETCH_ASSOC); 
		$empty=$limit['COUNT(*)'];
		if($empty==0)
		{
			//$stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());
			//$expense=$stmt->fetch(PDO::FETCH_ASSOC); 
			//$all_expense=$expense['SUM(amount)'];
			return false;
		}
		else return true;
	
	}
	public static function getLimit($user_id,$id_kategory)
	{
		$exist = static::existLimits($user_id,$id_kategory);
		if($exist == false) return false;
		else{
			$sql='SELECT kwota FROM limits WHERE expense_id=:id_kategory AND users_id=:id';
			$db = static::getDB();
			
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':id', $user_id,PDO::PARAM_INT);
			$stmt->bindValue(':id_kategory', $id_kategory,PDO::PARAM_INT);
			$stmt->execute();
			$limit=$stmt->fetch(PDO::FETCH_ASSOC); 
			$kwota=$limit['kwota'];
			
			return $kwota;
		
		}
		
	}
	
	
		public static function getSumLimit($user_id,$id_kategory,$month,$year)
	{
		$sql='SELECT COUNT(*) FROM expenses WHERE expense_category_assigned_to_user_id=:id_kategory AND user_id=:id AND YEAR(date_of_expense)=:year AND MONTH(date_of_expense)=:month';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':id', $user_id,PDO::PARAM_INT);
		$stmt->bindValue(':id_kategory',$id_kategory ,PDO::PARAM_INT);
		$stmt->bindValue(':year',$year,PDO::PARAM_INT);
		$stmt->bindValue(':month',$month,PDO::PARAM_INT);
		$stmt->execute();
		$limit=$stmt->fetch(PDO::FETCH_ASSOC); 
		$empty=$limit['COUNT(*)'];
		if($empty==0)
		{
			return 0;
		}
		else
		{
				$sql='SELECT SUM(amount) FROM expenses WHERE expense_category_assigned_to_user_id=:id_kategory AND user_id=:id AND YEAR(date_of_expense)=:year AND MONTH(date_of_expense)=:month';
				$db = static::getDB();
				$stmt = $db->prepare($sql);
				$stmt->bindValue(':id', $user_id,PDO::PARAM_INT);
				$stmt->bindValue(':id_kategory',$id_kategory ,PDO::PARAM_INT);
				$stmt->bindValue(':year',$year,PDO::PARAM_INT);
				$stmt->bindValue(':month',$month,PDO::PARAM_INT);
				
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());
				$sum=$stmt->fetch(PDO::FETCH_ASSOC); 
				$suma=$sum['SUM(amount)'];
				return $suma;	
		}
	}
public function removeLimit()
	{
		if(empty($this->errors))
		{
			$user_id =$_SESSION['user_id'];
			
			$expense_category=static::getCategoryExpense($user_id,$this->wybor);
		
		$sql = 'DELETE FROM `limits` WHERE users_id=:user_id AND expense_id=:expense_id';
		
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':user_id', $user_id,PDO::PARAM_INT);
		$stmt->bindValue(':expense_id',$expense_category,PDO::PARAM_INT);

		
		return $stmt->execute();
		}
		else return false;
	}
}