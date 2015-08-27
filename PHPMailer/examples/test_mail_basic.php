<html>
<head>
<title>PHPMailer - Mail() basic test</title>
</head>
<body>

<?php

require_once('../class.phpmailer.php');

$mail             = new PHPMailer(); // defaults to using php "mail()"

$body             = file_get_contents('contents.html');
$body             = eregi_replace("[\]",'',$body);

$mail = new PHPMailer(); // create a new object
$mail->IsSMTP(); // enable SMTP
$mail->SMTPDebug = 2; // debugging: 1 = errors and messages, 2 = messages only
$mail->SMTPAuth = true; // authentication enabled
$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
// $mail->SMTPSecure = "tls";
$mail->Host = "smtp.gmail.com";
$mail->Port = 465;//465; // or 587
$mail->IsHTML(true);
$mail->Username = "liutauras.rusaitis@gmail.com";
$mail->Password = "afg1gena1";
// $mail->SetFrom(liutauras.rusaitis@gmail.com);
// $mail->Subject = "Test";
// $mail->Body = "hello";

$mail->SetFrom('liutauras.rusaitis@gmail.com', 'Liutauras Rusaitis');

$mail->AddReplyTo("liutauras.rusaitis@gmail.com","Liutauras Rusaitis");

$mail->AddAddress('liutauras.rusaitis@icloud.com');

// $mail->AddReplyTo("name@yourdomain.com","First Last");

// $address = "whoto@otherdomain.com";
// $mail->AddAddress($address, "John Doe");

$mail->Subject    = "PHPMailer Test Subject via mail(), Working!";

$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

$mail->MsgHTML($body);


$mail->AddAttachment("images/phpmailer.gif");      // attachment
$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment

if(!$mail->Send()) {
  echo "Mailer Error: " . $mail->ErrorInfo;
} else {
  echo "Message sent!";
}

?>

</body>
</html>
