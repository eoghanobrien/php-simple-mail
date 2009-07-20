<?php

echo '<h1>Simple Mail</h1>';

$Mail 	= new Simple_Mail();
$send	= $Mail->setTo('john@example.com', 'John Smith')
				->setSubject('Test Message')
				->setFrom('no-reply@domain.com', 'Domain.com')
				->addMailHeader('Reply-To', 'no-reply@domain.com', 'Domain.com')
				->addMailHeader('Cc', 'bill@example.com', 'Bill Gates')
				->addMailHeader('Bcc', 'steve@example.com', 'Steve Jobs')
				->setMessage('This is a test message.')
				->setWrap(100)
				->send();

if ($send)
	echo 'Email sent successfully';
else
	echo 'Could not send email';


?>