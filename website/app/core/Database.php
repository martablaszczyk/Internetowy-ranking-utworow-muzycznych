<?php

class Database extends PDO {

	public function __construct() {
		try {
			parent::__construct(DB_TYPE . ':dbname=' . DB_NAME . '; host=' . DB_HOST, 
					DB_USER, 
					DB_PASS,
					array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $e) {
			echo 'Connection with database failed: ' . $e->getMessage();
		}
	}

	public function selectAll($from, $where_array = [], $sortuj = []) {

		$where = '';
		$number = 0;
		$length = sizeof($where_array);
		foreach ($where_array as $key => $value) {
			$where.= $key . ' = :' . $key . ' ';
			if($number < $length-1) {
				$where.= 'AND ';
			}
			$number++;
		}

		$sortuj_string = '';
		if(!empty($sortuj)) {
			$column_sort = array_keys($sortuj)[0];
			$sortuj_string = ' ORDER BY ' . $column_sort . ' ' . $sortuj[$column_sort];
		}
		
		if(!empty($where_array)) {
			$query = "SELECT * FROM {$from} WHERE {$where} {$sortuj_form}";
		} else {
			$query = "SELECT * FROM {$from} {$sortuj_string}";
		}

		try {
			$sql = $this->prepare($query);
			foreach ($where_array as $key => $value) {
				$sql->bindValue(':' . $key, $value);
			}
	
			$sql->execute();
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $ex) {
			return $ex->getMessage();
		}
		
	}

	public function selectOne($from, $where_array = []) {

		$where = '';
		$number = 0;
		$length = sizeof($where_array);
		foreach ($where_array as $key => $value) {
			$where.= $key . ' = :' . $key . ' ';
			if($number < $length-1) {
				$where.= 'AND ';
			}
			$number++;
		}
		

		try {
			$sql = $this->prepare("SELECT * FROM {$from} WHERE {$where}");
			foreach ($where_array as $key => $value) {
				$sql->bindValue(':' . $key, $value);
			}
			$sql->execute();
			return $sql->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $ex) {
			return $ex->getMessage();
		}
		
	}

	public function insert($into, $fields = []) {
		$keys = array_keys($fields);
		$values = '';
		$number = 0;
		$length = sizeof($fields);
		foreach ($fields as $field => $value) {
			$values .= ':' . $field;
			if($number < $length-1) {
				$values .= ', ';
			}
			$number++;
		}

		try {
			$sql = $this->prepare("INSERT INTO {$into}(`" . implode("`,`", $keys) . "`) VALUES({$values})");
			foreach ($fields as $field => $value) {
				$sql->bindValue(':' . $field, $value);
			}


			$sql->execute();
			return $sql;
		} catch(PDOException $ex) {
			return $ex->getMessage();
		}
	}

	public function update($table, $set_array = [], $where_array = []) {
		$set = '';
		$number = 0;
		$length = sizeof($set_array);
		foreach ($set_array as $key => $value) {
			$set.= $key . ' = :' . $key . ' ';
			if($number < $length-1) {
				$set.= ', ';
			}
			$number++;
		}


		$where = '';
		$number = 0;
		$length = sizeof($where_array);
		foreach ($where_array as $key => $value) {
			// dodana dwójka na końcu by w przypadku powtarzających się nazw z $set_array nie było problemu
			$where.= $key . ' = :' . $key . '2 ';
			if($number < $length-1) {
				$where.= 'AND ';
			}
			$number++;
		}


		try {
			$sql = $this->prepare("UPDATE {$table} SET {$set} WHERE {$where}");
			foreach ($set_array as $key => $value) {
				$sql->bindValue(':' . $key, $value);
			}
			foreach ($where_array as $key => $value) {
				$sql->bindValue(':' . $key . '2', $value);
			}

			$sql->execute();
			return $sql;
		} catch(PDOException $ex) {
			return $ex->getMessage();
		}
	}

	public function count($from, $where_array = [], $where_not_array = []) {

		$where = '';
		$number = 0;
		$length = sizeof($where_array);
		foreach ($where_array as $key => $value) {
			$where.= $key . ' = :' . $key . ' ';
			if($number < $length-1) {
				$where.= 'AND ';
			}
			$number++;
		}
		
		$number = 0;
		$length = sizeof($where_not_array);

		if(!empty($where_not_array)) {
			$where_not = 'AND ';
		} else {
			$where_not = '';
		}

		foreach ($where_not_array as $key => $value) {
			$where_not.= $key . ' != :' . $key . ' ';
			if($number < $length-1) {
				$where_not.= 'AND ';
			}
			$number++;
		}

		try {
			$sql = $this->prepare("SELECT count(*) FROM {$from} WHERE {$where} {$where_not}");

			ChromePhp::log($sql);

			foreach ($where_array as $key => $value) {
				$sql->bindValue(':' . $key, $value);
				ChromePhp::log(':' . $key . ' ' . $value);
			}

			foreach ($where_not_array as $key => $value) {
				$sql->bindValue(':' . $key, $value);
				ChromePhp::log(':' . $key . ' ' . $value);
			}

			$sql->execute();
			return $sql;
		} catch(PDOException $ex) {
			return $ex->getMessage();
		}
		
	}

	public function search($from, $szukaj, $like_array = [], $sortuj = []) {
		$like = '';
		$number = 0;
		$length = sizeof($like_array);
		foreach ($like_array as $key) {
			$like.= $key . ' LIKE :' . $key . ' ';
			if($number < $length-1) {
				$like.= 'OR ';
			}
			$number++;
		}
		
		$sortuj_string = '';
		if(!empty($sortuj)) {
			$column_sort = array_keys($sortuj)[0];
			$sortuj_string = ' ORDER BY ' . $column_sort . ' ' . $sortuj[$column_sort];
		}

		try {
			$sql = $this->prepare("SELECT * FROM {$from} WHERE {$like} {$sortuj_string}");
			foreach ($like_array as $key) {
				$sql->bindValue(':' . $key, '%'.$szukaj.'%');
			}
			$sql->execute();
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $ex) {
			return $ex->getMessage();
		}
		
	}
}