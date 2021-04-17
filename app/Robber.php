<?php

class Robber
{
	public int $attempt = 0;
	public int $max_attempt;
	public int $lock = 0;

	function __construct(int $max_attempt)
	{
		$this->max_attempt = $max_attempt;
	}

	public function get_auto_pin(SafeBox $safeBox): string
	{
		global $g_DB;

		$DB_Attempt = new DB_Attempt($g_DB['host'], $g_DB['dbname'], $g_DB['user'], $g_DB['pass']);
		do
		{
			$pin = rand(0, 9999);
			$pin = sprintf("%04d", $pin);
		}
		while ($DB_Attempt->check_pin($pin, $safeBox->id) == true);
		return ($pin);
	}

	public function safe_cracking(SafeBox $safeBox, string $pin): bool
	{
		$this->attempt++;
		$this->check_lock();
		if (isset($pin) && $safeBox->pin == $pin)
			return (true);
		return (false);
	}

	public function check_lock()
	{
		if ($this->lock != 0)
		{
			if ($this->lock > time())
				$this->lock += 60;
			else
			{
				$this->lock = 0;
				$this->attempt = 0;
			}
		}
		else if ($this->attempt >= $this->max_attempt)
			$this->lock = time() + 60;
	}

	static function re_create($object): Robber
	{
		$object = (array)$object;
		$new_object = new Robber(1);
		foreach($object as $k => $v)
		{
			$parts = explode(chr(0), $k);
			if ($k != '__PHP_Incomplete_Class_Name')
			{
				if ($parts[0] == $k)
					$new_object->re_create_private($k, $v);
				else
					$new_object->re_create_private($parts[2], $v);
			}
		}
		return $new_object;
	}

	private function re_create_private($name, $value)
	{
		$this->$name = $value;
	}
}