<?php require '../class.simple_mail.php';

echo '<h1>Simple Mail</h1>';

/* @var SimpleMail $mail */
$mail = SimpleMail::make()
    ->setTo('test1@example.com', 'Recipient 1')
    ->setSubject("Hi Amy O'Neill, Welcome!")
    ->setFrom('sender@gmail.com', 'Mail Bot')
    ->setReplyTo('reply@test.com', 'Mail Bot')
    ->setCc(['Recipient 2' => 'test2@example.com', 'Recipient 3' => 'test3@example.com'])
    ->setBcc(['Recipient 4' => 'test4@example.com'])
    ->addGenericHeader('X-Mailer', 'PHP/' . phpversion())
    ->setHtml()
    ->setMessage('<strong>This is a test message.</strong>')
    ->setWrap(78);
$send = $mail->send();
//echo $mail->debug();

if ($send) {
    echo 'Email was sent successfully!';
} else {
    echo 'An error occurred. We could not send email';
}
