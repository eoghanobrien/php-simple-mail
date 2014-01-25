<?php

require '../class.simple_mail.php';
echo '<h1>Simple Mail</h1>';

$mailer = new Simple_Mail();

$send	= $mailer->setTo('email@receiver-domain.com', 'John Smith')
				 ->setSubject('Test Message')
				 ->setFrom('no-reply@sender-domain.com', 'Domain.com')
				 ->addMailHeader('Reply-To', 'no-reply@sender-domain.com', 'Domain.com')
				 ->addMailHeader('Cc', 'bill@receiver-domain', 'Bill Gates')
				 ->addMailHeader('Bcc', 'steve@receiver-domain', 'Steve Jobs')
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