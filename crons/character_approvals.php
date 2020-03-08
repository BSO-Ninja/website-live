<?php

    // Get characters waiting for approval and verify them

    $charsBeingApproved = \core\DB::select("SELECT * FROM `tibia_characters_approving` ORDER BY `approvals_tried` ASC");

    if($charsBeingApproved) {
      // We got some chars to be approved, let's cycle through them and try to approve them

      foreach($charsBeingApproved AS $char) {

        echo '<p><b>' . $char['name'] . '</b>: being checked for approval<br />';
        //Lets get the latest character info from the TibiaAPI site
        $characterAPIData = file_get_contents('https://api.tibiadata.com/v2/characters/' . strtolower(urldecode($char['name'])) . '.json');
        $characterData = json_decode($characterAPIData);

        if (isset($characterData->characters->data->name) && !empty($characterData->characters->data->name)) {
          echo '<b>' . $char['name'] . '</b>: api for character found<br />';
          // We've found a match, let's get the comment and see if the approval code has been added in the comments
          //if(stristr($characterData->characters->data->comment, $char['approval_code']) === FALSE) {
          //  echo '"earth" not found in string';
          //}

          if (preg_match("/{$char['approval_code']}/i", $characterData->characters->data->comment)) {
            // We found a match, now let's approve the character
            echo '<b>' . $char['name'] . '</b>: code found in the character comment<br />';
            \core\DB::execute("INSERT INTO `tibia_characters` SET 
                                        `account_id` = ?, 
                                        `name` = ?, 
                                        `character_api_data` = ?
                                    ", [
                $char['account_id'],
                $char['name'],
                $char['character_api_data']
            ]);

            // Once that is done, we remove it from the `tibia_characters_approving` table
            \core\DB::execute("DELETE FROM `tibia_characters_approving` WHERE id = ?", [$char['id']]);
            echo '<b>' . $char['name'] . '</b>: <span style="color:green"> character successfully approved</span><br />';
          } else {
            \core\DB::execute("UPDATE `tibia_characters_approving` SET `approvals_tried` = `approvals_tried`+1 WHERE id = ?", [$char['id']]);
            echo '<b>'. $char['name'] .'</b>: <span style="color:red">code not found on character comment</span><br />';
          }
        }
        echo '</p>';
      }
    }