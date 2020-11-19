<?php

namespace App\Controllers;

use \App\Models\User;

class Account extends \Core\Controller
{
	public function validateEmailAction()
	{
		$is_valid = ! User::emailExist($_GET['email'], $_GET['potencjal_id'] ?? null);
		
		header('Content-Type: application/json');
		echo json_encode($is_valid);
	}
	
	public function validateNameAction()
	{
				$is_valid = ! User::nameExist($_GET['name'], $_GET['potencjal_id'] ?? null);
		
		header('Content-Type: application/json');
		echo json_encode($is_valid);
	}
	
	
}