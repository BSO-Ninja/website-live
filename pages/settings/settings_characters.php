<?php

  if(isset($_POST['approve_character_submit'])) {
    $approveAddChar = \helper\Account::approveCharacterSubmit();
  }

?>
<h1>Characters</h1>

<p>
  To add a character linked to your TibiaMate account, you need to click on the "Add character" below to the left.
  After that you will get a verification code that you need to put on the specified characters official Tibia.com
  character information. When you have done that, you may press the "Approve Character" and the server will try to
  find the verification code give to you, linked to that character. If the server find the code on the character
  information page, it will automatically approve and link the character to your account.
</p>
<p>
  <strong>The process of approving characters are 100% automatic by the server, and could take up to 24 hours.</strong>
</p>

<div class="box clearfix">
  <div class="content clearfix flex">
    <div class="half box-brown" style="font-size:11px;text-align:center;width:450px;float:left;">

      <h3>Characters Waiting for Approval</h3>

      <div class="divTable blueTable">
        <div class="divTableHeading">
          <div class="divTableRow">
            <div class="divTableHead textLeft" style="width:45%;">Character Name</div>
            <div class="divTableHead" style="width:15%;">Approving</div>
            <div class="divTableHead" style="width:40%;">Code</div>
          </div>
        </div>
        <div class="divTableBody">

          <?php

            $charactersBeingApproved = \helper\Tibia::getAccountCharactersBeingApproved();

            if($charactersBeingApproved) {
              foreach($charactersBeingApproved AS $character) {
                ?>
                <div class="divTableRow">
                  <div class="divTableCell textLeft">
                    <img src="/public/images/icons/16x16/delete.png" class="cursorPointer" />
                    <a href="https://www.tibia.com/community/?subtopic=characters&name=<?=str_replace(" ","+",$character['name'])?>" target="_blank"><?=$character['name']?></a>
                  </div>
                  <div class="divTableCell">
                    <img src="/public/images/default/loading-slow.png" style="width:18px;" />
                    <img src="/public/images/icons/16x16/help.png" class="cursorHelp" title="Add the code to the right, on your characters comment on Tibia.com. The server will check the code once in a while, please be patient." />
                  </div>
                  <div class="divTableCell">
                    <div class="approval_code_box approval_code">
                      <div class="click">Click to see code</div>
                      <div class="code" style="display:none;"><?=$character['approval_code']?></div>
                    </div>
                  </div>
                </div>
                <?php
              }
              ?>
                <script>
                  $(document).ready(function(){
                    $('.approval_code').click(function(){
                      if(!$(this).hasClass('approval_code_shown')) {
                        $(this).find('.click').toggle();
                        $(this).find('.code').toggle();
                        $(this).addClass('approval_code_shown');
                        $(this).addClass('approval_code_box_green');
                      }
                    });
                  });
                </script>
              <?php
            }
            else {
              // No character being approved
              ?>
              <div class="divTableRow">
                <div class="divTableCell textLeft"><strong>No character being approved</strong></div>
              </div>
              <?php
            }
          ?>

        </div>
      </div>

      <p style="margin:0px;"></p>

      <div class="divTable blueTable">
        <div class="divTableHeading">
          <div class="divTableRow">
            <div class="divTableHead textLeft" style="width:65%;">Add Character</div>
          </div>
        </div>
        <div class="divTableBody">
          <div class="divTableRow">
            <div class="divTableCell textLeft">
              <?php
                if(isset($approveAddChar) && $approveAddChar > 0) {
                  Switch($approveAddChar) {
                    case 1: $error = "Character does not exist"; break;
                    case 2: $error = "Please write your character name"; break;
                  }
              ?>
                  <script>
                    $(document).ready(function(){
                      $("#approve_character_name").notify("<?=$error?>", { className: "error", position:"top", clickToHide: true, autoHide: false });
                    });
                  </script>
              <?php
                }
              ?>
              <form method="POST" action="<?=\core\template::$fullURL?>">
                <input type="TEXT" id="approve_character_name" name="approve_character_name" placeholder="Character Name" />
                <input type="SUBMIT" class="button-big button-green" name="approve_character_submit" value="Add Character" />
              </form>
            </div>
          </div>
        </div>
      </div>


    </div>
    <div class="half " style="font-size:11px;text-align:center;width:364px;float:right;">

      <h3>Characters Approved</h3>

      <div class="divTable blueTable">
        <div class="divTableHeading">
          <div class="divTableRow">
            <div class="divTableHead textLeft" style="width:65%;">Character Name</div>
            <div class="divTableHead">Information</div>
          </div>
        </div>
        <div class="divTableBody">

          <?php
          $characters = \helper\Tibia::getAccountCharacters();

          if($characters) {
            foreach($characters AS $character) {

              $charData = json_decode($character['character_api_data']);

              ?>
            <div class="divTableRow">
              <div class="divTableCell textLeft">
                <img src="/public/images/icons/16x16/delete.png" class="cursorPointer" />
                <a href="https://www.tibia.com/community/?subtopic=characters&name=<?=str_replace(" ","+",$character['name'])?>" target="_blank"><?=$character['name']?></a>
              </div>
              <div class="divTableCell">
                <span class="icon16 icon16_world" title="<?=$charData->characters->data->world?>"></span>
                <?php
                  if(!empty($charData->characters->data->guild->name)) {
                    ?>
                <span class="icon16 icon16_guild" title="<?=$charData->characters->data->guild->name?>"></span>
                    <?php
                  }
                ?>
                <span class="icon16 icon16_level" title="<?=$charData->characters->data->level?>"></span>
                <span class="icon16 icon16_vocation" title="<?=$charData->characters->data->vocation?>"></span>
                <span class="icon16 icon16_male" title="<?=$charData->characters->data->sex?>"></span>
              </div>
            </div>
              <?php
            }
          }
          else {
            // No character being approved
            ?>
            <div class="divTableRow">
              <div class="divTableCell textLeft"><strong>No character approved yet</strong></div>
              <div class="divTableCell textLeft"></div>
              <div class="divTableCell textLeft"></div>
            </div>
            <?php
          }
          ?>



          </div>
        </div>



      </div>
  </div>
</div>