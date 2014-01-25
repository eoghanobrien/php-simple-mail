<?php

require '../class.simple_mail.php';
echo '<h1>Simple Mail</h1>';

$mailer = new SimpleMail();

$mailer->setTo('test@receiver-domain.com', 'John Smith')
    ->setFrom('test@sender-domain.com', 'Jack Sprat')
    ->setSubject('This is a test message')
    ->addAttachment('test.txt')
    ->setMessage('HALLO');
    
$send = $mailer->send();

if ($send) {
    echo 'Email sent successfully';
}
else {
    echo 'Could not send email';
}

?>