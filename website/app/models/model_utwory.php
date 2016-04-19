<?php

class Model_Utwory extends Model {

	public function pobierzUtwory($sortuj = []) {

		
		$utwory = $this->_db->selectAll('utwory', [], $sortuj);

		if(is_array($utwory)) {
			if(empty($utwory)) {
				return 'Brak klientów w bazie danych';
			} else {
				$utwory = $this->utworyIOceny($utwory);
				return $utwory;
			}
		} else {
			return 'Wystąpił błąd podczas pobierania listy użytkowników<br>' . print_r($utwory, true);
		}
	}

	
	
}