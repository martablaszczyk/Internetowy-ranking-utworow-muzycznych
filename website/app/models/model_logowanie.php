<?php

class Model_Logowanie extends Model {
	

	public function logowanie($post) {
		//sprawdzenie czy login i haslo ok wtedy return true, else ret. false

		

		$select = $this->_db->selectOne('administratorzy', array(
			'username' => $post['login']
			));
		

		if(isset($select['id'])) {
			if($select['password'] === $post['password']) {
				$_SESSION['logged'] = true;

				return true;
			}
		}

		return false;
	}

	public function wyloguj() {
		session_unset();
	}
}