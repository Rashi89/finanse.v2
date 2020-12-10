<?php

//require '../App/Controllers/Posts.php';

//require '../Core/Router.php';

//10 dni nie wyloguje przez 10 dni
ini_set('session.cookie_lifetime','864000');

//automatyczne ładowanie

//require_once dirname(__DIR__).'/vendor/vendor/autoload.php';
//Twig_Autoloader::register();

require '../vendor/autoload.php';
//require dirname(__DIR__) . '/vendor/autoload.php';

/*spl_autoload_register(function($class)
{
		$root = dirname(__DIR__);
		$file = $root.'/'.str_replace('\\','/',$class).'.php';
		if(is_readable($file))
		{
			require $root.'/'.str_replace('\\','/',$class).'.php';
		}
}
);*/
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

session_start();

$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('login', ['controller' => 'Home', 'action' => 'index']);
$router->add('logout', ['controller' => 'Login', 'action' => 'destroy']);
$router->add('password/reset/{token:[\da-f]+}', ['controller' => 'Password', 'action' => 'reset']);
$router->add('signup/activate/{token:[\da-f]+}', ['controller' => 'Signup', 'action' => 'activate']);
$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');
$router->add('admin/{controller}/{action}',['namespace'=>'Admin']);
    
$router->dispatch($_SERVER['QUERY_STRING']);
?>