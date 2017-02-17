<?php require '../class.simple_mail.php';

echo '<h1>Simple Mail</h1>';

/* @var SimpleMail $mail */
$mail = new SimpleMail();
$mail->setTo('test1@example.com', 'Recipient 1')
    ->setSubject('Testing multiple attachments!')
    ->setFrom('sender@gmail.com', 'Mail Bot')
    ->setReplyTo('reply@test.com', 'Mail Bot')
    ->setCc(['Recipient 2' => 'test2@example.com', 'Recipient 3' => 'test3@example.com'])
    ->setBcc(['Recipient 4' => 'test4@example.com'])
    ->addAttachment(__DIR__ . '/pbXBsZSwgY2hh.jpg', 'lolcat_finally_arrived.jpg')
    ->addAttachment(__DIR__ . '/lolcat_what.jpg')
    ->setHtml()
    ->setMessage('<strong>This is a test message.</strong>')
    ->setWrap(100);

$send = $mail->send();
//$mailer->debug();

if ($send) {
    echo 'Email sent successfully';
} else {
    echo 'Could not send email';
}
