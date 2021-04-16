<?php

$g_debug = false;

// Config Database
$g_DB = [
	'host' => '',
	'dbname' => '',
	'user' => '',
	'pass' => ''
];

// Safes (Number => PIN)
$g_safe = [
	1 => "1234",
	2 => "4325",
	3 => "5461",
	4 => "4234"
];

ini_set('display_errors', $g_debug ? 1 : 0);
error_reporting(E_ERROR | E_WARNING);

ini_set('session.gc_maxlifetime', 1209600);
ini_set('session.cookie_lifetime', 1209600);

ini_set('log_errors', 'on');
ini_set('error_log', 'logs/php_errors.log');

mb_internal_encoding('UTF-8');

session_start();