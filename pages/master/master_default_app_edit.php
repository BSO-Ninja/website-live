<?php
  // Check if we are allowed to edit pages
  if(!\helper\Account::hasPermission(4)) { // 4 = Edit Pages
    header("Location: /access-denied");
    exit();
  }
?>
<!DOCTYPE html>
<head>
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
  <!-- Jodit -->
  <script src="/public/js/libs/jodit/jodit.min.js"></script>
  <link rel="stylesheet" type="text/css" href="/public/js/libs/jodit/jodit.min.css" />

</head>
<body>

  <div id="main_edit">

    <div id="main_content_edit_toolbar">
      <div class="main_content_edit_toolbar-button">
        <a href="<?php echo str_replace("?edit_page=true","", \core\template::$page['url']); ?>"><img id="page_go_back_button" src="/public/images/icons/resultset_previous.png" title="Go back" /></a>
      </div>

      <div class="main_content_edit_toolbar-divider">
        <img src="/public/images/icons/user_edit.png" title="Editors Toolbar" />
      </div>
      <div class="main_content_edit_toolbar-button">
        <img id="page_send_review_button" src="/public/images/icons/client_account_template.png" title="Send for review" />
      </div>
      <div class="main_content_edit_toolbar-button">
        <img id="page_reload_button" src="/public/images/icons/arrow_undo.png" title="Reload Original" />
      </div>
      <div class="main_content_edit_toolbar-button">
        <img id="page_change_language_button" src="/public/images/icons/change_language.png" title="Change Language" />
      </div>

    <?php
      if(\helper\Account::hasPermission(5)) { // 2 = Publish pages
    ?>
      <div class="main_content_edit_toolbar-divider">
        <img src="/public/images/icons/user_suit.png" title="Senior Editors Toolbar" />
      </div>
      <div class="main_content_edit_toolbar-button">
        <img id="page_publish_page_button" src="/public/images/icons/accept_document.png" title="Publish page" />
      </div>
      <div class="main_content_edit_toolbar-button">
        <img id="page_update_required_button" src="/public/images/icons/to_do_list_checked_1.png" title="Update required" />
      </div>
    <?php
      }
    ?>

    <?php
      if(\helper\Account::hasPermission(2)) { // 2 = Admin access
        ?>
        <div class="main_content_edit_toolbar-divider">
          <img src="/public/images/icons/administrator.png" title="Administrators Toolbar"/>
        </div>

        <?php
        if (\core\template::$page['id'] <= 0) {
          ?>
          <div class="main_content_edit_toolbar-button">
            <img id="page_create_page_button" src="/public/images/icons/page_add.png" title="Create page"/>
          </div>
        <?php
        } else {
        ?>
          <div class="main_content_edit_toolbar-button">
            <?php
            if (\core\template::$page['locked'] == "false") {
              ?>
              <img id="page_lock_page_button" src="/public/images/icons/lock_open.png" title="Page is OPEN"/>
              <?php
            } else {
              ?>
              <img id="page_lock_page_button" src="/public/images/icons/lock.png" title="Page is LOCKED"/>
              <?php
            }
            ?>
          </div>
          <div class="main_content_edit_toolbar-button">
            <img id="page_settings_button" src="/public/images/icons/cog.png" title="Page Settings"/>
          </div>
          <?php
        }
      }
    ?>
    </div>

    <div id="main_content_wrapper_edit">
      <div id="main_content">
        <?php
        $loadJodit = true;
        $draft = \helper\Editor::loadDraft($draft['id']);

        if(\core\template::$page['locked'] == "false" || empty(\core\template::$page)) {
          $loadJodit = false;
        ?>
          <textarea id="editor">
            <?=$draft['html']?>
          </textarea>
        <?php
        }
        else {
        ?>
          <div class="toast__container">
            <div class="toast__cell">
              <div class="toast toast--yellow">
                <div class="toast__icon">
                  <svg version="1.1" class="toast__svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 301.691 301.691" style="enable-background:new 0 0 301.691 301.691;" xml:space="preserve">
                    <g>
                      <polygon points="119.151,0 129.6,218.406 172.06,218.406 182.54,0  "></polygon>
                      <rect x="130.563" y="261.168" width="40.525" height="40.523"></rect>
                    </g>
                  </svg>
                </div>
                <div class="toast__content">
                  <p class="toast__type">Page LOCKED</p>
                  <p class="toast__message">This page has been locked and can not be edited at the moment.</p>
                </div>
              </div>
            </div>
          </div>

          <?=$draft['html']?>
        <?php
        }
        ?>


        <div id="page_settings_dialog" title="Page Settings">
          <p>Page Settings</p>
        </div>

        <script>
          $(document).ready(function() {
            $("#page_settings_dialog").dialog({
              autoOpen: false
            });
            $( "#page_settings_button" ).on( "click", function() {
              $( "#page_settings_dialog" ).dialog( "open" );
            });
            $( "#page_reload_button" ).on( "click", function() {
              if(confirm("Are you sure you want to delete this draft and reload the original live version?")) {
                alert("reloading...");
              }
            });

            <?php
            if(\helper\Account::hasPermission(5)) { // 5 = Publish Pages
            ?>
            $("#page_publish_page_button").on("click", function () {
              $.ajax({
                method: "POST",
                url: "ajax.php?action=editor_page_publish",
                data: {page: "<?=\core\template::$urlPath?>"}
              })
                .done(function (msg) {
                  var call = JSON.parse(msg);
                  if (call.status == "page_published") {$.notify("SUCCESS: The page has been published", "success");}
                  if (call.status == "page_locked") {$.notify("ERROR: The page is locked, and can not be updated", "error");}
                  if (call.status == "page_need_be_created") {$.notify("ERROR: The page does not exist, and needs to be created first", "error");}
                });
            });
            <?php
            }
            ?>

            <?php
              if(\helper\Account::hasPermission(6)) { // 6 = Lock Pages
            ?>
            $("#page_lock_page_button").on("click", function () {
              $.ajax({
                method: "POST",
                url: "ajax.php?action=editor_page_lock",
                data: {page: "<?=\core\template::$urlPath?>"}
              })
                .done(function (msg) {
                  var call = JSON.parse(msg);
                  if (call.status == "page_locked_true") {
                    $("#page_lock_page_button").attr('src', '/public/images/icons/lock.png');
                    $("#page_lock_page_button").attr('title', 'Page is LOCKED');
                    $.notify("SUCCESS: The page has been LOCKED from editing", "success");
                  }
                  if (call.status == "page_locked_false") {
                    $("#page_lock_page_button").attr('src', '/public/images/icons/lock_open.png');
                    $("#page_lock_page_button").attr('title', 'Page is OPEN');
                    $.notify("SUCCESS: The page has been OPENED for editing", "success");
                  }

                  location.reload();
                });
            });
            <?php
              }
            ?>

            <?php
              if($loadJodit == false) {
            ?>
            var editor = new Jodit("#editor", {
              "language": "en",
              "direction": "ltr",
              "enter": "P",
              "defaultMode": "1",
              "width": 868,
              "buttons": ",,,,,,,,,,,,,,,align,|,font,fontsize,brush,paragraph,|,image,file,video,table,link,|,,,cut,hr,eraser,copyformat,selectall",
              "buttonsXS": ",,,,,,,,,,,,,,,align,|,font,fontsize,brush,paragraph,|,image,file,video,table,link,|,,,cut,hr,eraser,copyformat,selectall",
              "buttonsSM": ",,,,,,,,,,,,,,,align,|,font,fontsize,brush,paragraph,|,image,file,video,table,link,|,,,cut,hr,eraser,copyformat,selectall",
              "buttonsMD": "source,bold,strikethrough,underline,italic,superscript,subscript,ul,ol,outdent,indent,font,fontsize,brush,paragraph,image,file,video,table,link,align,undo,redo,cut,hr,eraser,selectall",
              uploader: {
                url: 'http://tibiamate.local/connector.php?action=fileUpload'
              },
              filebrowser: {
                ajax: {
                  url: 'http://tibiamate.local/connector.php'
                }
              },
              extraButtons: [
                {
                  name: 'save',
                  iconURL: '/public/images/icons/16x16/diskette.png',
                  exec: function (editor) {
                    $.ajax({
                      method: "POST",
                      url: "ajax.php?action=editor_page_save",
                      data: {html: editor.value, page: "<?=\core\template::$urlPath?>"}
                    })
                      .done(function (msg) {
                        var call = JSON.parse(msg);
                        if (call.status == "page_locked") {
                          $.notify("ERROR: The page is locked, cannot save draft", "error");
                        }
                        if (call.status == "page_saved") {
                          $.notify("SUCCESS: The page has been saved", "success");
                        }
                        if (call.status == "same_html") {
                          $.notify("INFO: The draft is the same as the saved one", "info");
                        }
                      });
                  }
                }
              ]
            });
            <?php
            } // END LOAD JODIT
          ?>
          });
        </script>
      </div>

    </div>

  </div>

</body>
</html>
