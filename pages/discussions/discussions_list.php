<?php

  function isActive($route, $return = " active") {
    return (\core\template::route(1) == $route) ? $return : "";
  }
  function isTag($tagURL, $return = " active") {
    return (\core\template::route(1) == "tag" && \core\template::route(2) == $tagURL) ? $return : "";
  }

?>
<div class="block_main block" id="block_main">
  <div class="block clear content" id="block">
      <h1 class="header luckiest_guy">Discussions</h1>

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

          <?php
          $discussions = \helper\Discussions::getDiscussions();
          foreach($discussions as $discussion) {
            ?>
              <div class="discuss_item" onClick="window.location='/discussions/view/<?=$discussion['id']?>/<?=$discussion['title']?>'">
                <div class="discuss_item_tags">
                  <div class="tag" style="background-color:#ef564f;">General</div>
                  <div class="tag" style="background-color:#48bf83;">Schematics</div>
                  <div class="tag" style="background-color:#6dbb3e;">Patches/Updates</div>
                </div>
                <span class="discuss_title"><?=$discussion['title']?></span><br />
                <span class="discuss_info">By <strong>PeekaBooz</strong> <?=\helper\Functions::timeago($discussion['created_at'])?> | <i class="fas fa-eye"></i> 1337 views | <i class="fas fa-comment"></i> 13 replies</span><br />
                <p>
                  <?=(strlen($discussion['text']) > 50) ? substr(strip_tags($discussion['text']), 0, 50) . '...' : strip_tags($discussion['text'])?>
                </p>
              </div>

            <?php
          }
          ?>

        </div>

      </div>

  </div>
</div>