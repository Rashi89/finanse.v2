<?php

namespace App\Models;

use PDO;

class Income extends \Core\Model
{
	public $errors=[];
	
	public function __construct($data = [])
	{
		foreach($data as $key => $value)
		{
			$this->$key = $value;
		};
	}
	
	public function validate()
	{
		if($this->kwota<0)
		{
			$this->errors[]="Kwota nie może byc ujemna!";
		}
	}
	
	public function saveIncome()
	{
		if(empty($this->errors))
		{
			$user_id =$_SESSION['user_id'];
			$max_id = static::getMaxID();
			$income_category=static::getCategoryIncome($user_id,$this->wybor);

		
		$sql = 'INSERT INTO incomes (id,user_id,income_category_assigned_to_user_id,amount, date_of_income, income_comment) VALUES (:id,:user_id,:income_category_assigned_to_user_id,:amount, :date_of_income, :income_comment)';
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':id', $max_id,PDO::PARAM_INT);
		$stmt->bindValue(':user_id', $user_id,PDO::PARAM_INT);
		$stmt->bindValue(':income_category_assigned_to_user_id',$income_category,PDO::PARAM_INT);
		$stmt->bindValue(':amount', $this->kwota,PDO::PARAM_STR);
		$stmt->bindValue(':date_of_income', $this->date ,PDO::PARAM_STR);
		$stmt->bindValue(':income_comment', $this->komentarz ,PDO::PARAM_STR);
		
		return $stmt->execute();
		}
		
