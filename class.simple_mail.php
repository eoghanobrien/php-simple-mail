<?php

/**
* Simple Mail class.
*
* @author Eoghan O'Brien http://github.com/eoghanobrien
* @package Simple Mail
* @version 1.1
* @copyright 2009-2010
* @license Free http://unlicense.org/
*/

class Simple_Mail
{
    const CRLF = "\r\n";

    /**
     * @var int $wrap
     */
    protected $wrap = 78;

    /**
     * @var string $to
     */
    protected $to = array();

    /**
     * @var string $subject
     */
    protected $subject;

    /**
     * @var string $message
     */
    protected $message;

    /**
     * @var array $headers
     */
    protected $headers = array();

    /**
     * @var string $additionalParameters
     */
    protected $additionalParameters;

    /**
     * @var array $attachments
     */
    protected $attachments = array();

    /**
     * @var array $attachmentsPath
     */
    protected $attachmentsPath = array();

    /**
     * @var array $attachment_filename
     */
    protected $attachmentsFilename = array();


    /**
     * __construct.
     *
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * reset.
     *
     * Resets all variables to initial state.
     *
     * @return Simple_Mail
     */
    public function reset()
    {
        $this->to      = array();
        $this->headers = array();
        $this->subject = null;
        $this->message = null;
        $this->wrap    = 78;
        $this->additionalParameters = null;
        $this->attachments         = array();
        $this->attachmentsPath     = array();
        $this->attachmentsFilename = array();

        return $this;
    }

    /**
     * setTo.
     *
     * @param  string $email
     * @param  string $name
     *
     * @throws InvalidArgumentException on non string value for $email
     * @throws InvalidArgumentException on non string value for $name
     *
     * @return Simple_Mail
     */
    public function setTo($email, $name)
    {
        if (! is_string($email)) {
            throw new InvalidArgumentException('$email must be a string');
        }

        if (! is_string($name)) {
            throw new InvalidArgumentException('$name must be a string.');
        }

        $this->to[] = $this->formatHeader($email, $name);

        return $this;
    }

    /**
     * Return an array of formatted To addresses.
     *
     * @return array
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * setSubject function.
     *
     * @param  string$subject
     * @throws InvalidArgumentException on non string value for $subject
     * @return Simple_Mail
     */
    public function setSubject($subject)
    {
        if (! is_string($subject)) {
            throw new InvalidArgumentException();
        }

        $this->subject = $this->filterOther($subject);

        return $this;
    }

    /**
     * getSubject function.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * setMessage function.
     *
     * @access public
     * @param  string $message
     * @throws InvalidArgumentException on non string value for $message
     * @return Simple_Mail
     */
    public function setMessage($message)
    {
        if (! is_string($message)) {
            throw new InvalidArgumentException();
        }

        $this->message = str_replace("\n.", "\n..", $message);

        return $this;
    }

    /**
     * getMessage function.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * addAttachment function.
     *
     * @todo   Test this.
     * @access public
     * @param  string $path
     * @param  string $filename
     * @return Simple_Mail
     */
    public function addAttachment($path, $filename = null)
    {
        $this->addAttachmentPath($path);
        $this->addAttachmentFilename(empty($filename) ? basename($path) : $filename);
        $this->attachments[] = $this->getAttachmentData($path);

        return $this;
    }

    /**
     * addAttachmentPath function.
     *
     * @todo   Test this.
     * @param  string $path
     * @return Simple_Mail
     */
    public function addAttachmentPath($path)
    {
        $this->attachmentsPath[] = $path;

        return $this;
    }

    /**
     * addAttachmentFilename function.
     *
     * @todo   Test this.
     * @param  string $filename
     * @return Simple_Mail
     */
    public function addAttachmentFilename($filename)
    {
        $this->attachmentsFilename[] = $filename;

        return $this;
    }

    /**
     * getAttachmentData function.
     *
     * @todo   Test this.
     * @param  string $path
     * @return string
     */
    public function getAttachmentData($path)
    {
        $filesize   = filesize($path);
        $handle     = fopen($path, "r");
        $attachment = fread($handle, $filesize);
        fclose($handle);

        return chunk_split(base64_encode($attachment));
    }

