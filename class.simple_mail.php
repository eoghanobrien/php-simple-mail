<?php
/**
 * Simple Mail
 *
 * A simple PHP wrapper class for sending email using the mail() method.
 *
 * PHP version > 5.2
 *
 * LICENSE: This source file is subject to the MIT license, which is
 * available through the world-wide-web at the following URI:
 * http://github.com/eoghanobrien/php-simple-mail/LICENCE.txt
 *
 * @category  SimpleMail
 * @package   SimpleMail
 * @author    Eoghan O'Brien <eoghan@eoghanobrien.com>
 * @copyright 2009 - 2017 Eoghan O'Brien
 * @license   http://github.com/eoghanobrien/php-simple-mail/LICENCE.txt MIT
 * @version   1.7.1
 * @link      http://github.com/eoghanobrien/php-simple-mail
 */

/**
 * Simple Mail class.
 *
 * @category  SimpleMail
 * @package   SimpleMail
 * @author    Eoghan O'Brien <eoghan@eoghanobrien.com>
 * @copyright 2009 - 2017 Eoghan O'Brien
 * @license   http://github.com/eoghanobrien/php-simple-mail/LICENCE.txt MIT
 * @version   1.7.1
 * @link      http://github.com/eoghanobrien/php-simple-mail
 */
class SimpleMail
{
    /**
     * @var int $_wrap
     */
    protected $_wrap = 78;

    /**
     * @var array $_to
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
    protected $_params;

    /**
     * @var array $_attachments
     */
    protected $_attachments = array();

    /**
     * @var string $_uid
     */
    protected $_uid;

