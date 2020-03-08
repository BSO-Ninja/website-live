<?php
  if(!\helper\Account::checkLogin()) {
    require(INTERNAL_PATH. '/pages/noaccess.php');
    exit;
  }
?>
<div class="content_menu">
  <div class="title">
    Settings
  </div>
  <div class="menu">
    <a href="/settings/account">Account</a> | <a href="/settings/characters">Characters</a>
  </div>
</div>

<?php

  Switch(\core\template::$route['routes'][1]) {
    default: include(INTERNAL_PATH. '/pages/settings/settings_account.php'); break;
    case 'characters': include(INTERNAL_PATH. '/pages/settings/settings_characters.php'); break;
  }