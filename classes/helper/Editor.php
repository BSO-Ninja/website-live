<?php

namespace helper;

use core;

class Editor {

  private static $errors = array();
  public static $editor = array();

	public static function init() {

    /**
     * Load current page container and blocks
     */
    self::loadContent();

    return true;
	}

  public static function publishPage() {
    // Check if we can lock a page
      $page = $_POST['page'];

      // Check if the page is locked or not
      $pageCheck = core\DB::selectFirst("SELECT * FROM `pages` WHERE `url` = ?",[$page]);

      if($pageCheck['id'] > 0) {
        if($pageCheck['locked'] == "false") {
          // We can publish the new draft

          // Get the current draft we want to update with
          $currentDraft = core\DB::selectFirst("SELECT * FROM `pages_draft` WHERE `url` = ?",[$page]);
          if($pageCheck['content_id'] > 0) {
            // There should be a draft content for this page, lets find it
            // Check if there exist a draft content
            $draftContent = core\DB::selectFirst("SELECT * FROM `pages_content` WHERE `id` = ?", [$pageCheck['content_id']]);

            if($draftContent['id'] <= 0) {
              // The draft content did not exist, so we need to create it first
              $draftContentID = core\DB::execute("INSERT INTO `pages_content` SET `html` = ?", [$currentDraft['html']]);
              // Now we update the page with the newly created content ID
              core\DB::execute("UPDATE `pages` SET `content_id` = ? WHERE `url` = ?", [$draftContentID, $page]);
            }
            else {
              // There were already a pages content, so we just update that
              core\DB::execute("UPDATE `pages_content` SET `html` = ? WHERE `id` = ?", [$currentDraft['html'], $pageCheck['content_id']]);
            }
            return ['status' => 'page_published'];
          }

        }
        else {
          return ['status' => 'page_locked'];
        }
      }
      else {
        return ['status' => 'page_need_be_created'];
      }




      core\DB::execute("UPDATE `pages` SET `locked` = ? WHERE `url` = ?", [$lock, $page]);


  }

  public static function lockPage() {
    // Check if we can lock a page
      $page = $_POST['page'];

      // Check if the page is locked or not
      $pageCheck = core\DB::selectFirst("SELECT * FROM `pages` WHERE `url` = ?",[$page]);

      $lock = ($pageCheck['locked'] == "false") ? 'true' : 'false';

      core\DB::execute("UPDATE `pages` SET `locked` = ? WHERE `url` = ?", [$lock, $page]);

      return ['status' => 'page_locked_'. $lock];
  }

	public static function savePage() {
    $html = $_POST['html'];
    $page = $_POST['page'];

      // Check if the page is locked or not
      $pageCheck = core\DB::selectFirst("SELECT * FROM `pages` WHERE `url` = ?",[$page]);

      if($pageCheck['locked'] == "false" || !$pageCheck) {
        // Check if its same draft or not
        $draft = core\DB::selectFirst("SELECT `html` FROM `pages_draft` WHERE `url` = ?", [$page]);

        if ($draft['html'] != $html) {
          // We're able to save the draft
          core\DB::execute("UPDATE `pages_draft` SET `html` = ? WHERE `url` = ?", [$html, $page]);
          $log = ["message" => "Saved a page draft", "page_url" => $page];
          Account::log($log);
          return ["status" => 'page_saved'];
        } else {
          return ['status' => 'same_html'];
        }
      }
      else {
        return ['status' => 'page_locked'];
      }
  }

  public static function hasDraft() {
    return (self::$editor['id'] > 0) ? true : false;
  }

  public static function publishDraft() {
    // Check if a draft already exist
    if(self::$editor['id'] > 0) {

      exit();

      $url = core\template::$urlPath;
      // No draft found, load the published page
      $pageDraft = core\DB::selectFirst("SELECT * FROM `pages_draft` WHERE `url` = ?",[$url]);

      if($pageDraft['id'] > 0 && $pageDraft['locked'] == "false") {
        core\DB::execute("UPDATE `pages_draft` SET 
                                  `template` = ? 
                                WHERE `url` = ?
                              ", [
            $pageDraft['template'],
            $url
        ]);
      }

      // Now we grab all page blocks and add them to the real page
      $blocks = \core\DB::select("SELECT * FROM `pages_blocks` WHERE `page_id` = ? ORDER BY `block_index` ASC",[$page['id']]);

      foreach($blocks as $block) {
        // Insert each block as a draft block for the drafted page
        core\DB::execute("INSERT INTO `pages_draft_blocks` SET 
                                      `draft_id` = ?,  
                                      `container_name` = ?,  
                                      `block_name` = ?,  
                                      `block_index` = ?,  
                                      `content` = ?"
            ,[
                $page['id'],
                $block['container_name'],
                $block['name'],
                $block['index'],
                $block['content']
            ]);
      }

      // Last but not least, we cache the new live version of the page

      Admin::log($log[]['text'] = "Published the draft for page {$pageDraft['name']} (PAGE#{$pageDraft['id']})");
    }

