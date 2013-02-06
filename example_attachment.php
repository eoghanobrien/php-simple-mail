<?php

require 'class.simple_mail.php';
echo '<h1>Simple Mail</h1>';

$mailer = new Simple\Mail();

$send	= $mailer->setTo('youremail@gmail.com', 'Your Email')
				 ->setSubject('Test Message')
				 ->setFrom('no-reply@domain.com', 'Domain.com')
				 ->addMailHeader('Reply-To', 'no-reply@domain.com', 'Domain.com')
				 ->addMailHeader('Cc', 'bill@example.com', 'Bill Gates')
				 ->addMailHeader('Bcc', 'steve@example.com', 'Steve Jobs')
				 ->addAttachment('example/pbXBsZSwgY2hh.jpg', 'lolcat_finally_arrived.jpg')
				 ->addAttachment('example/lolcat_what.jpg')
				 ->setMessage('<strong>This is a test message.</strong>')
				 ->setWrap(100)
				 ->send();
				 
//$mailer->debug();

if ($send) {
	echo 'Email sent successfully';
}
else {
	echo 'Could not send email';
}

?>