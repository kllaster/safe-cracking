<?php

class SafeBox
{
	public int		$id;
	public string	$pin;
	public bool		$opened = false;

	function __construct(int $id, string $pin)
	{
		$this->id = $id;
		$this->pin = $pin;
	}
}