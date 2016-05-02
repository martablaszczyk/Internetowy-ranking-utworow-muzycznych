<?php

class Utwory extends Controller {

	public function index() {

		if($this->czyZalogowany()) {
			$this->view->czyZalogowany = true;
		} else {
			$this->view->czyZalogowany = false;
		}

		$this->view->css = array(
			'css/utwory/style.css'
		);

		$this->view->js = array(
			'js/utwory/utwory_script.js'
		);

		$utwory = $this->model->pobierzUtwory();
		if(is_array($utwory)) {
			$this->view->utwory = $utwory;
		} else {
			$this->view->wiadomosc = $utwory;
		} 
		
		$this->view->tytul = 'Internetowy Ranking Utworów Muzycznych';
		$this->view->display('utwory/index');
	}

	public function dodaj() {
		if(!$this->czyZalogowany()) {
			$this->redirect('utwory');
			exit;	
		}

		echo $this->model->dodaj($_POST);
	}

	public function edytuj() {
		if(!$this->czyZalogowany()) {
			$this->redirect('utwory');
			exit;	
		}

		echo $this->model->edytuj($_POST);
	}

	public function szukaj() {
		if($this->czyZalogowany()) {
			$this->view->czyZalogowany = true;
		} else {
			$this->view->czyZalogowany = false;
		}

		$this->view->css = array(
			'css/utwory/style.css'
		);

		$this->view->js = array(
			'js/utwory/utwory_script.js'
		);

		$utwory = $this->model->szukajUtworu();
		if(is_array($utwory)) {
			$this->view->utwory = $utwory;
		} else {
			$this->view->wiadomosc = $utwory;
		} 
		
		$this->view->tytul = 'Internetowy Ranking Utworów Muzycznych';
		$this->view->display('utwory/index');
	}

	public function glosuj() {
		echo $this->model->glosuj();
	}

	public function usun() {
		if(!$this->czyZalogowany()) {
			$this->redirect('utwory');
			exit;	
		}
		echo $this->model->usun($_POST);
	}

}