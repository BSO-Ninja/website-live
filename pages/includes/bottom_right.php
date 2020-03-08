


<?php
if(!\helper\Account::loggedIn()) {
  ?>
  <form method="POST" action="?" style="width:473px;float:left;">
      <input type="text" placeholder="Email" name="login_email" />
      <input type="password" placeholder="Password" name="login_password" />
      <input class="button-big button-green" type="submit" name="login_form" value="LOGIN" />
  </form>
  <a class="button-big button-blue" type="submit" href="/register" style="line-height:27px !important;font-size:11px !important;padding: 3px 8px 5px 8px !important;">Can't login?</a>
  <?php
} else if(\helper\Account::loggedIn()) {
  ?>

  <!--<img src="/public/images/icons/bell.png" title="Notifications" />
  <img src="/public/images/icons/comments.png" title="Live Chat" />
  <img src="/public/images/icons/notes_pin.png" title="Personal Notes" />
  <img src="/public/images/icons/users_5.png" title="Sacred" />
  <img src="/public/images/icons/group.png" title="Friends" />
  <img src="/public/images/icons/email_inbox.png" title="Messages" />
  <img src="/public/images/icons/ssl_tls_manager.png" title="Your Account" />-->

  <?php
    if(\helper\Tibia::getAccountCharacters() == false) {
      ?>
      <script>
        $(document).ready(function(){
          $("#icon_settings").notify("You need to approve atleast one of your characters", { className: "info", position:"left", clickToHide: false, autoHide: false });
        });
      </script>
      <a href="/settings/characters"><img src="/public/images/icons/cog.png" title="Settings" id="icon_settings" /></a>
  <?php
    }
    else {
  ?>
      <a href="/settings"><img src="/public/images/icons/cog.png" title="Settings" id="icon_settings" /></a>
  <?php
    }
  ?>

  <?php
    if(\helper\Account::hasPermission(2)) {
    echo '<a href="/admin"><img src="/public/images/icons/administrator.png" title="Admin" id="icon_admin" /></a>';
    }
  ?>

  <a href="?logout=true"><img src="/public/images/icons/door_in.png" title="Logout" /></a>

<?php
}