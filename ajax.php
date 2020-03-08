<?php

include("config.php");
require("classes/core/template.php");
require("classes/helper/Account.php");
require("classes/helper/Editor.php");

\helper\Account::init();

/**
 * ACCOUNT
 */
switch($_GET['get']) {
  case 'reg_code': getRegCode(); break;
}

/**
 * EDITOR
 */


if(isset($_GET['action']) && !empty($_GET['action'])) {
  switch($_GET['action']) {
    case 'editor_page_save': editorPageSave(); break;
    case 'editor_page_lock': editorLockPage(); break;
    case 'editor_page_publish': editorPublishPage(); break;
  }
}
else {
  switch($_GET['get']) {
    case 'image_upload': imageUpload(); break;
    case 'editor_get_pages': editorGetPages(); break;
    case 'editor_get_groups': editorGetGroups(); break;
  }
}

/**
 * SENIOR EDITOR
 */
if(\helper\Account::inGroup(6)) {
  switch($_GET['get']) {
    case 'image_delete': imageDelete(); break;
  }
}

/**
 * GLOBAL CHECKS
 */
switch($_GET['get']) {
  case 'check_username': checkUsername(); break;
  case 'check_email': checkEmail(); break;
}

function editorPublishPage() {
  if(\helper\Account::hasPermission(5)) { // 4 = Publish pages
    $publishPage = \helper\Editor::publishPage();
    exit(json_encode($publishPage));
  }
}
function editorPageSave() {
  if(\helper\Account::hasPermission(4)) { // 4 = Edit pages
    $savePage = \helper\Editor::savePage();
    exit(json_encode($savePage));
  }
}
function editorLockPage() {
  if(\helper\Account::hasPermission(6)) { // 6 = Lock pages
    $lockPage = \helper\Editor::lockPage();
    exit(json_encode($lockPage));
  }
}

function getRegCode() {
  $uniqueCode = false;
  $charName = urldecode($_GET['tibia_character_name']);

  $check = \core\DB::selectFirst("SELECT `id` FROM `tibia_characters` WHERE `name` = ?", [$charName]);
  // If the character has already been registered, do not proceed from here
  if(isset($check['id']) && $check['id'] > 0) {
    exit(json_encode(["error"=>"character_already_registered"]));
  }
  else {
    // The tibia character has not yet been registered, proceed to create a unique registration code
    while (!$uniqueCode) {
      $regCode = "tibiamate_" . \helper\Account::generateRandomString(6);
      $check = \core\DB::selectFirst("SELECT `reg_code` FROM `accounts_registration` WHERE `reg_code` = ?", [$regCode]);

      if (!isset($check['reg_code']) && $check['reg_code'] != $regCode) {
        // We found a unique reg code
        \core\DB::execute("INSERT INTO `accounts_registration` SET 
                                  reg_code = ?,
                                  tibia_character_name = ?,
                                  session_hash = ?
                              ", [
            $regCode,
            $charName,
            $_COOKIE['tibiamate_session']
        ]);
        // Let's create a JSON response with the registration code
        $response = ["reg_code" => $regCode];
        $uniqueCode = true;
        exit(json_encode($response));
      }
    }
  }
}


function checkEmail() {
  $check = \core\DB::selectFirst("SELECT `id` FROM `accounts` WHERE `email` = ?", [urldecode($_GET['check_email'])]);
  if(isset($check) && $check['id'] > 0) {
    exit("duplicate");
  }
  else {
    exit("available");
  }
}
function checkUsername() {
  $check = \core\DB::selectFirst("SELECT `id` FROM `accounts` WHERE `username` = ?", [urldecode($_GET['check_username'])]);
  if(isset($check) && $check['id'] > 0) {
    exit("duplicate");
  }
  else {
    exit("available");
  }
}



function imageDelete() {
  $file = str_replace("//", "/", $_GET['file']);
  $folder = isset($_GET['folder']) ? $_GET['folder'] .'/' : '';
  if(file_exists("./public/images/". $folder . $file)) {
    unlink("./public/images/". $folder . $file);
    exit('File deleted ("./public/images/'. $folder . $file .'"');
  }
  else {
    exit('Failed deleting file ("./public/images/'. $folder . $file .'"');
  }
}

function imageUpload() {
  $folder = ($_GET['folder'] == "Root") ? "" : $_GET['folder'] .'/';
  $allowedMimes = array('image/gif', 'image/jpeg', 'image/jpg', 'image/png', 'image/bmp', 'image/wbmp');
  $allowedExtensions = array('jpg', 'gif', 'jpeg', 'png', 'bmp', 'wbmp');
  $msgs = "";


  if (isset($_FILES['files']) && !empty($_FILES['files'])) {
    $no_files = count($_FILES["files"]['name']);
    for ($i = 0; $i < $no_files; $i++) {

      @$extension = end(explode('.', $_FILES["files"]["name"][$i]));

      //is the extension allowed?
      if(in_array($extension, $allowedExtensions)){

          // is an allowed image type
          if ($_FILES["files"]["error"][$i] > 0) {
            $msgs .= "Error: " . $_FILES["files"]["error"][$i] . "<br />";
          } else {
            if (@file_exists('./public/images/'. $folder . $_FILES["files"]["name"][$i])) {
              $msgs .= 'File already exists : /public/images/'. $folder . $_FILES["files"]["name"][$i] . '<br />';
            } else {
              @move_uploaded_file($_FILES["files"]["tmp_name"][$i], './public/images/'. $folder . $_FILES["files"]["name"][$i]);
              $msgs .= 'File successfully uploaded : /public/images/'. $folder . $_FILES["files"]["name"][$i] . '<br />';
            }
          }

      }

    }
  } else {
    $msgs .= 'Please choose at least one file';
  }

  exit();
}

function editorGetPages() {
  // GET PAGES
  $pages = \core\DB::select("SELECT * FROM pages WHERE `name` LIKE ? AND hidden = 'false' ORDER BY `name` ASC",["%".$_GET['q']."%"]);
  $results = array();

  $i = 1;
  $results['results'][0]['id'] = 0;
  $results['results'][0]['text'] = "Index (/)";
  foreach ($pages AS $page) {
    $results['results'][$i]['id'] = $page['id'];
    $results['results'][$i]['text'] = "{$page['name']} ({$page['url']})";
    $i++;
  }

  exit(json_encode($results));
}

function editorGetGroups() {
  // GET GROUPS
  $groups = \core\DB::select("SELECT * FROM accounts_groups ORDER BY `id` ASC",[]);
  $results = array();

  $i = 0;
  foreach ($groups AS $group) {
    $results['results'][$i]['id'] = $group['id'];
    $results['results'][$i]['text'] = "{$group['name']}";
    $i++;
  }

  exit(json_encode($results));
}