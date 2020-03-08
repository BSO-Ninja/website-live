<?php

namespace helper;

use core\DB;

class Permission {

	public static function get($permissionID, $get = "") {
		$permissionID = (string)$permissionID;
		$permission = explode(".",$permissionID);
		$permission[1] = !isset($permission[1]) ? 0 : $permission[1];

		if($permission[1] <= 0) {
			// We want to retrieve a parent permission
			$sql = DB::selectFirst("SELECT * FROM permissions WHERE
						id = ?
					",
				[
					$permission[0]
				]);
		}
		else {
			// We want to retrieve a sub permission
			$sql =  DB::selectFirst("SELECT * FROM permissions_sub WHERE
						parent_id = ?
						AND sub_id = ?
					",
				[
					$permission[0],
					$permission[1]
				]);
		}

		if(empty($get)) {
			return $sql;
		}
		else {
			return $sql["$get"];
		}
	}

	public static function grant($account_id, $permissionID) {
		if(!AuthUser::inGroup(1)) { return false; }

		$permission = explode(".",$permissionID);
		$permission[1] = !isset($permission[1]) ? 0 : $permission[1];

		DB::execute("INSERT INTO accounts_permissions SET
						account_id = ?,
						parent_id = ?,
						sub_id = ?
					",
			[
				$account_id,
				$permission[0],
				$permission[1]
			]);

		// ADMIN LOG
		Admin::log(AuthUser::getId(), "Granted permission [ID#{$permissionID}:". self::get($permissionID, 'name') ."] for [ID#{$account_id}:". Account::findById($account_id, "username") ."]");

		return true;
	}

	public static function remove($account_id, $permissionID) {
		if(!AuthUser::inGroup(1)) { return false; }

		$permission = explode(".",$permissionID);
		$permission[1] = !isset($permission[1]) ? 0 : $permission[1];

		DB::execute("DELETE FROM accounts_permissions WHERE
						account_id = ?
						AND parent_id = ?
						AND sub_id = ?
					",
			[
				$account_id,
				$permission[0],
				$permission[1]
			]);

		// ADMIN LOG
		Admin::log(AuthUser::getId(), "Removed permission [ID#{$permissionID}:". self::get($permissionID, "name") ."] for [ID#{$account_id}:". Account::findById($account_id, "username") ."]");

		return true;
	}
}