<?php

require_once '../config.php';
require_once 'Database.php';
require_once 'DB_Attempt.php';
require_once 'Robber.php';
require_once 'utils.php';

global $g_DB, $g_max_attempt;

$action = $_GET['action'];
if (empty($action))
	return ;
if ($action == "attempt")
{
	$auto = $_GET['auto'];
	$robber_id = $_GET['robber'];
	$id_safe = $_GET['safe_box'];
	$Bank = $_SESSION['Bank'];
	$Robber = $_SESSION['robbers'][$robber_id];
	$Robber = Robber::re_create($Robber);
	if (empty($Bank) || empty($Robber) || ($SafeBox = $Bank->get($id_safe)) == false)
		return ;
	if ($SafeBox->opened == true)
	{
		$DB_Attempt = new DB_Attempt($g_DB['host'], $g_DB['dbname'], $g_DB['user'], $g_DB['pass']);
		$robber_id = $DB_Attempt->get_robber_result($SafeBox->pin, $SafeBox->id);
		$Robber = $_SESSION['robbers'][$robber_id];
		echo json_encode(['auto' => $auto,
							'robber' => $Robber->id,
							'pin' => $_SESSION['result'][$SafeBox->id],
							'attempt' => $Robber->attempt,
							'result' => true]);
		return ;
	}
	if ($auto == "true")
		$pin = $Robber->get_auto_pin($SafeBox);
	else
		$pin = $_GET['pin'];
	$SafeBox->opened = $Robber->safe_cracking($SafeBox, $pin);
	$DB_Attempt = new DB_Attempt($g_DB['host'], $g_DB['dbname'], $g_DB['user'], $g_DB['pass']);
	if ($DB_Attempt->add($robber_id, $pin, $SafeBox->id, $SafeBox->opened))
	{
		echo json_encode(['auto' => $auto,
							'lock' => $Robber->lock ? $Robber->lock - time() : 0,
							'robber' => $robber_id,
							'pin' => $pin,
							'attempt' => $Robber->attempt,
							'result' => $SafeBox->opened]);
		$_SESSION['robbers'][$robber_id] = $Robber;
	}
}
else if ($action == "add_object")
{
	$key = 1;
	if (!empty($_SESSION['robbers']))
		$key = array_key_last($_SESSION['robbers']) + 1;
	$_SESSION['robbers'][$key] = new Robber($g_max_attempt);
	echo json_encode(['key' => $key]);
}
