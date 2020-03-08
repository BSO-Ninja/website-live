<div class="block_main block" id="block_main">
  <div class="block_left" id="block_left">
    <?php
      $topics = \helper\Forthusk::topics($page['forum_id']);

      foreach($topics AS $topic) {
      ?>

        <div class="block">
          <h1 class="header luckiest_guy"><?=$topic['topic_title']?></h1>
          <p>
            <?=generate_text_for_display($topic['post_text'],"","","")?>
          </p>
        </div>

      <?php
      }
    ?>
  </div>
  <div class="block_right" id="block_right">
    [blocks:block_right]
  </div>
</div>