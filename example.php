<?php

require 'class.simple_mail.php';
echo '<h1>Simple Mail</h1>';

$mailer = new Simple_Mail(TRUE); // Set to TRUE to enable exception throwing

$send	= $mailer->setTo('youremail@gmail.com', 'Your Email')
				 ->setSubject('Test Message')
				 ->setFrom('no-reply@domain.com', 'Domain.com')
				 ->addMailHeader('Reply-To', 'no-reply@domain.com', 'Domain.com')
				 ->addMailHeader('Cc', 'bill@example.com', 'Bill Gates')
				 ->addMailHeader('Bcc', 'steve@example.com', 'Steve Jobs')
				 ->addGenericHeader('X-Mailer', 'PHP/' . phpversion())
				 ->addGenericHeader('Content-Type', 'text/html; charset="utf-8"')
				 ->setMessage('<strong>This is a test message.</strong>')
				 ->setWrap(100)/*
				 ->send()*/;
echo '<pre>';
$mailer->debug();
echo '</pre>';

if ($send) {
	echo 'Email sent successfully';
}
else {
	echo 'Could not send email';
}

?>