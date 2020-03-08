<?php

if (!\helper\Account::inGroup(5) || !\helper\Account::editMode()) {
  /* TODO: Add Admin/Dev notification here for security checks */
  header("Location: /home");
  exit;
}

?>

<!-- CKEditor -->
<script src="/public/js/libs/ckeditor_4.7.2/ckeditor.js"></script>






<!-- TEMPLATE START -->
<?php
  $template = \helper\Editor::$draft['template'];

  // Loop through each block and place them in their right containers
  $blockHTML = array();
  foreach(\helper\Editor::$blocks as $block) {
    $blockHTML[$block['block_parent']] .= str_replace("[INNER_CONTENT]", $block['inner_content'], $block['content']);
  }

  foreach($blockHTML as $parent => $content) {
    $template = str_replace("[blocks:{$parent}]", $content, $template);
  }

  // Fallback if no blocks available
  $template = str_replace("[blocks:block_main]", "No blocks created yet", $template);
  $template = str_replace("[blocks:block_left]", "No blocks created yet", $template);
  $template = str_replace("[blocks:block_right]", "No blocks created yet", $template);

  echo "{$template}";
?>





<div class="fh-editor-static">
  <span id="fh-editor-static-nav-toggle_blocks" class="tooltip"><span class="tooltiptext">Toggle Blocks</span><i class="far fa-window-restore"></i></span>
  <span id="fh-editor-static-nav-toggle_grid" class="tooltip"><span class="tooltiptext">Toggle Grid</span><i class="fas fa-th-large"></i></span>
  <span id="fh-editor-static-nav-draft" class="tooltip"><span class="tooltiptext">This is a draft, and not public.</span>Draft</span>
  <span id="fh-editor-static-nav-toggle_editor" class="tooltip"><span class="tooltiptext">Page Settings</span>Page Settings</span>
</div>

<div id="fh-editor" class="ui-widget-content">
  <p id="fh-editor-header" class="ui-widget-header no-border-radius">Forthusk Editor <span style="float:right" id="fh-editor-close"><i class="fas fa-window-minimize"></i></span></p>

  <div id="tabs" class="no-border-radius">
    <ul>
      <li><a href="#tabs-1">Page Settings</a></li>
    </ul>
    <div id="tabs-1">
      Page Settings here
    </div>
  </div>
</div>







<div id="hidden_stuff" style="visibility:hidden;">

  <div class="fheditor-add_block tooltip" fheditor-add-block="top"><span class="tooltiptext">Add Block</span><i class="far fa-plus-square fheditor-add_block_button"></i></div>

</div>


<!-- MODALS -->

<!-- ADD BLOCK -->
<div id="fhe-block-add" title="Add a block">
  <strong>Select a block:</strong>

  <form method="POST" action="?" id="new_block_form">

    <input type="hidden" id="new_block_draft_id" name="new_block_draft_id" value="<?=\helper\Editor::$draft['id']?>" />
    <input type="hidden" id="new_block_parent_id" name="new_block_parent_id" value="" />
    <input type="hidden" id="new_block_position" name="new_block_position" value="" />

  <?php

    $savedBlocks = \helper\Editor::getSavedBlocks();
    foreach($savedBlocks as $block) {
    ?>

      <label class="image" style="float:left;">
        <input type="RADIO" name="new_block" id="<?=$block['id']?>" value="<?=$block['id']?>" <?=($block['id']==2)?' checked="checked"':''?> />
        <span>
          <strong><?=$block['name']?></strong>
          <p class="text-small">
            <?=$block['description']?>
          </p>
        </span>
      </label>

    <?php
    }
  ?>
  </form>

</div>

<script>

  CKEDITOR.disableAutoInline = true;

  $(document).ready(function() {


    /**
     * ADD THE ADD_BLOCK_DIVS ON TOP AND BOTTOM
     */
    $("div[fheditor-accept-blocks='true']" ).each(function( index ) {
      var add_block_clone_top = $('#hidden_stuff .fheditor-add_block').clone();
      var add_block_clone_bottom = $('#hidden_stuff .fheditor-add_block').clone();

      // First we add the TOP one
      add_block_clone_top.attr("fheditor-add-block", "top");
      // Prepend the add_block div
      $(this).prepend(add_block_clone_top);

      // Finally we add the BOTTOM one
      add_block_clone_bottom.attr("fheditor-add-block", "bottom");
      // Prepend the add_block div
      $(this).append(add_block_clone_bottom);
    });

    /**
     * FORTHUSK EDITOR
     */
    // Editor
    $( "#fh-editor" ).draggable({ handle: "#fh-editor-header" }).resizable();

    $("#fh-editor-static-nav-toggle_blocks").click(function(){
      $(".fheditor-add_block").toggle();
    });

    $("#fh-editor-static-nav-toggle_editor").click(function(){
      $('#fh-editor').toggle();
    });

    $("#fh-editor-static-nav-toggle_grid").click(function(){
      $(".block").toggleClass('block_border');
    });

    $("#fh-editor-close").click(function(){
      $('#fh-editor').toggle();
    });

    // Tabs
    $( "#tabs" ).tabs();

    /**
     * OPEN BLOCK PICKER
     */
    $("#fhe-block-add").dialog({
      autoOpen: false,
      resizable: false,
      height: 600,
      width: 600,
      modal: true,
      buttons: {
        "Insert Block": function() {
          $("#new_block_form").submit();
          $( this ).dialog( "close" );
        },
        Cancel: function() {
          $( this ).dialog( "close" );
        }
      }
    });

    $( ".fheditor-add_block_button" ).on( "click", function() {
      var parentBlock = $(this).parent('.fheditor-add_block');
      var parentContainer = parentBlock.parent('div');
      //console.log(parentContainer.attr('id') +":"+ parentBlock.attr('fheditor-add-block'));
      $("#new_block_parent_id").val(parentContainer.attr('id'));
      $("#new_block_position").val(parentBlock.attr('fheditor-add-block'));
      $("#fhe-block-add").dialog( "open" );
    });


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
              url: "/savePage.php",
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