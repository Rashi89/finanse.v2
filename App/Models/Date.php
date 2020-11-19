<?php

namespace App\Models;

use PDO;
use \Core\View;


class Date extends \Core\Model
{
	//zmienna publiczna
	public $errors=[];
	
	
	public function __construct($data = [])
	{
		foreach($data as $key => $value)
		{
			$this->$key = $value;
		};
	}
	
	public static function goodRange($data)
	{
		$start_date = $data['start_date'];
		$end_date = $data['end_date'];
		
		if(strtotime($start_date)>strtotime($end_date)||$start_date==""||$end_date=="")
				{
					return false;
				}
				else
				{
					return true;
				}
	}
	
	public static function getStartData($data)
	{
		$start_date = $data['start_date'];
		return $start_date;
	}
	
		public static function getEndData($data)
	{
		$end_date = $data['end_date'];
		return $end_date;
	}
	
}