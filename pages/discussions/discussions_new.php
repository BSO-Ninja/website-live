<?php

  if(isset($_POST['discuss_submit'])){
    $discussID = \helper\Discussions::submitDiscussion();
    if($discussID > 0) {
      // Successfully created the discussion
      echo "<script>window.location='/discussions/view/{$discussID}/{$_POST['discuss_title']}';</script>";
    }
  }

  //echo("<pre>".var_dump($_POST)."</pre>");

?>
<div class="block_main block" id="block_main">
  <div class="block clear content" id="block">
      <h1 class="header luckiest_guy">Start a new Discussion</h1>

      <div class="row">

        <form method="POST" action="?">

          <div class="column left center" style="width:25% !important;color:#ffffff;">

            <p style="margin-top:10px;font-size:14px;" class="center">
              Please select the tags of which this discussion belong to<br />(must have atleast one tag):
            </p>

            <hr />

            <div class="discuss_tags_list<?=(\helper\Discussions::$errors['discuss_tags']=="true")?' input-error':''?>">

              <?php
                $tags = \helper\Discussions::getTags();
                foreach($tags as $tag) {
                  $checked = (in_array($tag['id'], $_POST['discuss_tags'])) ? true : false;
                  ?>
                  <div class="tag">
                    <input type="checkbox" name="discuss_tags[]" id="tag_<?=$tag['id']?>" value="<?=$tag['id']?>" <?=($checked)?' checked="checked"':''?> />
                    <label for="tag_<?=$tag['id']?>">
                      <i class="fas fa-square" style="margin-right:5px;color:#<?=$tag['tag_color']?>;"></i> <?=$tag['tag_name']?>
                    </label>
                  </div>
                  <?php
                }

              ?>

            </div>

          </div>

          <div class="column right form" style="width:75% !important;">

              <p>
                <label for="discuss_title">
                  Discussion Title <br />
                  <input type="TEXT" name="discuss_title" id="discuss_title" class="form-control<?=(\helper\Discussions::$errors['discuss_title']=="true")?' input-error':''?>" value="<?=@$_POST['discuss_title']?>" />
                </label>
              </p>

              <p>
                <label for="discuss_text">
                  Discussion Text<br />
                  <textarea data-autoresize name="discuss_text" id="discuss_text" class="form-control<?=(\helper\Discussions::$errors['discuss_text']=="true")?' input-error':''?>" rows="20"><?=@$_POST['discuss_text']?></textarea>
                </label>
              </p>

              <p style="text-align:right;">
                <a href="/discussions">Go Back to Discussions</a> | <input type="submit" value="Create Discussion" class="button" name="discuss_submit" />
              </p>

          </div>

        </form>

      </div>

  </div>
</div>

<link rel="stylesheet" href="/public/js/libs/sceditor-2.1.3/minified/themes/default.min.css" />
<script src="/public/js/libs/sceditor-2.1.3/minified/sceditor.min.js"></script>


<script src="/public/js/libs/sceditor-2.1.3/minified/formats/bbcode.min.js"></script>
<script>
  // Replace the textarea #example with SCEditor
  var textarea = document.getElementById('discuss_text');
  sceditor.create(textarea, {
    plugins: 'autosave,undo',
    format: 'bbcode',
    style: 'minified/themes/content/default.min.css',
    toolbar: "bold,italic,underline,strike,|left,center,right,justify|" +
    "size,color,removeformat|bulletlist,orderedlist|quote,link,unlink,youtube|source",
    resizeEnabled: true
  });
</script>