    /**
     * setFrom.
     *
     * @param  string $email
     * @param  string $name
     *
     * @throws InvalidArgumentException on non string value for $email
     * @throws InvalidArgumentException on non string value for $name
     *
     * @return Simple_Mail
     */
    public function setFrom($email, $name)
    {
        if ( ! is_string($email)) {
            throw new InvalidArgumentException();
        }

        if ( ! is_string($name)) {
            throw new InvalidArgumentException();
        }

        $this->addMailHeader('From', $email, $name);

        return $this;
    }

    /**
     * addMailHeader function.
     *
     * @param  string $header
     * @param  string $email
     * @param  string $name
     *
     * @throws InvalidArgumentException on non string value for $header
     * @throws InvalidArgumentException on non string value for $email
     * @throws InvalidArgumentException on non string value for $name
     *
     * @return Simple_Mail
     */
    public function addMailHeader($header, $email = null, $name = null)
    {
        if ( ! is_string($header)) {
            throw new InvalidArgumentException('$header must be a string.');
        }

        if ( ! is_string($email)) {
            throw new InvalidArgumentException('$email must be a string.');
        }

        if ( ! is_string($name)) {
            throw new InvalidArgumentException('$name must be a string.');
        }

        $this->headers[] = sprintf('%s: %s', $header, $this->formatHeader($email, $name));

        return $this;
    }

    /**
     * addGenericHeader function.
     *
     * @param  string $header
     * @param  mixed $value
     *
     * @throws InvalidArgumentException on non string value for $header
     * @throws InvalidArgumentException on non string value for $value
     *
     * @return Simple_Mail
     */
    public function addGenericHeader($header, $value)
    {
        if ( ! is_string($header)) {
            throw new InvalidArgumentException('$header must be a string.');
        }

        if ( ! is_string($value) || ! is_string($value)) {
            throw new InvalidArgumentException('$value must be a string.');
        }

        $this->headers[] = "$header: $value";

        return $this;
    }

    /**
     * Return the headers registered so far as an array.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * setAdditionalParameters function.
     *
     * Such as "-fyouremail@yourserver.com
     *
     * @param  string $additionalParameters
     *
     * @throws InvalidArgumentException on non string value for $additionalParameters
     *
     * @return Simple_Mail
     */
    public function setAdditionalParameters($additionalParameters)
    {
        if (! is_string($additionalParameters)) {
            throw new InvalidArgumentException('$additionalParameters must be a string.');
        }

        $this->additionalParameters = $additionalParameters;

        return $this;
    }

    /**
     * getAdditionalParameters function
     *
     * @return string
     */
    public function getAdditionalParameters()
    {
        return $this->additionalParameters;
    }

    /**
     * setWrap function.
     *
     * @param  int  $wrap
     *
     * @throws InvalidArgumentException on non int value or int less than 1 for $wrap
     *
     * @return Simple_Mail
     */
    public function setWrap($wrap = 78)
    {
        if (! is_int($wrap) || $wrap < 1) {
            throw new InvalidArgumentException('Wrap must be an integer larger than 0');
        }

        $this->wrap = $wrap;

        return $this;
    }

    /**
     * getWrap function.
     *
     * @return int
     */
    public function getWrap()
    {
        return $this->wrap;
    }

    /**
     * Checks if the email has an registered attachments.
     *
     * @return bool
     */
    public function hasAttachments()
    {
        return !empty($this->attachments);
    }

