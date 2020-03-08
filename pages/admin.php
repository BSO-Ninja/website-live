<?php
  if(!\helper\Account::checkLogin() || !\helper\Account::hasPermission(2)) {
    require(INTERNAL_PATH. '/pages/noaccess.php');
    exit;
  }
?>
<div class="content_menu">
  <div class="title">
    Admin
  </div>
  <div class="menu">
    <a href="/admin">Admin Panel</a> | <a href="/admin/users">Users</a> | <a href="/admin/reports">Reports</a> | <a href="/admin/groups">Groups</a> | <a href="/admin/permissions">Permissions</a>
  </div>
</div>

<?php

  Switch(\core\template::$route['routes'][1]) {
    default: include(INTERNAL_PATH. '/pages/admin/admin_panel.php'); break;
    case 'users': include(INTERNAL_PATH. '/pages/admin/admin_users.php'); break;
    case 'reports': include(INTERNAL_PATH. '/pages/admin/admin_reports.php'); break;
    case 'permissions': include(INTERNAL_PATH. '/pages/admin/admin_permissions.php'); break;
    case 'groups': include(INTERNAL_PATH. '/pages/admin/admin_groups.php'); break;
  }