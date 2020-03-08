<?php

  function isActive($route, $return = " active") {
    return (\core\template::route(1) == $route) ? $return : "";
  }
  function isTag($tagURL, $return = " active") {
    return (\core\template::route(1) == "tag" && \core\template::route(2) == $tagURL) ? $return : "";
  }

  $discussion = \helper\Discussions::getDiscussion(\core\template::route(2));

?>
<div class="block_main block" id="block_main">
  <div class="block clear content" id="block">
      <h1 class="header luckiest_guy">Discussions - <?=$discussion['title']?></h1>

      <div class="row">
        <div class="column left center discuss_tags_list" style="width:25% !important;color:#ffffff;">

          <p style="margin-top:10px;" class="center">
            <input type="submit" value="Start a Discussion" class="button" style="" onClick="window.location='/discussions/new'" />
          </p>

          <hr />

          <div class="discuss_tags_list">

              <a href="/discussions/all">
                <div class="tag1<?=isActive("all")?>">
                  <i class="fas fa-comments" style="margin-right:5px;"></i> All Discussions
                </div>
              </a>

              <a href="/discussions/following">
              <div class="tag1<?=isActive("following")?>">
                <i class="fas fa-star" style="margin-right:5px;"></i> Following
              </div>
              </a>

              <a href="/discussions/popular">
              <div class="tag1<?=isActive("popular")?>">
                <i class="fas fa-thumbs-up" style="margin-right:5px;"></i> Popular
              </div>
              </a>

              <a href="/discussions/newest">
              <div class="tag1<?=isActive("newest")?>">
                <i class="fas fa-lightbulb" style="margin-right:5px;"></i> Newest
              </div>
              </a>

          </div>

          <hr />

          <strong>Tags</strong><br />

          <div class="discuss_tags_list">
            <?php
            $tags = \helper\Discussions::getTags();
            foreach($tags as $tag) {
              ?>
              <a href="/discussions/tag/<?=$tag['tag_url']?>">
                <div class="tag1<?=isTag($tag['tag_url'])?>">
                  <i class="fas fa-square" style="margin-right:5px;color:#<?=$tag['tag_color']?>;"></i> <?=$tag['tag_name']?>
                </div>
              </a>
              <?php
            }
            ?>
          </div>

        </div>

        <div class="column right" style="width:75% !important;">

          <p>
            <span class="discuss_title"><?=$discussion['title']?></span><br />
            <span class="discuss_info">By <strong>PeekaBooz</strong> <?=\helper\Functions::timeago($discussion['created_at'])?> | <i class="fas fa-eye"></i> 1337 views | <i class="fas fa-comment"></i> 13 replies</span><br />
            <p>
            <?=strip_tags($discussion['text'])?>
            </p>
          </p>

          <hr />

          <p>
            <label for="discuss_reply_text">
              Reply<br />
              <textarea data-autoresize name="discuss_reply_text" id="discuss_reply_text" class="form-control<?=(\helper\Discussions::$errors['discuss_reply_text']=="true")?' input-error':''?>" rows="8"><?=@$_POST['discuss_reply_text']?></textarea>
            </label>
          </p>

          <p style="text-align:right;">
            <a href="/discussions">Go Back to Discussions</a> | <input type="submit" value="Reply" class="button" name="discuss_reply_submit" />
          </p>

          <hr />

          <p>
            <strong>PeekaBooz</strong> <span style="font-size:11px;">| 1 minute ago</span><br />
            This is a comment on the discussion
          </p>

          <hr />

          <p>
            <strong>PeekaBooz</strong> <span style="font-size:11px;">| 1 minute ago</span><br />
            This is a comment on the discussion
          </p>

        </div>

      </div>

  </div>
</div>



<link rel="stylesheet" href="/public/js/libs/sceditor-2.1.3/minified/themes/default.min.css" />
<script src="/public/js/libs/sceditor-2.1.3/minified/sceditor.min.js"></script>


<script src="/public/js/libs/sceditor-2.1.3/minified/formats/bbcode.min.js"></script>
<script>
  // Replace the textarea #example with SCEditor
  var textarea = document.getElementById('discuss_reply_text');
  sceditor.create(textarea, {
    plugins: 'autosave,undo',
    format: 'bbcode',
    style: 'minified/themes/content/default.min.css',
    toolbar: "bold,italic,underline,strike,|left,center,right,justify|" +
    "size,color,removeformat|bulletlist,orderedlist|quote,link,unlink,youtube|source",
    resizeEnabled: true
  });
</script>