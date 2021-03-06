<?php
if(\helper\Account::loggedIn()) {
  ?>

  <div class="account_nav_text">
    <span style="float:right;">Welcome <?=\helper\Account::user('username')?>  <i class="fas fa-user" style="color:limegreen;"></i></span>
  </div>


  <div class="logged_in">
    <span style="float:right;">[<a href="?logout=true"><span style="color:sandybrown">Logout</span></a>]</span>
    <span style="float:right;">[<a href="/account/settings">Settings</a>]</span>

    <?php if(\helper\Account::inGroup(3) || \helper\Account::inGroup(2) || \helper\Account::inGroup(1)) { ?>
    <span style="float:right;">[<a href="/admin">Admin</a>]</span>
    <?php } ?>

    <?php if(\helper\Account::inGroup(5)) { ?>
    <span style="float:right;position:relative;" class="fheditor_main_menu_hover">
                  <ul id="fheditor_main_menu">

                    <li class="ui-widget-header"><div>Forthusk Editor</div></li>

                    <?php
                    if(\helper\Editor::hasDraft()) {
                      if(\helper\Account::editMode()) {
                        ?>
                        <li onClick="window.location='<?= str_replace(strstr($_SERVER['REQUEST_URI'], "?"), "", str_replace("/draft","",$_SERVER['REQUEST_URI'])) ?>';">
                          <div><span class="ui-icon ui-icon-pencil"></span>Load Live Version</div></li><?php
                      } else {
                        ?>
                        <li onClick="window.location='<?= str_replace(strstr($_SERVER['REQUEST_URI'], "?"), "", $_SERVER['REQUEST_URI']) ?>/draft';">
                          <div><span class="ui-icon ui-icon-pencil"></span>Load Draft</div></li><?php
                      }
                    }
                    else if(\core\template::$page['id'] > 0) {
                      ?><li onClick="window.location='?create_draft=true';"><div><span class="ui-icon ui-icon-arrowthickstop-1-e"></span>Create Draft</div></li>

                    <li>
                      <div>This Page</div>
                      <ul>
                        <li><div><span class="ui-icon ui-icon-gear"></span>Settings</div></li>
                        <li class="ui-widget-header"><div></div></li>
                        <li><div><span class="ui-icon ui-icon-transferthick-e-w"></span>Clone Page</div></li>
                      </ul>
                    </li><?php
                    }
                    ?>

                    <li class="ui-widget-header"><div></div></li>
                    <li onClick="window.location='/editor/page/new';" class="fheditor-page_new_button"><div><span class="ui-icon ui-icon-newwin"></span>New Page</div></li>

                    <li class="ui-widget-header"><div>Senior Editor</div></li>
                    <li><div><span class="ui-icon ui-icon-signal"></span>Page Versions</div></li>

                    <?php
                      if(\helper\Editor::hasDraft() && \helper\Account::editMode()) {
                        ?><li onClick="window.location='?draft_publish=true';"><div><span class="ui-icon ui-icon-circle-check"></span>Publish Draft</div></li><?php
                      }
                    ?>
                  </ul>
                [<a href="#"><?=(\helper\Account::editMode()?'<span style="color:red">DRAFT</span>':'<span style="color:limegreen">LIVE</span>')?></a>]

                <script>
                  $( function() {
                    $( "#fheditor_main_menu" ).menu({
                      items: "> :not(.ui-widget-header)"
                    });
                  } );
                </script>
    </span>
    <?php } ?>
  </div>



  <div class="account_nav">

    <p class="account_nav_button" style="margin-right:8px !important;">
      <i class="fas fa-bell"></i><br />
      Notices
    </p>

    <p class="account_nav_button">
      <i class="fas fa-heart"></i><br />
      Friends
    </p>

    <p class="account_nav_button">
      <i class="fas fa-comments"></i><br />
      Messages
    </p>

    <p class="account_nav_button">
      <i class="fas fa-home"></i><br />
      Profile
    </p>

  </div>

  <?php
}
else {
  ?>

  <form method="POST" action="<?=$_SERVER['REQUEST_URI']?>">
    <input name="login_account_email" type="text" placeholder="Email" class="top_right_login_input" />
    <input name="login_account_password" type="password" placeholder="Password" class="top_right_login_input" />

    <div class="login_button">
      <input name="login_account_submit" type="submit" value="Login" class="luckiest_guy" /><br />
      <a href="/forum/ucp.php?mode=sendpassword"><strong>Lost Password</strong></a>
    </div>
  </form>

  <div class="social_login" style="padding-top: 10px;">

    <span style="font-size:12px !important;margin-top:3px;">Not a member?</span>
    <br />
    <a href="/forum/ucp.php?mode=register" style="font-size:16px !important;"><strong>Register now!</strong></a>

    <!--
    ... or login with ...<br />
    <div class="social_login_facebook"></div>
    <div class="social_login_google"></div>
    -->
  </div>

  <?php
}