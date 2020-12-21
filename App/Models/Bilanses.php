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
	
	private static function getOption($user_id,$option)
	{
		if($option ==1||$option=="Bieżący miesiąc")
			return 1;
		else if($option ==2||$option=="Poprzedni miesiąc")
			return 2;
		else if($option ==3||$option=="Bieżący rok")
			return 3;
		else if($option == 4)
			return 4;
		else if($option == 5||$option=="Niestandardowy")
			return 5;
		
	}
	public static function infoBilans($user,$allBilans)
	{
		if($allBilans<0) return 'Uważaj '.$user->name. ' wpadasz w długi!';
		else return $user->name.' świetnie zarządzasz finansami!';
	}
	public function getOptionName()
	{
		$user_id =$_SESSION['user_id'];
		$option = $this->opcja_bilansu;
		$option_name=static::getName($user_id,$option);
		return $option_name;
	}
	private static function getName($user_id,$option)
	{
		if($option ==1||$option=="Bieżący miesiąc")
			return "Bieżący miesiąc";
		else if($option ==2||$option=="Poprzedni miesiąc")
			return "Poprzedni miesiąc";
		else if($option ==3||$option=="Bieżący rok")
			return "Bieżący rok";
		else if($option == 4)
			return "Niestandardowy";
		else if($option == 5||$option=="Niestandardowy")
			return "Niestandardowy";
	}
}