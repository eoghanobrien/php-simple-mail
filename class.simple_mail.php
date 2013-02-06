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
	 * @access protected
	 */
	protected $_wrap = 78;
	
	/**
	 * @var string $_to (default value: null)
	 * @access protected
	 */
	protected $_to = array();
	
	/**
	 * @var string $_subject (default value: null)
	 * @access protected
	 */
	protected $_subject = null;
	
	/**
	 * @var string $_message (default value: null)
	 * @access protected
	 */
	protected $_message = null;
	
	/**
	 * @var array $_headers (default value: array())
	 * @access protected
	 */
	protected $_headers = array();
	
	/**
	 * @var string $_additionalParameters (default value: null)
	 * @access protected
	 */
	protected $_additionalParameters	= null;
	
	/**
	 * @var string $_attachments (default value: array())
	 * @access protected
	 */
	protected $_attachments = array();
	
	/**
	 * @var    string $_attachmentsPath (default value: array())
	 * @access protected
	 */
	protected $_attachmentsPath = array();
	
	/**
	 * @var    string $attachment_filename (default value: array())
	 * @access protected
	 */
	protected $_attachmentsFilename = array();
	
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		$this->_headers = array();
	}

	/**
	 * setTo function.
	 * 
	 * @access public
	 * @param  string	$email
	 * @param  string	$name
	 * @param  boolean	$addHeader	(default: false)
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
		
		$this->_to[] = $this->formatHeader($email, $name);

		return $this;
	}

	/**
	 * Return an array of formatted To addresses.
	 *
	 * @access public
	 * @return array
	 */
	public function getTo()
	{
		return $this->_to;
	}
	
	/**
	 * setSubject function.
	 * 
	 * @access public
	 * @param  string	$subject
	 * @return Simple_Mail
	 */
	public function setSubject($subject)
	{
		if (! is_string($subject)) {
			throw new InvalidArgumentException();
		}
		
		$this->_subject = $this->_filterOther($subject);
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
	 * @param  string	$message
	 * @return Simple_Mail
	 */
	public function setMessage($message)
	{
		if (! is_string($message)) {
			throw new InvalidArgumentException();
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
	 * @param  string		$message
	 * @return Simple_Mail
	 */
	public function addAttachment($path, $filename = null)
	{
		$this->addAttachmentPath($path);
		$this->addAttachmentFilename(empty($filename) ? basename($path) : $filename);
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
		$filesize   = filesize($path);
		$handle     = fopen($path, "r");
		$attachment = fread($handle, $filesize);
		fclose($handle);

		return chunk_split(base64_encode($attachment));
	}

	/**
	 * setFrom function.
	 * 
	 * @access public
	 * @param  string	$email
	 * @param  string	$name
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
	 * @access public
	 * @param  string	$header
	 * @param  string	$email	(default: null)
	 * @param  string	$name	(default: null)
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

		$this->_headers[] = sprintf('%s: %s', $header, $this->formatHeader($email, $name));

		return $this;
	}
	
	/**
	 * addGenericHeader function.
	 * 
	 * @access public
	 * @param  string $header
	 * @param  mixed $value
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
	 * @access public
	 * @param  string	$additionalParameters
	 * @return Simple_Mail
	 */
	public function setAdditionalParameters($additionalParameters)
	{
		if ( ! is_string($additionalParameters)) {
			throw new InvalidArgumentException('$additionalParameters must be a string.');
		}
		
		$this->_additionalParameters = $additionalParameters;

		return $this;
	}

	/**
	 * getAdditionalParameters function
	 *
	 * @return string
	 */
	public function getAdditionalParameters()
	{
		return $this->_additionalParameters;
	}
	
	/**
	 * setWrap function.
	 * 
	 * @access public
	 * @param  int      $wrap. (default: 78)
	 * @return object
	 */
	public function setWrap($wrap = 78)
	{
		if (! is_int($wrap) || $wrap < 1) {
			throw new InvalidArgumentException('Wrap must be an integer larger than 0');
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
	 * Checks if the email has an registered attachments.
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
		$uid = md5(uniqid(time()));
		$headers  = sprintf('MIME-Version: 1.0', self::CRLF);
		$headers .= sprintf('Content-Type: multipart/mixed; boundary="%s"%s%s', self::CRLF, self::CRLF, $uid);
		$headers .= sprintf('This is a multi-part message in MIME format.%s', self::CRLF);
		$headers .= sprintf('--%s%s', $uid, self::CRLF);
		$headers .= sprintf('Content-type:text/html; charset="utf-8"%s', self::CRLF);
		$headers .= sprintf('Content-Transfer-Encoding: 7bit%s', self::CRLF, self::CRLF);
		$headers .= sprintf('%s%s%s', $this->_message, self::CRLF, self::CRLF);
		$headers .= sprintf('--%s%s', $uid, self::CRLF);

		foreach ($this->_attachmentsFilename as $key => $value) {
			$headers .= sprintf('Content-Type: application/octet-stream; name="%s"%s', $value, self::CRLF);
			$headers .= sprintf('Content-Transfer-Encoding: base64%s', self::CRLF);
			$headers .= sprintf('Content-Disposition: attachment; filename="%s"%s%s', $value, self::CRLF, self::CRLF);
			$headers .= sprintf('%s%s%s', $this->_attachments[$key], self::CRLF, self::CRLF);
			$headers .= sprintf('--%s%s', $uid, self::CRLF);
		}

		return $headers;
	}
	
	/**
	 * send function.
	 * 
	 * @access public
	 * @return void
	 */
	public function send()
	{	
		$headers = (!empty($this->_headers)) ? join("\r\n", $this->_headers) : array();
		$to      = (is_array($this->_to) && !empty($this->_to)) ? join(", ", $this->_to) : false;

		if ($to === false) {
			throw new RuntimeException('Unable to send, no To address has been set.');
		}

		if ($this->hasAttachments()) {
			$headers += $this->assembleAttachmentHeaders();
			return mail($to, $this->_subject, "", $headers, $this->_additionalParameters);
		}
		else {
			return mail($to, $this->_subject, wordwrap($this->_message, $this->_wrap), $headers, $this->_additionalParameters);
		}
	}
	
	/**
	 * debug function.
	 * 
	 * @access public
	 * @return void
	 */
	public function debug()
	{
		return '<pre>'.print_r($this, 1).'</pre>';
	}
	
	/**
	 * magic __toString function.
	 * 
	 * @access public
	 * @return string
	 */
	public function __toString()
	{
		return print_r($this, 1);
	}
	
	/**
	 * Format headers
	 *
	 * @todo   Test this.
	 * @access public
	 * @param  string $email
	 * @param  string $name
	 * @return string
	 */
	public function formatHeader($email, $name)
	{
		$name	= $this->_filterName($name);
		$email	= $this->_filterEmail($email);
		return sprintf('%s <%s>', $name, $email);
	}
	
	/**
	 * Filter of email data
	 *
	 * @todo   Test this.
	 * @access protected
	 * @param  string $email
	 * @return string
	 */
	protected function _filterEmail($email)
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
	 * Filter of name data
	 *
	 * @todo   Test this.
	 * @access protected
	 * @param  string $name
	 * @return string
	 */
	protected function _filterName($name)
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
	 * Filter of other data
	 *
	 * @todo   Test this.
	 * @access protected
	 * @param  string $data
	 * @return string
	 */
	protected function _filterOther($data)
	{
		$rule = array("\r" => '',
					  "\n" => '',
					  "\t" => '',
		);

		return strtr(filter_var($data, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH), $rule);
	}

}