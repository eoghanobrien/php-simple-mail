<?php

/**
* Simple Mail class.
*
* @author Eoghan O'Brien <eoghan@eoghanobrien.com> (www.eoghanobrien.com)
* @package Simple Mail
* @version 1.1
* @copyright 2009-2010
* @see http://eoghanobrien.com/code/simple-mail-php/
*/

class Simple_Mail
{
	/**
	 * @var int $wrap
	 * @access protected
	 */
	protected $_wrap		= 70;
	
	/**
	 * @var string $_to (default value: NULL)
	 * @access protected
	 */
	protected $_to			= NULL;
	
	/**
	 * @var string $_subject (default value: NULL)
	 * @access protected
	 */
	protected $_subject		= NULL;
	
	/**
	 * @var string $_message (default value: NULL)
	 * @access protected
	 */
	protected $_message		= NULL;
	
	/**
	 * @var array $_headers (default value: array())
	 * @access protected
	 */
	protected $_headers		= array();
	
	/**
	 * @var boolean $_throwExceptions (default value: FALSE)
	 * @access protected
	 */
	protected $_throwExceptions = FALSE;
	
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct($throwExceptions = FALSE)
	{
		$this->_headers = array();
		$this->_throwExceptions = $throwExceptions;
	}

	/**
	 * setTo function.
	 * 
	 * @access public
	 * @param	string	$email
	 * @param	string	$name
	 * @param	boolean	$addHeader	(default: FALSE)
	 * @return void
	 */
	public function setTo($email, $name, $addHeader = FALSE)
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
		
		$this->_to = $this->_formatHeader($email, $name);
		if ( $addHeader ) $this->addMailHeader('To', $email, $name);
		return $this;
	}
	
	/**
	 * setSubject function.
	 * 
	 * @access public
	 * @param	string	$subject
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
	 * @param	string		$message
	 * @param	boolean		$html		default: FALSE	Sends the message in plain text format
	 * @return void
	 */
	public function setMessage($message, $html = FALSE)
	{
		if ( ! is_string($message) && $this->_throwExceptions) {
			throw new InvalidArgumentException();
		}
		
		if ($html === TRUE) {
			$this->addGenericHeader('Content-type', 'text/html');
		}
		
		$this->_message = str_replace("\n.", "\n..", $message);
		return $this;
	}
	
	/**
	 * setFrom function.
	 * 
	 * @access public
	 * @param	string	$email
	 * @param	string	$name
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
	 * @param	string	$header
	 * @param	string	$email	(default: NULL)
	 * @param	string	$name	(default: NULL)
	 * @return void
	 */
	public function addMailHeader($header, $email = NULL, $name = NULL)
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
		
		$this->_headers[] = "$header: " . $this->_formatHeader($email, $name);		
		return $this;
	}
	
	/**
	 * addGenericHeader function.
	 * 
	 * @access public
	 * @param	string $header
	 * @param	mixed $value
	 * @return void
	 */
	public function addGenericHeader($header, $value)
	{
		if ( ! is_string($header) && $this->_throwExceptions) {
			throw new InvalidArgumentException();
		}
		
		if ( ! is_string($header) || ! is_int($value) && $this->_throwExceptions) {
			throw new InvalidArgumentException();
		}
		
		$this->_headers[] = "$header: $value";
		return $this;
	}
	
	/**
	 * setWrap function.
	 * 
	 * @access public
	 * @param mixed $wrap. (default: 70)
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
		
		$send = mail($this->_to, $this->_subject, wordwrap($this->_message, $this->_wrap), $headers);
		
		if ( ! $send && $this->_throwExceptions) {
			throw new Exception('Email failed to send');
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
		echo sprintf('<h1>Var Dump of Simple Mail instance</h1><pre>%s</pre><h1>PrintR of Simple Mail instance</h1><pre>%s</pre>', var_dump($this), print_r($this, 1));
	}
	
	/**************************************************************************************************
	PROTECTED METHODS
	**************************************************************************************************/
	
	/**
	 * Format headers
	 * 
	 * @access protected
	 * @param string $email
	 * @param string $name
	 * @return string
	 */
	protected function _formatHeader($email, $name)
	{
		$name	= $this->_filterName($name);
		$email	= $this->_filterEmail($email);
		return sprintf('%s <%s>', $name, $email);
	}
	
	
	/**
     * Filter of email data
     *
     * @access protected
     * @param string $email
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
    	
        return strtr($email, $rule);
    }

    /**
     * Filter of name data
     *
     * @access protected
     * @param string $name
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

        return trim(strtr($name, $rule));
    }

    /**
     * Filter of other data
     *
     * @access protected
     * @param string $data
     * @return string
     */
    protected function _filterOther($data)
    {
        $rule = array("\r" => '',
                      "\n" => '',
                      "\t" => '',
        );

        return strtr($data, $rule);
    }

}

?>