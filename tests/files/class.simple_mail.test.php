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

		$header = sprintf('%s:%s', 'To', $this->mailer->formatHeader('test@gmail.com', 'Tester'));

		$this->assertContains($header, $this->mailer->getHeaders());
	}

	public function tearDown()
	{
		unset($this->mailer);
	}
}