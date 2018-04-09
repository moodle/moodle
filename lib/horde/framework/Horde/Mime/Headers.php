<?php
/**
 * Copyright 2002-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2002-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 */

/**
 * This class represents the collection of header values for a single mail
 * message part.
 *
 * It supports the base e-mail spec (RFC 5322) and the MIME extensions to that
 * spec (RFC 2045).
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2002-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 */
class Horde_Mime_Headers
implements ArrayAccess, IteratorAggregate, Serializable
{
    /* Serialized version. */
    const VERSION = 3;

    /**
     * The default charset to use when parsing text parts with no charset
     * information.
     *
     * @todo Make this a non-static property or pass as parameter to static
     *       methods in Horde 6.
     * @var string
     */
    public static $defaultCharset = 'us-ascii';

    /**
     * Cached handler information for Header Element objects.
     *
     * @var array
     */
    protected static $_handlers = array();

    /**
     * The internal headers array.
     *
     * @var Horde_Support_CaseInsensitiveArray
     */
    protected $_headers;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->_headers = new Horde_Support_CaseInsensitiveArray();
    }

    /**
     */
    public function __clone()
    {
        $copy = new Horde_Support_CaseInsensitiveArray();
        foreach ($this->_headers as $key => $val) {
            $copy[$key] = clone $val;
        }
        $this->_headers = $copy;
    }

    /**
     * Returns the headers in array format.
     *
     * @param array $opts  Optional parameters:
     * <pre>
     *   - broken_rfc2231: (boolean) Attempt to work around non-RFC
     *                     2231-compliant MUAs by generating both a RFC
     *                     2047-like parameter name and also the correct RFC
     *                     2231 parameter
     *                     DEFAULT: false
     *   - canonical: (boolean) Use canonical (RFC 822/2045) CRLF EOLs?
     *                DEFAULT: Uses "\n"
     *   - charset: (string) Encodes the headers using this charset. If empty,
     *              encodes using UTF-8.
     *              DEFAULT: No encoding.
     *   - defserver: (string) The default domain to append to mailboxes.
     *                DEFAULT: No default name.
     *   - lang: (string) The language to use when encoding.
     *           DEFAULT: None specified
     *   - nowrap: (integer) Don't wrap the headers.
     *             DEFAULT: Headers are wrapped.
     * </pre>
     *
     * @return array  The headers in array format. Keys are header names, but
     *                case sensitivity cannot be guaranteed. Values are
     *                header values.
     */
    public function toArray(array $opts = array())
    {
        $charset = array_key_exists('charset', $opts)
            ? (empty($opts['charset']) ? 'UTF-8' : $opts['charset'])
            : null;
        $eol = empty($opts['canonical'])
            ? $this->_eol
            : "\r\n";
        $ret = array();

        foreach ($this->_headers as $ob) {
            $sopts = array(
                'charset' => $charset
            );

            if (($ob instanceof Horde_Mime_Headers_Addresses) ||
                ($ob instanceof Horde_Mime_Headers_AddressesMulti)) {
                if (!empty($opts['defserver'])) {
                    $sopts['defserver'] = $opts['defserver'];
                }
            } elseif ($ob instanceof Horde_Mime_Headers_ContentParam) {
                $sopts['broken_rfc2231'] = !empty($opts['broken_rfc2231']);
                if (!empty($opts['lang'])) {
                    $sopts['lang'] = $opts['lang'];
                }
            }

            $tmp = array();

            foreach ($ob->sendEncode(array_filter($sopts)) as $val) {
                if (empty($opts['nowrap'])) {
                    /* Remove any existing linebreaks and wrap the line. */
                    $htext = $ob->name . ': ';
                    $val = ltrim(
                        substr(
                            wordwrap(
                                $htext . strtr(trim($val), array("\r" => '', "\n" => '')),
                                76,
                                $eol . ' '
                            ),
                            strlen($htext)
                        )
                    );
                }

                $tmp[] = $val;
            }

            $ret[$ob->name] = (count($tmp) == 1)
                ? reset($tmp)
                : $tmp;
        }

        return $ret;
    }

    /**
     * Returns all headers concatenated into a single string.
     *
     * @param array $opts  See toArray().
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
            foreach ((is_array($val) ? $val : array($val)) as $entry) {
                $text .= $key . ': ' . $entry . $eol;
            }
        }

        return $text . $eol;
    }

    /**
     * Add/append/replace a header.
     *
     * @param string $header  The header name.
     * @param string $value   The header value (UTF-8).
     * @param array $opts     DEPRECATED
     */
    public function addHeader($header, $value, array $opts = array())
    {
        /* Existing header? Add to that object. */
        $header = trim($header);
        if ($hdr = $this[$header]) {
            $hdr->setValue($value);
            return;
        }

        $classname = $this->_getHeaderClassName($header);

        try {
            $ob = new $classname($header, $value);
        } catch (InvalidArgumentException $e) {
            /* Ignore an invalid header. */
            return;
        } catch (Horde_Mime_Exception $e) {
            return;
        }

        switch ($classname) {
        case 'Horde_Mime_Headers_ContentParam_ContentDisposition':
        case 'Horde_Mime_Headers_ContentParam_ContentType':
            /* BC */
            if (!empty($opts['params'])) {
                foreach ($opts['params'] as $key => $val) {
                    $ob[$key] = $val;
                }
            }
            break;
        }

        $this->_headers[$ob->name] = $ob;
    }

    /**
     * Add a Horde_Mime_Headers_Element object to the current header list.
     *
     * @since 2.5.0
     *
     * @param Horde_Mime_Headers_Element $ob  Header object to add.
     * @param boolean $check                  Check that the header and object
     *                                        type match?
     *
     * @throws InvalidArgumentException
     */
    public function addHeaderOb(Horde_Mime_Headers_Element $ob, $check = false)
    {
        if ($check) {
            $cname = $this->_getHeaderClassName($ob->name);
            if (!($ob instanceof $cname)) {
                throw new InvalidArgumentException(sprintf(
                    'Object is not correct class: %s',
                    $cname
                ));
            }
        }

        /* Existing header? Add to that object. */
        if ($hdr = $this[$ob->name]) {
            $hdr->setValue($ob);
        } else {
            $this->_headers[$ob->name] = $ob;
        }
    }

    /**
     * Return the header class to use for a header name.
     *
     * @param string $header  The header name.
     *
     * @return string  The Horde_Mime_Headers_* class to use.
     */
    protected function _getHeaderClassName($header)
    {
        if (empty(self::$_handlers)) {
            $search = array(
                'Horde_Mime_Headers_Element_Single',
                'Horde_Mime_Headers_AddressesMulti',
                'Horde_Mime_Headers_Addresses',
                'Horde_Mime_Headers_ContentDescription',
                'Horde_Mime_Headers_ContentId',
                'Horde_Mime_Headers_ContentLanguage',
                'Horde_Mime_Headers_ContentParam_ContentDisposition',
                'Horde_Mime_Headers_ContentParam_ContentType',
                'Horde_Mime_Headers_ContentTransferEncoding',
                'Horde_Mime_Headers_Date',
                'Horde_Mime_Headers_Identification',
                'Horde_Mime_Headers_MessageId',
                'Horde_Mime_Headers_Mime',
                'Horde_Mime_Headers_MimeVersion',
                'Horde_Mime_Headers_Received',
                'Horde_Mime_Headers_Subject',
                'Horde_Mime_Headers_UserAgent'
            );

            foreach ($search as $val) {
                foreach ($val::getHandles() as $hdr) {
                    self::$_handlers[$hdr] = $val;
                }
            }
        }

        $header = Horde_String::lower($header);

        return isset(self::$_handlers[$header])
            ? self::$_handlers[$header]
            : 'Horde_Mime_Headers_Element_Multiple';
    }

    /**
     * Get a header from the header array.
     *
     * @param string $header  The header name.
     *
     * @return Horde_Mime_Headers_Element  Element object, or null if not
     *                                     found.
     */
    public function getHeader($header)
    {
        return $this[$header];
    }

    /**
     * Remove a header from the header array.
     *
     * @param string $header  The header name.
     */
    public function removeHeader($header)
    {
        unset($this[$header]);
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
    public static function parseHeaders($text)
    {
        $curr = null;
        $headers = new Horde_Mime_Headers();
        $hdr_list = array();

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

            if ($curr && (($val[0] == ' ') || ($val[0] == "\t"))) {
                $curr->text .= ' ' . ltrim($val);
            } else {
                $pos = strpos($val, ':');

                $curr = new stdClass;
                $curr->header = substr($val, 0, $pos);
                $curr->text = ltrim(substr($val, $pos + 1));

                $hdr_list[] = $curr;
            }
        }

        foreach ($hdr_list as $val) {
            /* When parsing, only keep the FIRST header seen for single value
             * text-only headers, since newer headers generally are appended
             * to the top of the message. */
            if (!($ob = $headers[$val->header]) ||
                !($ob instanceof Horde_Mime_Headers_Element_Single) ||
                ($ob instanceof Horde_Mime_Headers_Addresses)) {
                $headers->addHeader($val->header, rtrim($val->text));
            }
        }

        if (!($text instanceof Horde_Stream)) {
            $stream->close();
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
            $this->_headers->getArrayCopy(),
            // TODO: BC
            $this->_eol
        );

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

        $this->_headers = new Horde_Support_CaseInsensitiveArray($data[1]);
        // TODO: BC
        $this->_eol = $data[2];
    }

    /* ArrayAccess methods. */

    /**
     * Does header exist?
     *
     * @since 2.5.0
     *
     * @param string $header  Header name.
     *
     * @return boolean  True if header exists.
     */
    public function offsetExists($offset)
    {
        return isset($this->_headers[trim($offset)]);
    }

    /**
     * Return header element object.
     *
     * @since 2.5.0
     *
     * @param string $header  Header name.
     *
     * @return Horde_Mime_Headers_Element  Element object, or null if not
     *                                     found.
     */
    public function offsetGet($offset)
    {
        return $this->_headers[trim($offset)];
    }

    /**
     * Store a header element object.
     *
     * @since 2.5.0
     *
     * @param string $offset                   Not used.
     * @param Horde_Mime_Headers_Element $elt  Header element.
     */
    public function offsetSet($offset, $value)
    {
        $this->addHeaderOb($value);
    }

    /**
     * Remove a header element object.
     *
     * @since 2.5.0
     *
     * @param string $offset  Header name.
     */
    public function offsetUnset($offset)
    {
        unset($this->_headers[trim($offset)]);
    }

    /* IteratorAggregate function */

    /**
     * @since 2.5.0
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_headers);
    }

    /* Deprecated functions */

    /**
     * Handle deprecated methods.
     */
    public function __call($name, $arguments)
    {
        $d = new Horde_Mime_Headers_Deprecated($this);
        return call_user_func_array(array($d, $name), $arguments);
    }

    /**
     * Handle deprecated static methods.
     */
    public static function __callStatic($name, $arguments)
    {
        $d = new Horde_Mime_Headers_Deprecated();
        return call_user_func_array(array($d, $name), $arguments);
    }

    /**
     * @deprecated
     */
    protected $_eol = "\n";

    /**
     * @deprecated
     */
    public function setEOL($eol)
    {
        $this->_eol = $eol;
    }

    /**
     * @deprecated
     */
    public function getEOL()
    {
        return $this->_eol;
    }

    /* Constants for getValue(). @deprecated */
    const VALUE_STRING = 1;
    const VALUE_BASE = 2;
    const VALUE_PARAMS = 3;

}
