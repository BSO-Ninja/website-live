<?php

include("config.php");
require("classes/core/template.php");
require("classes/helper/Account.php");

if(isset($_POST)) {

  if(isset($_GET['email']) && \helper\Account::inGroup(6)) {
      $post = json_encode($_POST);
      // \core\DB::execute("INSERT INTO dump SET dump = ?", [$post]);

      if(isset($_GET['email_id']) && $_GET['email_id'] > 0) {
        $content = (!empty($_POST['content'])) ? $_POST['content'] : $_POST['editabledata'];

        \core\DB::execute("UPDATE `emails_content` SET 
                                  `inner_content` = ?, 
                                  `last_edit_account_id` = ? 
                                WHERE `email_id` = ?",
            [
                $content,
                \helper\Account::user('id'),
                $_GET['email_id']
            ]);

        echo 'success';
      }
      else {
        echo 'failed';
      }
  }
  else {
    // CHECK IF WE'RE AN EDITOR
    if (\helper\Account::inGroup(5)) {
      $post = json_encode($_POST);
      // \core\DB::execute("INSERT INTO dump SET dump = ?", [$post]);

      $content = (!empty($_POST['content'])) ? $_POST['content'] : $_POST['editabledata'];
      $blockID = str_replace("block_content_", "", $_POST['content_id']);

      \core\DB::execute("UPDATE `pages_draft_blocks` SET 
                                  `inner_content` = ?, 
                                  `last_edit_account_id` = ? 
                                WHERE `id` = ?",
          [
              $content,
              \helper\Account::user('id'),
              $blockID
          ]);

      echo 'success';
    } else {
      echo 'failed';
    }
  }
}