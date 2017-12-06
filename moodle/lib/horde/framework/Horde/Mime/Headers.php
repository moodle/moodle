<?php
/**
 * This class contains functions related to handling the headers of MIME data.
 *
 * Copyright 2002-2014 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author   Michael Slusarz <slusarz@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Mime
 */
class Horde_Mime_Headers implements Serializable
{
    /* Serialized version. */
    const VERSION = 2;

    /* Constants for getValue(). */
    const VALUE_STRING = 1;
    const VALUE_BASE = 2;
    const VALUE_PARAMS = 3;

    /**
     * The default charset to use when parsing text parts with no charset
     * information.
     *
     * @var string
     */
    static public $defaultCharset = 'us-ascii';

    /**
     * The internal headers array.
     *
     * Keys are the lowercase header name.
     * Values are:
     *   - h: The case-sensitive header name.
     *   - p: Parameters for this header.
     *   - v: The value of the header. Values are stored in UTF-8.
     *
     * @var array
     */
    protected $_headers = array();

    /**
     * The sequence to use as EOL for the headers.
     * The default is currently to output the EOL sequence internally as
     * just "\n" instead of the canonical "\r\n" required in RFC 822 & 2045.
     * To be RFC complaint, the full <CR><LF> EOL combination should be used
     * when sending a message.
     *
     * @var string
     */
    protected $_eol = "\n";

    /**
     * The User-Agent string to use.
     *
     * @var string
     */
    protected $_agent = null;

    /**
     * List of single header fields.
     *
     * @var array
     */
    protected $_singleFields = array(
        // Mail: RFC 5322
        'to', 'from', 'cc', 'bcc', 'date', 'sender', 'reply-to',
        'message-id', 'in-reply-to', 'references', 'subject',
        // MIME: RFC 1864
        'content-md5',
        // MIME: RFC 2045
        'mime-version', 'content-type', 'content-transfer-encoding',
        'content-id', 'content-description',
        // MIME: RFC 2110
        'content-base',
        // MIME: RFC 2183
        'content-disposition',
        // MIME: RFC 2424
        'content-duration',
        // MIME: RFC 2557
        'content-location',
        // MIME: RFC 2912 [3]
        'content-features',
        // MIME: RFC 3282
        'content-language',
        // MIME: RFC 3297
        'content-alternative',
        // Importance: See, e.g., RFC 4356 [2.1.3.3.1]
        'importance',
        // OTHER: X-Priority
        // See: http://kb.mozillazine.org/Emulate_Microsoft_email_clients
        'x-priority'
    );

    /**
     * Returns the internal header array in array format.
     *
     * @param array $opts  Optional parameters:
     *   - canonical: (boolean) Use canonical (RFC 822/2045) line endings?
     *                DEFAULT: Uses $this->_eol
     *   - charset: (string) Encodes the headers using this charset. If empty,
     *              encodes using internal charset (UTF-8).
     *              DEFAULT: No encoding.
     *   - defserver: (string) The default domain to append to mailboxes.
     *                DEFAULT: No default name.
     *   - nowrap: (integer) Don't wrap the headers.
     *             DEFAULT: Headers are wrapped.
     *
     * @return array  The headers in array format.
     */
    public function toArray(array $opts = array())
    {
        $address_keys = $this->addressFields();
        $charset = array_key_exists('charset', $opts)
            ? (empty($opts['charset']) ? 'UTF-8' : $opts['charset'])
            : null;
        $eol = empty($opts['canonical'])
            ? $this->_eol
            : "\r\n";
        $mime = $this->mimeParamFields();
        $ret = array();

        foreach ($this->_headers as $header => $ob) {
            $val = is_array($ob['v']) ? $ob['v'] : array($ob['v']);

            foreach (array_keys($val) as $key) {
                if (in_array($header, $address_keys) ) {
                    /* Address encoded headers. */
                    $rfc822 = new Horde_Mail_Rfc822();
                    $text = $rfc822->parseAddressList($val[$key], array(
                        'default_domain' => empty($opts['defserver']) ? null : $opts['defserver']
                    ))->writeAddress(array(
                        'encode' => $charset,
                        'idn' => true
                    ));
                } elseif (in_array($header, $mime) && !empty($ob['p'])) {
                    /* MIME encoded headers (RFC 2231). */
                    $text = $val[$key];
                    foreach ($ob['p'] as $name => $param) {
                        foreach (Horde_Mime::encodeParam($name, $param, array('charset' => $charset, 'escape' => true)) as $name2 => $param2) {
                            $text .= '; ' . $name2 . '=' . $param2;
                        }
                    }
                } else {
                    $text = is_null($charset)
                        ? $val[$key]
                        : Horde_Mime::encode($val[$key], $charset);
                }

                if (empty($opts['nowrap'])) {
                    /* Remove any existing linebreaks and wrap the line. */
                    $header_text = $ob['h'] . ': ';
                    $text = ltrim(substr(wordwrap($header_text . strtr(trim($text), array("\r" => '', "\n" => '')), 76, $eol . ' '), strlen($header_text)));
                }

                $val[$key] = $text;
            }

            $ret[$ob['h']] = (count($val) == 1) ? reset($val) : $val;
        }

        return $ret;
    }

