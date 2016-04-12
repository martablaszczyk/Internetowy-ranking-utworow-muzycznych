<?php

class Logowanie extends Controller {

	public function index() {

		if($this->czyZalogowany()) {
			$this->redirect('utwory');
			exit;	
		} 

		$this->view->css = array(
				'css/logowanie/style.css'
				);

		
		$this->view->tytul = 'Internetowy Ranking Utworów Muzycznych - Logowanie';
		$this->view->display('logowanie/index');
	}

	public function zaloguj() {

		if($this->czyZalogowany()) {
			$this->redirect('utwory');
			exit;	
		} 

		$czy_pomyslnie_zalogowany = $this->model->logowanie($_POST);

		if($czy_pomyslnie_zalogowany) {
			$this->redirect('utwory');
			
		} else {
			$this->view->css = array(
					'css/logowanie/style.css'
					);
			$this->view->blad = 'Niepoprawny login/hasło';

			$this->view->tytul = 'Internetowy Ranking Utworów Muzycznych - Logowanie';
			$this->view->display('logowanie/index');
		}
		 

		
	}

	public function wyloguj() {
		if($this->czyZalogowany()) {
			$this->model->wyloguj();
		} 
		
		$this->redirect('utwory');
	}

}