<?php

namespace App\Models;

use PDO;
use \App\Models\Income;

class Option extends \Core\Model
{
	public $errors=[];
	
	public function __construct($data = [])
	{
		foreach($data as $key => $value)
		{
			$this->$key = $value;
		};
	}
	
	public function existCategory()
	{
		$user_id =$_SESSION['user_id'];
		$category = $this->new_category;
	
			$option = static::findCategoryPayment($user_id,$category);
		
			if($option) return false;
			else return true;
		
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
	
	public function addCategoryPayment()
	{
		$user_id =$_SESSION['user_id'];
		$category = $this->new_category;
		
		$sql = "INSERT INTO payment_methods_assigned_to_users VALUES (NULL,:user_id,:category)";
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':category', $category ,PDO::PARAM_STR);
		$stmt->bindValue(':user_id', $user_id ,PDO::PARAM_INT);
		
		return $stmt->execute();
	}
	
	public function existCategoryIncome()
	{
		$user_id =$_SESSION['user_id'];
		$category = $this->new_category;
	
			$option = static::findCategoryIncome($user_id,$category);
		
			if($option) return false;
			else return true;
		
	}
	
	public static function findCategoryIncome($user_id,$category)
	{
		$sql = "SELECT id FROM incomes_category_assigned_to_users WHERE name=:category AND user_id=:user_id";
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':category', $category ,PDO::PARAM_STR);
		$stmt->bindValue(':user_id', $user_id ,PDO::PARAM_INT);
		
		$stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());
		
		$stmt->execute();
		
		return $stmt->fetch();
		
	}
	
	public function addCategoryIncome()
	{
		$user_id =$_SESSION['user_id'];
		$category = $this->new_category;
		
		$sql = "INSERT INTO incomes_category_assigned_to_users VALUES (NULL,:user_id,:category)";
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':category', $category ,PDO::PARAM_STR);
		$stmt->bindValue(':user_id', $user_id ,PDO::PARAM_INT);
		
		return $stmt->execute();
	}
	
	public function existCategoryExpense()
	{
		$user_id =$_SESSION['user_id'];
		$category = $this->new_category;
	
			$option = static::findCategoryExpense($user_id,$category);
		
			if($option) return false;
			else return true;
		
	}
	
	public static function findCategoryExpense($user_id,$category)
	{
		$sql = "SELECT id FROM expenses_category_assigned_to_users WHERE name=:category AND user_id=:user_id";
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':category', $category ,PDO::PARAM_STR);
		$stmt->bindValue(':user_id', $user_id ,PDO::PARAM_INT);
		
		$stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());
		
		$stmt->execute();
		
		return $stmt->fetch();
		
	}
	
	public function addCategoryExpense()
	{
		$user_id =$_SESSION['user_id'];
		$category = $this->new_category;
		
		$sql = "INSERT INTO expenses_category_assigned_to_users VALUES (NULL,:user_id,:category)";
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':category', $category ,PDO::PARAM_STR);
		$stmt->bindValue(':user_id', $user_id ,PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->execute();
	}
	
	public static function deleteIncome($user_id,$max_id)
	{
		$sql = 'DELETE FROM incomes WHERE id=:max_id AND user_id=:id';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':id', $user_id,PDO::PARAM_INT);
		$stmt->bindValue(':max_id', $max_id,PDO::PARAM_INT);
		return $stmt->execute();
		
		
	}
	
	public function saveIncome()
	{
		$user_id=$_SESSION['user_id'];
		$max_id=Income::getMax($_SESSION['user_id']);
		$presentCategoryID=Income::getCategory($_SESSION['user_id'],$max_id);
		
		if(!static::deleteIncome($user_id,$max_id))
		{
			return false;
		}
		else
		{
		$income_category=Income::getCategoryIncome($user_id,$this->wybor);
		$sql='INSERT INTO incomes (id,user_id,income_category_assigned_to_user_id,amount, date_of_income, income_comment) VALUES (:id,:user_id,:income_category_assigned_to_user_id,:amount, :date_of_income, :income_comment)';
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':id', $max_id,PDO::PARAM_INT);
		$stmt->bindValue(':user_id', $user_id,PDO::PARAM_INT);
		$stmt->bindValue(':income_category_assigned_to_user_id',$income_category,PDO::PARAM_INT);
		$stmt->bindValue(':amount', $this->kwota,PDO::PARAM_STR);
		$stmt->bindValue(':date_of_income', $this->date ,PDO::PARAM_STR);
		$stmt->bindValue(':income_comment', $this->komentarz ,PDO::PARAM_STR);
		$stmt->execute();
		return true;
		}
		
		
	}
}