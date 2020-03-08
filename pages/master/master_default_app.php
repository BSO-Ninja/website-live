<!DOCTYPE html>
<head>
  <?php
  if(!DEVELOPMENT) {
    ?>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-133287828-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-133287828-1');
    </script>
    <?php
  }
  ?>
  <title>TibiaMate by Peekaboii</title>
  <!-- META -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="-1" />
  <meta http-equiv="Cache-Control" content="max-age=0" />
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

  <!--<script src='/public/js/libs/autosize/autosize.js'></script>-->

  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <!-- JQUERY -->
    <link rel="stylesheet" href="/public/css/jquery-ui-1.12.1.min.css">
    <script src="/public/js/libs/jquery/jquery-3.3.1/jquery-3.3.1.min.js"></script>
    <script src="/public/js/libs/jquery/jquery-3.3.1/jquery-ui-1.12.1.min.js"></script>
 <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
  <!-- NotifyJS -->
    <script src="/public/js/libs/notify/notify.min.js"></script>
  <!-- Select2 -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
  <!-- TibiaMate CSS -->
  <link rel="stylesheet" type="text/css" href="/public/css/tibiamate_app.css" />
  <!-- NanoScroller -->
  <link rel="stylesheet" type="text/css" href="/public/js/libs/nanoscroller/nanoscroller.css" />

</head>
<body>
  <div id="main">
    <?php
      if(\helper\Account::hasPermission(7)) { // 7 = View Mod Toolbar
        ?>
    <div id="main_content_edit_toolbar">
      <?php
        // Is not in edit page mode
        if(!\helper\Account::editMode() && empty($page['script_path'])) {
        ?>
      <div class="main_content_edit_toolbar-button">
        <a href="?edit_page=true"><img id="page_settings_button" src="/public/images/icons/edit_button.png" title="Edit Page" /></a>
      </div>
      <?php
        }


        // View Page Settings
      ?>
      <div class="main_content_edit_toolbar-button">
        <img id="page_settings_button" src="/public/images/icons/cog.png" title="Page Settings" />
      </div>


    </div>
    <?php
      }
    ?>




    <div id="main_content_wrapper">

      <div id="main_top">

        <div class="main_top_left">
          <span>Avalanche_1</span>
        </div>
        <div class="main_top_middle"></div>
        <div class="main_top_right">
          <span><?=\helper\Tibia::rashid()?></span>
          <div class="rashid"></div>
        </div>

      </div>

      <div id="menu" class="clearfix">

        <?php include("./pages/includes/menu.php") ?>

      </div>

      <div id="main_content">
      <?php

      if(!empty($page['script_path']) && file_exists('./pages/' . $page['script_path'])) {
        include('./pages/' . $page['script_path']);
      }
      else {
        /* LOAD THE TOPICS FOR THE SPECIFIC PAGE */
        include('./pages/page.php');
      }

      ?>
      </div>

      <div id="main_bottom">

        <div class="left">
          <?php include("./pages/includes/bottom_left.php") ?>
        </div>

        <div class="right">
          <?php include("./pages/includes/bottom_right.php") ?>
        </div>

      </div>

    </div>

  </div>

</body>
</html>
