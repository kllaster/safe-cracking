<?php

class DB_Attempt extends DataBase
{
	function create_table(): bool
	{
		return (bool)self::$db->query("CREATE TABLE IF NOT EXISTS `attempt_pins` (	
											`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
											`robber` INT,
											`pin` VARCHAR(4),
											`safe_box` INT,
											`result` BOOL,
											`session_id` VARCHAR(40)
											) CHARACTER SET utf8 COLLATE utf8_general_ci;");
	}

	function select(int $safe_box, int $limit = 12): array
	{
		if (empty($sid = session_id()))
			return array();
		$select = self::$db->prepare("SELECT `robber`, `pin`, `result` FROM `attempt_pins`
											WHERE `safe_box` = :safe_box AND `session_id` = :session_id
										  	ORDER BY `id` DESC LIMIT $limit;");
		$select->bindParam(':safe_box', $safe_box);
		$select->bindParam(':session_id', $sid);
		$select->execute();
		if (empty($result = $select->fetchAll()))
			return array();
		return ($result);
	}

	function add(int $robber_id, string $pin, int $safe_box, bool $result): bool
	{
		if (empty($sid = session_id()))
			return false;
		$insert = self::$db->prepare("INSERT INTO `attempt_pins` (`id`, `robber`, `pin`, `safe_box`, `result`, `session_id`) 
                                                            VALUES ('', :robber, :pin, :safe_box, :result, :session_id);");
		$insert->bindParam(':robber', $robber_id);
		$insert->bindParam(':pin', $pin);
		$insert->bindParam(':safe_box', $safe_box);
		$insert->bindParam(':result', $result);
		$insert->bindParam(':session_id', $sid);
		return ($insert->execute());
	}

	function check_pin(string $pin, int $safe_box): bool
	{
		if (empty($sid = session_id()))
			return false;
		$select = self::$db->prepare("SELECT `pin` FROM `attempt_pins`
											WHERE `safe_box` = :safe_box AND `pin` = :pin AND 
											      `session_id` = :session_id;");
		$select->bindParam(':pin', $pin);
		$select->bindParam(':safe_box', $safe_box);
		$select->bindParam(':session_id', $sid);
		$select->execute();
		return (bool)($select->fetchAll());
	}

	function get_robber_result(string $pin, int $safe_box): int
	{
		if (empty($sid = session_id()))
			return false;
		$select = self::$db->prepare("SELECT `robber` FROM `attempt_pins`
											WHERE `safe_box` = :safe_box AND `pin` = :pin AND 
											      `session_id` = :session_id;");
		$select->bindParam(':pin', $pin);
		$select->bindParam(':safe_box', $safe_box);
		$select->bindParam(':session_id', $sid);
		$select->execute();
		return (int)($select->fetchAll()[0]['robber']);
	}
}