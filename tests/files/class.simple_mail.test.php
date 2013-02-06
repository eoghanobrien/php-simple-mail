<?php

require_once(realpath('./class.simple_mail.php'));

class testSimpleMail extends PHPUnit_Framework_TestCase
{
	protected $mailer;

	protected $directory;

	public function setUp()
	{
		$this->mailer    = new Simple\Mail();
		$this->directory = realpath('./');
	}

	public function testSetToWithExpectedValues()
	{
		$this->mailer->setTo('test@gmail.com', 'Tester');
		$this->assertContains('Tester <test@gmail.com>', $this->mailer->getTo());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetToThrowsInvalidArgumentExceptionWithInvalidEmail()
	{
		$this->mailer->setTo(123, 'Tester');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetToThrowsInvalidArgumentExceptionWithInvalidName()
	{
		$this->mailer->setTo('test@gmail.com', 123);
	}

	public function testSetToAddsHeader()
	{
		$this->mailer->setTo('test@gmail.com', 'Tester');
		$header = $this->mailer->formatHeader('test@gmail.com', 'Tester');

		$this->assertContains($header, $this->mailer->getTo());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetSubjectThrowsInvalidArgumentExceptionWithInvalidSubject()
	{
		$this->mailer->setSubject(12345);
	}

	public function testSetSubjectReturnsCorrectValue()
	{
		$this->mailer->setSubject('Testing Simple Mail');

		$this->assertSame($this->mailer->getSubject(), 'Testing Simple Mail');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetMessageThrowsInvalidArgumentExceptionWithInvalidMessage()
	{
		$this->mailer->setMessage(123);
	}

	public function testSetMessageReturnsCorrectValue()
	{
		$this->mailer->setMessage('Testing Simple Mail');

		$this->assertSame($this->mailer->getMessage(), 'Testing Simple Mail');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetFromThrowsInvalidArgumentWithInvalidEmail()
	{
		$this->mailer->setFrom(123, 'Tester');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetMessageThrowsInvalidArgumentWithInvalidName()
	{
		$this->mailer->setFrom('test@gmail.com', 123);
	}

	public function testSetMessageIsAddedToHeaders()
	{
		$this->mailer->setFrom('test@gmail.com', 'Tester', true);
		$header = sprintf('%s: %s', 'From', $this->mailer->formatHeader('test@gmail.com', 'Tester'));

		$this->assertContains($header, $this->mailer->getHeaders());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetWrapThrowsInvalidArgumentExceptionWithNonInt()
	{
		$this->mailer->setWrap('non int');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetWrapThrowsInvalidArgumentExceptionWithZero()
	{
		$this->mailer->setWrap(0);
	}

	public function testSetWrapAssignsCorrectValue()
	{
		$this->mailer->setWrap(50);

		$this->assertSame(50, $this->mailer->getWrap());
	}

	public function testgetWrapDefaultsTo78()
	{
		$this->assertSame(78, $this->mailer->getWrap());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testAddMailHeaderThrowsInvalidArgumentExceptionWithInvalidHeader()
	{
		$this->mailer->addMailHeader(123);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testAddMailHeaderThrowsInvalidArgumentExceptionWithInvalidEmail()
	{
		$this->mailer->addMailHeader('Testing', 213);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testAddMailHeaderThrowsInvalidArgumentExceptionWithInvalidName()
	{
		$this->mailer->addMailHeader('Testing', 'testing@gmail.com', 123);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetAdditionalParametersThrowsInvalidArgumentExceptionWithInvalidParams()
	{
		$this->mailer->setAdditionalParameters(123);
	}

	public function testSetAdditionalParamatersReturnsCorrectString()
	{
		$this->mailer->setAdditionalParameters("-ftest@gmail.com");
		$params = $this->mailer->getAdditionalParameters();

		$this->assertSame("-ftest@gmail.com", $params);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testAddGenericHeaderThrowsInvalidArgumentExceptionWithInvalidHeader()
	{
		$this->mailer->addGenericHeader(false, 'Value');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testAddGenericHeaderThrowsInvalidArgumentExceptionWithInvalidValue()
	{
		$this->mailer->addGenericHeader('Version', false);
	}

	public function testAddGenericHeaderReturnsCorrectHeader()
	{
		$this->mailer->addGenericHeader('Version', 'PHP5');
		$this->assertContains("Version: PHP5", $this->mailer->getHeaders());
	}

	public function testFormatHeaderWithoutNameReturnsOnlyTheEmail()
	{
		$email  = 'test@domain.tld';
		$header = $this->mailer->formatHeader($email);

		$this->assertSame($email, $header);
	}

	public function testDebug()
	{
		$this->assertSame($this->mailer->debug(), '<pre>'.print_r($this->mailer, 1).'</pre>');
	}

	public function testToString()
	{
		$stringifyObject = print_r($this->mailer, 1);

		$this->assertSame((string) $this->mailer, $stringifyObject);
	}

	public function testHasAttachmentsReturnsTrueWithAttachmentPassed()
	{
		$this->mailer->addAttachment($this->directory.'/example/pbXBsZSwgY2hh.jpg', 'lolcat_finally_arrived.jpg');

		$this->assertTrue($this->mailer->hasAttachments());
	}

	public function testHasAttachmentsReturnsFalseWithNoAttachmentPassed()
	{
		$this->assertFalse($this->mailer->hasAttachments());
	}

	public function testAssembleAttachmentReturnsString()
	{
		$this->mailer->addAttachment($this->directory.'/example/pbXBsZSwgY2hh.jpg', 'lolcat_finally_arrived.jpg');

		$this->assertTrue(is_string($this->mailer->assembleAttachmentHeaders()));
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function testSendThrowsRuntimeExceptionWhenNoToAddressIsSet()
	{
		$this->mailer->send();
	}

	public function testSendReturnsBoolean()
	{
		$this->mailer->setTo('test@asdf123asdfa.com', "Recipient")
					 ->setFrom('tester@gmail.com', 'Tester')
					 ->setSubject('Hello From PHPUnit')
					 ->setMessage('Hello message.');

		$bool = $this->mailer->send();
		$this->assertTrue(is_bool($bool));
	}

	public function testSendAttachmentReturnsBoolean()
	{
		$this->mailer->setTo('test@asdf123asdfa.com', "Recipient")
					 ->setFrom('tester@gmail.com', 'Tester')
					 ->setSubject('Hello From PHPUnit')
					 ->setMessage('Hello message.')
					 ->addAttachment($this->directory.'/example/pbXBsZSwgY2hh.jpg', 'lolcat_finally_arrived.jpg');

		$bool = $this->mailer->send();
		$this->assertTrue(is_bool($bool));
	}

	public function tearDown()
	{
		unset($this->mailer);
	}
}