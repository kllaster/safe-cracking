<?php

class Bank
{
	public array $safeBoxes = array();

	public function __construct($safe_cfg)
	{
		foreach ($safe_cfg as $key => $value)
		{
			$new_SafeBox = new SafeBox(($key + 1), $value);
			$this->add($new_SafeBox);
		}
	}

	public function add(SafeBox $safeBox): void
	{
		array_push($this->safeBoxes, $safeBox);
	}

	public function update(SafeBox $safeBox): bool
	{
		$result = false;
		foreach ($this->safeBoxes as &$item)
		{
			if ($item->id == $safeBox->id)
			{
				$item = $safeBox;
				$result = true;
			}
		}
		return ($result);
	}

	public function get(int $id): SafeBox | bool
	{
		$safeBox = false;
		foreach ($this->safeBoxes as $item)
		{
			if ($item->id == $id)
				$safeBox = $item;
		}
		return ($safeBox);
	}
}