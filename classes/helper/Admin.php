<?php

namespace helper;

use core\DB;

class Admin {

  public static function log($log) {

    $logLevel = "log";
    $account = Account::user();
    $ipaddress = Account::getIP();

    // Log level
    if($ipaddress == "UNKNOWN") {
      $logLevel = "warning";
    }

    // Insert the admin log
    DB::execute("INSERT INTO `admin_logs` SET 
                          log = ?, 
                          log_level = ?, 
                          account_id = ?, 
                          account_ip = ?, 
                          added_at = ?
                       ", [
                          json_encode($log),
                          $logLevel,
                          $account['id'],
                          $ipaddress,
                          time()
    ]);

    return true;
  }

}