    /**
     * Returns the internal header array in string format.
     *
     * @param array $opts  Optional parameters:
     *   - canonical: (boolean) Use canonical (RFC 822/2045) line endings?
     *                DEFAULT: Uses $this->_eol
     *   - charset: (string) Encodes the headers using this charset.
     *              DEFAULT: No encoding.
     *   - defserver: (string) The default domain to append to mailboxes.
     *                DEFAULT: No default name.
     *   - nowrap: (integer) Don't wrap the headers.
     *             DEFAULT: Headers are wrapped.
     *
     * @return string  The headers in string format.
     */
    public function toString(array $opts = array())
    {
        $eol = empty($opts['canonical'])
            ? $this->_eol
            : "\r\n";
        $text = '';

        foreach ($this->toArray($opts) as $key => $val) {
            if (!is_array($val)) {
                $val = array($val);
            }
            foreach ($val as $entry) {
                $text .= $key . ': ' . $entry . $eol;
            }
        }

        return $text . $eol;
    }

    /**
     * Generate the 'Received' header for the Web browser->Horde hop
     * (attempts to conform to guidelines in RFC 5321 [4.4]).
     *
     * @param array $opts  Additional opts:
     *   - dns: (Net_DNS2_Resolver) Use the DNS resolver object to lookup
     *          hostnames.
     *          DEFAULT: Use gethostbyaddr() function.
     *   - server: (string) Use this server name.
     *             DEFAULT: Auto-detect using current PHP values.
     */
    public function addReceivedHeader(array $opts = array())
    {
        $old_error = error_reporting(0);
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            /* This indicates the user is connecting through a proxy. */
            $remote_path = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $remote_addr = $remote_path[0];
            if (!empty($opts['dns'])) {
                $remote = $remote_addr;
                try {
                    if ($response = $opts['dns']->query($remote_addr, 'PTR')) {
                        foreach ($response->answer as $val) {
                            if (isset($val->ptrdname)) {
                                $remote = $val->ptrdname;
                                break;
                            }
                        }
                    }
                } catch (Net_DNS2_Exception $e) {}
            } else {
                $remote = gethostbyaddr($remote_addr);
            }
        } else {
            $remote_addr = $_SERVER['REMOTE_ADDR'];
            if (empty($_SERVER['REMOTE_HOST'])) {
                if (!empty($opts['dns'])) {
                    $remote = $remote_addr;
                    try {
                        if ($response = $opts['dns']->query($remote_addr, 'PTR')) {
                            foreach ($response->answer as $val) {
                                if (isset($val->ptrdname)) {
                                    $remote = $val->ptrdname;
                                    break;
                                }
                            }
                        }
                    } catch (Net_DNS2_Exception $e) {}
                } else {
                    $remote = gethostbyaddr($remote_addr);
                }
            } else {
                $remote = $_SERVER['REMOTE_HOST'];
            }
        }
        error_reporting($old_error);

        if (!empty($_SERVER['REMOTE_IDENT'])) {
            $remote_ident = $_SERVER['REMOTE_IDENT'] . '@' . $remote . ' ';
        } elseif ($remote != $_SERVER['REMOTE_ADDR']) {
            $remote_ident = $remote . ' ';
        } else {
            $remote_ident = '';
        }

        if (!empty($opts['server'])) {
            $server_name = $opts['server'];
        } elseif (!empty($_SERVER['SERVER_NAME'])) {
            $server_name = $_SERVER['SERVER_NAME'];
        } elseif (!empty($_SERVER['HTTP_HOST'])) {
            $server_name = $_SERVER['HTTP_HOST'];
        } else {
            $server_name = 'unknown';
        }