		return false;
	}
	
	public static function getMaxID()
	{
		$sql='SELECT COUNT(*) FROM incomes';
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
		$sql='SELECT MAX(id) FROM incomes';
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());
		$max=$stmt->fetch(PDO::FETCH_ASSOC); 
		$maxID=$max['MAX(id)'];
		return $maxID +1;
		}
	}
	
	public static function getCategoryIncome($user_id,$category_name)
	{
		$sql ='SELECT id FROM incomes_category_assigned_to_users WHERE user_id=:user_id AND name=:name';
		$db=static::getDB();
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue(':user_id', $user_id,PDO::PARAM_INT);
		$stmt->bindValue(':name', $category_name,PDO::PARAM_STR);
		$stmt->execute();
		$id_cat=$stmt->fetch(PDO::FETCH_ASSOC);
		$id_kategoria=$id_cat['id'];
		return $id_kategoria;
		
	}
	
	public static function getIncomeBilans($user_id,$opcja)
	{
		if($opcja ==1)
		{
		$month=date('n');
		$sql ='SELECT income_category_assigned_to_user_id,SUM(amount), incomes_category_assigned_to_users.name FROM incomes INNER JOIN incomes_category_assigned_to_users ON incomes.income_category_assigned_to_user_id=incomes_category_assigned_to_users.id WHERE MONTH(date_of_income)=:month AND incomes.user_id=:user_id GROUP BY income_category_assigned_to_user_id';
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
			
		else if($opcja ==2)
		{
		$month=date('n')-1;
		$sql ='SELECT income_category_assigned_to_user_id,SUM(amount), incomes_category_assigned_to_users.name FROM incomes INNER JOIN incomes_category_assigned_to_users ON incomes.income_category_assigned_to_user_id=incomes_category_assigned_to_users.id WHERE MONTH(date_of_income)=:month AND incomes.user_id=:user_id GROUP BY income_category_assigned_to_user_id';
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
			
		else if($opcja == 3)
		{
		$year=date('Y');
		$sql ='SELECT income_category_assigned_to_user_id,SUM(amount), incomes_category_assigned_to_users.name FROM incomes INNER JOIN incomes_category_assigned_to_users ON incomes.income_category_assigned_to_user_id=incomes_category_assigned_to_users.id WHERE YEAR(date_of_income)=:year AND incomes.user_id=:user_id GROUP BY income_category_assigned_to_user_id';
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
		else if($opcja ==4)
		{
			$start_date=Date::getStartData($_POST);
			$end_date=Date::getEndData($_POST);
			
			$sql ='SELECT income_category_assigned_to_user_id,SUM(amount), incomes_category_assigned_to_users.name FROM `incomes`INNER JOIN incomes_category_assigned_to_users ON incomes.income_category_assigned_to_user_id=incomes_category_assigned_to_users.id WHERE date_of_income BETWEEN :start_date AND :end_date AND incomes.user_id=:user_id GROUP BY income_category_assigned_to_user_id';
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
	
	public static function getIncomeSum($user_id,$opcja)
	{
		if($opcja==1)
		{
		$month=date('n');
		$sql ='SELECT income_category_assigned_to_user_id,SUM(amount), incomes_category_assigned_to_users.name FROM incomes INNER JOIN incomes_category_assigned_to_users ON incomes.income_category_assigned_to_user_id=incomes_category_assigned_to_users.id WHERE MONTH(date_of_income)=:month AND incomes.user_id=:user_id GROUP BY income_category_assigned_to_user_id';
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
		else if($opcja ==2)
		{
		$month=date('n')-1;
		$sql ='SELECT income_category_assigned_to_user_id,SUM(amount), incomes_category_assigned_to_users.name FROM incomes INNER JOIN incomes_category_assigned_to_users ON incomes.income_category_assigned_to_user_id=incomes_category_assigned_to_users.id WHERE MONTH(date_of_income)=:month AND incomes.user_id=:user_id GROUP BY income_category_assigned_to_user_id';
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
		else if($opcja==3)
		{
		$year = date('Y');
		$sql ='SELECT income_category_assigned_to_user_id,SUM(amount), incomes_category_assigned_to_users.name FROM incomes INNER JOIN incomes_category_assigned_to_users ON incomes.income_category_assigned_to_user_id=incomes_category_assigned_to_users.id WHERE YEAR(date_of_income)=:year AND incomes.user_id=:user_id GROUP BY income_category_assigned_to_user_id';
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
		else if($opcja ==4)
		{
			$start_date=Date::getStartData($_POST);
			$end_date=Date::getEndData($_POST);
			
			$sql ='SELECT income_category_assigned_to_user_id,SUM(amount), incomes_category_assigned_to_users.name FROM `incomes`INNER JOIN incomes_category_assigned_to_users ON incomes.income_category_assigned_to_user_id=incomes_category_assigned_to_users.id WHERE date_of_income BETWEEN :start_date AND :end_date AND incomes.user_id=:user_id GROUP BY income_category_assigned_to_user_id';
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
		$tablica_nazw =  static::getIncomeBilans($user_id,$opcja);
		$tablica_sum = static::getIncomeSum($user_id,$opcja);
		
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
	
	public static function getSumAllIncomes($user_id,$opcja)
	{
		if($opcja ==1)
		{
			$month=date('n');
			$sql ='SELECT income_category_assigned_to_user_id,SUM(amount), incomes_category_assigned_to_users.name FROM incomes INNER JOIN incomes_category_assigned_to_users ON incomes.income_category_assigned_to_user_id=incomes_category_assigned_to_users.id WHERE MONTH(date_of_income)=:month AND incomes.user_id=:user_id GROUP BY income_category_assigned_to_user_id';
			$db=static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':user_id', $user_id,PDO::PARAM_INT);
			$stmt->bindValue(':month', $month,PDO::PARAM_INT);
			$stmt->execute();
			
			$count = $stmt->rowCount();
			
			$tablica_sum = static::getIncomeSum($user_id,$opcja);
			
			$suma_przychodow=0;
		if(!empty($tablica_sum))
		{
			for($k=0; $k<$count; $k++){
					$suma_przychodow=$suma_przychodow+$tablica_sum[$k];
		}
			$allSum=round($suma_przychodow,2);
			return $allSum;
		}
		else return 0;
		}
		else if($opcja==2)
		{
			$month=date('n')-1;
			$sql ='SELECT income_category_assigned_to_user_id,SUM(amount), incomes_category_assigned_to_users.name FROM incomes INNER JOIN incomes_category_assigned_to_users ON incomes.income_category_assigned_to_user_id=incomes_category_assigned_to_users.id WHERE MONTH(date_of_income)=:month AND incomes.user_id=:user_id GROUP BY income_category_assigned_to_user_id';
			$db=static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':user_id', $user_id,PDO::PARAM_INT);
			$stmt->bindValue(':month', $month,PDO::PARAM_INT);
			$stmt->execute();
			
			$count = $stmt->rowCount();
			
			$tablica_sum = static::getIncomeSum($user_id,$opcja);
			
			$suma_przychodow=0;
			if(!empty($tablica_sum))
			{
			for($k=0; $k<$count; $k++){
					$suma_przychodow=$suma_przychodow+$tablica_sum[$k];
			}
			$allSum=round($suma_przychodow,2);
			return $allSum;
			}
		else return 0;
		}
		else if($opcja==3)
		{
			$year = date('Y');
			$sql ='SELECT income_category_assigned_to_user_id,SUM(amount), incomes_category_assigned_to_users.name FROM incomes INNER JOIN incomes_category_assigned_to_users ON incomes.income_category_assigned_to_user_id=incomes_category_assigned_to_users.id WHERE YEAR(date_of_income)=:year AND incomes.user_id=:user_id GROUP BY income_category_assigned_to_user_id';
			$db=static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':user_id', $user_id,PDO::PARAM_INT);
			$stmt->bindValue(':year', $year,PDO::PARAM_INT);
			$stmt->execute();
			$count = $stmt->rowCount();
			$tablica_sum = static::getIncomeSum($user_id,$opcja);
			
			$suma_przychodow=0;
			if(!empty($tablica_sum))
			{
			for($k=0; $k<$count; $k++){
					$suma_przychodow=$suma_przychodow+$tablica_sum[$k];
			}
			$allSum=round($suma_przychodow,2);
			return $allSum;
			}
			else return 0;
		}
		else if($opcja==4)
		{
			$start_date=Date::getStartData($_POST);
			$end_date=Date::getEndData($_POST);
			
			$sql ='SELECT income_category_assigned_to_user_id,SUM(amount), incomes_category_assigned_to_users.name FROM `incomes`INNER JOIN incomes_category_assigned_to_users ON incomes.income_category_assigned_to_user_id=incomes_category_assigned_to_users.id WHERE date_of_income BETWEEN :start_date AND :end_date AND incomes.user_id=:user_id GROUP BY income_category_assigned_to_user_id';
			$db=static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':user_id', $user_id,PDO::PARAM_INT);
			$stmt->bindValue(':start_date', $start_date,PDO::PARAM_STR);
			$stmt->bindValue(':end_date', $end_date,PDO::PARAM_STR);
			$stmt->execute();
			
			$count = $stmt->rowCount();
			$tablica_sum = static::getIncomeSum($user_id,$opcja);
			
			$suma_przychodow=0;
			if(!empty($tablica_sum))
			{
			for($k=0; $k<$count; $k++){
					$suma_przychodow=$suma_przychodow+$tablica_sum[$k];
			}
			$allSum=round($suma_przychodow,2);
			return $allSum;
			}
			else return 0;
		}
	}
	public function removeIncome()
	{
		if(empty($this->errors))
		{
		$user_id =$_SESSION['user_id'];
			
		$income_category=static::getCategoryIncome($user_id,$this->wybor);
		
		$sql = 'DELETE FROM `incomes` WHERE user_id=:user_id AND income_category_assigned_to_user_id
		=:income_category_assigned_to_user_id;
		DELETE FROM incomes_category_assigned_to_users WHERE user_id=:user_id AND id=:income_category_assigned_to_user_id';
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':user_id', $user_id,PDO::PARAM_INT);
		$stmt->bindValue(':income_category_assigned_to_user_id',$income_category,PDO::PARAM_INT);

		
		return $stmt->execute();
		}
		return false;
	}
	
	
}