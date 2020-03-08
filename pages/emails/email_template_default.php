<?php

if(!\helper\Account::inGroup(6)) {
  /* TODO: Add Admin/Dev notification here for security checks */
  header("Location: /home");
  exit;
}

// LOAD THE EMAIL AND ITS CONTENTS
@$emailID = $_GET['email_id'];

$email = \core\DB::selectFirst("SELECT * FROM `emails` WHERE `id` = ?", [$emailID]);

if(!$email) {
  // Load the Emails in a list
  $emails = \core\DB::select("SELECT * FROM `emails` ORDER BY `name` ASC");

  foreach($emails AS $email) {
  ?>
      <a href="/emails?email_id=<?=$email['id']?>" target="_blank"><?=$email['name']?></a> [<a href="/emails?email_id=<?=$email['id']?>&editmode=true" target="_blank">Edit</a>]<br />
  <?php
  }

  exit;
}

$emailContent = \core\DB::selectFirst("SELECT * FROM `emails_content` WHERE `email_id` = ?", [$emailID]);

$editMode = (isset($_GET['editmode']) && $_GET['editmode'] == "true") ? true : false;

?>

<!doctype html>
<html>
  <head>
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Email - <?=$email['name']?></title>
    <style>
    /* -------------------------------------
        RESPONSIVE AND MOBILE FRIENDLY STYLES
    ------------------------------------- */
    @media only screen and (max-width: 620px) {
      table[class=body] h1 {
        font-size: 28px !important;
        margin-bottom: 10px !important;
      }
      table[class=body] p,
            table[class=body] ul,
            table[class=body] ol,
            table[class=body] td,
            table[class=body] span,
            table[class=body] a {
        font-size: 16px !important;
      }
      table[class=body] .wrapper,
            table[class=body] .article {
        padding: 10px !important;
      }
      table[class=body] .content {
        padding: 0 !important;
      }
      table[class=body] .container {
        padding: 0 !important;
        width: 100% !important;
      }
      table[class=body] .main {
        border-left-width: 0 !important;
        border-radius: 0 !important;
        border-right-width: 0 !important;
      }
      table[class=body] .btn table {
        width: 100% !important;
      }
      table[class=body] .btn a {
        width: 100% !important;
      }
      table[class=body] .img-responsive {
        height: auto !important;
        max-width: 100% !important;
        width: auto !important;
      }
    }

    /* -------------------------------------
        PRESERVE THESE STYLES IN THE HEAD
    ------------------------------------- */
    @media all {
      body {
        width: 100vw;
        height: 100vh;
        background-color: #6a89b1;
      }
      .ExternalClass {
        width: 100%;
      }
      .ExternalClass,
            .ExternalClass p,
            .ExternalClass span,
            .ExternalClass font,
            .ExternalClass td,
            .ExternalClass div {
        line-height: 100%;
      }
      .apple-link a {
        color: inherit !important;
        font-family: inherit !important;
        font-size: inherit !important;
        font-weight: inherit !important;
        line-height: inherit !important;
        text-decoration: none !important;
      }
      .btn-primary table td:hover {
        background-color: #34495e !important;
      }
      .btn-primary a:hover {
        background-color: #34495e !important;
        border-color: #34495e !important;
      }
      .content_header_logo {

      }
    }
    </style>

  <?php
    if($editMode) {
    ?>
    <!-- JQUERY -->
    <link rel="stylesheet" href="/public/css/jquery-ui-1.12.1.min.css">
    <script src="/public/js/libs/jquery/jquery-3.3.1/jquery-3.3.1.min.js"></script>
    <script src="/public/js/libs/jquery/jquery-3.3.1/jquery-ui-1.12.1.min.js"></script>
    <!-- CKEditor -->
    <script src="/public/js/libs/ckeditor_4.7.2/ckeditor.js"></script>
    <!-- NotifyJS -->
    <script src="/public/js/libs/notify/notify.min.js"></script>
  <?php
  }
  ?>
  </head>
  <body class="" style="background-color: #6a89b1; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
    <table border="0" cellpadding="0" cellspacing="0" class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
      <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
        <td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; Margin: 0 auto; max-width: 580px; padding: 10px; width: 580px;">
          <div class="content" style="box-sizing: border-box; display: block; Margin: 0 auto; max-width: 580px; padding: 10px;">
            <div class="content_header_logo">
              <img src="https://www.forthusk.com/public/images/email_header.png" />
            </div>

            <!-- START CENTERED WHITE CONTAINER -->
            <span class="preheader" style="color: transparent; display: none; height: 0; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all; visibility: hidden; width: 0;">This is preheader text. Some clients will show this text as a preview.</span>
            <table class="main" style=" border: 1px solid #101332; border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background: #e7f2ff; border-radius: 3px; color: #000724;">

              <!-- START MAIN CONTENT AREA -->
              <tr>
                <td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 10px;">
                  <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                    <tr>
                      <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">
                        <?php

                          if($editMode) {
                            echo str_replace("[INNER_CONTENT]", $emailContent['inner_content'], $emailContent['content']);
                          }
                          else {
                            echo str_replace("[INNER_CONTENT]", $emailContent['inner_content'], str_replace(' fheditor-accept-blocks="true" contenteditable="true"','',$emailContent['content']));
                          }

                        ?>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>

            <!-- END MAIN CONTENT AREA -->
            </table>

            <!-- START FOOTER -->
            <div class="footer" style="clear: both; Margin-top: 10px; text-align: center; width: 100%;">
              <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                <tr>
                  <td class="content-block" style="font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; font-size: 12px; color: #ffffff; text-align: center;">
                    <span class="apple-link" style="color: #ffffff; font-size: 12px; text-align: center;">
                      Game content and materials are trademarks and copyrights of their respective publisher and its licensors.<br />
                      Forthusk.com is not affiliated with the game publisher.
                    </span>
                    <br> Don't like these emails? <a href="https://www.forthusk.com/email_unsubscribe" style="text-decoration: underline; color: #ffffff; font-size: 12px; text-align: center;">Unsubscribe</a>.
                  </td>
                </tr>
                <tr>
                  <td class="content-block powered-by" style="font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; font-size: 12px; color: #ffffff; text-align: center;">
                    FortHusk.com - All Rights Reserved
                  </td>
                </tr>
              </table>
            </div>
            <!-- END FOOTER -->

          <!-- END CENTERED WHITE CONTAINER -->
          </div>
        </td>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
      </tr>
    </table>


  <?php
  if($editMode) {
  ?>
    <script>

      CKEDITOR.disableAutoInline = true;

      $(document).ready(function() {
        /**
         * CKEDITOR
         */
        $("div[contenteditable='true']" ).each(function( index ) {

          var content_id = $(this).attr('id');

          CKEDITOR.inline(content_id, {
            on: {
              blur: function( event ) {
                var data = event.editor.getData();

                var request = jQuery.ajax({
                  url: "/savePage.php?email=true&email_id=<?=$emailID?>",
                  type: "POST",
                  data: {
                    content : data,
                    content_id : content_id
                  },
                  dataType: "html"
                });

                request.done(function( msg ) {
                  if(msg == "success") {
                    $.notify("Content saved [ID#" + content_id + "][" + msg + "]", "success");
                  }
                  else if (msg == "failed") {
                    $.notify("Error saving content. [ID# " + content_id + "][" + msg + "]\n\nPlease contact a developer.", "error");
                  }
                });

              }
            },
            "extraPlugins": "imgbrowse",
            "filebrowserImageBrowseUrl": "/public/js/libs/ckeditor_4.7.2/plugins/imgbrowse/imgbrowse.html?imgroot=public/images/"
          });

        });


      });

    </script>
  <?php
  }
  ?>


  </body>
</html>