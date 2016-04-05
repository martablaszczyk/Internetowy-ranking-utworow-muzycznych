<?php

class Controller {

	public function __construct() {
		$this->view = new View();
	}

	public function czyZalogowany() {
		if(isset($_SESSION['logged'])) {
			return true;
		}
		return false;
	}

	public function loadModel($model) {
		$model = 'model_' . $model;
		if(file_exists('../app/models/'. $model .'.php')) {
			require_once '../app/models/'. $model .'.php';
			$this->model = new $model();
		} else {
			$this->model = new Model();
		}

	}

	public function redirect($where) {
		header('Location: ' . __URL__ . $where);
		exit;
	}
}