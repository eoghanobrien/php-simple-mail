<?php

/**
* Simple Mail class.
*
* @author Eoghan O'Brien http://github.com/eoghanobrien
* @package Simple
* @version 1.2
* @copyright 2009-2010
* @license Free http://unlicense.org/
*/

class SimpleMail
{
  const CRLF = "\r\n";

  /**
   * @var int $_wrap
   */
  protected $_wrap = 78;

  /**
   * @var string $_to
   */
  protected $_to = array();

  /**
   * @var string $_subject
   */
  protected $_subject;

  /**
   * @var string $_message
   */
  protected $_message;

  /**
   * @var array $_headers
   */
  protected $_headers = array();

  /**
   * @var string $_parameters
   */
  protected $_parameters = '-f';

  /**
   * @var array $_attachments
   */
  protected $_attachments = array();

  /**
   * @var array $_attachmentsPath
   */
  protected $_attachmentsPath = array();

  /**
   * @var array $_attachment_filename
   */
  protected $_attachmentsFilename = array();


  /**
   * __construct.
   *
   * Resets the class properties.
   */
  public function __construct()
  {
    $this->reset();
  }

  /**
   * reset.
   *
   * Resets all properties to initial state.
   *
   * @return Simple_Mail
   */
  public function reset()
  {
    $this->_to = array();
    $this->_headers = array();
    $this->_subject = null;
    $this->_message = null;
    $this->_wrap = 78;
    $this->_parameters = null;
    $this->_attachments = array();
    $this->_attachmentsPath = array();
    $this->_attachmentsFilename = array();

    return $this;
  }

  /**
   * setTo.
   *
   * @param  string $email
   * @param  string $name
   *
   * @throws \InvalidArgumentException on non string value for $email
   * @throws \InvalidArgumentException on non string value for $name
   *
   * @return Simple_Mail
   */
  public function setTo($email, $name)
  {
    if (! is_string($email)) {
      throw new \InvalidArgumentException('$email must be a string');
    }

    if (! is_string($name)) {
      throw new \InvalidArgumentException('$name must be a string.');
    }

    $this->_to[] = $this->formatHeader($email, $name);

    return $this;
  }

  /**
   * Return an array of formatted To addresses.
   *
   * @return array
   */
  public function getTo()
  {
    return $this->_to;
  }

  /**
   * setSubject function.
   *
   * @param  string$subject
   * @throws \InvalidArgumentException on non string value for $subject
   * @return Simple_Mail
   */
  public function setSubject($subject)
  {
    if (! is_string($subject)) {
      throw new \InvalidArgumentException('$subject must be a string.');
    }

    $this->_subject = $this->filterOther($subject);

    return $this;
  }

  /**
   * getSubject function.
   *
   * @return string
   */
  public function getSubject()
  {
    return $this->_subject;
  }

  /**
   * setMessage function.
   *
   * @access public
   * @param  string $message
   * @throws \InvalidArgumentException on non string value for $message
   * @return Simple_Mail
   */
  public function setMessage($message)
  {
    if (! is_string($message)) {
      throw new \InvalidArgumentException('$message must be a string.');
    }

    $this->_message = str_replace("\n.", "\n..", $message);

    return $this;
  }

