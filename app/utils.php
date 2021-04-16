<?php

function create_log(string $message, string $file): void
{
	$path = $_SERVER['DOCUMENT_ROOT'].'/logs/'.$file.'.log';
	if (!file_exists($path))
		file_put_contents($path, '');
	if (!empty($fp = fopen($path, "a+")))
	{
		$message = date("[d.m.y (H:i)] ").$message;
		fwrite($fp, $message . "\r\n");
		fclose($fp);
	}
}