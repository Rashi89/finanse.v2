<?php

namespace App\Models;

use PDO;

class Post extends \Core\Model
{
	public static function getAll()
	{
			/*$host = "localhost";
			$db_user = "root";
			$db_password = "";
			$db_name = "mvc";*/
		
		try
		{
			//$db = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8",$db_user,$db_password);
			
			$db=static::getDB();
			
			$stmt = $db->query('SELECT id, title, content FROM posts ORDER BY created_at');
			
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			return $results;
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}
	
}