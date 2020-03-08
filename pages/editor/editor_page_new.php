<?php

if (!\helper\Account::inGroup(5)) {
  /* TODO: Add Admin/Dev notification here for security checks */
  header("Location: /home");
  exit;
}

?>
<div class="block_main">

  <div class="block">
    <div class="header luckiest_guy">
      Editor - Page
    </div>

    <div class="content">
      <h2 class="title">New</h2>

      <div class="form">
        <form method="POST" action="?">

          <div class="left">
            <p>
              <label for="editor-new-page-parent_id">
                Parent Page
                <select name="editor-new-page-parent_id" id="editor-new-page-parent_id" class="form-control" style="width:100%"></select>
              </label>
            </p>

            <p>
              <label for="editor-new-page-page_name">
                Page Name<br />
                <input type="TEXT" name="editor-new-page-page_name" id="editor-new-page-page_name" class="form-control<?=(!empty(\helper\Editor::getError('name_empty')))?' input-error':''?>" placeholder="<?=(!empty(\helper\Editor::getError('name_empty')))?\helper\Editor::getError('name_empty'):''?>" />
              </label>
            </p>

            <p>
              <label for="editor-new-page-page_url">
                Page URL <span class="text-small">(This URL is automaticly created from the parent page and page name)</span><br />
                <input type="TEXT" disabled="disabled" name="editor-new-page-page_url" id="editor-new-page-page_url" class="form-control" />
              </label>
            </p>


            <span style="float:right;text-align:right !important;width:100%;">
              <?php
              if(!empty(\helper\Editor::getError('url_duplicate'))) {
              ?>

                <div class="error_box text-small">
                  <?=\helper\Editor::getError('url_duplicate')?>
                </div>

              <?php
              }
              ?>
              <input align="top" type="SUBMIT" class="button" name="editor-new-page-create_page" id="editor-new-page-create_page" value="Create Page" />
            </span>
          </div>

          <div class="right">

            <p style="margin:10px 0 0 0;display:inline-block;box-sizing:border-box;">
              Page Template<br />
              <label class="image" style="float:left;">
                One Block<br />
                <input type="radio" name="editor-new-page-template" value="blocks_one" checked="checked" />
                <img src="/public/images/default/template_one_block.png">
              </label>

              <label class="image" style="float:right;">
                Two Blocks<br />
                <input type="radio" name="editor-new-page-template" value="blocks_two"/>
                <img src="/public/images/default/template_two_blocks.png">
              </label>
            </p>

              <hr />

            <div class="spoiler-container">
              <div class="spoiler" data-spoiler-link="1"><i class="fas fa-cogs"></i> Advanced Settings</div>
              <div class="spoiler-content" data-spoiler-link="1">

                <p style="margin:10px;">
                  <label for="editor-new-page-redirect_url">
                    Redirect URL <span class="text-small">(This page will redirect to this URL)</span><br />
                    <input type="TEXT" name="editor-new-page-redirect_url" id="editor-new-page-redirect_url" class="form-control" />
                  </label>
                </p>

                <hr />

                <p style="margin:10px;">
                  Require Login? <span class="text-small">(Visitor has to be logged in to view this page)</span><br />
                  <input type="RADIO" id="editor-new-page-require_login_true" name="editor-new-page-require_login" value="true" />
                  <label for="editor-new-page-require_login_true"><span></span>True</label>
                  <br />
                  <input type="RADIO" id="editor-new-page-require_login_false" name="editor-new-page-require_login" checked="checked" value="false" />
                  <label for="editor-new-page-require_login_false"><span></span>False</label>
                </p>

                <hr />

                <p style="margin:10px;">
                  <label for="editor-new-page-parent_id">
                    Group Permission<br /><span class="text-small">(If no group is selected, Everyone got permission)</span>
                    <select name="editor-new-page-groups[]" id="editor-new-page-groups" class="form-control" style="width:100%" multiple="multiple"></select>
                  </label>
                </p>

              </div>
            </div>
          </div>



        </form>
      </div>



      <script src="/public/js/libs/jquery-seo-url-master/jquery.seourl.min.js"></script>
      <script src="/public/js/libs/jquery-spoiler-master/jquery.spoiler.min.js"></script>
      <script>

        $(document).ready(function() {

          /**
           * SPOILER
           */
          $(".spoiler").spoiler();

          var parentURL = "/parent";
          var pageName = "/new-page";

          $('#editor-new-page-parent_id').select2({
            placeholder: 'Select a Parent Page',
            ajax: {
              url: '/ajax.php?get=editor_get_pages',
              dataType: 'json',
              delay: 250
            }
          });

          // MULTIPLE GROUP PERMISSIONS
          $('#editor-new-page-groups').select2({
            placeholder: 'Select a Group',
            ajax: {
              url: '/ajax.php?get=editor_get_groups',
              dataType: 'json',
              delay: 250
            }
          });

          $('#editor-new-page-parent_id').on("select2:select", function (e) {
            parentURL = e.params.data.text.match(/\(([^)]+)\)/)[1];
            updateURL();
          });

          $('#editor-new-page-page_name').on('input', function () {
            pageName = $('#editor-new-page-page_name').val();
            updateURL();
          });

          function updateURL() {
            $("#editor-new-page-page_url").val(parentURL + "/" + pageName.seoURL({'transliterate': true, 'lowercase': true}));
          }

          updateURL();
        });

      </script>


    </div>
  </div>

</div>