<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;

// rozszerzamy Authenticated bo musi sie najpierw zalogowac
class Profile extends Authenticated
{
	
	protected function before()
	{
		parent::before();
		$this->user = Auth::getUser();
	}
	
	
	public function showAction()
	{
		View::renderTemplate('Profile/show.html', [
				'user' => $this->user
		]);
	}
	
	public function editnameAction()
	{
		View::renderTemplate('Profile/editname.html', [
				'user' => $this->user
		]);		
	}
	
	public function updatenameAction()
	{
		$name=$this->user->name;
		if($this->user->sameLogins($_POST,$name))
		{
			if($this->user->updateProfileLogin($_POST,$name))
			{
				Flash::addMessage('Changes saved');
				
				$this->redirect('/profile/show');
			}
			else
			{
				Flash::addMessage('Taka nazwa już istnieje!');
				$this->redirect('/profile/editname');
			}
		}
		else
		{
			Flash::addMessage('To Twój login!');
				$this->redirect('/profile/editname');
		}
		
	}
	
	public function editpassAction()
	{
		View::renderTemplate('Profile/editpass.html', [
				'user' => $this->user
		]);		
	}
	
	public function updatepassAction()
	{	
		$hash=$this->user->password_hash;
		
		if($this->user->goodOldPass($_POST,$hash))
		{
			if($this->user->editPassword($_POST))
			{
				Flash::addMessage('Hasło zostało zmienione!');
				$this->redirect('/profile/editpass');
			}
			else{
				Flash::addMessage('Hasło niezostało zmienione!');
			$this->redirect('/profile/editpass');	
			}
		}
		else
		{
				Flash::addMessage('Błędne stare hasło!');
				$this->redirect('/profile/editpass');
		}
	}
	public function editemailAction()
	{
		View::renderTemplate('Profile/editemail.html', [
				'user' => $this->user
		]);		
	}
	
	public function updateemailAction()
	{
			$id=$this->user->id;
			
			if($this->user->editEmail($id,$_POST))
			{
				Flash::addMessage('Adres e-mail został zmieniony!');
				$this->redirect('/profile/editemail');
			}
			else
			{
				Flash::addMessage('Adres e-mail niezostał zmieniony!');
				$this->redirect('/profile/editemail');
			}
	}
		
}