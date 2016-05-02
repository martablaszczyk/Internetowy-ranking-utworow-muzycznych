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

	public function glosuj() {
		if (isset($_POST['tytul']) && isset($_POST['ocena'])) {
			$tytul = $_POST['tytul'];
			$ocena = $_POST['ocena'];

			$utwor = $this->_db->selectOne('utwory', array(
				'tytul' => $tytul
			));
			$utwor_id = $utwor['id'];

			$rate = $this->_db->selectOne('oceny', array(
				'id_utworu' => $utwor_id
			));

			if(empty($rate)) {
				$result_insert = $this->_db->insert('oceny', array(
					'id_utworu' => $utwor_id,
					'ocena' => $ocena
				));
			} else {
				$rate = $rate['ocena'];
				$new_rate = ($ocena + $rate)/2;
				$result_insert = $this->_db->update('oceny', array(
					'ocena' => $new_rate
				), array(
					'id_utworu' => $utwor_id
				));
			}

			if(is_object($result_insert)) {
				return 'ok';
			} else {
				return 'Wystąpił błąd podczas aktualizacji oceny ' . $result_insert;
			}

		} else {
			return 'Nie ustawiono wszystkich zmiennych';
		}
		
	}

	public function utworyIOceny($utwory) {
		$oceny = $this->_db->selectAll('oceny');
		//łączenie oceny z odpowiednim tytułem, tak wiem- powinno to byc w sql bo szybciej
		for($i = 0; $i < sizeof($utwory); $i++) {
			foreach ($oceny as $ocena) {
				if($utwory[$i]['id'] == $ocena['id_utworu']) {
					$utwory[$i]['ocena'] = $ocena['ocena'];
				}
			}
		}
		return $utwory;
	}
	
}