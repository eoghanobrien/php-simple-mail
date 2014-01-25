#README

[![Build Status](https://travis-ci.org/eoghanobrien/php-simple-mail.png?branch=master)](https://travis-ci.org/eoghanobrien/php-simple-mail) [![Latest Stable Version](https://poser.pugx.org/eoghanobrien/php-simple-mail/v/stable.png)](https://packagist.org/packages/eoghanobrien/php-simple-mail) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/eoghanobrien/php-simple-mail/badges/quality-score.png?s=a6850c4ef51c0d56ed50513d3749d6c1617dfaff)](https://scrutinizer-ci.com/g/eoghanobrien/php-simple-mail/) [![Code Coverage](https://scrutinizer-ci.com/g/eoghanobrien/php-simple-mail/badges/coverage.png?s=d167e7faf23471deeef69d26ff23812a64e74326)](https://scrutinizer-ci.com/g/eoghanobrien/php-simple-mail/) [![Total Downloads](https://poser.pugx.org/eoghanobrien/php-simple-mail/downloads.png)](https://packagist.org/packages/eoghanobrien/php-simple-mail) [![License](https://poser.pugx.org/eoghanobrien/php-simple-mail/license.png)](https://packagist.org/packages/eoghanobrien/php-simple-mail)

#### Installation via Composer

    {
       "require": {
        "eoghanobrien/php-simple-mail": "dev-master"
      }
    }

####Version 1.0

Simple Mail Class provides a simple, chainable PHP class for sending basic emails

    $mailer = new SimpleMail();
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

	$mailer = new SimpleMail();
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