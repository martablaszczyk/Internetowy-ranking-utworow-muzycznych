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

	public function dodaj($post) {
		//sprawdzenie czy isset i nie puste
		//czy dlugosc jest odpowiednia
		//czy nie ma juz takiego maila w bazie
		//dodaj goscia do bazy i zwroc ok

		$bledy = array();

		if(!isset($post['tytul']) || !isset($post['album']) || !isset($post['wykonawca'])) {
			$bledy[] = 'Nie kombinuj hakerze.';
		}

		$tytul = $post['tytul'];
		$album = $post['album'];
		$wykonawca = $post['wykonawca'];

		if(empty($tytul) || empty($album) || empty($wykonawca)) {
			$bledy[] = 'Nie wprowadzono wszystkich danych.';
		}

		if((strlen($tytul) > 50) || (strlen($album) > 50) || (strlen($wykonawca) > 50)) {
			$bledy[] = 'Nie zmieniaj maksymalnej wartości pól.';
		}

		if(count($bledy) == 0) {
			$czyZdjecie = false;
			if(!empty($_FILES['zdjecie']['name'])) {

				$zdjecie = $_FILES['zdjecie'];
				// Check if image file is a actual image or fake image
				$check = getimagesize($zdjecie['tmp_name']);
				if($check) {
					$czyZdjecie = true;
					$dir = 'okladki/';
					//name with file extension
					$target_file = $dir . basename($zdjecie['name']);
					$image_type = pathinfo($target_file, PATHINFO_EXTENSION);
					$filename = basename($zdjecie['name']);

					$i = 1;
					if(file_exists($target_file)) {
						//name without extension
						$name = basename($zdjecie['name'], '.' . $image_type);
						//katalog + nazwa + cyfra + . + rozszerzenie
						while(file_exists($dir . basename($name . $i . '.' . $image_type))) {
							$i++;
						}
						$target_file = $dir . basename($name . $i . '.' . $image_type);
						$filename = $name . $i . '.' . $image_type;
					}		
				
				}
			}

			$result =  $this->_db->count('utwory', array(
							'tytul' => $tytul
						));

			if(is_object($result)) {

				//rowCount() nie zawsze działa w MySQL, więc fetchCoulmn()
				if($result->fetchColumn() == 0) {
					$this->_db->beginTransaction();

					if($czyZdjecie) {
						$result_insert = $this->_db->insert('utwory', array(
							'tytul' => $tytul,
							'album' => $album,
							'wykonawca' => $wykonawca,
							'zdjecie' => $filename
						));
					} else {
						$result_insert = $this->_db->insert('utwory', array(
							'tytul' => $tytul,
							'album' => $album,
							'wykonawca' => $wykonawca
						));
					}
					

					if(is_object($result_insert)) {
						if($czyZdjecie) {
							if(move_uploaded_file($zdjecie['tmp_name'], $target_file)) {
								$this->_db->commit();
								return 'ok';
							} else {
								$this->_db->rollBack();
								return 'Wystąpił błąd podczas wgrywania zdjęcia';
							}
						} else {
							$this->_db->commit();
							return 'ok';
						}
					} else {
						$this->_db->rollBack();
						return 'Wystąpił błąd podczas dodawania utworu do bazy. ' . $result_insert;
					}

					
				} else {
					return 'Podany Tytuł istnieje już w bazie danych.';
				}
			} else {
				return 'Wystąpił błąd w bazie danych. ' . $result;
			}

		} else {
			$raport = '';
			foreach ($bledy as $blad) {
				$raport.= $blad . ' ';
			}
			return $raport;
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