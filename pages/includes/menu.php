<div class="menu_button">
  <a href="<?=LOCATION_PREFIX?>/home" class="menu_item daspan">Home</a>
</div>

<div class="menu_button<?php echo(!\helper\Account::loggedIn()) ? ' grey" title="Coming soon">' : '">' ?>
  <span class="menu_item daspan">Quests</span>
  <?php if(\helper\Account::loggedIn()) { ?>
  <div class="menu_mega">
    <div class="box">
      <div class="content box-brown" style="padding:0px !important;">
        <h3>Quests</h3>

      </div>
    </div>
  </div>
  <?php } ?>
</div>

<div class="menu_button<?php echo(!\helper\Account::loggedIn()) ? ' grey" title="Coming soon">' : '">' ?>
  <span class="menu_item daspan">Items</span>
  <?php if(\helper\Account::loggedIn()) { ?>
  <div class="menu_mega">
    <div class="box">
      <div class="content box-brown" style="padding:0px !important;">
        <h3>Items</h3>

      </div>
    </div>
  </div>
  <?php } ?>
</div>

<div class="menu_button<?php echo(!\helper\Account::loggedIn()) ? ' grey" title="Coming soon">' : '">' ?>
  <span class="menu_item daspan">Guides</span>
  <?php if(\helper\Account::loggedIn()) { ?>
  <div class="menu_mega">
    <div class="box">
      <div class="content box-brown" style="padding:0px !important;">
        <h3>Guides</h3>

      </div>
    </div>
  </div>
  <?php } ?>
</div>

<div class="menu_button<?php echo(!\helper\Account::loggedIn()) ? ' grey" title="Coming soon">' : '">' ?>
    <span class="menu_item daspan">LFG</span>
  <?php if(\helper\Account::loggedIn()) { ?>
    <div class="menu_mega">
      <div class="box">
        <div class="content box-brown" style="padding:0px !important;">
          <h3>Looking for group</h3>

        </div>
      </div>
    </div>
  <?php } ?>
</div>

<div class="menu_button">
    <span class="menu_item daspan">Tools</span>
  <div class="menu_mega">
    <div class="box">
      <div class="content box-brown" style="padding:0px !important;">

        <div>
          <div class="menu_mega_box50 mmb50_imbuements">
            <a href="<?=LOCATION_PREFIX?>/imbuements"></a>
          </div>
          <div class="menu_mega_box50 mmb50_loot_calculator">
            <a href="<?=LOCATION_PREFIX?>/loot-calculator"></a>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<div class="menu_button">
  <a href="<?=LOCATION_PREFIX?>/about-us" class="menu_item daspan">About Us</a>
</div>

  <?php
  if(!\helper\Account::loggedIn()) {
  ?>
    <div class="menu_button login">
      <a href="<?=LOCATION_PREFIX?>/register" class="menu_item daspan">Register</a>
    </div>
  <?php
  } else if(\helper\Account::loggedIn()) {
  ?>
    <div class="menu_button login">
      <a href="<?=LOCATION_PREFIX?>/account" class="menu_item daspan">Account</a>
    </div>
  <?php
  }
  ?>

<script>
  $( function() {
    $( document ).tooltip({
      position: {
        my: "center bottom-20",
        at: "center top",
        using: function( position, feedback ) {
          $( this ).css( position );
          $( "<div>" )
            .addClass( "arrow" )
            .addClass( feedback.vertical )
            .addClass( feedback.horizontal )
            .appendTo( this );
        }
      }
    });
  } );
</script>