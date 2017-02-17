# README

[![Build Status](https://travis-ci.org/eoghanobrien/php-simple-mail.png?branch=master)](https://travis-ci.org/eoghanobrien/php-simple-mail) [![Latest Stable Version](https://poser.pugx.org/eoghanobrien/php-simple-mail/v/stable.png)](https://packagist.org/packages/eoghanobrien/php-simple-mail) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/eoghanobrien/php-simple-mail/badges/quality-score.png?s=a6850c4ef51c0d56ed50513d3749d6c1617dfaff)](https://scrutinizer-ci.com/g/eoghanobrien/php-simple-mail/) [![Code Coverage](https://scrutinizer-ci.com/g/eoghanobrien/php-simple-mail/badges/coverage.png?s=d167e7faf23471deeef69d26ff23812a64e74326)](https://scrutinizer-ci.com/g/eoghanobrien/php-simple-mail/) [![Total Downloads](https://poser.pugx.org/eoghanobrien/php-simple-mail/downloads.png)](https://packagist.org/packages/eoghanobrien/php-simple-mail) [![License](https://poser.pugx.org/eoghanobrien/php-simple-mail/license.png)](https://packagist.org/packages/eoghanobrien/php-simple-mail)

## Introduction

Simple Mail Class provides a simple, chainable wrapper for creating and sending emails using the PHP `mail()` function. There are better options out there for sending SMTP email, which are more secure and more reliable than the `mail()` function. However, sometimes you just need to send a simple email. That's what we cover.

## Installation via Composer

```
$ composer require eoghanobrien/php-simple-mail
```

## Usage

### Instantiating the class.

You have two options, you can 'new up' the class in the traditional way:

```php
$mailer = new SimpleMail();
```
or instantiate it using the named static constructor `make()`
```php
$mailer = SimpleMail::make();
```
The static constructor can be useful when you want to continue chaining methods after instantiating.
```php
SimpleMail::make()
->setTo($email, $name)
->setFrom($fromEmail, $fromName)
->setSubject($subject)
->setMessage($message)
->send();
```
 


### `To` header
 
The `To` header can be called multiple time, in order to pass more than one `To` address, simply call the `setTo` method as many times as needed. It takes two string parameters. The first parameter is for the email address, the second is for the name.
 
```php
SimpleMail::make()
 ->setTo($email1, $name1)
 ->setTo($email2, $name2);
```
 
 
### `From` header

You can carbon copy one or more addresses using the `setBcc` method. It takes two string parameters. The first parameter is for the email address, the second is for the name.

```php
SimpleMail::make()
  ->setFrom('john.smith@example.com', 'John Smith');
```
 
 
 
### `Cc` header

You can carbon copy one or more addresses using the `setCc` method. It takes an array of `$name => $email` pairs. Alternatively, you can pass a simple numerically keyed array an the value is assumed to be the email.

```php
SimpleMail::make()
  ->setCc(['John Smith', 'john.smith@example.com');
```
 
 
### `Bcc` header

You can blind carbon copy one or more addresses using the `setBcc` method. It takes an array of `$name => $email` pairs. Alternatively, you can pass a simple numerically keyed array an the value is assumed to be the email.

```php
SimpleMail::make()
  ->setBcc(['John Smith', 'john.smith@example.com');
```
 
### `Subject` header

You can set the subject using `setSubject` method. It takes a string as the only parameter.

```php
SimpleMail::make()
    ->setSubject("Important information about your account");
```
 
### `Message` header

You can set the message using `setMessage` method. It takes a string as the only parameter.

```php
SimpleMail::make()
    ->setMessage("My important message!");
```

### `HTML` emails

If you want to include HTML in your email. Simply call the `setHtml()` method. It takes no parameters.

```php
SimpleMail::make()
    ->setMessage("<strong>My important message!</strong>")
    ->setHtml();
```

### `send` emails

Once you've set all your headers. Use the `send()` method to finally send it on it's way.

```php
SimpleMail::make()
    ->setMessage("<strong>My important message!</strong>")
    ->send();
```
 
### Full example of sending an email
 
```php
$send = SimpleMail::make()
    ->setTo($email, $name)
    ->setFrom($fromEmail, $fromName)
    ->setSubject($subject)
    ->setMessage($message)
    ->setReplyTo($replyEmail, $replyName)
    ->setCc(['Bill Gates' => 'bill@example.com'])
    ->setBcc(['Steve Jobs' => 'steve@example.com'])
    ->setHtml()
    ->setWrap(100)
    ->send();
    
echo ($send) ? 'Email sent successfully' : 'Could not send email';
```

### Example of sending an email with attachments

If you are sending an attachment there is no need to add any addGenericHeader()'s. To properly send the attachments the necessary headers will be set for you. You can also chain as many attachments as you want (see example).

```php
$send = SimpleMail::make()
    ->setTo($email, $name)
    ->setFrom($fromEmail, $fromName)
    ->setSubject($subject)
    ->setMessage($message)
    ->setReplyTo($replyEmail, $replyName)
    ->setCc(['Bill Gates' => 'bill@example.com'])
    ->setBcc(['Steve Jobs' => 'steve@example.com'])
    ->setHtml()
    ->setWrap(100)
    ->addAttachment('example/pbXBsZSwgY2hh.jpg', 'lolcat_finally_arrived.jpg')
    ->addAttachment('example/lolcat_what.jpg')
    ->send();
    
echo ($send) ? 'Email sent successfully' : 'Could not send email';
```

## License
php-simple-mail is free and unencumbered public domain software. For more information, see [http://opensource.org/licenses/MIT](http://opensource.org/licenses/MIT) or the accompanying MIT file.

