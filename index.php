<?php
//exit(sha1(md5("[PASS]PoEoRaClE2017")));

define("LOCATION_PREFIX","");

/*
 * CHECK IF APP
 */
if(isset($_GET['app']) || 1 == 1 && !isset($_COOKIE['is_app'])) {
  setcookie('is_app', "true", time() + 60*24*30*365);
  header("Location: /home");
  exit;
}

include("config.php");
require("classes/core/template.php");
require("classes/helper/Account.php");
require("classes/helper/Editor.php");
//require("classes/helper/Admin.php");
require("classes/helper/Discussions.php");
require("classes/helper/Functions.php");
require("classes/helper/Tibia.php");
// require("classes/helper/API_Twitch.php");

\helper\Account::init();

/*
 * CRONS
 */
if(isset($_GET['cron_run']) && !empty($_GET['cron_run']) && $_GET['cron_access'] == CRON_ACCESS) {
    if(file_exists("crons/". $_GET['cron_run'] .".php")) {
        include("crons/". $_GET['cron_run'] .".php");
        exit();
    }
}


/*
 * TibiaMate Session
 */
if(!isset($_COOKIE['tibiamate_session']) || empty($_COOKIE['tibiamate_session'])) {
  \helper\Account::checkSession();
}
else {
  // Update the session
  \helper\Account::updateSession();
}


// Load the Template engine
$route = core\template::load();

// Load the Editor
\helper\Editor::init();

if(isset($_GET['devaccess']) && md5($_GET['devaccess']) == md5("ZeBalloonHasLanded")) {
  setcookie('devaccess', md5("LarsHasBeenFound"), time() + LOGIN_EXPIRATION_ALWAYS, "/");
}

// Check if user is logged in, otherwise send to start/login page
if(MAINTENANCE && !isset($_COOKIE['devaccess']) && $_COOKIE['devaccess'] != md5("LarsHasBeenFound")) {
  if($route['baseurl'] != "maintenance") {
    // ADD EXCEPTION FOR STAFF HERE
    header("Location: /maintenance");
    exit;
  }
	include(INTERNAL_PATH. '/pages/master/master_maintenance.php');
}
else {
	/* LOGIN PAGE (START PAGE)
	if(!AuthUser::isAuthenticated() && (!isset($masterpage[0]) || $masterpage[0] != "register") && @$masterpage[0] != "facebook" && @$masterpage[1] != "login" && @$masterpage[0] != "verification"){
		if(!empty($masterpage[0])){
			echo "<script>window.location='/';</script>";
		}
		include('pages/master/master_login.php');
	} else {
	*/





	// GET THE CURRENT PAGE

  $url = core\template::$urlPath;
  @$page = core\DB::selectFirst("SELECT * FROM `pages` WHERE `url` = ?", [$url]);

  // CHECK IF WE WANT TO EDIT THIS PAGE
  // IF IT EXIST PUBLISHED, BUT NO DRAFT = COPY LIVE VERSION TO DRAFT
  // IF IT DOESNT EXIST, CREATE A NEW EMPTY DRAFT


  /*
   * #############
   * # EDIT MODE #
   * #############
   */
  if(\helper\Account::editMode()) {

    if(isset($page) && $page['id'] > 0 && empty($page['script_path'])) {
      \core\template::$page = $page;
      // There is a live version of this page, lets check if it has a draft and load it
      // if no draft exist, create a new draft for that url
      $draft = core\DB::selectFirst("SELECT * FROM `pages_draft` WHERE `url` = ?", [$url]);
      if(isset($draft) && $draft['id'] > 0) {
        // A draft was found, lets load it
        // Load the edit master page
        require(str_replace(".php", "_edit.php", MASTER_PATH));
      }
      else {
        // No draft found, create one using the live version
        $draftContent = \helper\Editor::loadContent($page['content_id']);
        $draftID = \core\DB::execute("INSERT INTO `pages_draft` SET 
                                    `name` = ?, 
                                    `url` = ?, 
                                    `html` = ?, 
                                    `last_edit_by` = ?,
                                    `created_by` = ?
                                ",[
                                    $page['name'],
                                    $page['url'],
                                    $draftContent['html'],
                                    \helper\Account::user('id'),
                                    \helper\Account::user('id')
        ]);

        $draft = core\DB::selectFirst("SELECT * FROM `pages_draft` WHERE `id` = ?", [$draftID]);
        // Now we load the draft
        require(str_replace(".php", "_edit.php", MASTER_PATH));
      }
    }
    else if(!empty($page['script_path'])) {
      // This page cant be edited since it has a script attached to the page, load the page normally
      require(MASTER_PATH);
    }
    else {
      // First we need to check if there is a draft already created, if so load it instead
      $draft = core\DB::selectFirst("SELECT * FROM `pages_draft` WHERE `url` = ?", [$url]);

      if(!isset($draft['id'])) {
        // So we want to create a draft for a page that does not exist yet, create the empty draft
        $newDraft = core\DB::execute("INSERT INTO `pages_draft` SET 
                                            `url` = ?,
                                            `last_edit_by` = ?,
                                            `created_by` = ?
                                          ",
            [
                $url,
                \helper\Account::user('id'),
                \helper\Account::user('id')
            ]);
        // Now we load the new draft and load the edit template
        $draft = core\DB::selectFirst("SELECT * FROM `pages_draft` WHERE `id` = ?", [$newDraft]);
      }

      // Load the edit template
      require(str_replace(".php", "_edit.php", MASTER_PATH));
    }

  }
  else {
    /*
     * #############
     * # PAGE MODE #
     * #############
     */
    // TODO: THIS NEEDS A RE-WORK





















    if(isset($page) && $page['id'] > 0) {
      // We found a page for the exact URL, now load it
      \core\template::$page = $page;

      // Check if this page is an editor page or a script page
      if (isset($page['script_path']) && !empty($page['script_path']) && $page['skip_design'] == "true" && file_exists('./pages/' . $page['script_path'])) {
        // Load the script
        require(INTERNAL_PATH . '/pages/' . $page['script_path']);
      } else {
        // Page is an Editor page, load the template/editor
        require(MASTER_PATH);
      }
    } else {
      // We did not find a page for this specific URL, lets see if we can find a parent page

      $rurlArray = explode("/", $url);
      // Now lets build the different urls backwards
      // For example: /events/event/1337
      // Or: 			/events/event/1337/edit
      // We want to load the /events/event page, so we try removing the last words "1337" and "edit"
      // to find a matching page, and then pass them on as a rest URL to be used by the /events/event page, as variables.
      $match = false;

      $i = 0;
      while ($i <= count($rurlArray)) {
        if (!empty($rurlArray[$i])) {
          $temp = array();
          for ($x = 0; $x <= $i; $x++) {
            if (!empty($rurlArray[$x])) {
              $temp[] = $rurlArray[$x];
            }
          }
          $urls[] = "/" . implode("/", $temp);
        }
        $i++;
      }
      @$urls = array_reverse($urls);

      // Now we search for a parent page for each url, removing the last word each time
      if (!empty($urls)) {
        foreach ($urls as $url) {
          @$page = core\DB::selectFirst("SELECT * FROM `pages` WHERE `url` = ?", [$url]);

          if (isset($page) && $page['id'] > 0) {
            // We found a page for the exact URL, now load it
            \core\template::$page = $page;
            break;
          }
        }
      }

      if (isset($page) && $page['id'] > 0) {
        // Check if this page is an editor page or a script page
        if (isset($page['script_path']) && !empty($page['script_path']) && $page['skip_design'] == "true" && file_exists('./pages/' . $page['script_path'])) {
          // Load the script
          require(INTERNAL_PATH . '/pages/' . $page['script_path']);
        } else {
          // Page is an Editor page, load the template/editor
          require(MASTER_PATH);
        }
      } else {
        // Check if we have the Edit permission
        if (\helper\Account::hasPermission(4) || \helper\Account::editMode()) {
          $page = \core\DB::selectFirst("SELECT * FROM `pages_draft` WHERE `url` = ?", [core\template::$urlPath]);
          if (isset($page) && $page['id'] > 0) {
            // Check if this page is an editor page or a script page
            if (isset($page['script_path']) && !empty($page['script_path']) && $page['skip_design'] == "true" && file_exists('/pages/' . $page['script_path'])) {
              // Load the script
              require(INTERNAL_PATH . '/pages/' . $page['script_path']);
            } else {
              // Page is an Editor page, load the template/editor
              // Load the edit master page
              require(str_replace(".php", "_edit.php", MASTER_PATH));
            }
          } else {
            // A page wasn't found, load the 404 page
            if (!empty(\core\template::getCurrentUri())) {
              require(INTERNAL_PATH . '/pages/master/master_404.php');
            } else {
              header("Location: /home");
              exit;
            }
          }
        } else {
          // A page wasn't found, load the default page
          header("Location: /home");
          exit;
        }
      }

    }
  }


















