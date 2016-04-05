<?php

class View {

	public function display($view, $disable=false) {
		// zmienia podswietlenie menu
		$page = $view;

		if($disable) {
			//tak dla pokoleń: tu raczej powinien być po prostu header_min.php, bez sidebar menu itd.
			require_once '../app/views/'. $view .'.php';
		} else {
			require_once '../app/views/header.php';
			require_once '../app/views/'. $view .'.php';
			require_once '../app/views/footer.php';
		}
	}

	public function displayXML($view) {
		// zmienia podswietlenie menu
		$page = $view;

		require_once '../app/views/'. $view .'.php';
	}

}