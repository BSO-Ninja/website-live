<?php

namespace core;

use PDO;

/**
 * Static database class, so there is no need to use the global _DATABASE anymore
 * Class DB
 * @package core
 */
class DB {

	private static $db_host = DB_HOST;
	private static $db_user = DB_USER;
	private static $db_password = DB_PASSWORD;
	private static $db_database = DB_DATABASE;
	private static $initialized = false;
	/**
	 * @var PDO
	 */
	private static $pdo;

	public static function init() {
		if(!self::$initialized){
			self::$pdo = new PDO('mysql:host=' . self::$db_host . ';dbname=' . self::$db_database . ';charset=utf8', self::$db_user, self::$db_password);
			self::$initialized = true;
		}
	}

	/**
	 * @param String $query the executed query
	 * @param array $params the params
	 * @return array rows
	 *
	 * Examples:
	 * prep->("select * from users where id = ?", [50]);
	 * prep->("select * from users where id = :id or name = :name", ['id'=>50, 'name'=>'hodor']);
	 */
	public static function select($query, $params = []) {
		$stmt = self::$pdo->prepare($query);
		$stmt->execute($params);
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		return $rows;
	}

	/**
	 * Same as select, except it returns only the first row
	 * @see select
	 * @param $query
	 * @return array
	 */
	public static function count($query) {
		$stmt = self::$pdo->prepare($query);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		return count($rows);
	}

	/**
	 * Same as select, except it returns only the first row
	 * @see select
	 * @param $query
	 * @param array $params
	 * @return array
	 */
	public static function selectFirst($query, $params = []) {
		$returnRow = [];
		$rows = self::select($query, $params);
		if(count($rows) > 0){
			$returnRow = $rows[0];
		}
		return $returnRow;
	}

	/**
	 * Same as the select, only returning nothing, for insert/update queries
	 * @see database::select
	 * @param String $query
	 * @param array $params
	 * @return int lastInsertedId
	 */

	public static function execute($query, $params = []) {
		$stmt = self::$pdo->prepare($query);
		$stmt->execute($params);
		return self::$pdo->lastInsertId();
	}

	public static function timestamp($time = 0) {
		return date('Y-m-d H:i:s', (($time > 0) ? $time : time() ));
	}
}