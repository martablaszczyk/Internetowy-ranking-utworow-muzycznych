<!DOCTYPE html>
<html>
	<head>
		<base href="<?=__URL__?>"/>
		<title><?php echo (isset($this->tytul)) ? $this->tytul : 'Internetowy Ranking Utworów Muzycznych'?></title>
		<link rel="icon" type="image/x-icon" href="img/favicon.ico">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="search" href="public/opensearch.xml" type="application/opensearchdescription+xml" title="You site name"/>
		<link href='https://fonts.googleapis.com/css?family=Lato&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
		<link rel="stylesheet" type="text/css" href="public/css/font-awesome.css">
		<link rel="stylesheet" type="text/css" href="public/css/lightbox.min.css">
		<link rel="stylesheet" type="text/css" href="public/css/default.css">
		<?php
			if(isset($this->css)) {
				foreach ($this->css as $css) {
					echo "<link rel='stylesheet' href='public/" . $css . "'>";
				}
			}
		?>
	</head>
	<body>
		<div class="container-fluid">