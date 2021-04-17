<?php

require_once 'app/Bank.php';
require_once 'app/SafeBox.php';

$g_debug = false;

// Config Database
$g_DB = [
	'host' => '',
	'dbname' => '',
	'user' => '',
	'pass' => ''
];

// array Safes (string PIN)
$g_safe = ["1234", "4325", "5461", "4234"];

// Maximum number of attempts before blocking
$g_max_attempt = 10;

ini_set('display_errors', $g_debug ? 1 : 0);
error_reporting(E_ERROR | E_WARNING);

ini_set('session.gc_maxlifetime', 1209600);
ini_set('session.cookie_lifetime', 1209600);

ini_set('log_errors', 'on');
ini_set('error_log', 'logs/php_errors.log');

mb_internal_encoding('UTF-8');

session_start();

// Creating Bank
if (empty($_SESSION['Bank']))
	$_SESSION['Bank'] = new Bank($g_safe);