#README

##Description

####Version 1.0

Simple Mail Class provides a simple, chainable PHP class for sending basic emails

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
				 ->setWrap(100)
				 ->send();
    echo ($send) ? 'Email sent successfully' : 'Could not send email';


####Sending an Attachment

If you are sending an attachment there is no need to add any addGenericHeader()'s. To properly send the attachments the necessary headers will be set for you. You can also chain as many attachments as you want (see example).

	$mailer = new Simple_Mail(TRUE); // Set to TRUE to enable exception throwing
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
	echo ($send) ? 'Email sent successfully' : 'Could not send email';
	
## License
php-simple-mail is free and unencumbered public domain software. For more information, see http://unlicense.org/ or the accompanying UNLICENSE file.