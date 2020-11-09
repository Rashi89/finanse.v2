<?php
 namespace App\Models;
 
 use PDO;
 
 use \App\Token;
 
 class RememberedLogin extends \Core\Model
 {
	 public static function findByToken($token)
	 {
		 //nowy obiekt typu Token
		 $token = new Token($token);
		 //zrobmy jego hash
		 $token_hash=$token->getHash();
		 //szukamy go w bazie
		 
		 $sql = 'SELECT * FROM remembered_logins WHERE token_hash=:token_hash';
		 
		 $db=static::getDB();
		 $stmt = $db->prepare($sql);
		 
		 $stmt->bindValue(':token_hash',$token_hash,PDO::PARAM_STR);
		 
		 $stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());
		 
		 $stmt->execute();
		 
		 return $stmt->fetch();
	 }
	 //metoda ktora pobierze usera z tym tokenem
	 
	 public function getUser()
	 {
		 return User::findByID($this->user_id);
	 }
	 
	 public function hasExpired()
	 {
		 //jesli ilosc sekund os 1 stycznia 1970 w naszej tabeli w kolumnie expires_at //jest mniejsza niz ilosc sekund do dnia obecnego to zwracamy true
		 return strtotime($this->expires_at) < time();
	 }
	 
	 public function delete()
	 {
		 $sql = 'DELETE FROM remembered_logins WHERE token_hash=:token_hash';
		 
		 $db=static::getDB();
		 $stmt = $db->prepare($sql);
		 
		 $stmt->bindValue(':token_hash',$this->token_hash,PDO::PARAM_STR);
		 $stmt->execute();
	 }
 }