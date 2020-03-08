<?php
namespace core;

class system {

	public $log = array();

	public function __construct() {
		$this->log['time'] = time();
		$this->log['ip'] = $_SERVER['REMOTE_ADDR'];
		$this->log['url'] = $_SERVER['REQUEST_URI'];
	}

	/*
	 * types: information, warning, error, critical, hack_attempt
	 */
	public function log($type = 'unknown', $message = '', $params = array()) {
		DB::execute("INSERT INTO system_logs SET
						log_type = ?,
						time = ?,
						ip = ?,
						url = ?,
						message = ?,
						parameters = ?
					",[$type,$this->log['time'],$this->log['ip'],$this->log['url'],$message,$params]);
	}

}
