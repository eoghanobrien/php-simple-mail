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
	/**
	 * @var int $wrap
	 * @access protected
	 */
	protected $_wrap = 70;
	
	/**
	 * @var string $_to (default value: null)
	 * @access protected
	 */
	protected $_to = null;
	
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
	 * @var boolean $_throwExceptions (default value: false)
	 * @access protected
	 */
	protected $_throwExceptions = false;
	
	/**
	 * @var string $_attachment (default value: array())
	 * @access protected
	 */
	protected $_attachment = array();
	
	/**
	 * @var    string $_attachmentPath (default value: array())
	 * @access protected
	 */
	protected $_attachmentPath = array();
	
	/**
	 * @var    string $attachment_filename (default value: array())
	 * @access protected
	 */
	protected $_attachmentFilename = array();
	
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct($throwExceptions = false)
	{
		$this->_headers = array();
		$this->setThrowExceptions($throwExceptions);
	}
	
	/**
	 * setThrowExceptions function.
	 * 
	 * @access public
	 * @param  mixed	$bool (default: false)
	 * @return void
	 */
	public function setThrowExceptions($bool = false)
	{
		if ( ! is_bool($bool)) {
			throw new InvalidArgumentException('First parameter must be boolean');
		}
	
		$this->_throwExceptions = $bool;
	}

	/**
	 * Determine whether or not the class should throw exceptions.
	 *
	 * @access public
	 * @return bool
	 */
	public function shouldThrowExceptions()
	{
		return $this->_throwExceptions;
	}

	/**
	 * setTo function.
	 * 
	 * @access public
	 * @param  string	$email
	 * @param  string	$name
	 * @param  boolean	$addHeader	(default: false)
	 * @return void
	 */
	public function setTo($email, $name, $addHeader = false)
	{
		if ( ! is_string($email) && $this->_throwExceptions) {
			throw new InvalidArgumentException();
		}
		
		if ( ! is_string($name) && $this->_throwExceptions) {
			throw new InvalidArgumentException();
		}
		
		if ( ! is_bool($addHeader) && $this->_throwExceptions) {
			throw new InvalidArgumentException();
		}
		
		$this->_to = $this->formatHeader($email, $name);

		if ( $addHeader ) {
			$this->addMailHeader('To', $email, $name);
		}
		return $this;
	}

	/**
	 * Return the formatted To address
	 *
	 * @access public
	 * @return string
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
	 * @return void
	 */
	public function setSubject($subject)
	{
		if ( ! is_string($subject) && $this->_throwExceptions) {
			throw new InvalidArgumentException();
		}
		
		$this->_subject = $this->_filterOther($subject);
		return $this;
	}
	
	/**
	 * setMessage function.
	 * 
	 * @access public
	 * @param  string		$message
	 * @return void
	 */
	public function setMessage($message)
	{
		if ( ! is_string($message) && $this->_throwExceptions) {
			throw new InvalidArgumentException();
		}
		
		$this->_message = str_replace("\n.", "\n..", $message);
		return $this;
	}
	
	/**
	 * addAttachment function.
	 * 
	 * @access public
	 * @param  string		$message
	 * @return void
	 */
	public function addAttachment($path, $filename = null)
	{
		$this->_attachmentPath[] = $path;
		$this->_attachmentFilename[] = empty($filename) ? basename($path) : $filename;
		
		$filesize = filesize($path);
		$handle = fopen($path, "r");
		$attachment = fread($handle, $filesize);
		fclose($handle);
		$this->_attachment[] = chunk_split(base64_encode($attachment));

		return $this;
	}
	
	/**
	 * setFrom function.
	 * 
	 * @access public
	 * @param  string	$email
	 * @param  string	$name
	 * @return void
	 */
	public function setFrom($email, $name)
	{
		if ( ! is_string($email) && $this->_throwExceptions) {
			throw new InvalidArgumentException();
		}
		
		if ( ! is_string($name) && $this->_throwExceptions) {
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
	 * @return void
	 */
	public function addMailHeader($header, $email = null, $name = null)
	{
		if ( ! is_string($header) && $this->_throwExceptions) {
			throw new InvalidArgumentException();
		}
		
		if ( ! is_string($email) && $this->_throwExceptions) {
			throw new InvalidArgumentException();
		}
		
		if ( ! is_string($name) && $this->_throwExceptions) {
			throw new InvalidArgumentException();
		}

		$this->_headers[] = sprintf('%s:%s', $header, $this->formatHeader($email, $name));

		return $this;
	}
	
	/**
	 * addGenericHeader function.
	 * 
	 * @access public
	 * @param  string $header
	 * @param  mixed $value
	 * @return void
	 */
	public function addGenericHeader($header, $value)
	{
		if ( ! is_string($header) && $this->_throwExceptions) {
			throw new InvalidArgumentException();
		}
		
		if ( ! is_string($value) || ! is_string($value) && $this->_throwExceptions) {
			throw new InvalidArgumentException();
		}
		
		$this->_headers[] = "$header: $value";
		return $this;
	}

	public function getHeaders()
	{
		return $this->_headers;
	}
		
	/**
	 * setAdditionalParameters function.
	 * 
	 * @access public
	 * @param  string	$additionalParameters
	 * @return void
	 */
	public function setAdditionalParameters($additionalParameters)
	{
		if ( ! is_string($additionalParameters) && $this->_throwExceptions) {
			throw new InvalidArgumentException();
		}
		
		$this->_additionalParameters = $additionalParameters;
		return $this;
	}
	
	/**
	 * setWrap function.
	 * 
	 * @access public
	 * @param  int      $wrap. (default: 70)
	 * @return object
	 */
	public function setWrap($wrap = 70)
	{
		if ( ! is_int($wrap) && $wrap < 1 && $this->_throwExceptions) {
			throw new InvalidArgumentException('Wrap must be an integer larger than 0');
		}
		
		$this->_wrap = $wrap;
		return $this;
	}
	
	/**
	 * send function.
	 * 
	 * @access public
	 * @return void
	 */
	public function send()
	{	
		$headers = ( !empty($this->_headers) ) ? join("\r\n", $this->_headers) : array();
		
		if ( ! empty($this->_attachment)) {
			$uid = md5(uniqid(time()));
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= sprintf("Content-Type: multipart/mixed; boundary=\"%s\"\r\n\r\n", $uid);
			$headers .= "This is a multi-part message in MIME format.\r\n";
			$headers .= sprintf("--%s\r\n", $uid);
			$headers .= "Content-type:text/html; charset=\"utf-8\"\r\n";
			$headers .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
			$headers .= $this->_message."\r\n\r\n";
			$headers .= sprintf("--%s\r\n", $uid);
			
			foreach ($this->_attachmentFilename as $key => $value) {
				$headers .= sprintf("Content-Type: application/octet-stream; name=\"%s\"\r\n", $value);
				$headers .= "Content-Transfer-Encoding: base64\r\n";
				$headers .= sprintf("Content-Disposition: attachment; filename=\"%s\"\r\n\r\n", $value);
				$headers .= sprintf("%s\r\n\r\n", $this->_attachment[$key]);
				$headers .= sprintf("--%s\r\n", $uid);
			}
			$send = mail($this->_to, $this->_subject, "", $headers, $this->_additionalParameters);
		}
		else {
			$send = mail($this->_to, $this->_subject, wordwrap($this->_message, $this->_wrap), $headers, $this->_additionalParameters);
		}
		
		if ( ! $send && $this->_throwExceptions) {
			throw new Exception('Email failed to send.');
		}
		
		if ( ! $send) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * debug function.
	 * 
	 * @access public
	 * @return void
	 */
	public function debug()
	{
		var_dump($this);
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