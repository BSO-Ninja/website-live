<?php

  Switch(\core\template::route(1)) {
    Case "new": require("./pages/discussions_new.php"); break;
    Case "view": require("./pages/discussions_view.php"); break;
    Default: require("./pages/discussions_list.php"); break;
  }
