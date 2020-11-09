<?php

namespace App\Controllers;

use \Core\View;

class Items extends Authenticated
{
	
	/*ta metoda wywola sie zawsze przed innymi metodami
	protected function before()
	{
		$this->requireLogin();
	}*/
	
	public function indexAction()
	{
		View::renderTemplate('Items/index.html');		
	}
	
	public function newAction()
	{
		echo "new action";
	}
}