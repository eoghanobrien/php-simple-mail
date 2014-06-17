<?php require '../class.simple_mail.php';

echo '<h1>Simple Mail</h1>';

/* @var SimpleMail $mail */
$mail = new SimpleMail();
$mail->setTo('not_a_real_email@gmail.com', 'John Smith')
     ->setSubject('Just Test Message')
     ->setFrom('no-reply@sender-domain.com', 'Domain.com')
     ->addMailHeader('Reply-To', 'no-reply@sender-domain.com', 'Domain.com')
     ->addMailHeader('Cc', 'bill@receiver-domain', 'Bill Gates')
     ->addMailHeader('Bcc', 'steve@receiver-domain', 'Steve Jobs')
     ->addAttachment(__DIR__ . '/pbXBsZSwgY2hh.jpg', 'lolcat_finally_arrived.jpg')
     ->addAttachment(__DIR__ . '/lolcat_what.jpg')
     ->setMessage('<strong>This is a test message.</strong>')
     ->setWrap(100);
$send = $mail->send();
//$mailer->debug();

if ($send) {
    echo 'Email sent successfully';
} else {
    echo 'Could not send email';
}
