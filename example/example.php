<?php

require '../class.simple_mail.php';
echo '<h1>Simple Mail</h1>';

$mailer = new SimpleMail();

$send	= $mailer->setTo('test@gmail.com', 'Recipient 1')
				 ->setTo('test2@gmail.com', 'Recipient 2')
				 ->setSubject('Test Message')
				 ->setFrom('sender@gmail.com', 'Sender')
				 ->addMailHeader('Reply-To', 'sender@gmail.com', 'Sender')
				 ->addMailHeader('Cc', 'bill@example.com', 'Bill Gates')
				 ->addMailHeader('Bcc', 'steve@example.com', 'Steve Jobs')
				 ->addGenericHeader('X-Mailer', 'PHP/' . phpversion())
				 ->addGenericHeader('Content-Type', 'text/html; charset="utf-8"')
				 ->setMessage('<strong>This is a test message.</strong>')
				 ->setWrap(78)
				 ->send();

echo $mailer->debug();

if ($send) {
	echo 'Email was sent successfully!';
}
else {
	echo 'An error occurred. We could not send email';
}
