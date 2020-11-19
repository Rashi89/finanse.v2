<?php

namespace App\Models;

use PDO;
use \App\Token;
use \App\Mail;
use \Core\View;


class User extends \Core\Model
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
	
	public function save()
	{
		//check validate
		
		$this->validate();
		
		if(empty($this->errors))
		{
		$password_hash = password_hash($this->password,PASSWORD_DEFAULT);
		
		$token = new Token();
		$hashed_token = $token->getHash();
		
		$this->activation_token = $token->getValue();
		
		$sql = "INSERT INTO users (name, email, password_hash,activation_hash) VALUES (:name,:email,:password_hash,:activation_hash)";
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':name', $this->name,PDO::PARAM_STR);
		$stmt->bindValue(':email', $this->email,PDO::PARAM_STR);
		$stmt->bindValue(':password_hash', $password_hash,PDO::PARAM_STR);
		$stmt->bindValue(':activation_hash', $hashed_token,PDO::PARAM_STR);
		
		return $stmt->execute();
		}
		
		return false;
	}
	
	public function validate()
	{
		//check name
		
		if($this->name =='')
		{
			$this->errors[]="Name is required";
		}
		
		if(static::nameExist($this->name,$this->id ?? null))
		{
			$this->errors[]="Name exist!";
		}
		//check email
		if(filter_var($this->email,FILTER_VALIDATE_EMAIL) === false)
		{
			$this->errors[]="Invalid email";
		}
		
		if(static::emailExist($this->email,$this->id ?? null))
		{
			$this->errors[]="This email exist";
			
		}
		
		//check passwords
		if(isset($this->password))
		{
			//dlugosc hasla
			if(strlen($this->password)<6)
			{
				$this->errors[]="Please enter at least 6 characters for the password";
			}
			//conajmniej jedna litera
			if(preg_match('/.*[a-z]+.*/i',$this->password)==0)
			{
				$this->errors[]="Password needs at least one letter";
			}
			//conajmniej jedna cyfra
			if(preg_match('/.*\d+.*/i',$this->password)==0)
			{
				$this->errors[]="Password needs at least one number";
			}
		}
	}
	//jesli chcemy nowego usera to mamy email i null
	//jesli mamy zmiane hasla to mamy juz email i id
	public static function emailExist($email,$potencjal_id = null)
	{
		//szukamy w bazie usera o tym emailu
		$user = static::findByEmail($email);
		//jesli istnieje
		if($user)
		{		//jesli okaze sie ze id znalezionego usera jest rozne od potencjalnego
				//to zwracamy true a zatem pojawi sie komunikat ze juz email jest
				if($user->id != $potencjal_id)
				{
					return true;
				}
				else return false;
		}
		//jesli nie istnieje zwracamy false
		return false;
	}
	
	public static function nameExist($name,$potencjal_id=null)
	{
		$user = static::findByName($name);
		
		if($user)
		{		//jesli okaze sie ze id znalezionego usera jest rozne od potencjalnego
				//to zwracamy true a zatem pojawi sie komunikat ze juz email jest
				if($user->id != $potencjal_id)
				{
					return true;
				}
				else return false;
		}
		//jesli nie istnieje zwracamy false
		return false;
		
	}
	
	public static function findByEmail($email)
	{
		$sql = "SELECT * FROM users WHERE email = :email";
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':email', $email ,PDO::PARAM_STR);
		
		$stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());
		
		$stmt->execute();
		
		return $stmt->fetch();
	}
	
		public static function findByName($name)
	{
		$sql = "SELECT * FROM users WHERE name= :name";
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':name', $name ,PDO::PARAM_STR);
		
		$stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());
		
		$stmt->execute();
		
		return $stmt->fetch();
	}
	
	public static function authenticate($email,$password)
	{
		$user = static::findByEmail($email);
		
		if($user && $user->is_active){
			
			if(password_verify($password,$user->password_hash)){
				return $user;
			}
		}
		return false;
	}
	
	public static function findByID($id)
	{
		$sql = 'SELECT * FROM users WHERE id = :id';
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':id', $id ,PDO::PARAM_INT);
		
		$stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());
		//wykonuje instrukcje
		$stmt->execute();
		//zwraca tablice z wynikiem czyli w tym przypadku id name email password_hash tam gdzie id jest rowne id osoby ktora chce sie zalogowac
		return $stmt->fetch();
	}
	
	public function rememberLogin()
    {
        $token = new Token();
        $hashed_token = $token->getHash();
		//ustawiamy zmienna remember token dla tego usera
		$this->remember_token=$token->getValue();
		//ustawiamy zmienna expiry_timestamp
        $this->expiry_timestamp = time() + 30*24*60*60;  // 30 days from now

        $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at)
                VALUES (:token_hash, :user_id, :expires_at)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR);
		//zwraca true or false i wykonuje wszystkie instrukcje
        return $stmt->execute();
    }	
	
	public static function sendPasswordReset($email)
	{
		$user=static::findByEmail($email);
		
		if($user)
		{
			if($user->startPasswordReset())
			{
				$user->sendPasswordResetEmail();
			}
		}
	}
	
	protected function startPasswordReset()
	{
		$token=new Token();
		$hashed_token=$token->getHash();
		$this->password_reset_token = $token->getValue();
		
		$expiry_timestamp=time()+60*60*2; //na dwie godziny
		
		$sql = 'UPDATE users 
					SET password_reset_hash=:token_hash,
						   password_reset_expires_at=:expires_at 
					WHERE id=:id';
		$db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $expiry_timestamp), PDO::PARAM_STR);
		$stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
		//zwraca true or false i wykonuje wszystkie instrukcje
        return $stmt->execute();
	}
	
	protected function sendPasswordResetEmail()
	{
		$url = 'http://'.$_SERVER['HTTP_HOST'].'/password/reset/'.$this->password_reset_token;
		
		$text = View::getTemplate('Password/reset_password.txt',['url' => $url]);
		$html=View::getTemplate('Password/reset_password.html',['url' => $url]);
		Mail::send($this->email,'Password reset',$text,$html);
	}
	
	public static function findByPasswordReset($token)
	{
		$token=new Token($token);
		$hashed_token=$token->getHash();
		
		$sql = 'SELECT * FROM users WHERE password_reset_hash = :token_hash';
		$db = static::getDB();
		
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
		$stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());
		$stmt->execute();
		
		$user= $stmt->fetch(); 
		
		if($user)
		{
			if(strtotime($user->password_reset_expires_at)>time())
			{
				return $user;
			}
		}
		
	}
	
	public function resetPassword($password)
	{
		$this->password =$password;
		
		$this->validate();
		
		if(empty($this->errors))
		{
			$password_hash= password_hash($this->password,PASSWORD_DEFAULT);
			
			$sql = 'UPDATE users
						SET password_hash =:password_hash,
								password_reset_hash = NULL,
								password_reset_expires_at = NULL
						WHERE id=:id';
			
			$db=static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':password_hash',$password_hash,PDO::PARAM_STR);
			$stmt->bindValue(':id',$this->id,PDO::PARAM_INT);
			
			return $stmt->execute();
		}
		return false;
	}
	
	public function sendActivationEmail()
	{
		$url = 'http://'.$_SERVER['HTTP_HOST'].'/signup/activate/'.$this->activation_token;
		
		$text = View::getTemplate('Signup/activation_email.txt',['url' => $url]);
		$html=View::getTemplate('Signup/activation_email.html',['url' => $url]);
		Mail::send($this->email,'Account activation',$text,$html);
	}
	
	public static function activate($value)
	{
		$token = new Token($value);
		$hashed_token = $token->getHash();
		
		$sql = 'UPDATE users
					SET is_active =1,
							activation_hash=NULL
					WHERE activation_hash=:hashed_token';
					
		$db=static::getDB();
		$stmt = $db->prepare($sql);
			
		$stmt->bindValue(':hashed_token',$hashed_token,PDO::PARAM_STR);
		
		$stmt->execute();
		
	}
	
	public function updateProfile($data)
	{
		$this->name = $data['name'];
		$this->email = $data['email'];
		
		if($data['password'] !=''){
			$this->password = $data['password'];
		}
		
		$this->validate();
		
		if(empty($this->errors))
		{
				$sql = 'UPDATE users
							SET name=:name,
									email=:email';
				if(isset($this->password))
				{
					$sql .=' , password_hash=:password_hash';
				}
							
					$sql .="\nWHERE id=:id";
				
				$db = static::getDB();
				$stmt = $db->prepare($sql);
				
				$stmt->bindValue(':name',$this->name,PDO::PARAM_STR);
				$stmt->bindValue(':email',$this->email,PDO::PARAM_STR);
				$stmt->bindValue(':id',$this->id,PDO::PARAM_INT);
				
				if(isset($this->password))
				{
					$password_hash = password_hash($this->password,PASSWORD_DEFAULT);
					$stmt->bindValue(':password_hash',$password_hash,PDO::PARAM_STR);
				}
				
				return $stmt->execute();
		}
		
		return false;
	}

	
	public function upgradeBase()
	{

		$sql ="INSERT INTO incomes_category_assigned_to_users (user_id, name) SELECT users.id,incomes_category_default.name FROM incomes_category_default, users WHERE users.name=:name;
						SET @max_id = (SELECT MAX(id) FROM incomes_category_assigned_to_users) + 1;
						#SELECT @max_id;
						SET @sql = CONCAT('ALTER TABLE `incomes_category_assigned_to_users` AUTO_INCREMENT = ', @max_id);
						PREPARE stmt FROM @sql;
						EXECUTE stmt;
						
						INSERT INTO payment_methods_assigned_to_users (user_id, name) SELECT users.id,payment_methods_default.name FROM payment_methods_default, users WHERE users.name=:name;
						SET @max_id = (SELECT MAX(id) FROM payment_methods_assigned_to_users) + 1;
						#SELECT @max_id;
						SET @sql = CONCAT('ALTER TABLE `payment_methods_assigned_to_users` AUTO_INCREMENT = ', @max_id);
						PREPARE stmt FROM @sql;
						EXECUTE stmt;
						
						INSERT INTO expenses_category_assigned_to_users (user_id, name) SELECT users.id,expenses_category_default.name FROM expenses_category_default, users WHERE users.name=:name;
						SET @max_id = (SELECT MAX(id) FROM expenses_category_assigned_to_users) + 1;
						#SELECT @max_id;
						SET @sql = CONCAT('ALTER TABLE `expenses_category_assigned_to_users` AUTO_INCREMENT = ', @max_id);
						PREPARE stmt FROM @sql;
						EXECUTE stmt;";
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':name',$this->name,PDO::PARAM_STR);
		return $stmt->execute();
	}
	
	public static function sumExpenses($id)
	{
		
		$sql='SELECT COUNT(*) FROM expenses';
		$db=static::getDB();
		$stmt = $db->prepare($sql);
		
		//$stmt->bindValue(':id',$user->id,PDO::PARAM_INT);
		
		$stmt->execute();
		$expenses=$stmt->fetch(PDO::FETCH_ASSOC); 
		//$stmt->rowCount();
		$empty=$expenses['COUNT(*)'];
		if($empty==0)
		{
			//$stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());
			//$expense=$stmt->fetch(PDO::FETCH_ASSOC); 
			//$all_expense=$expense['SUM(amount)'];
			return 0;
		}
		else
		{
			$sql1 = 'SELECT SUM(amount) FROM expenses WHERE user_id=:id';
			$db=static::getDB();
			$stmt = $db->prepare($sql1);
			$stmt->bindValue(':id',$id,PDO::PARAM_INT);
			$stmt->execute();
			$stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());
			$expense=$stmt->fetch(PDO::FETCH_ASSOC); 
			$all_expense=$expense['SUM(amount)'];
			return $all_expense;
			
		}
	}
	
		public static function sumIncomes($id)
	{
		
		$sql='SELECT COUNT(*) FROM incomes';
		$db=static::getDB();
		$stmt = $db->prepare($sql);
		
		//$stmt->bindValue(':id',$user->id,PDO::PARAM_INT);
		
		$stmt->execute();
		$incomes=$stmt->fetch(PDO::FETCH_ASSOC); 
		//$stmt->rowCount();
		$empty=$incomes['COUNT(*)'];
		if($empty==0)
		{
			//$stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());
			//$expense=$stmt->fetch(PDO::FETCH_ASSOC); 
			//$all_expense=$expense['SUM(amount)'];
			return 0;
		}
		else
		{
			$sql1 = 'SELECT SUM(amount) FROM incomes WHERE user_id=:id';
			$db=static::getDB();
			$stmt = $db->prepare($sql1);
			$stmt->bindValue(':id',$id,PDO::PARAM_INT);
			$stmt->execute();
			$stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());
			$income=$stmt->fetch(PDO::FETCH_ASSOC); 
			$all_income=$income['SUM(amount)'];
			return $all_income;
			
		}
	}
	
	public static function saldo($id)
	{
		$sum_income=static::sumIncomes($id);
		$sum_expense=static::sumExpenses($id);
		
		$saldo=$sum_income-$sum_expense;
		$saldo=number_format($saldo, 2, '.', ' ');
		
		return $saldo;
		
	}
	
	public static function getAllIncomes($id)
	{
		$sql='SELECT name FROM incomes_category_assigned_to_users WHERE user_id=:id GROUP BY name';
		$db=static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':id',$id,PDO::PARAM_INT);
		$stmt->execute();
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		return $results;
	}
	
	public static function getAllExpenses($id)
	{
		$sql='SELECT name FROM expenses_category_assigned_to_users WHERE user_id=:id GROUP BY name';
		$db=static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':id',$id,PDO::PARAM_INT);
		$stmt->execute();
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		return $results;
	}
	
	public static function getAllPayMethod($id)
	{
		$sql='SELECT name FROM payment_methods_assigned_to_users WHERE user_id=:id GROUP BY name';
		$db=static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':id',$id,PDO::PARAM_INT);
		$stmt->execute();
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		return $results;
	}

}