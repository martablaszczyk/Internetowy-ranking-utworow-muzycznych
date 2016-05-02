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

	public function szukajUtworu() {
		
		if(isset($_GET['szukaj'])) {
			// wyszukiwanie na pewno
			$szukaj = $_GET['szukaj'];

			$columns = array('tytul', 'album', 'wykonawca');
			
			if(isset($_GET['sortuj'])) {
				// wyszukiwanie + sortowanie
				$sort_array = $this->getSortArray($_GET['sortuj']);
				$sort_key = array_keys($sort_array)[0];
				if($sort_key == 'ocena') {
					//bo nie ma w tabeli utwory ocen
					$utwory = $this->_db->search('utwory', $szukaj, $columns);
				} else {
					$utwory = $this->_db->search('utwory', $szukaj, $columns, $sort_array);
				}
				
			} else {
				//tylko wyszukiwanie
				$utwory = $this->_db->search('utwory', $szukaj, $columns);
			}


			if(is_array($utwory)) {
				if(!empty($utwory)) {
					$utwory = $this->utworyIOceny($utwory);
					if($sort_key == 'ocena') {
						//this should be bind by sql
						if($sort_array['ocena'] == 'ASC') {
							$this->array_sort_by_column($utwory, 'ocena');
						} else {
							$this->array_sort_by_column($utwory, 'ocena', SORT_DESC);
						}
					} 

					return $utwory;
				} else {
					return 'Brak wyników wyszukiwania';
				}
			} else {
				return 'Wystąpił błąd podczas pobierania listy utworów<br>' . $utwory;
			}
		} else {
			//tylko sortowanie
			$sort_array = $this->getSortArray($_GET['sortuj']);
			$sort_key = array_keys($sort_array)[0];
				if($sort_key == 'ocena') {
					//bo nie ma w tabeli utwory ocen.
					//this should be bind by sql
					$utwory = $this->pobierzUtwory();
					if($sort_array['ocena'] == 'ASC') {
						$this->array_sort_by_column($utwory, 'ocena');
					} else {
						$this->array_sort_by_column($utwory, 'ocena', SORT_DESC);
					}

					return $utwory;
				} else {
					return $this->pobierzUtwory($sort_array);
				}
		}
		
	}

	public function getSortArray($sortuj = null) {
		if(isset($sortuj)) {
			$column = substr($sortuj, 0, strlen($sortuj) - 1);
			$direction = substr($sortuj, -1);
			if($direction == 'A') {
				$direction = 'ASC';
			} else if($direction == 'D') {
				$direction = 'DESC';
			}

			$sortuj = array(
				$column => $direction
			);
			return $sortuj;
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

	//sortuje dwuwymiarowe tablice(tablica w tablicy)
	function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
	    $sort_col = array();
	    foreach ($arr as $key=> $row) {
	        $sort_col[$key] = $row[$col];
	    }
		array_multisort($sort_col, $dir, $arr);
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