    /**
     * Named constructor.
     *
     * @return static
     */
    public static function make()
    {
        return new SimpleMail();
    }

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
     * @return self
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
        $this->_uid = $this->getUniqueId();
        return $this;
    }

    /**
     * setTo
     *
     * @param string $email The email address to send to.
     * @param string $name  The name of the person to send to.
     *
     * @return self
     */
    public function setTo($email, $name)
    {
        $this->_to[] = $this->formatHeader((string) $email, (string) $name);
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
     * setFrom
     *
     * @param string $email The email to send as from.
     * @param string $name  The name to send as from.
     *
     * @return self
     */
    public function setFrom($email, $name)
    {
        $this->addMailHeader('From', (string) $email, (string) $name);
        return $this;
    }

    /**
     * setCc
     *
     * @param array  $pairs  An array of name => email pairs.
     *
     * @return self
     */
    public function setCc(array $pairs)
    {
        return $this->addMailHeaders('Cc', $pairs);
    }

    /**
     * setBcc
     *
     * @param array  $pairs  An array of name => email pairs.
     *
     * @return self
     */
    public function setBcc(array $pairs)
    {
        return $this->addMailHeaders('Bcc', $pairs);
    }

    /**
     * setReplyTo
     *
     * @param string $email
     * @param string $name
     *
     * @return self
     */
    public function setReplyTo($email, $name = null)
    {
        return $this->addMailHeader('Reply-To', $email, $name);
    }

    /**
     * setHtml
     *
     * @return self
     */
    public function setHtml()
    {
        return $this->addGenericHeader(
            'Content-Type', 'text/html; charset="utf-8"'
        );
    }

    /**
     * setSubject
     *
     * @param string $subject The email subject
     *
     * @return self
     */
    public function setSubject($subject)
    {
        $this->_subject = $this->encodeUtf8(
            $this->filterOther((string) $subject)
        );
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
     * @param string $message The message to send.
     *
     * @return self
     */
    public function setMessage($message)
    {
        $this->_message = str_replace("\n.", "\n..", (string) $message);
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
     * @param string $path The file path to the attachment.
     * @param string $filename The filename of the attachment when emailed.
     * @param null $data
     * 
     * @return self
     */
    public function addAttachment($path, $filename = null, $data = null)
    {
        $filename = empty($filename) ? basename($path) : $filename;
        $filename = $this->encodeUtf8($this->filterOther((string) $filename));
        $data = empty($data) ? $this->getAttachmentData($path) : $data;
        $this->_attachments[] = array(
            'path' => $path,
            'file' => $filename,
            'data' => chunk_split(base64_encode($data))
        );
        return $this;
    }

    /**
     * getAttachmentData
     *
     * @param string $path The path to the attachment file.
     *
     * @return string
     */
    public function getAttachmentData($path)
    {
        $filesize = filesize($path);
        $handle = fopen($path, "r");
        $attachment = fread($handle, $filesize);
        fclose($handle);
        return $attachment;
    }

    /**
     * addMailHeader
     *
     * @param string $header The header to add.
     * @param string $email  The email to add.
     * @param string $name   The name to add.
     *
     * @return self
     */
    public function addMailHeader($header, $email, $name = null)
    {
        $address = $this->formatHeader((string) $email, (string) $name);
        $this->_headers[] = sprintf('%s: %s', (string) $header, $address);
        return $this;
    }

    /**
     * addMailHeaders
     *
     * @param string $header The header to add.
     * @param array  $pairs  An array of name => email pairs.
     *
     * @return self
     */
    public function addMailHeaders($header, array $pairs)
    {
        if (count($pairs) === 0) {
            throw new InvalidArgumentException(
                'You must pass at least one name => email pair.'
            );
        }
        $addresses = array();
        foreach ($pairs as $name => $email) {
            $name = is_numeric($name) ? null : $name;
            $addresses[] = $this->formatHeader($email, $name);
        }
        $this->addGenericHeader($header, implode(',', $addresses));
        return $this;
    }

    /**
     * addGenericHeader
     *
     * @param string $header The generic header to add.
     * @param mixed  $value  The value of the header.
     *
     * @return self
     */
    public function addGenericHeader($header, $value)
    {
        $this->_headers[] = sprintf(
            '%s: %s',
            (string) $header,
            (string) $value
        );
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
     * @param string $additionalParameters The addition mail parameter.
     *
     * @return self
     */
    public function setParameters($additionalParameters)
    {
        $this->_params = (string) $additionalParameters;
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
     * @param int $wrap The number of characters at which the message will wrap.
     *
     * @return self
     */
    public function setWrap($wrap = 78)
    {
        $wrap = (int) $wrap;
        if ($wrap < 1) {
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
     * 
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
        $head = array();
        $head[] = "MIME-Version: 1.0";
        $head[] = "Content-Type: multipart/mixed; boundary=\"{$this->_uid}\"";

        return join(PHP_EOL, $head);
    }

    /**
     * assembleAttachmentBody
     *
     * @return string
     */
    public function assembleAttachmentBody()
    {
        $body = array();
        $body[] = "This is a multi-part message in MIME format.";
        $body[] = "--{$this->_uid}";
        $body[] = "Content-Type: text/html; charset=\"utf-8\"";
        $body[] = "Content-Transfer-Encoding: quoted-printable";
        $body[] = "";
        $body[] = quoted_printable_encode($this->_message);
        $body[] = "";
        $body[] = "--{$this->_uid}";

        foreach ($this->_attachments as $attachment) {
            $body[] = $this->getAttachmentMimeTemplate($attachment);
        }

        return implode(PHP_EOL, $body) . '--';
    }

    /**
     * getAttachmentMimeTemplate
     *
     * @param array  $attachment An array containing 'file' and 'data' keys.
     *
     * @return string
     */
    public function getAttachmentMimeTemplate($attachment)
    {
        $file = $attachment['file'];
        $data = $attachment['data'];

        $head = array();
        $head[] = "Content-Type: application/octet-stream; name=\"{$file}\"";
        $head[] = "Content-Transfer-Encoding: base64";
        $head[] = "Content-Disposition: attachment; filename=\"{$file}\"";
        $head[] = "";
        $head[] = $data;
        $head[] = "";
        $head[] = "--{$this->_uid}";

        return implode(PHP_EOL, $head);
    }

    /**
     * send
     *
     * @return boolean
     * @throws \RuntimeException on no 'To: ' address to send to.
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
            $message  = $this->assembleAttachmentBody();
            $headers .= PHP_EOL . $this->assembleAttachmentHeaders();
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
     * magic __toString function
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
     * @param string $email The email address.
     * @param string $name  The display name.
     *
     * @return string
     */
    public function formatHeader($email, $name = null)
    {
        $email = $this->filterEmail((string) $email);
        if (empty($name)) {
            return $email;
        }
        $name = $this->encodeUtf8($this->filterName((string) $name));
        return sprintf('"%s" <%s>', $name, $email);
    }

    /**
     * encodeUtf8
     *
     * @param string $value The value to encode.
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
     * encodeUtf8Word
     *
     * @param string $value The word to encode.
     *
     * @return string
     */
    public function encodeUtf8Word($value)
    {
        return sprintf('=?UTF-8?B?%s?=', base64_encode($value));
    }

    /**
     * encodeUtf8Words
     *
     * @param string $value The words to encode.
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
     * @param string $email The email to filter.
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
     * @param string $name The name to filter.
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
     * Removes ASCII control characters including any carriage return, line
     * feed or tab characters.
     *
     * @param string $data The data to filter.
     *
     * @return string
     */
    public function filterOther($data)
    {
        return filter_var($data, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW);
    }

    /**
     * getHeadersForSend
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
     * getToForSend
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
     * getUniqueId
     *
     * @return string
     */
    public function getUniqueId()
    {
        return md5(uniqid(time()));
    }

    /**
     * getWrapMessage
     *
     * @return string
     */
    public function getWrapMessage()
    {
        return wordwrap($this->_message, $this->_wrap);
    }
}
