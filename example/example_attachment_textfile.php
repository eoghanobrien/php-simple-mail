<?php

require 'class.simple_mail.php';
echo '<h1>Simple Mail</h1>';

$mailer = new Simple_Mail();

$mailer->setTo('eoghan@eoghanobrien.com', 'Eoghan')
    ->setFrom('no-reply@domain.com', 'Domain.com')
    ->setSubject('This is a test message')
    ->addAttachment('test.txt')
    ->setMessage('HALLO');
$send = $mailer->send();

//$mailer->debug();

if ($send) {
    echo 'Email sent successfully';
}
else {
    echo 'Could not send email';
}

?>