<?php

namespace helper;

use core;

class Account {

  public static $user = array();
  public static $loginHash = "NONE";

  public static function init() {

    // Set the loginHash global so it can be reached from everywhere
    self::$loginHash = (isset($_COOKIE['tibiamate_user']) && !empty($_COOKIE['tibiamate_user'])) ? $_COOKIE['tibiamate_user'] : 'NONE';

    // Check if the user is logged in
    if(self::checkLogin()) {
      // User is logged in, update the last_activity

      /*
      * LOGOUT???
      */
      if(isset($_GET['logout'])) {
        if(self::logoutUser()) {
          header("Location: /home");
          exit;
        }
      }

    }
    else {
      // The user is not logged in

      /*
       * LOGIN???
       */
      if(isset($_POST['login_form'])) {
        // Try to login the user
        if(self::loginUser($_POST['login_email'], $_POST['login_password'])) {
          // Successfully logged in, redirect the user to the same page
          $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
              || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
          header('Location: '.$protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
          exit;
        }
      }

    }

  }

  public static function hasPermission($permissionID)
  {
    // First we check if we have full access (permission 1)
    if(self::getPermission(1)) {
      return true;
    }
    else {
      // Then we check the permission we initially wanted to check
      if (self::getPermission($permissionID)) {
        return true;
      } else {
        return false;
      }
    }
  }
  public static function getPermission($permissionID)
  {
    // Select all groups the account is tied to
    $groups = core\DB::select("SELECT `group_id` from `accounts_to_groups` WHERE `account_id` = ?",[self::user('id')]);

    if($groups) {
      //Look through each group and check if the permission is existing
      foreach($groups AS $group) {
        $check = core\DB::selectFirst("SELECT `id` FROM `permissions_to_groups` WHERE `group_id` = ? AND `permission_id` = ?",[$group['group_id'], $permissionID]);
        if($check['id'] > 0) {
          return true;
        }
      }
    }

    // Now we look through the permissions tied to the account
    $check2 = core\DB::selectFirst("SELECT `id` FROM `permissions_to_accounts` WHERE `account_id` = ? AND `permission_id` = ?",[self::user('id'), $permissionID]);
    if($check2['id'] > 0) {
      return true;
    }

    // Unfortunately no permission has been found for the account, return false :(
    return false;
  }

  public static function lostPassword() {
    $email = $_POST['lost_password_email'];

    if(self::checkRecaptcha()) {
      $accountCheck = core\DB::selectFirst("SELECT * FROM accounts WHERE 
                                                      email = ? 
                                                  ",
                                                  [
                                                      $email
                                                  ]);

      if(!empty($accountCheck) && strtolower($accountCheck['email']) == strtolower($email)) {
        $pass = self::generateRandomString();
        $tempPassword = self::generatePassword($pass);
        core\DB::execute("UPDATE `accounts` SET `temp_password` = ?, `temp_password_added` = ? WHERE `id` = ?", [$tempPassword, time(), $accountCheck['id']]);

        $vars['temp_password'] = $pass;
        core\DB::execute("INSERT INTO `crons_emails` SET 
                                    `account_id` = ?, 
                                    `email` = ?,
                                    `vars` = ?
                                  ", [$accountCheck['id'], 2, json_encode($vars)]);

        self::log("New temporary password requested", $accountCheck['id']);
      }
    }

  }

  public static function log($log, $account = 0) {
    $logLevel = "log";
    $accountID = ($account > 0) ? $account : Account::user('id');
    $ipaddress = Account::getIP();

    // Log level
    if($ipaddress == "UNKNOWN") {
      $logLevel = "warning";
    }

    // Insert the admin log
    core\DB::execute("INSERT INTO `account_logs` SET 
                          log = ?, 
                          log_level = ?, 
                          account_id = ?, 
                          account_ip = ?
                       ", [
        json_encode($log),
        $logLevel,
        $accountID,
        $ipaddress
    ]);

    return true;
  }

  public static function getIP() {
    $ipaddress = '';
    if ($_SERVER['HTTP_CLIENT_IP'])
      $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
      $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if($_SERVER['HTTP_X_FORWARDED'])
      $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if($_SERVER['HTTP_FORWARDED_FOR'])
      $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if($_SERVER['HTTP_FORWARDED'])
      $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if($_SERVER['REMOTE_ADDR'])
      $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
      $ipaddress = 'UNKNOWN';

    return $ipaddress;
  }

  public static function toggleEditMode() {
      if(self::inGroup(5)) {
          // We're an Editor
          if(isset($_COOKIE['tibiamate_draft_mode']) && $_COOKIE['tibiamate_draft_mode'] == "true") {
              setcookie('tibiamate_draft_mode', "false", time() + LOGIN_EXPIRATION_ALWAYS, "/");
          }
          else {
              setcookie('tibiamate_draft_mode', "true", time() + LOGIN_EXPIRATION_ALWAYS, "/");
          }
          return true;
      }
      return false;
  }

  public static function editMode() {
      if(self::hasPermission(4)) {
          // We may edit pages
          if(isset($_GET['edit_page']) && !empty($_GET['edit_page'])) {
              return true;
          }
          else {
              return false;
          }
      }
      return false;
  }

  public static function inGroup($group) {
      $groupCheck = core\DB::selectFirst("SELECT * FROM accounts_to_groups WHERE 
                                            account_id = ? 
                                            AND 
                                            group_id = ?
                                        ",
          [
              self::user('id'),
              $group
          ]);
      return !empty($groupCheck) ? true : false;
  }

  public static function user($return) {
    return self::$user["{$return}"];
  }

  public static function loggedIn() {
    return self::checkLogin();
  }

  public static function checkSession() {
      $unique = false;
      $hash = '';
      while(!$unique) {
          $hash = self::generateHash(32);
          $check = core\DB::selectFirst("SELECT COUNT(id) AS matched FROM user_sessions WHERE
                        session_hash = ?
                      ", [$hash]);
          if($check['matched'] <= 0){
              $unique = true;
          }
      }
      core\DB::execute("INSERT INTO user_sessions SET 
                                session_hash = ?, 
                                session_ip = ?, 
                                server_vars = ?, 
                                current_page = ?
                                ", [
                                    $hash,
                                    self::getIP(),
                                    json_encode($_SERVER),
                                    $_SERVER['REQUEST_URI']
      ]);
      setcookie('tibiamate_session', "{$hash}", time() + 60*24*30*365);
      return true;
  }

  public static function updateSession() {
      $URL = $_SERVER['REQUEST_URI'];

      core\DB::execute("UPDATE user_sessions SET 
                                session_last_action = ?, 
                                current_page = ? 
                                WHERE session_hash = ? 
                                ", [
                                    date('Y-m-d H:i:s',time()),
                                    $URL,
                                    self::$loginHash
      ]);
      return true;
  }

