#README

##Description

####Version 1.0

Simple Mail Class provides a simple, chainable PHP class for sending basic emails

    $mailer = new Simple_Mail(TRUE); // Set to TRUE to enable exception throwing

    $send	= $mailer->setTo('eoghan@eoghanobrien.com', 'Eoghan OBrien')
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