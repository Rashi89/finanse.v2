<?php

namespace App\Models;

use PDO;

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
		
		return $stmt->execute();
	}
}