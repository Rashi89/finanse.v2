<?php

namespace App\Models;

use PDO;

class Bilanses extends \Core\Model
{
	public $errors=[];
	
	public function __construct($data = [])
	{
		foreach($data as $key => $value)
		{
			$this->$key = $value;
		};
	}
	
	public function showOption()
	{
		$user_id =$_SESSION['user_id'];
		$option = $this->opcja_bilansu;
		$opcja = static::getOption($user_id,$option);
		return $opcja;
	}
	
	public static function getOption($user_id,$option)
	{
		if($option ==1)
			return 1;
		else if($option ==2)
			return 2;
		else if($option ==3)
			return 3;
		else if($option == 4)
		return 4;
		
	}
	public static function infoBilans($user,$allBilans)
	{
		if($allBilans<0) return 'Uważaj '.$user->name. ' wpadasz w długi!';
		else return $user->name.' świetnie zarządzasz finansami!';
	}
}