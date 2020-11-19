<?php

	namespace Core;
	
	class View
	{
		public static function render($view, $args = [])
		{
			extract($args,EXTR_SKIP);
			$file = "../App/Views/$view";
			
			if(is_readable($file))
			{
				require $file;
			}
			else
			{
				//echo "$file not found";
				throw new \Exception("$file not found");
			}
		}
		
		public static function renderTemplate($template,$args=[])
		{
			echo static::getTemplate($template,$args);
		}
		
		public static function getTemplate($template,$args=[])
		{
			static $twig = null;
			
			if($twig === null)
			{
				$loader = new \Twig\Loader\FilesystemLoader('../App/Views');
				$twig = new \Twig\Environment($loader);
				//$twig->addGlobal('session', $_SESSION);
				//$twig->addGlobal('is_logged_in', \App\Auth::isLogginedIn());
				$twig->addGlobal('current_user', \App\Auth::getUser());
				$twig->addGlobal('flash_messages', \App\Flash::getMessages());

				$twig->addGlobal('date',\App\Auth::getDate());
				$twig->addGlobal('saldo',\App\Auth::getSaldo());
				


			}
			
			return $twig->render($template,$args);
		}
	}