<?php
require 'class.simple_mail.php';
echo '<h1>Simple Mail</h1>';

$mailer = new Simple_Mail();
$send	= $mailer->setTo('john@example.com', 'John Smith')
				->setSubject('Test Message')
				->setFrom('no-reply@domain.com', 'Domain.com')
				->addMailHeader('Reply-To', 'no-reply@domain.com', 'Domain.com')
				->addMailHeader('Cc', 'bill@example.com', 'Bill Gates')
				->addMailHeader('Bcc', 'steve@example.com', 'Steve Jobs')
				->addGenericHeader('X-Mailer', 'PHP/' . phpversion())
				->setMessage('<strong>This is a test message.</strong>', TRUE) // Omit the second parameter if you would like to send the message plain text.
				->setWrap(100)
				->send();

if ($send)
{
	echo 'Email sent successfully';
}
else
{
	echo 'Could not send email';
}


?>