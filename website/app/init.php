<?php

header('Content-Type: text/html; charset=utf-8');
session_start();

require_once 'core/App.php';
require_once 'core/Controller.php';
require_once 'core/Database.php';
require_once 'core/Model.php';
require_once 'core/View.php';
require_once 'config/database.php';
require_once 'config/paths.php';

spl_autoload_register(function($lib) {
	require_once 'libs/'.$lib.'.php';
});

$app = new App;