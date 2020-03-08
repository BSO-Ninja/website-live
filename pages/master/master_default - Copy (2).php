<!DOCTYPE html>
<head>
  <?php
  if(!DEVELOPMENT) {
    ?>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-119019075-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag() {
        dataLayer.push(arguments);
      }
      gtag('js', new Date());
      gtag('config', 'UA-119019075-1');
    </script>
    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <script>
      (adsbygoogle = window.adsbygoogle || []).push({
        google_ad_client: "ca-pub-8703039721875743",
        enable_page_level_ads: true
      });
    </script>
    <?php
  }
  ?>
  <title>FortHusk.com - Home</title>
  <!-- META -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="-1" />
  <meta http-equiv="Cache-Control" content="max-age=0" />

  <link rel="apple-touch-icon" sizes="57x57" href="/public/images/favicon/apple-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="/public/images/favicon/apple-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="/public/images/favicon/apple-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href="/public/images/favicon/apple-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href="/public/images/favicon/apple-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href="/public/images/favicon/apple-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href="/public/images/favicon/apple-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href="/public/images/favicon/apple-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="/public/images/favicon/apple-icon-180x180.png">
  <link rel="icon" type="image/png" sizes="192x192"  href="/public/images/favicon/android-icon-192x192.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/public/images/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="96x96" href="/public/images/favicon/favicon-96x96.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/public/images/favicon/favicon-16x16.png">
  <link rel="manifest" href="/public/images/favicon/manifest.json">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
  <meta name="theme-color" content="#ffffff">

  <script src='/public/js/libs/autosize/autosize.js'></script>
  <!-- JQUERY -->
    <link rel="stylesheet" href="/public/css/jquery-ui-1.12.1.min.css">
    <script src="/public/js/libs/jquery/jquery-3.3.1/jquery-3.3.1.min.js"></script>
    <script src="/public/js/libs/jquery/jquery-3.3.1/jquery-ui-1.12.1.min.js"></script>
  <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
  <!-- NotifyJS -->
    <script src="/public/js/libs/notify/notify.min.js"></script>
  <!-- Forthusk CSS -->
    <link rel="stylesheet" type="text/css" href="/public/css/default.css" />
  <!-- Select2 -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

</head>
<body>

  <div class="main">

    <div class="top_bg"></div>

    <div class="top">

      <div class="text">
        <a href="https://www.epicgames.com/fortnite/en-US/home" target="_blank">Official Fortnite Website</a> | <a href="https://www.epicgames.com/" target="_blank">Epic Games</a>
      </div>

      <div class="logo">
        <a href="http://www.forthusk.com">
          <img src="/public/images/default/top_left_logo.png" />
        </a>
      </div>

      <div class="login">
        <?php include('./pages/includes/login.php'); ?>
      </div>

      <div class="menu">

        <?php include('./pages/includes/menu.php'); ?>

      </div>

    </div>

    <div class="nav_boxes">

      <?php include('./pages/includes/nav_boxes.php'); ?>

    </div>


    <?php

    if(!empty($page['script_path']) && file_exists('./pages/' . $page['script_path'])) {
      require('./pages/' . $page['script_path']);
    }
    else {
      /* LOAD THE TOPICS FOR THE SPECIFIC PAGE */
      include('./pages/page.php');
    }

    ?>



</body>
</html>