/**
  if(isset($page) && $page['id'] > 0) {
    // Check if this page is an editor page or a script page
    if(isset($page['script_path']) && !empty($page['script_path']) && $page['skip_design'] == "true" && file_exists('./pages/' . $page['script_path'])) {
      // Load the script
      require('./pages/' . $page['script_path']);
    }
    else {
      // Page is an Editor page, load the template/editor
      require(MASTER_PATH);
    }
  } else {
    // Check if we're an Editor and there is a drafted page
    if(\helper\Account::inGroup(5) || \helper\Account::editMode()) {
      $page = \core\DB::selectFirst("SELECT * FROM `pages_draft` WHERE `url` = ?",[core\template::$urlPath]);
      if(isset($page) && $page['id'] > 0) {
        // Check if this page is an editor page or a script page
        if(isset($page['script_path']) && !empty($page['script_path']) && $page['skip_design'] == "true" && file_exists('/pages/' . $page['script_path'])) {
          // Load the script
          require('./pages/' . $page['script_path']);
        } else {
          // Page is an Editor page, load the template/editor
          require(MASTER_PATH);
        }
      }
      else {
        // A page wasn't found, load the 404 page
        if(!empty(\core\template::getCurrentUri())) {
          require("./pages/master/master_404.php");
        }
        else {
          header("Location: /home");
          exit;
        }
      }
    }
    else {
      // A page wasn't found, load the default page
      header("Location: /home");
      exit;
    }
  }
**/


  /**
    @$page = core\DB::selectFirst("SELECT * FROM pages WHERE url = ?", ["/".$route['baseurl']]);

    if(isset($page) && $page['id'] > 0) {
      // Check for masterpage
      $masterPage = core\DB::selectFirst("SELECT * FROM templates_master WHERE id = ?", [$page['template_master']]);

        // Page found, lets get the master page and load the page
        if(!empty($masterPage['master_template_path']) && file_exists("pages/master/{$masterPage['master_template_path']}")) {
          require("pages/master/{$masterPage['master_template_path']}");
        }
    } else if (isset($route['baseurl']) && !empty($route['baseurl']) && $route['baseurl'] != "/") {
        require('pages/master/master_default.php');
    } else {
      // A page wasn't found, load the default page
      header("Location: /home");
      exit;
    }
   */


	/*
	}
	*/
}
