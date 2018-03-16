<?php

date_default_timezone_set('Europe/Dublin');

class testSimpleMail extends PHPUnit_Framework_TestCase
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $email;

    /** @var SimpleMail */
    protected $mailer;

    /** @var string */
    protected $directory;

    /**
     * Set up the SimpleMail class before each test.
     */
    public function setUp()
    {
        $this->name      = 'Tester';
        $this->email     = 'test@gmail.com';
        $this->mailer    = new SimpleMail();
        $this->directory = realpath('./');
    }

    /**
     * @test
     */
    public function it_can_be_constructed_via_named_static_method()
    {
        $this->assertInstanceOf('SimpleMail', SimpleMail::make());
    }

    /**
     * @test
     */
    public function it_will_set_expected_values_for_setTo()
    {
        $this->mailer->setTo($this->email, $this->name);

        $expected = sprintf('"%s" <%s>', $this->mailer->encodeUtf8($this->name), $this->email);

        $this->assertContains($expected, $this->mailer->getTo());
    }

    /**
     * @test
     */
    public function it_will_set_expected_header_for_setTo()
    {
        $this->mailer->setTo($this->email, $this->name);
        $header = $this->mailer->formatHeader($this->email, $this->name);

        $this->assertContains($header, $this->mailer->getTo());
    }

    /**
     * @test
     */
    public function it_will_set_expected_header_for_setSubject()
    {
        $this->mailer->setSubject('Testing Simple Mail');

        $this->assertSame($this->mailer->encodeUtf8('Testing Simple Mail'), $this->mailer->getSubject());
    }

    /**
     * @test
     */
    public function it_can_correctly_utf8_encode_words()
    {
        $expected = sprintf('=?UTF-8?B?%s?=', base64_encode('Test'));
        $encoded = $this->mailer->encodeUtf8('Test');

        $this->assertSame($expected, $encoded);
    }

    /**
     * @test
     */
    public function it_returns_the_correct_words_when_using_encodeUtf8()
    {
        $space = sprintf('=?UTF-8?B?%s?=', base64_encode(' '));
        $expected = array(
            sprintf('=?UTF-8?B?%s?=', base64_encode('Testing')),
            sprintf('=?UTF-8?B?%s?=', base64_encode('Multiple')),
            sprintf('=?UTF-8?B?%s?=', base64_encode('Words'))
        );
        $encoded = $this->mailer->encodeUtf8('Testing Multiple Words');

        $this->assertSame(implode($space, $expected), $encoded);
    }

    /**
     * @test
     */
    public function it_will_set_expected_message()
    {
        $this->mailer->setMessage('Testing Simple Mail');

        $this->assertSame($this->mailer->getMessage(), 'Testing Simple Mail');
    }

    /**
     * @test
     */
    public function it_sets_the_expected_header_for_carbon_copy()
    {
        $this->mailer->setCc(array($this->name => $this->email));
        $header = sprintf('%s: %s', 'Cc', $this->mailer->formatHeader($this->email, $this->name));

        $this->assertContains($header, $this->mailer->getHeaders());
    }

    /**
     * @test
     */
    public function it_sets_the_expected_header_for_blind_carbon_copy()
    {
        $this->mailer->setBcc(array('Tester' => 'test@gmail.com'));
        $header = sprintf('%s: %s', 'Bcc', $this->mailer->formatHeader($this->email, $this->name));

        $this->assertContains($header, $this->mailer->getHeaders());
    }

    /**
     * @test
     */
    public function it_sets_the_expected_header_for_html_content_type()
    {
        $this->mailer->setHtml();

        $this->assertContains('Content-Type: text/html; charset="utf-8"', $this->mailer->getHeaders());
    }

    /**
     * @test
     */
    public function it_sets_the_expected_header_for_reply_to()
    {
        $this->mailer->setReplyTo($this->email, $this->name);
        $header = sprintf('%s: %s', 'Reply-To', $this->mailer->formatHeader($this->email, $this->name));

        $this->assertContains($header, $this->mailer->getHeaders());
    }

    /**
     * @test
     */
    public function testSetWrapAssignsCorrectValue()
    {
        $this->mailer->setWrap(50);

        $this->assertSame(50, $this->mailer->getWrap());
    }

    /**
     * @test
     */
    public function it_defaults_message_wrapping_to_expected_number_when_wrap_is_set_to_zero()
    {
        $this->mailer->setWrap(0);
        $this->assertSame(78, $this->mailer->getWrap());
    }

    /**
     * @test
     */
    public function it_defaults_message_wrapping_to_expected_number()
    {
        $this->assertSame(78, $this->mailer->getWrap());
    }

    /**
     * @test
     */
    public function it_returns_the_correct_parameters()
    {
        $this->mailer->setParameters("-fuse@gmail.com");
        $params = $this->mailer->getParameters();
        $this->assertSame("-fuse@gmail.com", $params);
    }

    /**
     * @test
     */
    public function it_returns_the_correct_header_when_addGenericHeader_is_called_with_valid_arguments()
    {
        $this->mailer->addGenericHeader('Version', 'PHP5');
        $this->assertContains("Version: PHP5", $this->mailer->getHeaders());
    }

    /**
     * @test
     */
    public function it_returns_the_correct_header_when_addMailHeader_is_called_without_name()
    {
        $this->mailer->addMailHeader('Cc', $this->email);
        $this->assertContains("Cc: " . $this->email, $this->mailer->getHeaders());
    }

    /**
     * @test
     */
    public function it_returns_the_correct_header_when_addMailHeaders_is_called_without_name_keys()
    {
        $this->mailer->addMailHeaders('Cc', array(
            'jim@gmail.com', 'joe@gmail.com'
        ));

        $headers = $this->mailer->getHeaders();

        $this->assertContains("Cc: jim@gmail.com,joe@gmail.com", $headers);
    }

    /**
     * @test
     */
    public function it_allows_multiple_mail_headers_to_be_added_with_email_and_name_pairs()
    {
        $name1 = 'Jim Smith';
        $name2 = 'Joe Smith';

        $email1 = 'jim@gmail.com';
        $email2 = 'joe@gmail.com';

        $addresses = array(
            $name1 => $email1,
            $name2 => $email2,
        );

        $this->mailer->addMailHeaders('Bcc', $addresses);

        $expected = sprintf("Bcc: %s,%s",
            $this->mailer->formatHeader($email1, $name1),
            $this->mailer->formatHeader($email2, $name2)
        );

        $this->assertContains($expected, $this->mailer->getHeaders());
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function it_throws_exception_when_adding_mail_headers_without_email_and_name_pairs()
    {
        $this->mailer->addMailHeaders('Bcc', array());
    }

    /**
     * @test
     */
    public function it_formats_header_without_name_and_returns_only_the_email()
    {
        $email  = 'test@domain.tld';
        $header = $this->mailer->formatHeader($email);

        $this->assertSame($email, $header);
    }

    /**
     * @test
     */
    public function it_can_return_debug_information()
    {
        $this->assertSame($this->mailer->debug(), '<pre>'.print_r($this->mailer, 1).'</pre>');
    }

    /**
     * @test
     */
    public function it_can_convert_the_object_to_a_string()
    {
        $stringObject = print_r($this->mailer, 1);

        $this->assertSame((string) $this->mailer, $stringObject);
    }

    /**
     * @test
     */
    public function it_will_return_true_when_hasAttachments_called_with_attachment_already_passed()
    {
        $this->mailer->addAttachment($this->directory.'/example/pbXBsZSwgY2hh.jpg', 'lolcat_finally_arrived.jpg');

        $this->assertTrue($this->mailer->hasAttachments());
    }

    /**
     * @test
     */
    public function it_will_return_false_when_hasAttachments_called_with_no_attachment_passed()
    {
        $this->assertFalse($this->mailer->hasAttachments());
    }

    /**
     * @test
     */
    public function it_assembles_the_correct_headers_when_adding_attachments()
    {
        $this->mailer->addAttachment($this->directory.'/example/pbXBsZSwgY2hh.jpg', 'lolcat_finally_arrived.jpg');

        $this->assertTrue(is_string($this->mailer->assembleAttachmentHeaders()));
    }

    /**
     * @test
     * @expectedException RuntimeException
     */
    public function it_throws_a_runtime_exception_when_no_to_address_is_set()
    {
        $this->mailer->send();
    }

    /**
     * @test
     */
    public function it_returns_a_boolean_from_send()
    {
        $this->mailer->setTo($this->email, $this->name)
                     ->setFrom($this->email, $this->name)
                     ->setSubject('Hello From PHPUnit')
                     ->setMessage('Hello message.');

        $bool = $this->mailer->send();
        $this->assertTrue(is_bool($bool));
    }

    /**
     * @test
     */
    public function it_returns_a_boolean_when_sending_with_attachment()
    {
        $this->mailer->setTo($this->email, $this->name)
                     ->setFrom($this->email, $this->name)
                     ->setSubject('Hello From PHPUnit')
                     ->setMessage('Hello message.')
                     ->addAttachment($this->directory.'/example/pbXBsZSwgY2hh.jpg', 'lolcat_finally_arrived.jpg');

        $bool = $this->mailer->send();
        $this->assertTrue(is_bool($bool));
    }

    /**
     * @test
     */
    public function it_filters_out_carriage_returns_from_names()
    {
        $string = "\rHello World";
        $name = $this->mailer->filterName("\rHello World");

        $this->assertNotSame($string, $name);
    }

    /**
     * @test
     */
    public function it_filters_out_new_lines_from_names()
    {
        $string = "\nHello World";
        $name = $this->mailer->filterName($string);

        $this->assertNotSame($string, $name);
    }

    /**
     * @test
     */
    public function it_filters_out_tab_characters_from_names()
    {
        $string = "\tHello World\t";
        $name = $this->mailer->filterName($string);

        $this->assertNotSame($string, $name);
    }

    /**
     * @test
     */
    public function it_replaces_double_quotes_with_single_quote_entities_for_names()
    {
        $expected = "'Hello World'";
        $name     = $this->mailer->filterName('"Hello World"');

        $this->assertEquals($expected, $name);
    }

    public function it_filters_out_angle_brackets_from_names()
    {
        $expected = 'Hello World';
        $name     = $this->mailer->filterName('<> Hello World');

        $this->assertEquals($expected, $name);
    }

    /**
     * @test
     */
    public function it_filters_out_carriage_returns_from_other_data()
    {
        $expected = 'Hello World';
        $actual   = $this->mailer->filterOther("\rHello World");
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function it_filters_out_new_lines_from_other_data()
    {
        $expected = 'Hello World';
        $actual   = $this->mailer->filterOther("\nHello World");
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function it_filters_out_tab_characters_from_other_data()
    {
        $expected = 'Hello World';
        $actual   = $this->mailer->filterOther("\tHello World");
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function it_does_not_filter_out_quotes_from_other_data()
    {
        $expected = 'Hello "World"';
        $actual   = $this->mailer->filterOther($expected);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function it_does_not_filter_out_tags_from_other_data()
    {
        $expected = 'Hello <World>';
        $actual   = $this->mailer->filterOther($expected);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function it_does_not_filter_high_ascii_from_other_data()
    {
        $expected = "Hej världen!";
        $actual   = $this->mailer->filterOther($expected);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function it_filters_carriage_returns_from_emails()
    {
        $string = "test@test.com\r";
        $name = $this->mailer->filterEmail($string);

        $this->assertNotSame($string, $name);
    }

    /**
     * @test
     */
    public function it_filters_new_lines_from_emails()
    {
        $string = "test@test.com\n";
        $name = $this->mailer->filterEmail($string);

        $this->assertNotSame($string, $name);
    }

    /**
     * @test
     */
    public function it_filters_tabbed_characters_from_emails()
    {
        $string = "\ttest@test.com\t";
        $name = $this->mailer->filterEmail($string);

        $this->assertNotSame($string, $name);
    }

    /**
     * @test
     */
    public function it_filters_double_quotes_from_emails()
    {
        $expected = "test@test.com";
        $name     = $this->mailer->filterEmail('"test@test.com"');

        $this->assertEquals($expected, $name);
    }

    /**
     * @test
     */
    public function it_filters_commas_from_emails()
    {
        $expected = "test@test.com";
        $name     = $this->mailer->filterEmail('t,es,t@test.com');

        $this->assertEquals($expected, $name);
    }

    /**
     * @test
     */
    public function it_filters_angle_brackets_from_emails()
    {
        $expected = 'test@test.com';
        $name     = $this->mailer->filterEmail('<test@test.com>');

        $this->assertEquals($expected, $name);
    }

    /**
     * @test
     */
    public function it_quoted_printable_encodes_attachment_message_bodies()
    {
        $message = "J'interdis aux marchands de vanter trop leur marchandises. Car ils se font vite pédagogues et t'enseignent comme but ce qui n'est par essence qu'un moyen, et te trompant ainsi sur la route à suivre les voilà bientôt qui te dégradent, car si leur musique est vulgaire ils te fabriquent pour te la vendre une âme vulgaire.";

        $this->mailer->setMessage($message)
                     ->addAttachment($this->directory . '/example/pbXBsZSwgY2hh.jpg', 'lolcat_finally_arrived.jpg');

        $body = $this->mailer->assembleAttachmentBody();
        $this->assertRegExp('/^Content-Transfer-Encoding: quoted-printable$/m', $body);
    }

    public function tearDown()
    {
        unset($this->mailer);
    }
}
