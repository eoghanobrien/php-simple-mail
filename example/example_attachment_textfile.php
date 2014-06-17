<?php require '../class.simple_mail.php';

echo '<h1>Simple Mail</h1>';

/* @var SimpleMail $mail */
$mail = new SimpleMail();
$mail->setTo('not_a_real_email@gmail.com', "Raphaëlle Agogué")
     ->setFrom('not_a_real_email@gmail.com', 'Jack Sprat')
     ->setSubject('This is a test message')
     ->addAttachment('test.txt')
     ->setMessage('HALLO');
$send = $mail->send();
//echo $mail->debug();

if ($send) {
    echo 'Email sent successfully';
}
else {
    echo 'Could not send email';
}
