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
		$stmt->bindValue(':amount', $this->kwota,PDO::PARAM_INT);
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
	
}