  private static function setCookie($rememberMe = true) {
      $unique = false;
      $hash = '';
      while(!$unique) {
          $hash = self::generateHash(16);
          $check = core\DB::selectFirst("SELECT COUNT(id) AS matched FROM accounts WHERE
                        login_hash = ?
                      ", [$hash]);
          if($check['matched'] <= 0){
              $unique = true;
          }
      }
      $time = ($rememberMe) ? LOGIN_EXPIRATION_ALWAYS : LOGIN_EXPIRATION;
      setcookie('tibiamate_user', $hash, time() + $time, "/");
      return $hash;
  }

  public static function logoutUser(){
    core\DB::execute("UPDATE accounts SET login_hash = '' WHERE id = ?", [self::user('id')]);
    setcookie('tibiamate_user', "", time() - 36000, "/");
    return true;
  }

  public static function loginUser($accountEmail, $password){
      $check = core\DB::selectFirst("SELECT id FROM accounts WHERE 
                                            `email` = ? 
                                            AND
                                            `password` = ?
                                        ",
          [
              $accountEmail,
              self::generatePassword($password)
          ]);

      if($check['id'] > 0) {
        self::$loginHash = self::setCookie();
        core\DB::execute("UPDATE accounts SET login_hash = ? WHERE id = ?", [self::$loginHash, $check['id']]);
        self::log("User logged in", $check['id']);
        return true;
      }
      else {
        // Check if we have a temporary password available
        $check = core\DB::selectFirst("SELECT id, temp_password_added FROM accounts WHERE 
                                            `email` = ? 
                                            AND
                                            `temp_password` = ?
                                        ",
            [
                $accountEmail,
                self::generatePassword($password)
            ]);

        if($check['id'] > 0 && (intval($check['temp_password_added'])+(60*30)) >= time()) {
          self::$loginHash = self::setCookie();
          core\DB::execute("UPDATE accounts SET login_hash = ? WHERE id = ?", [self::$loginHash, $check['id']]);
          self::log("User logged in with temporary password", $check['id']);
          return true;
        }
        else {
          return false;
        }
      }
  }

  public static function checkLogin() {
      if(!empty(self::$loginHash) && self::$user['id'] <= 0) {
        $check = core\DB::selectFirst("SELECT * FROM `accounts` WHERE 
                                            `login_hash` = ?
                                        ",
            [
                self::$loginHash
            ]);
        if($check['id'] > 0) {
          self::$user = $check;
          return true;
        }
        else {
          return false;
        }
      }
      else {
        if(self::$user['id'] > 0) {
          return true;
        }
        else {
          return false;
        }
      }
  }

  public static function registerAccount(){

    /**
     *  Array (
     * [reg_email] => andreas.saarva@gmail.com
     * [reg_password] => Andan2387
     * [reg_password_again] => Andan2387
     * [g-recaptcha-response] => 03AOLTBLRlA4AEmI_R_wo1lOKqCaIKahp1kNK1DLjVD250XRjbM7XTMqeB4GrpkbJv9M4ORv0sG640ESay1zOWPCxGzhtCZHJUa3szXYYvO29xVHPugmMHBjVAVhoTeOzfaUCzxZqqbyoI9HOHUqDieCGvKO2OnNe4pC83FAmaFFWt6i45G6ai2XXkDTTqePcAoXNDpPCHMEMDOUtPNrT2ZOWB8KYKvLyBoUqS7ZGavahfafey7UOcDaWbR3OhCgk_bCwMWFY_D_GgBgzwHwRMqsFhH91rQkgX3cFGaCvDX3kIrDJTv_CcqWv0yscZ1Eca8JKWdyuQuGVbvaSGfaBb1MXqVp8fJptyBYTXXf8gVDxeLU9Vf-fTHqwqTFcyFMOnkby248IWQ7LtfEtYuVPCq2ezlvY-Ot0mng7OYW9cJvyBmIT361kuDFYgzL1-ozOhswwSozbYtmXMpzU9PFgct2466JabRoau89JNjLk3o_wfxtyUz1WZsW4Zfu6L6lkXNauC5ZUcsYQj
     * [reg_submit] => Register )
     */

    //exit(print_r($_POST));

    //RECAPTCHA
    if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
      $error = false;

      if(self::checkRecaptcha()) {

        // Check email duplicate
        $check_email = core\DB::selectFirst("SELECT `id` FROM `accounts` WHERE `email` = ?", [$_POST['reg_email']]);
        if(isset($check_email['id']) && $check_email['id'] > 0) {
          $error = true;
        }

        // Check password and confirm password
        if(!isset($_POST['reg_password'])
            || strlen($_POST['reg_password']) < 5
            || !isset($_POST['reg_password_again'])
            || md5($_POST['reg_password']) != md5($_POST['reg_password_again'])
        ) {
          $error = true;
        }

/*
        // Check if the character registration code exist on the character's comment
        $characterComment = file_get_contents('https://www.tibia.com/community/?subtopic=characters&name='. strtolower(urldecode($_POST['reg_character_name'])));
        if(strpos($characterComment, $_POST['reg_code_input']) === false) {
          $error = true;
          exit("hm3");
        }

        // Use the character registration code to retrieve the session hash and match it with the user
        $registrationSessionHash = core\DB::selectFirst("SELECT * FROM `accounts_registration` WHERE 
                                                reg_code = ?
                                             ",[
                                                $_POST['reg_code_input']
        ]);

        if(!isset($registrationSessionHash['session_hash'])
            || empty($registrationSessionHash['session_hash'])
            || $registrationSessionHash['session_hash'] != $_COOKIE['tibiamate_session']
        ){
          $error = true;
        }

        // Now we fetch the character API data, and save the character to the account
        $characterAPI = file_get_contents('https://api.tibiadata.com/v2/characters/'. strtolower(urldecode($_POST['reg_character_name'])) .'.json');
        $characterJSON = json_decode($characterAPI);

        if(!isset($characterJSON->characters->data->name) || empty($characterJSON->characters->data->name)) {
          $error = true;
        }
*/

        if(!$error) {
          // Registration passed all tests, now lets create the account

          core\DB::execute("INSERT INTO `accounts` SET 
                                    `password` = ?, 
                                    `email` = ?, 
                                    `registered_at` = ?, 
                                    `registered_ip` = ?, 
                                    `verified_email_token` = ? 
                                  ",[
                                      self::generatePassword($_POST['reg_password']),
                                      strtolower($_POST['reg_email']),
                                      core\DB::timestamp(),
                                      self::getIP(),
                                      self::generateHash(16)
          ]);

          $newAccount = core\DB::selectFirst("SELECT * FROM `accounts` WHERE `email` = ?",[$_POST['reg_email']]);

          $vars['username'] = $newAccount['username'];
          $vars['verified_email_token'] = $newAccount['verified_email_token'];
          $vars['verified_email_token_link'] = "<a href='https://www.tibiamate.com/verify_email?verified_email_token=". $newAccount['verified_email_token'] ."' target='_blank'>https://www.tibiamate.com/verify_email?verified_email_token=". $newAccount['verified_email_token'] ."</a>";

          // Now we add a Welcome email in the cron_emails queue
          core\DB::execute("INSERT INTO `crons_emails` SET 
                                    `account_id` = ?, 
                                    `email` = ?,
                                    `vars` = ?
                                  ", [$newAccount['id'], 1, json_encode($vars)]);

          self::log("User registered", $newAccount['id']);

          exit("<script>window.location='/welcome';</script>");
        }
        else {
          // Error while registering

        }
      } else {
        // Captcha failed
        $error = true;
      }
    }

  }