  /**
   * getMessage function.
   *
   * @return string
   */
  public function getMessage()
  {
    return $this->_message;
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
    $filename = empty($filename) ? basename($path) : $filename;
    $this->addAttachmentPath($path);
    $this->addAttachmentFilename($filename);
    $this->_attachments[] = $this->getAttachmentData($path);

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
    $this->_attachmentsPath[] = $path;

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
    $this->_attachmentsFilename[] = $filename;

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
    $filesize = filesize($path);
    $handle = fopen($path, "r");
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
   * @throws \InvalidArgumentException on non string value for $email
   * @throws \InvalidArgumentException on non string value for $name
   *
   * @return Simple_Mail
   */
  public function setFrom($email, $name)
  {
    if ( ! is_string($email)) {
      throw new \InvalidArgumentException();
    }

    if ( ! is_string($name)) {
      throw new \InvalidArgumentException();
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
   * @throws \InvalidArgumentException on non string value for $header
   * @throws \InvalidArgumentException on non string value for $email
   * @throws \InvalidArgumentException on non string value for $name
   *
   * @return Simple_Mail
   */
  public function addMailHeader($header, $email = null, $name = null)
  {
    if ( ! is_string($header)) {
      throw new \InvalidArgumentException('$header must be a string.');
    }

    if ( ! is_string($email)) {
      throw new \InvalidArgumentException('$email must be a string.');
    }

    if ( ! is_string($name)) {
      throw new \InvalidArgumentException('$name must be a string.');
    }

    $address = $this->formatHeader($email, $name);
    $this->_headers[] = sprintf('%s: %s', $header, $address);

    return $this;
  }

  /**
   * addGenericHeader function.
   *
   * @param  string $header
   * @param  mixed $value
   *
   * @throws \InvalidArgumentException on non string value for $header
   * @throws \InvalidArgumentException on non string value for $value
   *
   * @return Simple_Mail
   */
  public function addGenericHeader($header, $value)
  {
    if ( ! is_string($header)) {
      throw new \InvalidArgumentException('$header must be a string.');
    }

    if ( ! is_string($value) || ! is_string($value)) {
      throw new \InvalidArgumentException('$value must be a string.');
    }

    $this->_headers[] = "$header: $value";

    return $this;
  }

  /**
   * Return the headers registered so far as an array.
   *
   * @return array
   */
  public function getHeaders()
  {
    return $this->_headers;
  }

  /**
   * setAdditionalParameters function.
   *
   * Such as "-fyouremail@yourserver.com
   *
   * @param  string $additionalParameters
   * @throws \InvalidArgumentException on non string $additionalParameters
   * @return Simple_Mail
   */
  public function setParameters($additionalParameters)
  {
    if (! is_string($additionalParameters)) {
      throw new \InvalidArgumentException(
          '$additionalParameters must be a string.'
      );
    }

    $this->_parameters = $additionalParameters;

    return $this;
  }

  /**
   * getAdditionalParameters function
   *
   * @return string
   */
  public function getParameters()
  {
    return $this->_parameters;
  }

  /**
   * setWrap function.
   *
   * @param  int  $wrap
   *
   * @throws \InvalidArgumentException on non int value
   * @throws \InvalidArgumentException on int less than 1 for $wrap
   *
   * @return Simple_Mail
   */
  public function setWrap($wrap = 78)
  {
    if (! is_int($wrap) || $wrap < 1) {
      throw new \InvalidArgumentException(
          'Wrap must be an integer larger than 0'
      );
    }

    $this->_wrap = $wrap;

    return $this;
  }

  /**
   * getWrap function.
   *
   * @return int
   */
  public function getWrap()
  {
    return $this->_wrap;
  }

  /**
   * Checks if the email has any registered attachments.
   *
   * @return bool
   */
  public function hasAttachments()
  {
    return !empty($this->_attachments);
  }

  /**
   * assembleAttachment function.
   *
   * @return string
   */
  public function assembleAttachmentHeaders()
  {
    $u = md5(uniqid(time()));
    
    $h = '';
    $h .= "\r\nMIME-Version: 1.0\r\n";
    $h .= "Content-Type: multipart/mixed; boundary=\"".$u."\"\r\n\r\n";
    $h .= "This is a multi-part message in MIME format.\r\n";
    $h .= "--".$u."\r\n";
    $h .= "Content-type:text/html; charset=\"utf-8\"\r\n";
    $h .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $h .= $this->_message."\r\n\r\n";
    $h .= "--".$u."\r\n";
    
    foreach ($this->_attachmentsFilename as $k => $v) {
      $h .= "Content-Type: application/octet-stream; name=\"".$v."\"\r\n";
      $h .= "Content-Transfer-Encoding: base64\r\n";
      $h .= "Content-Disposition: attachment; filename=\"".$v."\"\r\n\r\n";
      $h .= $this->_attachments[$k]."\r\n\r\n";
      $h .= "--".$u."\r\n";
    }

    return $h;
  }

  /**
   * send function.
   *
   * @throws \RuntimeException on no To: address to send to
   * @return boolean
   */
  public function send()
  {
    $headers = (!empty($this->_headers))
             ? join(static::CRLF, $this->_headers) : array();

    $to = (is_array($this->_to) && !empty($this->_to))
        ? join(", ", $this->_to) : false;

    if ($to === false) {
      throw new \RuntimeException(
          'Unable to send, no To address has been set.'
      );
    }

    if ($this->hasAttachments()) {
      $headers .= $this->assembleAttachmentHeaders();
      return mail($to, $this->_subject, "", $headers, $this->_parameters);
    }

    $message = wordwrap($this->_message, $this->_wrap);

    return mail($to, $this->_subject, $message, $headers, $this->_parameters);
  }

  /**
   * debug function.
   *
   * @return string
   */
  public function debug()
  {
    return '<pre>'.print_r($this, true).'</pre>';
  }

  /**
   * magic __toString function.
   *
   * @return string
   */
  public function __toString()
  {
    return print_r($this, true);
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
  public function filterEmail($email)
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
  public function filterName($name)
  {
    $rule = array("\r" => '',
            "\n" => '',
            "\t" => '',
            '"'  => "'",
            '<'  => '[',
            '>'  => ']',
    );

    return trim(strtr(filter_var($name, FILTER_SANITIZE_STRING), $rule));
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
  public function filterOther($data)
  {
    $rule = array("\r" => '',
                  "\n" => '',
                  "\t" => '');

    return strtr(filter_var($data, FILTER_SANITIZE_STRING), $rule);
  }
}