    return true;
  }

  public static function createDraft() {
    // Check if a draft already exist
    if(self::$editor['id'] <= 0) {

      // No draft found, load the published page
      $page = core\DB::selectFirst("SELECT * FROM `pages` WHERE `url` = ?",[core\template::$urlPath]);

      if($page['id'] > 0 && $page['locked'] == "false") {
        core\DB::execute("INSERT INTO `pages_draft` SET 
                                `name` = ?,
                                `url` = ?,
                                `template` = ?
                              ", [
            $page['name'],
            $page['url'],
            $page['template']
        ]);
      }

      Admin::log($log[]['text'] = "Created a new draft for page {$page['name']} (PAGE#{$page['id']})");
    }

    return true;
  }

  public static function loadDraft($draftID = 0) {
    // Load the page
    if($draftID > 0) {
      return $draft = \core\DB::selectFirst("SELECT * FROM `pages_draft` WHERE `id` = ?", [$draftID]);
    }
    /* TODO: Add check if content not exist */
    return false;
  }

  public static function loadContent($contentID = 0) {
    // Load the page
    if($contentID > 0) {
      return $content = \core\DB::selectFirst("SELECT * FROM `pages_content` WHERE `id` = ?", [$contentID]);
    }
    /* TODO: Add check if content not exist */
    return false;
  }


  /**
   * @param $index
   * @param $value
   */
	public static function setError($index, $value) {
	  self::$errors[$index] = $value;
  }

  /**
   * @param $index
   * @return mixed
   */
	public static function getError($index) {
	  return self::$errors[$index];
  }

  /**
   * @return bool
   */
	private static function createPage() {
    /**
     * Array
        (
        [editor-new-page-parent_id] => 8
        [editor-new-page-page_name] => Fatalplays
        [editor-new-page-create_page] => Create Page
        [editor-new-page-template] => blocks_two
        [editor-new-page-require_login] => true
        [editor-new-page-groups] => Array
        (
        [0] => 5
        [1] => 7
        )
     */

    // CHECK PAGE NAME
    if(empty($_POST['editor-new-page-page_name'])) {
      self::$errors['name_empty'] = "You must specify a page name.";
    }
    $page['name'] = $_POST['editor-new-page-page_name'];

    // CHECK IF PARENT PAGE IS HIDDEN
    $parentPage = core\DB::selectFirst("SELECT `id`,`name`,`url`,`hidden` FROM pages WHERE id = ?", [$_POST['editor-new-page-parent_id']]);
    if($parentPage['hidden'] == 'true') {
      /* TODO: what to do here? allow any page? */
      // $errors[] = "Parent page is locked.";
    }

    // CHECK TEMPLATE
    Switch($_POST['editor-new-page-template']) {
      case 'blocks_one': $page['template'] = 1; break;
      case 'blocks_two': $page['template'] = 2; break;
      default: $page['template'] = 1; break;
    }

    // GROUPS
    $page['groups'] = json_encode($_POST['editor-new-page-groups']);

    // REQUIRE LOGIN
    $page['require_login'] = ($_POST['editor-new-page-require_login'] == 'true') ? 'true' : 'false';

    // URL
    $page['url'] = $parentPage['url'] .'/'. strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $page['name']), '-'));

    // REDIRECT URL
    $page['redirect_url'] = $_POST['editor-new-page-redirect_url'];

    // CHECK URL DUPLICATE
    @$urlDuplicateCheck = core\DB::selectFirst("SELECT `id` FROM pages WHERE url = ?", [$page['url']]);
    if($urlDuplicateCheck['id'] > 0) {
      self::$errors['url_duplicate'] = "The specified URL already exist.";
    }

    //exit("<pre>". print_r($page) ."</pre>");

    /*
    foreach($page as $index => $value) {
      $page[$index] = (!empty($value)) ? $value : 0 ;
    }
    */

    if(empty(self::$errors)) {
      core\DB::execute("INSERT INTO `pages_draft` SET 
                                `name` = ?,
                                `url` = ?,
                                `template` = ?,
                                `redirect_to` = ?,
                                `groups` = ?,
                                `require_login` = ?
                              ", [
                                    $page['name'],
                                    $page['url'],
                                    $page['template'],
                                    $page['redirect_url'],
                                    $page['groups'],
                                    $page['require_login']
                                ]);


      //echo "<pre>". print_r($page) ."</pre>";
      echo "<script>window.location.href = '{$page['url']}';</script>";
      exit;
    }

    return true;
  }
}