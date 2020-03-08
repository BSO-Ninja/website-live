<!DOCTYPE html>
<head>
  <?php
  //if(!DEVELOPMENT) {
  if(1 != 1) {
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
  <title>TibiaMate by Peekaboii</title>
  <!-- META -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="-1" />
  <meta http-equiv="Cache-Control" content="max-age=0" />

  <!--<script src='/public/js/libs/autosize/autosize.js'></script>-->
  <!-- JQUERY -->
    <link rel="stylesheet" href="/public/css/jquery-ui-1.12.1.min.css">
    <script src="/public/js/libs/jquery/jquery-3.3.1/jquery-3.3.1.min.js"></script>
    <script src="/public/js/libs/jquery/jquery-3.3.1/jquery-ui-1.12.1.min.js"></script>
  <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
  <!-- NotifyJS -->
    <script src="/public/js/libs/notify/notify.min.js"></script>
  <!-- Forthusk CSS -->
    <link rel="stylesheet" type="text/css" href="/public/css/tibiamate.css" />
  <!-- Select2 -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

</head>
<body>

  <div id="main">
    <div id="main_content_wrapper">


      <div id="main_top">
        Default Master Template
      </div>


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



  </div>

</body>
</html>
