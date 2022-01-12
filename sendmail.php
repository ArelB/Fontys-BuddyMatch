<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
  
  $dest = "recipient@gmail.com";
  $subjetc = "Test Email";
  $body = "Hi this is a test email send by a php script";
  $headers = "From: YourGmailId@gmail.com";
  if (mail($dest, $subjetc, $body, $headers)) {
    echo "Email successfully sent to $dest ...";
  } else {
    echo "Failed to send email...";
  }
?>