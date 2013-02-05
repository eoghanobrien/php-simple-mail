<?php

$dir = realpath('../../'.dirname(__FILE__));
require_once $dir . 'class.simple_mail.php';

class testSimpleMail extends PHPUnit_Framework_TestCase
{
	protected $mailer;

	public function setUp()
	{
		$this->mailer = new Simple_Mail();
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetThrowsExceptionsThrowsInvalidArgumentExceptionWhenParameterIsNotBoolean()
	{
		$this->mailer->setThrowExceptions('string');
	}

	public function testSetThrowsExceptionsReturnsAnticipatedValue()
	{
		$this->mailer->setThrowExceptions(true);
		$shouldThrowExceptions = $this->mailer->shouldThrowExceptions();

		$this->assertSame($shouldThrowExceptions, true);
	}

	public function testSetToWithExpectedValues()
	{
		$this->mailer->setTo('test@gmail.com', 'Tester');

		$this->assertEquals('Tester <test@gmail.com>', $this->mailer->getTo());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetToThrowsInvalidArgumentExceptionWithInvalidEmail()
	{
		$this->mailer->setThrowExceptions(true);
		$this->mailer->setTo(123, 'Tester');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetToThrowsInvalidArgumentExceptionWithInvalidName()
	{
		$this->mailer->setThrowExceptions(true);
		$this->mailer->setTo('test@gmail.com', 123);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetToThrowsInvalidArgumentExceptionWithInvalidAddHeadersValue()
	{
		$this->mailer->setThrowExceptions(true);
		$this->mailer->setTo('test@gmail.com', 'Tester', 'not bool');
	}

	public function testSetToAddHeaderParameterAddsHeader()
	{
		$this->mailer->setThrowExceptions(true);
		$this->mailer->setTo('test@gmail.com', 'Tester', true);
		$header = sprintf('%s: %s', 'To', $this->mailer->formatHeader('test@gmail.com', 'Tester'));

		$this->assertContains($header, $this->mailer->getHeaders());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetSubjectThrowsInvalidArgumentExceptionWithInvalidSubject()
	{
		$this->mailer->setThrowExceptions(true);
		$this->mailer->setSubject(12345);
	}

	public function testSetSubjectReturnsCorrectValue()
	{
		$this->mailer->setThrowExceptions(true);
		$this->mailer->setSubject('Testing Simple Mail');

		$this->assertSame($this->mailer->getSubject(), 'Testing Simple Mail');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetMessageThrowsInvalidArgumentExceptionWithInvalidMessage()
	{
		$this->mailer->setThrowExceptions(true);
		$this->mailer->setMessage(123);
	}

	public function testSetMessageReturnsCorrectValue()
	{
		$this->mailer->setThrowExceptions(true);
		$this->mailer->setMessage('Testing Simple Mail');

		$this->assertSame($this->mailer->getMessage(), 'Testing Simple Mail');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetFromThrowsInvalidArgumentWithInvalidEmail()
	{
		$this->mailer->setThrowExceptions(true);
		$this->mailer->setFrom(123, 'Tester');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetMessageThrowsInvalidArgumentWithInvalidName()
	{
		$this->mailer->setThrowExceptions(true);
		$this->mailer->setFrom('test@gmail.com', 123);
	}

	public function testSetMessageIsAddedToHeaders()
	{
		$this->mailer->setThrowExceptions(true);
		$this->mailer->setFrom('test@gmail.com', 'Tester', true);
		$header = sprintf('%s: %s', 'From', $this->mailer->formatHeader('test@gmail.com', 'Tester'));

		$this->assertContains($header, $this->mailer->getHeaders());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetWrapThrowsInvalidArgumentExceptionWithNonInt()
	{
		$this->mailer->setThrowExceptions(true);
		$this->mailer->setWrap('non int');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetWrapThrowsInvalidArgumentExceptionWithZero()
	{
		$this->mailer->setThrowExceptions(true);
		$this->mailer->setWrap(0);
	}

	public function testSetWrapAssignsCorrectValue()
	{
		$this->mailer->setThrowExceptions(true);
		$this->mailer->setWrap(50);

		$this->assertSame(50, $this->mailer->getWrap());
	}

	public function testgetWrapDefaultsTo78()
	{
		$this->mailer->setThrowExceptions(true);
		$this->assertSame(78, $this->mailer->getWrap());
	}

	public function tearDown()
	{
		unset($this->mailer);
	}
}