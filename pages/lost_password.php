<?php

  if(isset($_POST['lost_password_submit'])) {
    \helper\Account::lostPassword();
  }

?>
<div class="block_main block" id="block_main">
  <div class="block_left clear" id="block_left">
    <div class="block content">
      <h1 class="header luckiest_guy">Lost Password</h1>

      <div class="row">
        <div class="column left">
          <form method="POST" action="?" id="lost_password_form">

            <p style="margin-bottom:0px !important;">
              Specify your account email below. A temporary password will be sent to your inbox.<br />
              If you can't find the email, try looking in the Spam folder.
            </p>

            <p style="margin-bottom:0px !important;">
              <b>Your Email</b><br />
              <input name="lost_password_email" type="text" placeholder="Your Email" class="top_right_login_input" /><br />
            </p>

            <div style="margin:10px 0 0 20px !important;" class="g-recaptcha" data-sitekey="6LdPJlwUAAAAANhpiHsZ3t2kVtxbSH4himZJsFwf"></div>

            <p>
              <input id="register_account_submit" name="lost_password_submit" type="submit" value="Recover Account" class="button" style="float:left;" />
            </p>

          </form>
        </div>
        <div class="column right">
          <p style="margin-top:10px;">
            <b>Forthusk Membership</b><br />
          - Look for friends to play with<br />
          - Mission alert notifications<br />
          - Join in on discussions<br />
          ... and ALOT more!<br />
          </p>
          <p>
            <img src="/public/images/default/256px-Ray.png" style="width:200px;" />
          </p>
        </div>
      </div>

    </div>
  </div>
  <div class="block_right" id="block_right">
    <div class="block content">
      <p>Block here</p>
    </div>
  </div>
</div>