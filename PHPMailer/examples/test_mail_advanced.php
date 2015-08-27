<html>
<head>
<title>PHPMailer - Mail() advanced test</title>
</head>
<body>

<?php
require_once '../class.phpmailer.php';

$mail = new PHPMailer(true); //defaults to using php "mail()"; the true param means it will throw exceptions on errors, which we need to catch

try {
  $mail->IsSMTP(); // enable SMTP
  $mail->SMTPDebug = 2; // debugging: 1 = errors and messages, 2 = messages only
  $mail->SMTPAuth = true; // authentication enabled
  $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
  // $mail->SMTPSecure = "tls";
  $mail->Host = "smtp.gmail.com";
  $mail->Port = 465;//465; // or 587
  $mail->IsHTML(true);
  $mail->Username = "rgdx.pppl@gmail.com";
  $mail->Password = "PPPL*1234";
  // $mail->Username = "liutauras.rusaitis";
  // $mail->Password = "afg1gena1";
  // ------------------------------------------------------------------------
  $mail->AddReplyTo('rgdx.pppl@gmail.com', 'RGDX');
  $mail->AddAddress('liutauras.rusaitis@gmail.com', 'Liu Rus');
  $mail->SetFrom('rgdx.pppl@gmail.com', 'RGDX');
  $mail->AddReplyTo('rgdx.pppl@gmail.com', 'First Last');
  $mail->Subject = 'Welcome to RGDX Experiment!';
  $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
  // $mail->Body ="Hello User\r\n\r\n".
  //           "Thanks for your registration with ".$this->sitename."\r\n".
  //           "Please click the link below to confirm your registration.\r\n".
  //           "$confirm_url\r\n".
  //           "\r\n".
  //           "Regards,\r\n".
  //           "Webmaster\r\n".
  //           $this->sitename;
  $mail->MsgHTML(file_get_contents('contents.html'));
  $mail->AddAttachment('images/phpmailer.gif');      // attachment
  $mail->AddAttachment('images/phpmailer_mini.gif'); // attachment
  $mail->Send();
  echo "Message Sent OK</p>\n";
} catch (phpmailerException $e) {
  echo $e->errorMessage(); //Pretty error messages from PHPMailer
} catch (Exception $e) {
  echo $e->getMessage(); //Boring error messages from anything else!
}
?>
</body>
</html>
