<?php

namespace App;

use App\Models\User;
use App\Models\RememberedLogin;

class Auth
{
	public static function login($user, $remember_me)
    {
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user->id;

        if ($remember_me) {

            if ($user->rememberLogin()) {

                 setcookie('remember_me', $user->remember_token, $user->expiry_timestamp, '/');

            }
        }
    }

	public static function logout()
    {
      // Unset all of the session variables
      $_SESSION = [];

      // Delete the session cookie
      if (ini_get('session.use_cookies')) {
          $params = session_get_cookie_params();

          setcookie(
              session_name(),
              '',
              time() - 42000,
              $params['path'],
              $params['domain'],
              $params['secure'],
              $params['httponly']
          );
      }

      // Finally destroy the session
      session_destroy();
	  
	  static::forgetLogin();
    }
	
	/*public static function isLogginedIn()
	{
		return isset($_SESSION['user_id']);
	}*/
	
    public static function rememberRequestedPage()
    {
        $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
    }
	
    public static function getReturnToPage()
    {
        return $_SESSION['return_to'] ?? '/';
    }
	
    public static function getUser()
    {
        if (isset($_SESSION['user_id'])) {

            return User::findByID($_SESSION['user_id']);

        } else {

            return static::loginFromRememberCookie();
        }
    }
	
	protected static function loginFromRememberCookie()
    {//sprawdzamy czy istnieje cookie o nazwie remember me jesli tak to zwracamy jego wartość a jesli nie to zwracamy false
        $cookie = $_COOKIE['remember_me'] ?? false;

        if ($cookie) {
		//tu mamy tablice assoc z damymi
            $remembered_login = RememberedLogin::findByToken($cookie);
			//jesli ona istnieje  i przeczenie tego ze wygasla jest prawda to wowczas nadal nas pamieta w przeciwnym wypadku juz nie pamieta
            if ($remembered_login && ! $remembered_login->hasExpired()) {

                $user = $remembered_login->getUser();

                static::login($user, false);

                return $user;
            }
        }
    }
	
	protected static function forgetLogin()
	{
		$cookie = $_COOKIE['remember_me'] ?? false;
		
		if($cookie)
		{
			 $remembered_login = RememberedLogin::findByToken($cookie);
			 if($remembered_login)
			 {
				 //usuniecie w bazie danych
				 $remembered_login->delete();
			 }
			 //usuwanie cookie o nazwie remember me
			 setcookie('remember_me','',time()-3600,'/');
		}
	}

	public static function getDate()
	{
		return date("d.m.Y");
	}
	
		public static function getSumExpenses()
	{
		if(isset($_SESSION['user_id']))
		{
			$sum=User::sumExpenses($_SESSION['user_id']);
			return $sum;
		}
		else
			return 0;
	}
	
	public static function getSumIncomes()
	{
		if(isset($_SESSION['user_id']))
			{
				$sum=User::sumIncomes($_SESSION['user_id']);
				return $sum;
			}
		else
				return 0;
	}
	
	public static function getSaldo()
	{
		if(isset($_SESSION['user_id']))
		{
			$saldo=User::saldo($_SESSION['user_id']);
			$saldo=$saldo.' zł';
			return $saldo;
		}
	}
	
}