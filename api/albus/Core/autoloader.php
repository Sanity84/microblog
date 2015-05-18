<?php 

$req = new albus\Core\Request();
$res = new albus\Core\Response();
$db = new albus\Core\Database();
$router = new albus\Core\Router();

// Simpliest autoloader ever
function __autoload($class_name) {

	$core_file = ROOT.DS.str_replace('\\', DS, $class_name).'.php';
	require $core_file;
}