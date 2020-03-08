<?php

if (!\helper\Account::inGroup(5) || !\helper\Account::editMode()) {
  /* TODO: Add Admin/Dev notification here for security checks */
  header("Location: /home");
  exit;
}

  \helper\Editor::init();

  Switch(\core\template::route(1)) {
    case 'page':
      include("./pages/editor/sub/page.php");
    break;
  }
