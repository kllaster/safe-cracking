<?php

class DataBase
{
	/**
	 * @var PDO
	 */
	protected static PDO $db;

	function __construct(string $host, string $dbname, string $user, string $pass)
	{
		$opt = [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES => false,
			PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'"
		];

		if (empty(self::$db)) {
			try {
				self::$db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",
					$user, $pass, $opt);
				self::$db->query("SET collation_connection = 'utf8_general_ci';");
			} catch (PDOException $e) {
				create_log("Ошибка при установке соединения с бд\n".$e->getMessage(), 'php_errors');
				die(500);
			}
		}
	}
}