<?php

/**
 * Simple Mail class.
 *
 * @author Eoghan O'Brien http://github.com/eoghanobrien
 * @package Simple
 * @version 1.2
 * @copyright 2009-2014
 * @license Free http://unlicense.org/
 */
class SimpleMail
{
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
    protected $_params = '-f';

    /**
     * @var array $_attachments
     */
    protected $_attachments = array();

    /**
     * End of Line character, different on most popular OSs.
     * @var string $eol
     */
    protected $_eol;

    /**
     * __construct
     *
     * Resets the class properties.
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * reset
     *
     * Resets all properties to initial state.
     *
     * @return SimpleMail
     */
    public function reset()
    {
        $this->_to = array();
        $this->_headers = array();
        $this->_subject = null;
        $this->_message = null;
        $this->_wrap = 78;
        $this->_params = null;
        $this->_attachments = array();

        return $this;
    }

    /**
     * setTo
     *
     * @param  string $email
     * @param  string $name
     *
     * @return SimpleMail
     */
    public function setTo($email, $name)
    {
        $name = (string) $name;
        $email = (string) $email;

        $this->_to[] = $this->formatHeader($email, $name);

        return $this;
    }

    /**
     * getTo
     *
     * Return an array of formatted To addresses.
     *
     * @return array
     */
    public function getTo()
    {
        return $this->_to;
    }