        $received = 'from ' . $remote . ' (' . $remote_ident .
            '[' . $remote_addr . ']) ' .
            'by ' . $server_name . ' (Horde Framework) with HTTP; ' .
            date('r');

        $this->addHeader('Received', $received);
    }

    /**
     * Generate the 'Message-ID' header.
     */
    public function addMessageIdHeader()
    {
        $this->addHeader('Message-ID', Horde_Mime::generateMessageId());
    }

    /**
     * Generate the user agent description header.
     */
    public function addUserAgentHeader()
    {
        $this->addHeader('User-Agent', $this->getUserAgent());
    }

    /**
     * Returns the user agent description header.
     *
     * @return string  The user agent header.
     */
    public function getUserAgent()
    {
        if (is_null($this->_agent)) {
            $this->_agent = 'Horde Application Framework 5';
        }
        return $this->_agent;
    }

    /**
     * Explicitly sets the User-Agent string.
     *
     * @param string $agent  The User-Agent string to use.
     */
    public function setUserAgent($agent)
    {
        $this->_agent = $agent;
    }

    /**
     * Add a header to the header array.
     *
     * @param string $header  The header name.
     * @param string $value   The header value (UTF-8).
     * @param array $opts     Additional options:
     *   - params: (array) MIME parameters for Content-Type or
     *             Content-Disposition.
     *             DEFAULT: None
     *   - sanity_check: (boolean) Do sanity-checking on header value?
     *                   DEFAULT: false
     */
    public function addHeader($header, $value, array $opts = array())
    {
        $header = trim($header);
        $lcHeader = Horde_String::lower($header);

        if (!isset($this->_headers[$lcHeader])) {
            $this->_headers[$lcHeader] = array(
                'h' => $header
            );
        }
        $ptr = &$this->_headers[$lcHeader];

        if (!empty($opts['sanity_check'])) {
            $value = $this->_sanityCheck($value);
        }

        // Fields defined in RFC 2822 that contain address information
        if (in_array($lcHeader, $this->addressFields())) {
            $rfc822 = new Horde_Mail_Rfc822();
            $addr_list = $rfc822->parseAddressList($value);

            switch ($lcHeader) {
            case 'bcc':
            case 'cc':
            case 'from':
            case 'to':
                /* Catch malformed undisclosed-recipients entries. */
                if ((count($addr_list) == 1) &&
                    preg_match("/^\s*undisclosed-recipients:?\s*$/i", $addr_list[0]->bare_address)) {
                    $addr_list = new Horde_Mail_Rfc822_List('undisclosed-recipients:;');
                }
                break;
            }
            $value = strval($addr_list);
        } else {
            $value = Horde_Mime::decode($value);
        }

        if (isset($ptr['v'])) {
            if (!is_array($ptr['v'])) {
                $ptr['v'] = array($ptr['v']);
            }
            $ptr['v'][] = $value;
        } else {
            $ptr['v'] = $value;
        }

        if (!empty($opts['params'])) {
            $ptr['p'] = $opts['params'];
        }
    }

    /**
     * Remove a header from the header array.
     *
     * @param string $header  The header name.
     */
    public function removeHeader($header)
    {
        unset($this->_headers[Horde_String::lower(trim($header))]);
    }

    /**
     * Replace a value of a header.
     *
     * @param string $header  The header name.
     * @param string $value   The header value.
     * @param array $opts     Additional options:
     *   - params: (array) MIME parameters for Content-Type or
     *             Content-Disposition.
     *             DEFAULT: None
     *   - sanity_check: (boolean) Do sanity-checking on header value?
     *                   DEFAULT: false
     */
    public function replaceHeader($header, $value, array $opts = array())
    {
        $this->removeHeader($header);
        $this->addHeader($header, $value, $opts);
    }

    /**
     * Attempts to return the header in the correct case.
     *
     * @param string $header  The header to search for.
     *
     * @return string  The value for the given header.
     *                 If the header is not found, returns null.
     */
    public function getString($header)
    {
        $lcHeader = Horde_String::lower($header);
        return (isset($this->_headers[$lcHeader]))
            ? $this->_headers[$lcHeader]['h']
            : null;
    }

    /**
     * Attempt to return the value for a given header.
     * The following header fields can only have 1 entry, so if duplicate
     * entries exist, the first value will be used:
     *   * To, From, Cc, Bcc, Date, Sender, Reply-to, Message-ID, In-Reply-To,
     *     References, Subject (RFC 2822 [3.6])
     *   * All List Headers (RFC 2369 [3])
     * The values are not MIME encoded.
     *
     * @param string $header  The header to search for.
     * @param integer $type   The type of return:
     *   - VALUE_STRING: Returns a string representation of the entire header.
     *   - VALUE_BASE: Returns a string representation of the base value of
     *                 the header. If this is not a header that allows
     *                 parameters, this will be equivalent to VALUE_STRING.
     *   - VALUE_PARAMS: Returns the list of parameters for this header. If
     *                   this is not a header that allows parameters, this
     *                   will be an empty array.
     *
     * @return mixed  The value for the given header.
     *                If the header is not found, returns null.
     */
    public function getValue($header, $type = self::VALUE_STRING)
    {
        $header = Horde_String::lower($header);

        if (!isset($this->_headers[$header])) {
            return null;
        }

        $ptr = &$this->_headers[$header];
        if (is_array($ptr['v']) &&
            in_array($header, $this->singleFields(true))) {
            if (in_array($header, $this->addressFields())) {
                $base = str_replace(';,', ';', implode(', ', $ptr['v']));
            } else {
                $base = $ptr['v'][0];
            }
        } else {
            $base = $ptr['v'];
        }
        $params = isset($ptr['p']) ? $ptr['p'] : array();

        switch ($type) {
        case self::VALUE_BASE:
            return $base;

        case self::VALUE_PARAMS:
            return $params;

        case self::VALUE_STRING:
            foreach ($params as $key => $val) {
                $base .= '; ' . $key . '=' . $val;
            }
            return $base;
        }
    }

    /**
     * Returns the list of RFC defined header fields that contain address
     * info.
     *
     * @return array  The list of headers, in lowercase.
     */
    static public function addressFields()
    {
        return array(
            'from', 'to', 'cc', 'bcc', 'reply-to', 'resent-to', 'resent-cc',
            'resent-bcc', 'resent-from', 'sender'
        );
    }

    /**
     * Returns the list of RFC defined header fields that can only contain
     * a single value.
     *
     * @param boolean $list  Return list-related headers also?
     *
     * @return array  The list of headers, in lowercase.
     */
    public function singleFields($list = true)
    {
        return $list
            ? array_merge($this->_singleFields, array_keys($this->listHeaders()))
            : $this->_singleFields;
    }

    /**
     * Returns the list of RFC defined MIME header fields that may contain
     * parameter info.
     *
     * @return array  The list of headers, in lowercase.
     */
    static public function mimeParamFields()
    {
        return array('content-type', 'content-disposition');
    }

    /**
     * Returns the list of valid mailing list headers.
     *
     * @deprecated  Use Horde_ListHeaders#headers() instead.
     *
     * @return array  The list of valid mailing list headers.
     */
    static public function listHeaders()
    {
        return array(
            /* RFC 2369 */
            'list-help'         =>  Horde_Mime_Translation::t("List-Help"),
            'list-unsubscribe'  =>  Horde_Mime_Translation::t("List-Unsubscribe"),
            'list-subscribe'    =>  Horde_Mime_Translation::t("List-Subscribe"),
            'list-owner'        =>  Horde_Mime_Translation::t("List-Owner"),
            'list-post'         =>  Horde_Mime_Translation::t("List-Post"),
            'list-archive'      =>  Horde_Mime_Translation::t("List-Archive"),
            /* RFC 2919 */
            'list-id'           =>  Horde_Mime_Translation::t("List-Id")
        );
    }

    /**
     * Do any mailing list headers exist?
     *
     * @return boolean  True if any mailing list headers exist.
     */
    public function listHeadersExist()
    {
        return (bool)count(array_intersect(array_keys($this->listHeaders()), array_keys($this->_headers)));
    }

    /**
     * Sets a new string to use for EOLs.
     *
     * @param string $eol  The string to use for EOLs.
     */
    public function setEOL($eol)
    {
        $this->_eol = $eol;
    }

    /**
     * Get the string to use for EOLs.
     *
     * @return string  The string to use for EOLs.
     */
    public function getEOL()
    {
        return $this->_eol;
    }

    /**
     * Returns an address object for a header.
     *
     * @param string $field  The header to return as an object.
     *
     * @return Horde_Mail_Rfc822_List  The object for the requested field.
     *                                 Returns null if field doesn't exist.
     */
    public function getOb($field)
    {
        if (($value = $this->getValue($field)) === null) {
            return null;
        }

        $rfc822 = new Horde_Mail_Rfc822();
        return $rfc822->parseAddressList($value);
    }

    /**
     * Perform sanity checking on a raw header (e.g. handle 8-bit characters).
     *
     * @param string $data  The header data.
     *
     * @return string  The cleaned header data.
     */
    protected function _sanityCheck($data)
    {
        $charset_test = array(
            'windows-1252',
            self::$defaultCharset
        );

        if (!Horde_String::validUtf8($data)) {
            /* Appears to be a PHP error with the internal String structure
             * which prevents accurate manipulation of the string. Copying
             * the data to a new variable fixes things. */
            $data = substr($data, 0);

            /* Assumption: broken charset in headers is generally either
             * UTF-8 or ISO-8859-1/Windows-1252. Test these charsets
             * first before using default charset. This may be a
             * Western-centric approach, but it's better than nothing. */
            foreach ($charset_test as $charset) {
                $tmp = Horde_String::convertCharset($data, $charset, 'UTF-8');
                if (Horde_String::validUtf8($tmp)) {
                    return $tmp;
                }
            }
        }

        return $data;
    }

    /* Static methods. */

    /**
     * Builds a Horde_Mime_Headers object from header text.
     *
     * @param mixed $text  A text string (or, as of 2.3.0, a Horde_Stream
     *                     object or stream resource) containing the headers.
     *
     * @return Horde_Mime_Headers  A new Horde_Mime_Headers object.
     */
    static public function parseHeaders($text)
    {
        $currheader = $currtext = null;
        $mime = self::mimeParamFields();
        $to_process = array();

        if ($text instanceof Horde_Stream) {
            $stream = $text;
            $stream->rewind();
        } else {
            $stream = new Horde_Stream_Temp();
            $stream->add($text, true);
        }

        while (!$stream->eof()) {
            if (!($val = rtrim($stream->getToChar("\n", false), "\r"))) {
                break;
            }

            if (($val[0] == ' ') || ($val[0] == "\t")) {
                $currtext .= ' ' . ltrim($val);
            } else {
                if (!is_null($currheader)) {
                    $to_process[] = array($currheader, rtrim($currtext));
                }

                $pos = strpos($val, ':');
                $currheader = substr($val, 0, $pos);
                $currtext = ltrim(substr($val, $pos + 1));
            }
        }

        if (!is_null($currheader)) {
            $to_process[] = array($currheader, $currtext);
        }

        $headers = new Horde_Mime_Headers();

        reset($to_process);
        while (list(,$val) = each($to_process)) {
            /* Ignore empty headers. */
            if (!strlen($val[1])) {
                continue;
            }

            if (in_array(Horde_String::lower($val[0]), $mime)) {
                $res = Horde_Mime::decodeParam($val[0], $val[1]);
                $headers->addHeader($val[0], $res['val'], array(
                    'params' => $res['params'],
                    'sanity_check' => true
                ));
            } else {
                $headers->addHeader($val[0], $val[1], array(
                    'sanity_check' => true
                ));
            }
        }

        return $headers;
    }

    /* Serializable methods. */

    /**
     * Serialization.
     *
     * @return string  Serialized data.
     */
    public function serialize()
    {
        $data = array(
            // Serialized data ID.
            self::VERSION,
            $this->_headers,
            $this->_eol
        );

        if (!is_null($this->_agent)) {
            $data[] = $this->_agent;
        }

        return serialize($data);
    }

    /**
     * Unserialization.
     *
     * @param string $data  Serialized data.
     *
     * @throws Exception
     */
    public function unserialize($data)
    {
        $data = @unserialize($data);
        if (!is_array($data) ||
            !isset($data[0]) ||
            ($data[0] != self::VERSION)) {
            throw new Horde_Mime_Exception('Cache version change');
        }

        $this->_headers = $data[1];
        $this->_eol = $data[2];
        if (isset($data[3])) {
            $this->_agent = $data[3];
        }
    }

}
