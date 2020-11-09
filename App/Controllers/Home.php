<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

//rozszerzaenie klasy \core\controller o klase home
class Home extends \Core\Controller
{

	public function indexAction()
	{
		/*
		View::render('Home/index.php',[ 
		'name'=>'Dave',
		'colours'=>['red','blue','green','yellow']
		]);
		*/
		//tu do kogo, temat, tresc i ewentualnie jakis html 
		//\App\Mail::send('rachanek@yahoo.com','Test','This is a test','<h1>This is a big test</h1>');
		View::renderTemplate('Home/index.html',[]);
	}
}