    /**
     * setSubject
     *
     * @param  string $subject
     *
     * @throws \InvalidArgumentException on non string value for $subject
     * @return SimpleMail
     */
    public function setSubject($subject)
    {
        $subject = (string) $subject;

        $this->_subject = $this->encodeUtf8($this->filterOther($subject));

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
     * setMessage
     *
     * @access public
     *
     * @param  string $message
     *
     * @throws \InvalidArgumentException on non string value for $message
     * @return SimpleMail
     */
    public function setMessage($message)
    {
        $message = (string) $message;
        $this->_message = str_replace("\n.", "\n..", $message);
        return $this;
    }

    /**
     * getMessage
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * addAttachment
     *
     * @access public
     *
     * @param  string $path
     * @param  string $filename
     *
     * @return SimpleMail
     */
    public function addAttachment($path, $filename = null)
    {
        $filename = empty($filename) ? basename($path) : $filename;

        $this->_attachments[] = array(
            'path' => $path,
            'file' => $filename,
            'data' => $this->getAttachmentData($path)
        );

        return $this;
    }

    /**
     * getAttachmentData
     *
     * @param  string $path
     *
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
     * setFrom
     *
     * @param  string $email
     * @param  string $name
     * @return SimpleMail
     */
    public function setFrom($email, $name)
    {
        $email = (string) $email;
        $name = (string) $name;
        $this->addMailHeader('From', $email, $name);
        return $this;
    }

    /**
     * addMailHeader
     *
     * @param  string $header
     * @param  string $email
     * @param  string $name
     *
     * @return SimpleMail
     */
    public function addMailHeader($header, $email = null, $name = null)
    {
        $name = (string) $name;
        $email = (string) $email;
        $header = (string) $header;

        $address = $this->formatHeader($email, $name);
        $this->_headers[] = sprintf('%s: %s', $header, $address);

        return $this;
    }

    /**
     * addGenericHeader
     *
     * @param  string $header
     * @param  mixed  $value
     * @return SimpleMail
     */
    public function addGenericHeader($header, $value)
    {
        $value = (string) $value;
        $header = (string) $header;
        $this->_headers[] = "$header: $value";

        return $this;
    }

    /**
     * getHeaders
     *
     * Return the headers registered so far as an array.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * setAdditionalParameters
     *
     * Such as "-fyouremail@yourserver.com
     *
     * @param  string $additionalParameters
     * @return SimpleMail
     */
    public function setParameters($additionalParameters)
    {
        $additionalParameters = (string) $additionalParameters;

        $this->_params = $additionalParameters;

        return $this;
    }

    /**
     * getAdditionalParameters
     *
     * @return string
     */
    public function getParameters()
    {
        return $this->_params;
    }

    /**
     * setWrap
     *
     * @param  int $wrap
     * @return SimpleMail
     */
    public function setWrap($wrap = 78)
    {
        if (!is_int($wrap) || $wrap < 1) {
            $wrap = 78;
        }

        $this->_wrap = $wrap;

        return $this;
    }

    /**
     * getWrap
     *
     * @return int
     */
    public function getWrap()
    {
        return $this->_wrap;
    }

    /**
     * hasAttachments
     * Checks if the email has any registered attachments.
     *
     * @return bool
     */
    public function hasAttachments()
    {
        return !empty($this->_attachments);
    }

    /**
     * assembleAttachment
     *
     * @return string
     */
    public function assembleAttachmentHeaders()
    {
        $uid = $this->getUniqueId();
        $eol = PHP_EOL;

        $head = array();
        $head[] = "{$eol}MIME-Version: 1.0";
        $head[] = "Content-Type: multipart/mixed; boundary=\"{$uid}\"{$eol}";
        $head[] = "This is a multi-part message in MIME format.";
        $head[] = "--{$uid}";
        $head[] = "Content-type:text/html; charset=\"utf-8\"";
        $head[] = "Content-Transfer-Encoding: 7bit{$eol}";
        $head[] = $this->_message . "{$eol}";
        $head[] = "--{$uid}";

        foreach ($this->_attachments as $attachment) {
            $head[] = $this->getAttachmentMimeTemplate($attachment, $uid);
        }

        return join("{$eol}", $head);
    }

    /**
     * getAttachmentMimeTemplate()
     *
     * @param  array  $attachment
     * @param  string $uid
     * @return string
     */
    public function getAttachmentMimeTemplate($attachment, $uid)
    {
        $eol = PHP_EOL;
        $file = $attachment['file'];
        $data = $attachment['data'];

        $head = array();
        $head[] = "Content-Type: application/octet-stream; name=\"{$file}\"";
        $head[] = "Content-Transfer-Encoding: base64";
        $head[] = "Content-Disposition: attachment; filename=\"{$file}\"{$eol}";
        $head[]= "{$data}{$eol}";
        $head[]= "--{$uid}";

        return implode($eol, $head);
    }

    /**
     * send
     *
     * @throws \RuntimeException on no To: address to send to
     * @return boolean
     */
    public function send()
    {
        $to = $this->getToForSend();
        $headers = $this->getHeadersForSend();

        if (empty($to)) {
            throw new \RuntimeException(
                'Unable to send, no To address has been set.'
            );
        }

        if ($this->hasAttachments()) {
            $message = '';
            $headers .= $this->assembleAttachmentHeaders();
        } else {
            $message = $this->getWrapMessage();
        }

        return mail($to, $this->_subject, $message, $headers, $this->_params);
    }

    /**
     * debug
     *
     * @return string
     */
    public function debug()
    {
        return '<pre>' . print_r($this, true) . '</pre>';
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
     * formatHeader
     *
     * Formats a display address for emails according to RFC2822 e.g.
     * Name <address@domain.tld>
     *
     * @param  string $email
     * @param  string $name
     *
     * @return string
     */
    public function formatHeader($email, $name = null)
    {
        $email = $this->filterEmail($email);

        if (empty($name)) {
            return $email;
        }

        $name = $this->encodeUtf8($this->filterName($name));

        return sprintf('%s <%s>', $name, $email);
    }

    /**
     * encodeUtf8()
     *
     * @param  string $value
     *
     * @return string
     */
    public function encodeUtf8($value)
    {
        $value = trim($value);
        if (preg_match('/(\s)/', $value)) {
            return $this->encodeUtf8Words($value);
        }

        return $this->encodeUtf8Word($value);
    }

    /**
     * encodeUtf8Word()
     *
     * @param string $value
     *
     * @return string
     */
    public function encodeUtf8Word($value)
    {
        return sprintf('=?UTF-8?B?%s?=', base64_encode($value));
    }

    /**
     * encodeUtf8Words()
     *
     * @param  string $value
     *
     * @return string
     */
    public function encodeUtf8Words($value)
    {
        $words = explode(' ', $value);

        $encoded = array();
        foreach ($words as $word) {
            $encoded[] = $this->encodeUtf8Word($word);
        }

        return join($this->encodeUtf8Word(' '), $encoded);
    }

    /**
     * filterEmail
     *
     * Removes any carriage return, line feed, tab, double quote, comma
     * and angle bracket characters before sanitizing the email address.
     *
     * @param  string $email
     *
     * @return string
     */
    public function filterEmail($email)
    {
        $rule = array(
            "\r" => '',
            "\n" => '',
            "\t" => '',
            '"'  => '',
            ','  => '',
            '<'  => '',
            '>'  => ''
        );

        $email = strtr($email, $rule);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        return $email;
    }

    /**
     * filterName
     *
     * Removes any carriage return, line feed or tab characters. Replaces
     * double quotes with single quotes and angle brackets with square
     * brackets, before sanitizing the string and stripping out html tags.
     *
     * @param  string $name
     *
     * @return string
     */
    public function filterName($name)
    {
        $rule = array(
            "\r" => '',
            "\n" => '',
            "\t" => '',
            '"'  => "'",
            '<'  => '[',
            '>'  => ']',
        );

        $filtered = filter_var(
            $name,
            FILTER_SANITIZE_STRING,
            FILTER_FLAG_NO_ENCODE_QUOTES
        );

        return trim(strtr($filtered, $rule));
    }

    /**
     * filterOther
     *
     * Removes any carriage return, line feed or tab characters.
     *
     * @param  string $data
     *
     * @return string
     */
    public function filterOther($data)
    {
        $rule = array(
            "\r" => '',
            "\n" => '',
            "\t" => ''
        );

        return strtr(filter_var($data, FILTER_SANITIZE_STRING), $rule);
    }

    /**
     * getHeadersForSend()
     *
     * @return string
     */
    public function getHeadersForSend()
    {
        if (empty($this->_headers)) {
            return '';
        }

        return join(PHP_EOL, $this->_headers);
    }

    /**
     * getToForSend()
     *
     * @return string
     */
    public function getToForSend()
    {
        if (empty($this->_to)) {
            return '';
        }

        return join(', ', $this->_to);
    }

    /**
     * getUniqueId()
     *
     * @return string
     */
    public function getUniqueId()
    {
        return md5(uniqid(time()));
    }

    /**
     * getWrapMessage()
     *
     * @return string
     */
    public function getWrapMessage()
    {
        return wordwrap($this->_message, $this->_wrap);
    }
}