  public static function approveCharacterSubmit()
  {
    $characterName = $_POST['approve_character_name'];

    if(isset($characterName) && !empty($characterName)) {
      // There has been a character name submitted, lets get a unique approval code and add the character

      // Let's start by checking if the character exist
      $characterAPIData = file_get_contents('https://api.tibiadata.com/v2/characters/'. strtolower(urldecode($characterName)) .'.json');
      $characterData = json_decode($characterAPIData);

      if(isset($characterData->characters->data->name) && !empty($characterData->characters->data->name)) {
        // The character exist, continue the process
        $characterName = $characterData->characters->data->name;
        $uniqueCode = false;
        $approvalCode = "";

        while (!$uniqueCode) {
          $approvalCode = "tm_" . self::generateRandomString(10);
          $check = core\DB::selectFirst("SELECT * FROM `tibia_characters_approving` WHERE `approval_code` = ?", [$approvalCode]);
          if (empty($check['approval_code'])) {
            $uniqueCode = true;
          }
        }

        // Now we got a unique code, let's add the code with the character name and account id
        core\DB::execute("INSERT INTO `tibia_characters_approving` SET 
                                  `account_id` = ?,
                                  `name` = ?,
                                  `approval_code` = ?,
                                  `character_api_data` = ?
                              ",
            [
                self::user('id'),
                $characterName,
                $approvalCode,
                $characterAPIData
            ]);
        return 0;
      }
      else {
        return 1; // "Character does not exist";
      }
    }
    else {
      return 2; // "Please write a character name";
    }
  }

  public static function checkRecaptcha() {
    //get verify response data
    $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . RECAPTCHA_SECRET . '&response=' . $_POST['g-recaptcha-response'] .'&remoteip='. Account::getIP());
    $responseData = json_decode($verifyResponse);

    if($responseData->success) {
      return true;
    }
    else {
      return false;
    }
  }

  public static function generatePassword($password) {
      return $hash = sha1(md5($password . "TibiAmAte20192387"));
  }

	public static function saveRegistrationHash($accName, $hash, $poeChar){
        core\DB::execute("INSERT INTO accounts_registration SET 
                                    poe_account_name = ?,
                                    hash = ?,
                                    poe_char_name = ?,
                                    time_added = ?
                                ",
                                [
                                    $accName,
                                    $hash,
                                    $poeChar,
                                    time()
                                ]);
        return true;
	}

	public static function generateHash($length){
		return $hash = sha1(md5(self::generateRandomString($length) . time()));
	}

  public static function generateRandomString($length) {
      $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
      $charactersLength = strlen($characters);
      $randomString = '';
      for ($i = 0; $i < $length; $i++) {
          $randomString .= $characters[rand(0, $charactersLength - 1)];
      }
      return $randomString;
  }


}