    /**
     * assembleAttachment function.
     *
     * @return string
     */
    public function assembleAttachmentHeaders()
    {
        $uid = md5(uniqid(time()));
        $headers  = sprintf('MIME-Version: 1.0', self::CRLF);
        $headers .= sprintf('Content-Type: multipart/mixed; boundary="%s"%s%s', self::CRLF, self::CRLF, $uid);
        $headers .= sprintf('This is a multi-part message in MIME format.%s', self::CRLF);
        $headers .= sprintf('--%s%s', $uid, self::CRLF);
        $headers .= sprintf('Content-type:text/html; charset="utf-8"%s', self::CRLF);
        $headers .= sprintf('Content-Transfer-Encoding: 7bit%s', self::CRLF, self::CRLF);
        $headers .= sprintf('%s%s%s', $this->message, self::CRLF, self::CRLF);
        $headers .= sprintf('--%s%s', $uid, self::CRLF);

        foreach ($this->attachmentsFilename as $key => $value) {
            $headers .= sprintf('Content-Type: application/octet-stream; name="%s"%s', $value, self::CRLF);
            $headers .= sprintf('Content-Transfer-Encoding: base64%s', self::CRLF);
            $headers .= sprintf('Content-Disposition: attachment; filename="%s"%s%s', $value, self::CRLF, self::CRLF);
            $headers .= sprintf('%s%s%s', $this->attachments[$key], self::CRLF, self::CRLF);
            $headers .= sprintf('--%s%s', $uid, self::CRLF);
        }

        return $headers;
    }

    /**
     * send function.
     *
     * @throws RuntimeException on no To: address to send to
     * @return boolean
     */
    public function send()
    {
        $headers = (!empty($this->headers)) ? join("\r\n", $this->headers) : array();
        $to      = (is_array($this->to) && !empty($this->to)) ? join(", ", $this->to) : false;

        if ($to === false) {
            throw new RuntimeException('Unable to send, no To address has been set.');
        }

        if ($this->hasAttachments()) {
            $headers .= $this->assembleAttachmentHeaders();
            return mail($to, $this->subject, "", $headers, $this->additionalParameters);
        }

        return mail($to, $this->subject, wordwrap($this->message, $this->wrap), $headers, $this->additionalParameters);
    }

    /**
     * debug function.
     *
     * @return string
     */
    public function debug()
    {
        return '<pre>'.print_r($this, 1).'</pre>';
    }

    /**
     * magic __toString function.
     *
     * @return string
     */
    public function __toString()
    {
        return print_r($this, 1);
    }

    /**
     * formatHeader.
     *
     * Formats a display address for emails according to RFC2822 e.g.
     * Name <address@domain.tld>
     *
     * @todo   Test this.
     * @param  string $email
     * @param  string $name
     * @return string
     */
    public function formatHeader($email, $name = null)
    {
        $email = $this->filterEmail($email);

        if (is_null($name)) {
            return $email;
        }

        $name = $this->filterName($name);
        return sprintf('%s <%s>', $name, $email);
    }

    /**
     * filterEmail.
     *
     * Removes any carriage return, line feed, tab, double quote, comma
     * and angle bracket characters before sanitizing the email address.
     *
     * @todo   Test this.
     * @param  string $email
     * @return string
     */
    protected function filterEmail($email)
    {
        $rule = array("\r" => '',
                      "\n" => '',
                      "\t" => '',
                      '"'  => '',
                      ','  => '',
                      '<'  => '',
                      '>'  => '',
        );

        $email = strtr($email, $rule);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        return $email;
    }

    /**
     * Filter of name data.
     *
     * Removes any carriage return, line feed or tab characters. Replaces
     * double quotes with single quotes and angle brackets with square
     * brackets, before sanitizing the string and stripping out html tags.
     *
     * @todo   Test this.
     * @param  string $name
     * @return string
     */
    protected function filterName($name)
    {
        $rule = array("\r" => '',
                      "\n" => '',
                      "\t" => '',
                      '"'  => "'",
                      '<'  => '[',
                      '>'  => ']',
        );

        return trim(strtr(filter_var($name, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH), $rule));
    }

    /**
     * Filter of other data.
     *
     * Removes any carriage return, line feed or tab characters.
     *
     * @todo   Test this.
     * @param  string $data
     * @return string
     */
    protected function filterOther($data)
    {
        $rule = array("\r" => '',
                      "\n" => '',
                      "\t" => '',
        );

        return strtr(filter_var($data, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH), $rule);
    }
}

