<?php

require_once '../config.php';
require_once 'Database.php';
require_once 'DB_Attempt.php';
require_once 'utils.php';

global $g_DB, $g_safe;

$action = $_GET['action'];
if (empty($action))
	return ;
if ($action == "attempt")
{
	$auto = $_GET['auto'];
	$object = $_GET['object'];
	$safe_box = $_GET['safe_box'];
	if (empty($auto) || $auto == "false")
		$pin = $_GET['pin'];
	else
	{
		$DB_Attempt = new DB_Attempt($g_DB['host'], $g_DB['dbname'], $g_DB['user'], $g_DB['pass']);
		do
		{
			$pin = rand(0, 9999);
			$pin = sprintf("%04d", $pin);
		}
		while ($DB_Attempt->check_pin($pin, $safe_box) == true);
	}
	if (!isset($pin) || !isset($object) || !isset($safe_box))
		return ;
	$result = false;
	if ($g_safe[$safe_box] == $pin)
	{
		$_SESSION['result'][$safe_box] = $pin;
		$result = true;
	}
	$lock = 0;
	$lock_ses = $_SESSION['objs'][$object]['lock'];
	if (!empty($lock_ses))
	{
		if ($lock_ses > time())
		{
			$_SESSION['objs'][$object]['lock'] += 60;
			$lock = $_SESSION['objs'][$object]['lock'] - time();
		}
		else
		{
			$_SESSION['objs'][$object]['lock'] = 0;
			$_SESSION['objs'][$object]['attempt'] = 0;
		}
	}
	else
	{
		$_SESSION['objs'][$object]['attempt'] += 1;
		if ($_SESSION['objs'][$object]['attempt'] >= 10)
		{
			$_SESSION['objs'][$object]['lock'] = time() + 60;
			$lock = 60;
		}
	}
	if (empty($DB_Attempt))
		$DB_Attempt = new DB_Attempt($g_DB['host'], $g_DB['dbname'], $g_DB['user'], $g_DB['pass']);
	if ($DB_Attempt->add($object, $pin, $safe_box, $result))
		echo json_encode(['auto' => $auto,
							'lock' => $lock,
							'object' => $object,
							'pin' => $pin,
							'attempt' => $_SESSION['objs'][$object]['attempt'],
							'result' => $result]);
}
else if ($action == "add_object")
{
	$key = 1;
	if (!empty($_SESSION['objs']))
		$key = array_key_last($_SESSION['objs']) + 1;
	$_SESSION['objs'][$key]['attempt'] = 0;
	echo json_encode(['key' => $key]);
}
