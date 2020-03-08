<?php

  // Get emails and send them

  $emails = \core\DB::select("SELECT * FROM `crons_emails` WHERE `sent` = 'false' ORDER BY `added_at`");

  if($emails) {

    foreach($emails as $email) {

      // Check so that we have an email to send the mail to
      if(isset($email['recipient']) && !empty($email['recipient'])) {
        $recipient = $email['recipient'];
      }
      else if(isset($email['account_id']) && $email['account_id'] > 0) {
        $account = \core\DB::selectFirst("SELECT `email` FROM `accounts` WHERE `id` = ?", [$email['account_id']]);
        $recipient = $account['email'];
      }

      if(isset($recipient) && !empty($recipient)) {
        $emailTemplate = \core\DB::selectFirst("SELECT emails.*, emails_content.content AS content, emails_content.inner_content AS inner_content FROM emails  
                                                      INNER JOIN emails_content  
                                                      ON emails_content.email_id = emails.id 
                                                      WHERE emails.id = ?
                                                    ", [$email['email']]);

        // Let's replace some vars
        if (isset($email['vars']) && !empty($email['vars'])) {
          $vars = json_decode($email['vars']);

          foreach ($vars as $key => $value) {
            $emailTemplate['inner_content'] = str_replace("[{$key}]", $value, $emailTemplate['inner_content']);
          }
        }

        $emailContent = str_replace("[INNER_CONTENT]", $emailTemplate['inner_content'], $emailTemplate['template']);

        if(sendMail($recipient, $emailTemplate['subject'], $emailContent)) {
          // If we succeeded in sending the mail, we update `sent` to true
          \core\DB::execute("UPDATE `crons_emails` SET `sent` = 'true' WHERE `id` = ?", [$email['id']]);
        }
        else {
          // Log error here
          \helper\Admin::log("Failed sending email to '{$recipient}' from crons_email ID#{$email['id']}");
        }
      }

    }

  }


function sendMail($to, $subject, $bodyHTML) {

// Identify the sender, recipient, mail subject, and body
$from    = "FortHusk.com";
//$subject   = "{$subject}\r\n\r\n";

// Identify the mail server, username, password, and port


require_once "Mail.php";
//require_once "Mail/mime.php";

// see http://pear.php.net/manual/en/package.mail.mail-mime.php
// for further extended documentation on Mail_Mime

$bodyText = preg_replace("/\n\s+/", "\n", rtrim(html_entity_decode(strip_tags($bodyHTML))));
$crlf = "\n";

// create a new Mail_Mime for use
//$mime = new Mail_mime($crlf);
// define body for Text only receipt
//$mime->setTXTBody($bodyText);
// define body for HTML capable recipients
//$mime->setHTMLBody($bodyHTML);

/*
$headers = array (
'From' => $from,
'To' => $to,
'Subject' => $subject
);
*/
  $content = "text/html; charset=utf-8";
  $mimeVersion = "1.0";

$headers = array (
    'From' => $from,
    'To' => $to,
    'Subject' => $subject,
    'Reply-To' => "forthusk@gmail.com",
    'MIME-Version' => $mimeVersion,
    'Content-type' => $content);

$smtp = Mail::factory('smtp',
array (
'host' => EMAIL_HOST,
'auth' => true,
'username' => EMAIL_USERNAME,
'password' => EMAIL_PASSWORD,
'port'     => EMAIL_PORT
));

//$body = $mime->get();
//$headers = $mime->headers($headers);

$mail = $smtp->send($to, $headers, $bodyHTML);

if (PEAR::isError($mail)) {
  return false;
} else {
  return true;
}

}
