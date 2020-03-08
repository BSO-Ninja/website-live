<?php

namespace helper;

use core;

class Tibia {

  public static function getAccountCharacters()
  {
    $check = core\DB::select("SELECT * FROM `tibia_characters` WHERE `account_id` = ?", [Account::user('id')]);
    if($check) {
      return $check;
    }
    else {
      return false;
    }
  }

  public static function getAccountCharactersBeingApproved()
  {
    $check = core\DB::select("SELECT * FROM `tibia_characters_approving` WHERE `account_id` = ?", [Account::user('id')]);
    if($check) {
      return $check;
    }
    else {
      return false;
    }
  }

  public static function rashid() {
    // 1 (for Monday) through 7 (for Sunday)
    $day = date("N");
    // 0 through 23
    $hour = date("G");
    if($hour <= 9) {
      $day = ($day == 1) ? 7 : $day - 1;
    }
    $town = "Town";
    Switch($day) {
      Case 1: $town = "Svargrond"; break;
      Case 2: $town = "Liberty Bay"; break;
      Case 3: $town = "Port Hope"; break;
      Case 4: $town = "Ankrahmun"; break;
      Case 5: $town = "Darashia"; break;
      Case 6: $town = "Edron"; break;
      Case 7: $town = "Carlin"; break;
    }
    return $town;
  }



}