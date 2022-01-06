<?php
/**
 * Copyright 1999-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 1999-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 */

/**
 * Object-oriented representation of a MIME part (RFC 2045-2049).
 *
 * @author    Chuck Hagenbuch <chuck@horde.org>
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 1999-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 */
class Horde_Mime_Part
implements ArrayAccess, Countable, RecursiveIterator, Serializable
{
    /* Serialized version. */
    const VERSION = 2;

    /* The character(s) used internally for EOLs. */
    const EOL = "\n";

    /* The character string designated by RFC 2045 to designate EOLs in MIME
     * messages. */
    const RFC_EOL = "\r\n";

    /* The default encoding. */
    const DEFAULT_ENCODING = 'binary';

    /* Constants indicating the valid transfer encoding allowed. */
    const ENCODE_7BIT = 1;
    const ENCODE_8BIT = 2;
    const ENCODE_BINARY = 4;

    /* MIME nesting limit. */
    const NESTING_LIMIT = 100;

    /* Status mask value: Need to reindex the current part. */
    const STATUS_REINDEX = 1;
    /* Status mask value: This is the base MIME part. */
    const STATUS_BASEPART = 2;

    /**
     * The default charset to use when parsing text parts with no charset
     * information.
     *
     * @todo Make this a non-static property or pass as parameter to static
     *       methods in Horde 6.
     *
     * @var string
     */
    public static $defaultCharset = 'us-ascii';

    /**
     * The memory limit for use with the PHP temp stream.
     *
     * @var integer
     */
    public static $memoryLimit = 2097152;

    /**
     * Parent object. Value only accurate when iterating.
     *
     * @since 2.8.0
     *
     * @var Horde_Mime_Part
     */
    public $parent = null;

    /**
     * Default value for this Part's size.
     *
     * @var integer
     */
    protected $_bytes;

    /**
     * The body of the part. Always stored in binary format.
     *
     * @var resource
     */
    protected $_contents;

    /**
     * The sequence to use as EOL for this part.
     *
     * The default is currently to output the EOL sequence internally as
     * just "\n" instead of the canonical "\r\n" required in RFC 822 & 2045.
     * To be RFC complaint, the full <CR><LF> EOL combination should be used
     * when sending a message.
     *
     * @var string
     */
    protected $_eol = self::EOL;

    /**
     * The MIME headers for this part.
     *
     * @var Horde_Mime_Headers
     */
    protected $_headers;

    /**
     * The charset to output the headers in.
     *
     * @var string
     */
    protected $_hdrCharset = null;

    /**
     * Metadata.
     *
     * @var array
     */
    protected $_metadata = array();

    /**
     * The MIME ID of this part.
     *
     * @var string
     */
    protected $_mimeid = null;

    /**
     * The subparts of this part.
     *
     * @var array
     */
    protected $_parts = array();

    /**
     * Status mask for this part.
     *
     * @var integer
     */
    protected $_status = 0;

    /**
     * Temporary array.
     *
     * @var array
     */
    protected $_temp = array();

    /**
     * The desired transfer encoding of this part.
     *
     * @var string
     */
    protected $_transferEncoding = self::DEFAULT_ENCODING;

    /**
     * Flag to detect if a message failed to send at least once.
     *
     * @var boolean
     */
    protected $_failed = false;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->_headers = new Horde_Mime_Headers();

        /* Mandatory MIME headers. */
        $this->_headers->addHeaderOb(
            new Horde_Mime_Headers_ContentParam_ContentDisposition(null, '')
        );

        $ct = Horde_Mime_Headers_ContentParam_ContentType::create();
        $ct['charset'] = self::$defaultCharset;
        $this->_headers->addHeaderOb($ct);
    }

    /**
     * Function to run on clone.
     */
    public function __clone()
    {
        foreach ($this->_parts as $k => $v) {
            $this->_parts[$k] = clone $v;
        }

        $this->_headers = clone $this->_headers;

        if (!empty($this->_contents)) {
            $this->_contents = $this->_writeStream($this->_contents);
        }
    }

    /**
     * Set the content-disposition of this part.
     *
     * @param string $disposition  The content-disposition to set ('inline',
     *                             'attachment', or an empty value).
     */
    public function setDisposition($disposition = null)
    {
        $this->_headers['content-disposition']->setContentParamValue(
            strval($disposition)
        );
    }

    /**
     * Get the content-disposition of this part.
     *
     * @return string  The part's content-disposition.  An empty string means
     *                 no desired disposition has been set for this part.
     */
    public function getDisposition()
    {
        return $this->_headers['content-disposition']->value;
    }

    /**
     * Add a disposition parameter to this part.
     *
     * @param string $label  The disposition parameter label.
     * @param string $data   The disposition parameter data. If null, removes
     *                       the parameter (@since 2.8.0).
     */
    public function setDispositionParameter($label, $data)
    {
        $cd = $this->_headers['content-disposition'];

        if (is_null($data)) {
            unset($cd[$label]);
        } elseif (strlen($data)) {
            $cd[$label] = $data;

            if (strcasecmp($label, 'size') === 0) {
                // RFC 2183 [2.7] - size parameter
                $this->_bytes = $cd[$label];
            } elseif ((strcasecmp($label, 'filename') === 0) &&
                      !strlen($cd->value)) {
                /* Set part to attachment if not already explicitly set to
                 * 'inline'. */
                $cd->setContentParamValue('attachment');
            }
        }
    }

    /**
     * Get a disposition parameter from this part.
     *
     * @param string $label  The disposition parameter label.
     *
     * @return string  The data requested.
     *                 Returns null if $label is not set.
     */
    public function getDispositionParameter($label)
    {
        $cd = $this->_headers['content-disposition'];
        return $cd[$label];
    }

    /**
     * Get all parameters from the Content-Disposition header.
     *
     * @return array  An array of all the parameters
     *                Returns the empty array if no parameters set.
     */
    public function getAllDispositionParameters()
    {
        return $this->_headers['content-disposition']->params;
    }

    /**
     * Set the name of this part.
     *
     * @param string $name  The name to set.
     */
    public function setName($name)
    {
        $this->setDispositionParameter('filename', $name);
        $this->setContentTypeParameter('name', $name);
    }

    /**
     * Get the name of this part.
     *
     * @param boolean $default  If the name parameter doesn't exist, should we
     *                          use the default name from the description
     *                          parameter?
     *
     * @return string  The name of the part.
     */
    public function getName($default = false)
    {
        if (!($name = $this->getDispositionParameter('filename')) &&
            !($name = $this->getContentTypeParameter('name')) &&
            $default) {
            $name = preg_replace('|\W|', '_', $this->getDescription(false));
        }

        return $name;
    }

    /**
     * Set the body contents of this part.
     *
     * @param mixed $contents  The part body. Either a string or a stream
     *                         resource, or an array containing both.
     * @param array $options   Additional options:
     *   - encoding: (string) The encoding of $contents.
     *               DEFAULT: Current transfer encoding value.
     *   - usestream: (boolean) If $contents is a stream, should we directly
     *                use that stream?
     *                DEFAULT: $contents copied to a new stream.
     */
    public function setContents($contents, $options = array())
    {
        if (is_resource($contents) && ($contents === $this->_contents)) {
            return;
        }

        if (empty($options['encoding'])) {
            $options['encoding'] = $this->_transferEncoding;
        }

        $fp = (empty($options['usestream']) || !is_resource($contents))
            ? $this->_writeStream($contents)
            : $contents;

        /* Properly close the existing stream. */
        $this->clearContents();

        $this->setTransferEncoding($options['encoding']);
        $this->_contents = $this->_transferDecode($fp, $options['encoding']);
    }

    /**
     * Add to the body contents of this part.
     *
     * @param mixed $contents  The part body. Either a string or a stream
     *                         resource, or an array containing both.
     *   - encoding: (string) The encoding of $contents.
     *               DEFAULT: Current transfer encoding value.
     *   - usestream: (boolean) If $contents is a stream, should we directly
     *                use that stream?
     *                DEFAULT: $contents copied to a new stream.
     */
    public function appendContents($contents, $options = array())
    {
        if (empty($this->_contents)) {
            $this->setContents($contents, $options);
        } else {
            $fp = (empty($options['usestream']) || !is_resource($contents))
                ? $this->_writeStream($contents)
                : $contents;

            $this->_writeStream((empty($options['encoding']) || ($options['encoding'] == $this->_transferEncoding)) ? $fp : $this->_transferDecode($fp, $options['encoding']), array('fp' => $this->_contents));
            unset($this->_temp['sendTransferEncoding']);
        }
    }

    /**
     * Clears the body contents of this part.
     */
    public function clearContents()
    {
        if (!empty($this->_contents)) {
            fclose($this->_contents);
            $this->_contents = null;
            unset($this->_temp['sendTransferEncoding']);
        }
    }

    /**
     * Return the body of the part.
     *
     * @param array $options  Additional options:
     *   - canonical: (boolean) Returns the contents in strict RFC 822 &
     *                2045 output - namely, all newlines end with the
     *                canonical <CR><LF> sequence.
     *                DEFAULT: No
     *   - stream: (boolean) Return the body as a stream resource.
     *             DEFAULT: No
     *
     * @return mixed  The body text (string) of the part, null if there is no
     *                contents, and a stream resource if 'stream' is true.
     */
    public function getContents($options = array())
    {
        return empty($options['canonical'])
            ? (empty($options['stream']) ? $this->_readStream($this->_contents) : $this->_contents)
            : $this->replaceEOL($this->_contents, self::RFC_EOL, !empty($options['stream']));
    }

    /**
     * Decodes the contents of the part to binary encoding.
     *
     * @param resource $fp      A stream containing the data to decode.
     * @param string $encoding  The original file encoding.
     *
     * @return resource  A new file resource with the decoded data.
     */
    protected function _transferDecode($fp, $encoding)
    {
        /* If the contents are empty, return now. */
        fseek($fp, 0, SEEK_END);
        if (ftell($fp)) {
            switch ($encoding) {
            case 'base64':
                try {
                    return $this->_writeStream($fp, array(
                        'error' => true,
                        'filter' => array(
                            'convert.base64-decode' => array()
                        )
                    ));
                } catch (ErrorException $e) {}

                rewind($fp);
                return $this->_writeStream(base64_decode(stream_get_contents($fp)));

            case 'quoted-printable':
                try {
                    return $this->_writeStream($fp, array(
                        'error' => true,
                        'filter' => array(
                            'convert.quoted-printable-decode' => array()
                        )
                    ));
                } catch (ErrorException $e) {}

                // Workaround for Horde Bug #8747
                rewind($fp);
                return $this->_writeStream(quoted_printable_decode(stream_get_contents($fp)));

            case 'uuencode':
            case 'x-uuencode':
            case 'x-uue':
                /* Support for uuencoded encoding - although not required by
                 * RFCs, some mailers may still encode this way. */
                $res = Horde_Mime::uudecode($this->_readStream($fp));
                return $this->_writeStream($res[0]['data']);
            }
        }

        return $fp;
    }

    /**
     * Encodes the contents of the part as necessary for transport.
     *
     * @param resource $fp      A stream containing the data to encode.
     * @param string $encoding  The encoding to use.
     *
     * @return resource  A new file resource with the encoded data.
     */
    protected function _transferEncode($fp, $encoding)
    {
        $this->_temp['transferEncodeClose'] = true;

        switch ($encoding) {
        case 'base64':
            /* Base64 Encoding: See RFC 2045, section 6.8 */
            return $this->_writeStream($fp, array(
                'filter' => array(
                    'convert.base64-encode' => array(
                        'line-break-chars' => $this->getEOL(),
                        'line-length' => 76
                    )
                )
            ));

        case 'quoted-printable':
            // PHP Bug 65776 - Must normalize the EOL characters.
            stream_filter_register('horde_eol', 'Horde_Stream_Filter_Eol');
            $stream = new Horde_Stream_Existing(array(
                'stream' => $fp
            ));
            $stream->stream = $this->_writeStream($stream->stream, array(
                'filter' => array(
                    'horde_eol' => array('eol' => $stream->getEOL()
                )
            )));

            /* Quoted-Printable Encoding: See RFC 2045, section 6.7 */
            return $this->_writeStream($fp, array(
                'filter' => array(
                    'convert.quoted-printable-encode' => array_filter(array(
                        'line-break-chars' => $stream->getEOL(),
                        'line-length' => 76
                    ))
                )
            ));

        default:
            $this->_temp['transferEncodeClose'] = false;
            return $fp;
        }
    }

    /**
     * Set the MIME type of this part.
     *
     * @param string $type  The MIME type to set (ex.: text/plain).
     */
    public function setType($type)
    {
        /* RFC 2045: Any entity with unrecognized encoding must be treated
         * as if it has a Content-Type of "application/octet-stream"
         * regardless of what the Content-Type field actually says. */
        if (!is_null($this->_transferEncoding)) {
            $this->_headers['content-type']->setContentParamValue($type);
        }
    }

     /**
      * Get the full MIME Content-Type of this part.
      *
      * @param boolean $charset  Append character set information to the end
      *                          of the content type if this is a text/* part?
      *`
      * @return string  The MIME type of this part.
      */
    public function getType($charset = false)
    {
        $ct = $this->_headers['content-type'];

        return $charset
            ? $ct->type_charset
            : $ct->value;
    }

    /**
     * If the subtype of a MIME part is unrecognized by an application, the
     * default type should be used instead (See RFC 2046).  This method
     * returns the default subtype for a particular primary MIME type.
     *
     * @return string  The default MIME type of this part (ex.: text/plain).
     */
    public function getDefaultType()
    {
        switch ($this->getPrimaryType()) {
        case 'text':
            /* RFC 2046 (4.1.4): text parts default to text/plain. */
            return 'text/plain';

        case 'multipart':
            /* RFC 2046 (5.1.3): multipart parts default to multipart/mixed. */
            return 'multipart/mixed';

        default:
            /* RFC 2046 (4.2, 4.3, 4.4, 4.5.3, 5.2.4): all others default to
               application/octet-stream. */
            return 'application/octet-stream';
        }
    }

    /**
     * Get the primary type of this part.
     *
     * @return string  The primary MIME type of this part.
     */
    public function getPrimaryType()
    {
        return $this->_headers['content-type']->ptype;
    }

    /**
     * Get the subtype of this part.
     *
     * @return string  The MIME subtype of this part.
     */
    public function getSubType()
    {
        return $this->_headers['content-type']->stype;
    }

    /**
     * Set the character set of this part.
     *
     * @param string $charset  The character set of this part.
     */
    public function setCharset($charset)
    {
        $this->setContentTypeParameter('charset', $charset);
    }

    /**
     * Get the character set to use for this part.
     *
     * @return string  The character set of this part (lowercase). Returns
     *                 null if there is no character set.
     */
    public function getCharset()
    {
        return $this->getContentTypeParameter('charset')
            ?: (($this->getPrimaryType() === 'text') ? 'us-ascii' : null);
    }

    /**
     * Set the character set to use when outputting MIME headers.
     *
     * @param string $charset  The character set.
     */
    public function setHeaderCharset($charset)
    {
        $this->_hdrCharset = $charset;
    }

    /**
     * Get the character set to use when outputting MIME headers.
     *
     * @return string  The character set. If no preferred character set has
     *                 been set, returns null.
     */
    public function getHeaderCharset()
    {
        return is_null($this->_hdrCharset)
            ? $this->getCharset()
            : $this->_hdrCharset;
    }

    /**
     * Set the language(s) of this part.
     *
     * @param mixed $lang  A language string, or an array of language
     *                     strings.
     */
    public function setLanguage($lang)
    {
        $this->_headers->addHeaderOb(
            new Horde_Mime_Headers_ContentLanguage('', $lang)
        );
    }

    /**
     * Get the language(s) of this part.
     *
     * @param array  The list of languages.
     */
    public function getLanguage()
    {
        return $this->_headers['content-language']->langs;
    }

    /**
     * Set the content duration of the data contained in this part (see RFC
     * 3803).
     *
     * @param integer $duration  The duration of the data, in seconds. If
     *                           null, clears the duration information.
     */
    public function setDuration($duration)
    {
        if (is_null($duration)) {
            unset($this->_headers['content-duration']);
        } else {
            if (!($hdr = $this->_headers['content-duration'])) {
                $hdr = new Horde_Mime_Headers_Element_Single(
                    'Content-Duration',
                    ''
                );
                $this->_headers->addHeaderOb($hdr);
            }
            $hdr->setValue($duration);
        }
    }

    /**
     * Get the content duration of the data contained in this part (see RFC
     * 3803).
     *
     * @return integer  The duration of the data, in seconds. Returns null if
     *                  there is no duration information.
     */
    public function getDuration()
    {
        return ($hdr = $this->_headers['content-duration'])
            ? intval($hdr->value)
            : null;
    }

    /**
     * Set the description of this part.
     *
     * @param string $description  The description of this part. If null,
     *                             deletes the description (@since 2.8.0).
     */
    public function setDescription($description)
    {
        if (is_null($description)) {
            unset($this->_headers['content-description']);
        } else {
            if (!($hdr = $this->_headers['content-description'])) {
                $hdr = new Horde_Mime_Headers_ContentDescription(null, '');
                $this->_headers->addHeaderOb($hdr);
            }
            $hdr->setValue($description);
        }
    }

    /**
     * Get the description of this part.
     *
     * @param boolean $default  If the description parameter doesn't exist,
     *                          should we use the name of the part?
     *
     * @return string  The description of this part.
     */
    public function getDescription($default = false)
    {
        if (($ob = $this->_headers['content-description']) &&
            strlen($ob->value)) {
            return $ob->value;
        }

        return $default
            ? $this->getName()
            : '';
    }

    /**
     * Set the transfer encoding to use for this part.
     *
     * Only needed in the following circumstances:
     * 1.) Indicate what the transfer encoding is if the data has not yet been
     * set in the object (can only be set if there presently are not
     * any contents).
     * 2.) Force the encoding to a certain type on a toString() call (if
     * 'send' is true).
     *
     * @param string $encoding  The transfer encoding to use.
     * @param array $options    Additional options:
     *   - send: (boolean) If true, use $encoding as the sending encoding.
     *           DEFAULT: $encoding is used to change the base encoding.
     */
    public function setTransferEncoding($encoding, $options = array())
    {
        if (empty($encoding) ||
            (empty($options['send']) && !empty($this->_contents))) {
            return;
        }

        switch ($encoding = Horde_String::lower($encoding)) {
        case '7bit':
        case '8bit':
        case 'base64':
        case 'binary':
        case 'quoted-printable':
        // Non-RFC types, but old mailers may still use
        case 'uuencode':
        case 'x-uuencode':
        case 'x-uue':
            if (empty($options['send'])) {
                $this->_transferEncoding = $encoding;
            } else {
                $this->_temp['sendEncoding'] = $encoding;
            }
            break;

        default:
            if (empty($options['send'])) {
                /* RFC 2045: Any entity with unrecognized encoding must be
                 * treated as if it has a Content-Type of
                 * "application/octet-stream" regardless of what the
                 * Content-Type field actually says. */
                $this->setType('application/octet-stream');
                $this->_transferEncoding = null;
            }
            break;
        }
    }

    /**
     * Get a list of all MIME subparts.
     *
     * @return array  An array of the Horde_Mime_Part subparts.
     */
    public function getParts()
    {
        return $this->_parts;
    }

    /**
     * Add/remove a content type parameter to this part.
     *
     * @param string $label  The content-type parameter label.
     * @param string $data   The content-type parameter data. If null, removes
     *                       the parameter (@since 2.8.0).
     */
    public function setContentTypeParameter($label, $data)
    {
        $ct = $this->_headers['content-type'];

        if (is_null($data)) {
            unset($ct[$label]);
        } elseif (strlen($data)) {
            $ct[$label] = $data;
        }
    }

    /**
     * Get a content type parameter from this part.
     *
     * @param string $label  The content type parameter label.
     *
     * @return string  The data requested.
     *                 Returns null if $label is not set.
     */
    public function getContentTypeParameter($label)
    {
        $ct = $this->_headers['content-type'];
        return $ct[$label];
    }

    /**
     * Get all parameters from the Content-Type header.
     *
     * @return array  An array of all the parameters
     *                Returns the empty array if no parameters set.
     */
    public function getAllContentTypeParameters()
    {
        return $this->_headers['content-type']->params;
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
     * Returns a Horde_Mime_Header object containing all MIME headers needed
     * for the part.
     *
     * @param array $options  Additional options:
     *   - encode: (integer) A mask of allowable encodings.
     *             DEFAULT: Auto-determined
     *   - headers: (Horde_Mime_Headers) The object to add the MIME headers
     *              to.
     *              DEFAULT: Add headers to a new object
     *
     * @return Horde_Mime_Headers  A Horde_Mime_Headers object.
     */
    public function addMimeHeaders($options = array())
    {
        if (empty($options['headers'])) {
            $headers = new Horde_Mime_Headers();
        } else {
            $headers = $options['headers'];
            $headers->removeHeader('Content-Disposition');
            $headers->removeHeader('Content-Transfer-Encoding');
        }

        /* Add the mandatory Content-Type header. */
        $ct = $this->_headers['content-type'];
        $headers->addHeaderOb($ct);

        /* Add the language(s), if set. (RFC 3282 [2]) */
        if ($hdr = $this->_headers['content-language']) {
            $headers->addHeaderOb($hdr);
        }

        /* Get the description, if any. */
        if ($hdr = $this->_headers['content-description']) {
            $headers->addHeaderOb($hdr);
        }

        /* Set the duration, if it exists. (RFC 3803) */
        if ($hdr = $this->_headers['content-duration']) {
            $headers->addHeaderOb($hdr);
        }

        /* Per RFC 2046[4], this MUST appear in the base message headers. */
        if ($this->_status & self::STATUS_BASEPART) {
            $headers->addHeaderOb(Horde_Mime_Headers_MimeVersion::create());
        }

        /* message/* parts require no additional header information. */
        if ($ct->ptype === 'message') {
            return $headers;
        }

        /* RFC 2183 [2] indicates that default is no requested disposition -
         * the receiving MUA is responsible for display choice. */
        $cd = $this->_headers['content-disposition'];
        if (!$cd->isDefault()) {
            $headers->addHeaderOb($cd);
        }

        /* Add transfer encoding information. RFC 2045 [6.1] indicates that
         * default is 7bit. No need to send the header in this case. */
        $cte = new Horde_Mime_Headers_ContentTransferEncoding(
            null,
            $this->_getTransferEncoding(
                empty($options['encode']) ? null : $options['encode']
            )
        );
        if (!$cte->isDefault()) {
            $headers->addHeaderOb($cte);
        }

        /* Add content ID information. */
        if ($hdr = $this->_headers['content-id']) {
            $headers->addHeaderOb($hdr);
        }

        return $headers;
    }

    /**
     * Return the entire part in MIME format.
     *
     * @param array $options  Additional options:
     *   - canonical: (boolean) Returns the encoded part in strict RFC 822 &
     *                2045 output - namely, all newlines end with the
     *                canonical <CR><LF> sequence.
     *                DEFAULT: false
     *   - defserver: (string) The default server to use when creating the
     *                header string.
     *                DEFAULT: none
     *   - encode: (integer) A mask of allowable encodings.
     *             DEFAULT: self::ENCODE_7BIT
     *   - headers: (mixed) Include the MIME headers? If true, create a new
     *              headers object. If a Horde_Mime_Headers object, add MIME
     *              headers to this object. If a string, use the string
     *              verbatim.
     *              DEFAULT: true
     *   - id: (string) Return only this MIME ID part.
     *         DEFAULT: Returns the base part.
     *   - stream: (boolean) Return a stream resource.
     *             DEFAULT: false
     *
     * @return mixed  The MIME string (returned as a resource if $stream is
     *                true).
     */
    public function toString($options = array())
    {
        $eol = $this->getEOL();
        $isbase = true;
        $oldbaseptr = null;
        $parts = $parts_close = array();

        if (isset($options['id'])) {
            $id = $options['id'];
            if (!($part = $this[$id])) {
                return $part;
            }
            unset($options['id']);
            $contents = $part->toString($options);

            $prev_id = Horde_Mime::mimeIdArithmetic($id, 'up', array('norfc822' => true));
            $prev_part = ($prev_id == $this->getMimeId())
                ? $this
                : $this[$prev_id];
            if (!$prev_part) {
                return $contents;
            }

            $boundary = trim($this->getContentTypeParameter('boundary'), '"');
            $parts = array(
                $eol . '--' . $boundary . $eol,
                $contents
            );

            if (!isset($this[Horde_Mime::mimeIdArithmetic($id, 'next')])) {
                $parts[] = $eol . '--' . $boundary . '--' . $eol;
            }
        } else {
            if ($isbase = empty($options['_notbase'])) {
                $headers = !empty($options['headers'])
                    ? $options['headers']
                    : false;

                if (empty($options['encode'])) {
                    $options['encode'] = null;
                }
                if (empty($options['defserver'])) {
                    $options['defserver'] = null;
                }
                $options['headers'] = true;
                $options['_notbase'] = true;
            } else {
                $headers = true;
                $oldbaseptr = &$options['_baseptr'];
            }

            $this->_temp['toString'] = '';
            $options['_baseptr'] = &$this->_temp['toString'];

            /* Any information about a message is embedded in the message
             * contents themself. Simply output the contents of the part
             * directly and return. */
            $ptype = $this->getPrimaryType();
            if ($ptype == 'message') {
                $parts[] = $this->_contents;
            } else {
                if (!empty($this->_contents)) {
                    $encoding = $this->_getTransferEncoding($options['encode']);
                    switch ($encoding) {
                    case '8bit':
                        if (empty($options['_baseptr'])) {
                            $options['_baseptr'] = '8bit';
                        }
                        break;

                    case 'binary':
                        $options['_baseptr'] = 'binary';
                        break;
                    }

                    $parts[] = $this->_transferEncode($this->_contents, $encoding);

                    /* If not using $this->_contents, we can close the stream
                     * when finished. */
                    if ($this->_temp['transferEncodeClose']) {
                        $parts_close[] = end($parts);
                    }
                }

                /* Deal with multipart messages. */
                if ($ptype == 'multipart') {
                    if (empty($this->_contents)) {
                        $parts[] = 'This message is in MIME format.' . $eol;
                    }

                    $boundary = trim($this->getContentTypeParameter('boundary'), '"');

                    /* If base part is multipart/digest, children should not
                     * have content-type (automatically treated as
                     * message/rfc822; RFC 2046 [5.1.5]). */
                    if ($this->getSubType() === 'digest') {
                        $options['is_digest'] = true;
                    }

                    foreach ($this as $part) {
                        $parts[] = $eol . '--' . $boundary . $eol;
                        $tmp = $part->toString($options);
                        if ($part->getEOL() != $eol) {
                            $tmp = $this->replaceEOL($tmp, $eol, !empty($options['stream']));
                        }
                        if (!empty($options['stream'])) {
                            $parts_close[] = $tmp;
                        }
                        $parts[] = $tmp;
                    }
                    $parts[] = $eol . '--' . $boundary . '--' . $eol;
                }
            }

            if (is_string($headers)) {
                array_unshift($parts, $headers);
            } elseif ($headers) {
                $hdr_ob = $this->addMimeHeaders(array(
                    'encode' => $options['encode'],
                    'headers' => ($headers === true) ? null : $headers
                ));
                if (!$isbase && !empty($options['is_digest'])) {
                    unset($hdr_ob['content-type']);
                }
                if (!empty($this->_temp['toString'])) {
                    $hdr_ob->addHeader(
                        'Content-Transfer-Encoding',
                        $this->_temp['toString']
                    );
                }
                array_unshift($parts, $hdr_ob->toString(array(
                    'canonical' => ($eol == self::RFC_EOL),
                    'charset' => $this->getHeaderCharset(),
                    'defserver' => $options['defserver']
                )));
            }
        }

        $newfp = $this->_writeStream($parts);

        array_map('fclose', $parts_close);

        if (!is_null($oldbaseptr)) {
            switch ($this->_temp['toString']) {
            case '8bit':
                if (empty($oldbaseptr)) {
                    $oldbaseptr = '8bit';
                }
                break;

            case 'binary':
                $oldbaseptr = 'binary';
                break;
            }
        }

        if ($isbase && !empty($options['canonical'])) {
            return $this->replaceEOL($newfp, self::RFC_EOL, !empty($options['stream']));
        }

        return empty($options['stream'])
            ? $this->_readStream($newfp)
            : $newfp;
    }

    /**
     * Get the transfer encoding for the part based on the user requested
     * transfer encoding and the current contents of the part.
     *
     * @param integer $encode  A mask of allowable encodings.
     *
     * @return string  The transfer-encoding of this part.
     */
    protected function _getTransferEncoding($encode = self::ENCODE_7BIT)
    {
        if (!empty($this->_temp['sendEncoding'])) {
            return $this->_temp['sendEncoding'];
        } elseif (!empty($this->_temp['sendTransferEncoding'][$encode])) {
            return $this->_temp['sendTransferEncoding'][$encode];
        }

        if (empty($this->_contents)) {
            $encoding = '7bit';
        } else {
            switch ($this->getPrimaryType()) {
            case 'message':
            case 'multipart':
                /* RFC 2046 [5.2.1] - message/rfc822 messages only allow 7bit,
                 * 8bit, and binary encodings. If the current encoding is
                 * either base64 or q-p, switch it to 8bit instead.
                 * RFC 2046 [5.2.2, 5.2.3, 5.2.4] - All other messages
                 * only allow 7bit encodings.
                 *
                 * TODO: What if message contains 8bit characters and we are
                 * in strict 7bit mode? Not sure there is anything we can do
                 * in that situation, especially for message/rfc822 parts.
                 *
                 * These encoding will be figured out later (via toString()).
                 * They are limited to 7bit, 8bit, and binary. Default to
                 * '7bit' per RFCs. */
                $default_8bit = 'base64';
                $encoding = '7bit';
                break;

            case 'text':
                $default_8bit = 'quoted-printable';
                $encoding = '7bit';
                break;

            default:
                $default_8bit = 'base64';
                /* If transfer encoding has changed from the default, use that
                 * value. */
                $encoding = ($this->_transferEncoding == self::DEFAULT_ENCODING)
                    ? 'base64'
                    : $this->_transferEncoding;
                break;
            }

            switch ($encoding) {
            case 'base64':
            case 'binary':
                break;

            default:
                $encoding = $this->_scanStream($this->_contents);
                break;
            }

            switch ($encoding) {
            case 'base64':
            case 'binary':
                /* If the text is longer than 998 characters between
                 * linebreaks, use quoted-printable encoding to ensure the
                 * text will not be chopped (i.e. by sendmail if being
                 * sent as mail text). */
                $encoding = $default_8bit;
                break;

            case '8bit':
                $encoding = (($encode & self::ENCODE_8BIT) || ($encode & self::ENCODE_BINARY))
                    ? '8bit'
                    : $default_8bit;
                break;
            }
        }

        $this->_temp['sendTransferEncoding'][$encode] = $encoding;

        return $encoding;
    }

    /**
     * Replace newlines in this part's contents with those specified by either
     * the given newline sequence or the part's current EOL setting.
     *
     * @param mixed $text      The text to replace. Either a string or a
     *                         stream resource. If a stream, and returning
     *                         a string, will close the stream when done.
     * @param string $eol      The EOL sequence to use. If not present, uses
     *                         the part's current EOL setting.
     * @param boolean $stream  If true, returns a stream resource.
     *
     * @return string  The text with the newlines replaced by the desired
     *                 newline sequence (returned as a stream resource if
     *                 $stream is true).
     */
    public function replaceEOL($text, $eol = null, $stream = false)
    {
        if (is_null($eol)) {
            $eol = $this->getEOL();
        }

        stream_filter_register('horde_eol', 'Horde_Stream_Filter_Eol');
        $fp = $this->_writeStream($text, array(
            'filter' => array(
                'horde_eol' => array('eol' => $eol)
            )
        ));

        return $stream ? $fp : $this->_readStream($fp, true);
    }

    /**
     * Determine the size of this MIME part and its child members.
     *
     * @todo Remove $approx parameter.
     *
     * @param boolean $approx  If true, determines an approximate size for
     *                         parts consisting of base64 encoded data.
     *
     * @return integer  Size of the part, in bytes.
     */
    public function getBytes($approx = false)
    {
        if ($this->getPrimaryType() == 'multipart') {
            if (isset($this->_bytes)) {
                return $this->_bytes;
            }

            $bytes = 0;
            foreach ($this as $part) {
                $bytes += $part->getBytes($approx);
            }
            return $bytes;
        }

        if ($this->_contents) {
            fseek($this->_contents, 0, SEEK_END);
            $bytes = ftell($this->_contents);
        } else {
            $bytes = $this->_bytes;

            /* Base64 transfer encoding is approx. 33% larger than original
             * data size (RFC 2045 [6.8]). */
            if ($approx && ($this->_transferEncoding == 'base64')) {
                $bytes *= 0.75;
            }
        }

        return intval($bytes);
    }

    /**
     * Explicitly set the size (in bytes) of this part. This value will only
     * be returned (via getBytes()) if there are no contents currently set.
     *
     * This function is useful for setting the size of the part when the
     * contents of the part are not fully loaded (i.e. creating a
     * Horde_Mime_Part object from IMAP header information without loading the
     * data of the part).
     *
     * @param integer $bytes  The size of this part in bytes.
     */
    public function setBytes($bytes)
    {
        /* Consider 'size' disposition parameter to be the canonical size.
         * Only set bytes if that value doesn't exist. */
        if (!$this->getDispositionParameter('size')) {
            $this->setDispositionParameter('size', $bytes);
        }
    }

    /**
     * Output the size of this MIME part in KB.
     *
     * @todo Remove $approx parameter.
     *
     * @param boolean $approx  If true, determines an approximate size for
     *                         parts consisting of base64 encoded data.
     *
     * @return string  Size of the part in KB.
     */
    public function getSize($approx = false)
    {
        if (!($bytes = $this->getBytes($approx))) {
            return 0;
        }

        $localeinfo = Horde_Nls::getLocaleInfo();

        // TODO: Workaround broken number_format() prior to PHP 5.4.0.
        return str_replace(
            array('X', 'Y'),
            array($localeinfo['decimal_point'], $localeinfo['thousands_sep']),
            number_format(ceil($bytes / 1024), 0, 'X', 'Y')
        );
    }

    /**
     * Sets the Content-ID header for this part.
     *
     * @param string $cid  Use this CID (if not already set). Else, generate
     *                     a random CID.
     *
     * @return string  The Content-ID for this part.
     */
    public function setContentId($cid = null)
    {
        if (!is_null($id = $this->getContentId())) {
            return $id;
        }

        $this->_headers->addHeaderOb(
            is_null($cid)
                ? Horde_Mime_Headers_ContentId::create()
                : new Horde_Mime_Headers_ContentId(null, $cid)
        );

        return $this->getContentId();
    }

    /**
     * Returns the Content-ID for this part.
     *
     * @return string  The Content-ID for this part (null if not set).
     */
    public function getContentId()
    {
        return ($hdr = $this->_headers['content-id'])
            ? trim($hdr->value, '<>')
            : null;
    }

    /**
     * Alter the MIME ID of this part.
     *
     * @param string $mimeid  The MIME ID.
     */
    public function setMimeId($mimeid)
    {
        $this->_mimeid = $mimeid;
    }

    /**
     * Returns the MIME ID of this part.
     *
     * @return string  The MIME ID.
     */
    public function getMimeId()
    {
        return $this->_mimeid;
    }

    /**
     * Build the MIME IDs for this part and all subparts.
     *
     * @param string $id       The ID of this part.
     * @param boolean $rfc822  Is this a message/rfc822 part?
     */
    public function buildMimeIds($id = null, $rfc822 = false)
    {
        $this->_status &= ~self::STATUS_REINDEX;

        if (is_null($id)) {
            $rfc822 = true;
            $id = '';
        }

        if ($rfc822) {
            if (empty($this->_parts) &&
                ($this->getPrimaryType() != 'multipart')) {
                $this->setMimeId($id . '1');
            } else {
                if (empty($id) && ($this->getType() == 'message/rfc822')) {
                    $this->setMimeId('1.0');
                } else {
                    $this->setMimeId($id . '0');
                }
                $i = 1;
                foreach ($this as $val) {
                    $val->buildMimeIds($id . ($i++));
                }
            }
        } else {
            $this->setMimeId($id);
            $id = $id
                ? ((substr($id, -2) === '.0') ? substr($id, 0, -1) : ($id . '.'))
                : '';

            if (count($this)) {
                if ($this->getType() == 'message/rfc822') {
                    $this->rewind();
                    $this->current()->buildMimeIds($id, true);
                } else {
                    $i = 1;
                    foreach ($this as $val) {
                        $val->buildMimeIds($id . ($i++));
                    }
                }
            }
        }
    }

    /**
     * Is this the base MIME part?
     *
     * @param boolean $base  True if this is the base MIME part.
     */
    public function isBasePart($base)
    {
        if (empty($base)) {
            $this->_status &= ~self::STATUS_BASEPART;
        } else {
            $this->_status |= self::STATUS_BASEPART;
        }
    }

    /**
     * Determines if this MIME part is an attachment for display purposes.
     *
     * @since Horde_Mime 2.10.0
     *
     * @return boolean  True if this part should be considered an attachment.
     */
    public function isAttachment()
    {
        $type = $this->getType();

        switch ($type) {
        case 'application/ms-tnef':
        case 'application/pgp-keys':
        case 'application/vnd.ms-tnef':
            return false;
        }

        if ($this->parent) {
            switch ($this->parent->getType()) {
            case 'multipart/encrypted':
                switch ($type) {
                case 'application/octet-stream':
                    return false;
                }
                break;

            case 'multipart/signed':
                switch ($type) {
                case 'application/pgp-signature':
                case 'application/pkcs7-signature':
                case 'application/x-pkcs7-signature':
                    return false;
                }
                break;
            }
        }

        switch ($this->getDisposition()) {
        case 'attachment':
            return true;
        }

        switch ($this->getPrimaryType()) {
        case 'application':
            if (strlen($this->getName())) {
                return true;
            }
            break;

        case 'audio':
        case 'video':
            return true;

        case 'multipart':
            return false;
        }

        return false;
    }

    /**
     * Set a piece of metadata on this object.
     *
     * @param string $key  The metadata key.
     * @param mixed $data  The metadata. If null, clears the key.
     */
    public function setMetadata($key, $data = null)
    {
        if (is_null($data)) {
            unset($this->_metadata[$key]);
        } else {
            $this->_metadata[$key] = $data;
        }
    }

    /**
     * Retrieves metadata from this object.
     *
     * @param string $key  The metadata key.
     *
     * @return mixed  The metadata, or null if it doesn't exist.
     */
    public function getMetadata($key)
    {
        return isset($this->_metadata[$key])
            ? $this->_metadata[$key]
            : null;
    }

    /**
     * Sends this message.
     *
     * @param string $email                 The address list to send to.
     * @param Horde_Mime_Headers $headers   The Horde_Mime_Headers object
     *                                      holding this message's headers.
     * @param Horde_Mail_Transport $mailer  A Horde_Mail_Transport object.
     * @param array $opts                   Additional options:
     * <pre>
     *   - broken_rfc2231: (boolean) Attempt to work around non-RFC
     *                     2231-compliant MUAs by generating both a RFC
     *                     2047-like parameter name and also the correct RFC
     *                     2231 parameter (@since 2.5.0).
     *                     DEFAULT: false
     *   - encode: (integer) The encoding to use. A mask of self::ENCODE_*
     *             values.
     *             DEFAULT: Auto-determined based on transport driver.
     * </pre>
     *
     * @throws Horde_Mime_Exception
     * @throws InvalidArgumentException
     */
    public function send($email, $headers, Horde_Mail_Transport $mailer,
                         array $opts = array())
    {
        $old_status = $this->_status;
        $this->isBasePart(true);

        /* Does the SMTP backend support 8BITMIME (RFC 1652)? */
        $canonical = true;
        $encode = self::ENCODE_7BIT;

        if (isset($opts['encode'])) {
            /* Always allow 7bit encoding. */
            $encode |= $opts['encode'];
        } elseif ($mailer instanceof Horde_Mail_Transport_Smtp) {
            try {
                $smtp_ext = $mailer->getSMTPObject()->getServiceExtensions();
                if (isset($smtp_ext['8BITMIME'])) {
                    $encode |= self::ENCODE_8BIT;
                }
            } catch (Horde_Mail_Exception $e) {}
            $canonical = false;
        } elseif ($mailer instanceof Horde_Mail_Transport_Smtphorde) {
            try {
                if ($mailer->getSMTPObject()->data_8bit) {
                    $encode |= self::ENCODE_8BIT;
                }
            } catch (Horde_Mail_Exception $e) {}
            $canonical = false;
        }

        $msg = $this->toString(array(
            'canonical' => $canonical,
            'encode' => $encode,
            'headers' => false,
            'stream' => true
        ));

        /* Add MIME Headers if they don't already exist. */
        if (!isset($headers['MIME-Version'])) {
            $headers = $this->addMimeHeaders(array(
                'encode' => $encode,
                'headers' => $headers
            ));
        }

        if (!empty($this->_temp['toString'])) {
            $headers->addHeader(
                'Content-Transfer-Encoding',
                $this->_temp['toString']
            );
            switch ($this->_temp['toString']) {
            case '8bit':
                if ($mailer instanceof Horde_Mail_Transport_Smtp) {
                    $mailer->addServiceExtensionParameter('BODY', '8BITMIME');
                }
                break;
            }
        }

        $this->_status = $old_status;
        $rfc822 = new Horde_Mail_Rfc822();
        try {
            $mailer->send($rfc822->parseAddressList($email)->writeAddress(array(
                'encode' => $this->getHeaderCharset() ?: true,
                'idn' => true
            )), $headers->toArray(array(
                'broken_rfc2231' => !empty($opts['broken_rfc2231']),
                'canonical' => $canonical,
                'charset' => $this->getHeaderCharset()
            )), $msg);
        } catch (InvalidArgumentException $e) {
            // Try to rebuild the part in case it was due to
            // an invalid line length in a rfc822/message attachment.
            if ($this->_failed) {
                throw $e;
            }
            $this->_failed = true;
            $this->_sanityCheckRfc822Attachments();
            try {
                $this->send($email, $headers, $mailer, $opts);
            } catch (Horde_Mail_Exception $e) {
                throw new Horde_Mime_Exception($e);
            }
        } catch (Horde_Mail_Exception $e) {
            throw new Horde_Mime_Exception($e);
        }
    }

    /**
     * Finds the main "body" text part (if any) in a message.
     * "Body" data is the first text part under this part.
     *
     * @param string $subtype  Specifically search for this subtype.
     *
     * @return mixed  The MIME ID of the main body part, or null if a body
     *                part is not found.
     */
    public function findBody($subtype = null)
    {
        $this->buildMimeIds();

        foreach ($this->partIterator() as $val) {
            $id = $val->getMimeId();

            if (($val->getPrimaryType() == 'text') &&
                ((intval($id) === 1) || !$this->getMimeId()) &&
                (is_null($subtype) || ($val->getSubType() == $subtype)) &&
                ($val->getDisposition() !== 'attachment')) {
                return $id;
            }
        }

        return null;
    }

    /**
     * Returns the recursive iterator needed to iterate through this part.
     *
     * @since 2.8.0
     *
     * @param boolean $current  Include the current part as the base?
     *
     * @return Iterator  Recursive iterator.
     */
    public function partIterator($current = true)
    {
        $this->_reindex(true);
        return new Horde_Mime_Part_Iterator($this, $current);
    }

    /**
     * Returns a subpart by index.
     *
     * @return Horde_Mime_Part  Part, or null if not found.
     */
    public function getPartByIndex($index)
    {
        if (!isset($this->_parts[$index])) {
            return null;
        }

        $part = $this->_parts[$index];
        $part->parent = $this;

        return $part;
    }

    /**
     * Reindexes the MIME IDs, if necessary.
     *
     * @param boolean $force  Reindex if the current part doesn't have an ID.
     */
    protected function _reindex($force = false)
    {
        $id = $this->getMimeId();

        if (($this->_status & self::STATUS_REINDEX) ||
            ($force && is_null($id))) {
            $this->buildMimeIds(
                is_null($id)
                    ? (($this->getPrimaryType() === 'multipart') ? '0' : '1')
                    : $id
            );
        }
    }

    /**
     * Write data to a stream.
     *
     * @param array $data     The data to write. Either a stream resource or
     *                        a string.
     * @param array $options  Additional options:
     *   - error: (boolean) Catch errors when writing to the stream. Throw an
     *            ErrorException if an error is found.
     *            DEFAULT: false
     *   - filter: (array) Filter(s) to apply to the string. Keys are the
     *             filter names, values are filter params.
     *   - fp: (resource) Use this stream instead of creating a new one.
     *
     * @return resource  The stream resource.
     * @throws ErrorException
     */
    protected function _writeStream($data, $options = array())
    {
        if (empty($options['fp'])) {
            $fp = fopen('php://temp/maxmemory:' . self::$memoryLimit, 'r+');
        } else {
            $fp = $options['fp'];
            fseek($fp, 0, SEEK_END);
        }

        if (!is_array($data)) {
            $data = array($data);
        }

        $append_filter = array();
        if (!empty($options['filter'])) {
            foreach ($options['filter'] as $key => $val) {
                $append_filter[] = stream_filter_append($fp, $key, STREAM_FILTER_WRITE, $val);
            }
        }

        if (!empty($options['error'])) {
            set_error_handler(function($errno, $errstr) {
                throw new ErrorException($errstr, $errno);
            });
            $error = null;
        }

        try {
            foreach ($data as $d) {
                if (is_resource($d)) {
                    rewind($d);
                    while (!feof($d)) {
                        fwrite($fp, fread($d, 8192));
                    }
                } elseif (is_string($d)) {
                    $len = strlen($d);
                    $i = 0;
                    while ($i < $len) {
                        fwrite($fp, substr($d, $i, 8192));
                        $i += 8192;
                    }
                }
            }
        } catch (ErrorException $e) {
            $error = $e;
        }

        foreach ($append_filter as $val) {
            stream_filter_remove($val);
        }

        if (!empty($options['error'])) {
            restore_error_handler();
            if ($error) {
                throw $error;
            }
        }

        return $fp;
    }

    /**
     * Read data from a stream.
     *
     * @param resource $fp    An active stream.
     * @param boolean $close  Close the stream when done reading?
     *
     * @return string  The data from the stream.
     */
    protected function _readStream($fp, $close = false)
    {
        $out = '';

        if (!is_resource($fp)) {
            return $out;
        }

        rewind($fp);
        while (!feof($fp)) {
            $out .= fread($fp, 8192);
        }

        if ($close) {
            fclose($fp);
        }

        return $out;
    }

    /**
     * Scans a stream for content type.
     *
     * @param resource $fp  A stream resource.
     *
     * @return mixed  Either 'binary', '8bit', or false.
     */
    protected function _scanStream($fp)
    {
        rewind($fp);

        stream_filter_register(
            'horde_mime_scan_stream',
            'Horde_Mime_Filter_Encoding'
        );
        $filter_params = new stdClass;
        $filter = stream_filter_append(
            $fp,
            'horde_mime_scan_stream',
            STREAM_FILTER_READ,
            $filter_params
        );

        while (!feof($fp)) {
            fread($fp, 8192);
        }

        stream_filter_remove($filter);

        return $filter_params->body;
    }

    /* Static methods. */

    /**
     * Attempts to build a Horde_Mime_Part object from message text.
     *
     * @param string $text  The text of the MIME message.
     * @param array $opts   Additional options:
     *   - forcemime: (boolean) If true, the message data is assumed to be
     *                MIME data. If not, a MIME-Version header must exist (RFC
     *                2045 [4]) to be parsed as a MIME message.
     *                DEFAULT: false
     *   - level: (integer) Current nesting level of the MIME data.
     *            DEFAULT: 0
     *   - no_body: (boolean) If true, don't set body contents of parts (since
     *              2.2.0).
     *              DEFAULT: false
     *
     * @return Horde_Mime_Part  A MIME Part object.
     * @throws Horde_Mime_Exception
     */
    public static function parseMessage($text, array $opts = array())
    {
        /* Mini-hack to get a blank Horde_Mime part so we can call
         * replaceEOL(). Convert to EOL, since that is the expected EOL for
         * use internally within a Horde_Mime_Part object. */
        $part = new Horde_Mime_Part();
        $rawtext = $part->replaceEOL($text, self::EOL);

        /* Find the header. */
        $hdr_pos = self::_findHeader($rawtext, self::EOL);

        unset($opts['ctype']);
        $ob = self::_getStructure(substr($rawtext, 0, $hdr_pos), substr($rawtext, $hdr_pos + 2), $opts);
        $ob->buildMimeIds();
        return $ob;
    }

    /**
     * Creates a MIME object from the text of one part of a MIME message.
     *
     * @param string $header  The header text.
     * @param string $body    The body text.
     * @param array $opts     Additional options:
     * <pre>
     *   - ctype: (string) The default content-type.
     *   - forcemime: (boolean) If true, the message data is assumed to be
     *                MIME data. If not, a MIME-Version header must exist to
     *                be parsed as a MIME message.
     *   - level: (integer) Current nesting level.
     *   - no_body: (boolean) If true, don't set body contents of parts.
     * </pre>
     *
     * @return Horde_Mime_Part  The MIME part object.
     */
    protected static function _getStructure($header, $body,
                                            array $opts = array())
    {
        $opts = array_merge(array(
            'ctype' => 'text/plain',
            'forcemime' => false,
            'level' => 0,
            'no_body' => false
        ), $opts);

        /* Parse headers text into a Horde_Mime_Headers object. */
        $hdrs = Horde_Mime_Headers::parseHeaders($header);

        $ob = new Horde_Mime_Part();

        /* This is not a MIME message. */
        if (!$opts['forcemime'] && !isset($hdrs['MIME-Version'])) {
            $ob->setType('text/plain');

            if ($len = strlen($body)) {
                if ($opts['no_body']) {
                    $ob->setBytes($len);
                } else {
                    $ob->setContents($body);
                }
            }

            return $ob;
        }

        /* Content type. */
        if ($tmp = $hdrs['Content-Type']) {
            $ob->setType($tmp->value);
            foreach ($tmp->params as $key => $val) {
                $ob->setContentTypeParameter($key, $val);
            }
        } else {
            $ob->setType($opts['ctype']);
        }

        /* Content transfer encoding. */
        if ($tmp = $hdrs['Content-Transfer-Encoding']) {
            $ob->setTransferEncoding(strval($tmp));
        }

        /* Content-Description. */
        if ($tmp = $hdrs['Content-Description']) {
            $ob->setDescription(strval($tmp));
        }

        /* Content-Disposition. */
        if ($tmp = $hdrs['Content-Disposition']) {
            $ob->setDisposition($tmp->value);
            foreach ($tmp->params as $key => $val) {
                $ob->setDispositionParameter($key, $val);
            }
        }

        /* Content-Duration */
        if ($tmp = $hdrs['Content-Duration']) {
            $ob->setDuration(strval($tmp));
        }

        /* Content-ID. */
        if ($tmp = $hdrs['Content-Id']) {
            $ob->setContentId(strval($tmp));
        }

        if (($len = strlen($body)) && ($ob->getPrimaryType() != 'multipart')) {
            if ($opts['no_body']) {
                $ob->setBytes($len);
            } else {
                $ob->setContents($body);
            }
        }

        if (++$opts['level'] >= self::NESTING_LIMIT) {
            return $ob;
        }

        /* Process subparts. */
        switch ($ob->getPrimaryType()) {
        case 'message':
            if ($ob->getSubType() == 'rfc822') {
                $ob[] = self::parseMessage($body, array(
                    'forcemime' => true,
                    'no_body' => $opts['no_body']
                ));
            }
            break;

        case 'multipart':
            $boundary = $ob->getContentTypeParameter('boundary');
            if (!is_null($boundary)) {
                foreach (self::_findBoundary($body, 0, $boundary) as $val) {
                    if (!isset($val['length'])) {
                        break;
                    }
                    $subpart = substr($body, $val['start'], $val['length']);
                    $hdr_pos = self::_findHeader($subpart, self::EOL);
                    $ob[] = self::_getStructure(
                        substr($subpart, 0, $hdr_pos),
                        substr($subpart, $hdr_pos + 2),
                        array(
                            'ctype' => ($ob->getSubType() == 'digest') ? 'message/rfc822' : 'text/plain',
                            'forcemime' => true,
                            'level' => $opts['level'],
                            'no_body' => $opts['no_body']
                        )
                    );
                }
            }
            break;
        }

        return $ob;
    }

    /**
     * Attempts to obtain the raw text of a MIME part.
     *
     * @param mixed $text   The full text of the MIME message. The text is
     *                      assumed to be MIME data (no MIME-Version checking
     *                      is performed). It can be either a stream or a
     *                      string.
     * @param string $type  Either 'header' or 'body'.
     * @param string $id    The MIME ID.
     *
     * @return string  The raw text.
     * @throws Horde_Mime_Exception
     */
    public static function getRawPartText($text, $type, $id)
    {
        /* Mini-hack to get a blank Horde_Mime part so we can call
         * replaceEOL(). From an API perspective, getRawPartText() should be
         * static since it is not working on MIME part data. */
        $part = new Horde_Mime_Part();
        $rawtext = $part->replaceEOL($text, self::RFC_EOL);

        /* We need to carry around the trailing "\n" because this is needed
         * to correctly find the boundary string. */
        $hdr_pos = self::_findHeader($rawtext, self::RFC_EOL);
        $curr_pos = $hdr_pos + 3;

        if ($id == 0) {
            switch ($type) {
            case 'body':
                return substr($rawtext, $curr_pos + 1);

            case 'header':
                return trim(substr($rawtext, 0, $hdr_pos));
            }
        }

        $hdr_ob = Horde_Mime_Headers::parseHeaders(trim(substr($rawtext, 0, $hdr_pos)));

        /* If this is a message/rfc822, pass the body into the next loop.
         * Don't decrement the ID here. */
        if (($ct = $hdr_ob['Content-Type']) && ($ct == 'message/rfc822')) {
            return self::getRawPartText(
                substr($rawtext, $curr_pos + 1),
                $type,
                $id
            );
        }

        $base_pos = strpos($id, '.');
        $orig_id = $id;

        if ($base_pos !== false) {
            $id = substr($id, $base_pos + 1);
            $base_pos = substr($orig_id, 0, $base_pos);
        } else {
            $base_pos = $id;
            $id = 0;
        }

        if ($ct && !isset($ct->params['boundary'])) {
            if ($orig_id == '1') {
                return substr($rawtext, $curr_pos + 1);
            }

            throw new Horde_Mime_Exception('Could not find MIME part.');
        }

        $b_find = self::_findBoundary(
            $rawtext,
            $curr_pos,
            $ct->params['boundary'],
            $base_pos
        );

        if (!isset($b_find[$base_pos])) {
            throw new Horde_Mime_Exception('Could not find MIME part.');
        }

        return self::getRawPartText(
            substr(
                $rawtext,
                $b_find[$base_pos]['start'],
                $b_find[$base_pos]['length'] - 1
            ),
            $type,
            $id
        );
    }

    /**
     * Find the location of the end of the header text.
     *
     * @param string $text  The text to search.
     * @param string $eol   The EOL string.
     *
     * @return integer  Header position.
     */
    protected static function _findHeader($text, $eol)
    {
        $hdr_pos = strpos($text, $eol . $eol);
        return ($hdr_pos === false)
            ? strlen($text)
            : $hdr_pos;
    }

    /**
     * Find the location of the next boundary string.
     *
     * @param string $text      The text to search.
     * @param integer $pos      The current position in $text.
     * @param string $boundary  The boundary string.
     * @param integer $end      If set, return after matching this many
     *                          boundaries.
     *
     * @return array  Keys are the boundary number, values are an array with
     *                two elements: 'start' and 'length'.
     */
    protected static function _findBoundary($text, $pos, $boundary,
                                            $end = null)
    {
        $i = 0;
        $out = array();

        $search = "--" . $boundary;
        $search_len = strlen($search);

        while (($pos = strpos($text, $search, $pos)) !== false) {
            /* Boundary needs to appear at beginning of string or right after
             * a LF. */
            if (($pos != 0) && ($text[$pos - 1] != "\n")) {
                continue;
            }

            if (isset($out[$i])) {
                $out[$i]['length'] = $pos - $out[$i]['start'] - 1;
            }

            if (!is_null($end) && ($end == $i)) {
                break;
            }

            $pos += $search_len;
            if (isset($text[$pos])) {
                switch ($text[$pos]) {
                case "\r":
                    $pos += 2;
                    $out[++$i] = array('start' => $pos);
                    break;

                case "\n":
                    $out[++$i] = array('start' => ++$pos);
                    break;

                case '-':
                    return $out;
                }
            }
        }

        return $out;
    }

    /**
     * Re-enocdes message/rfc822 parts in case there was e.g., some broken
     * line length in the headers of the message in the part. Since we shouldn't
     * alter the original message in any way, we simply reset cause the part to
     * be encoded as base64 and sent as a application/octet part.
     */
    protected function _sanityCheckRfc822Attachments()
    {
        if ($this->getType() == 'message/rfc822') {
            $this->_reEncodeMessageAttachment($this);
            return;
        }
        foreach ($this->getParts() as $part) {
            if ($part->getType() == 'message/rfc822') {
                $this->_reEncodeMessageAttachment($part);
            }
        }
        return;
    }

    /**
     * Rebuilds $part and forces it to be a base64 encoded
     * application/octet-stream part.
     *
     * @param  Horde_Mime_Part $part   The MIME part.
     */
    protected function _reEncodeMessageAttachment(Horde_Mime_Part $part)
    {
        $new_part = Horde_Mime_Part::parseMessage($part->getContents());
        $part->setContents($new_part->getContents(array('stream' => true)), array('encoding' => self::ENCODE_BINARY));
        $part->setTransferEncoding('base64', array('send' => true));
    }

    /* ArrayAccess methods. */

    /**
     */
    public function offsetExists($offset)
    {
        return ($this[$offset] !== null);
    }

    /**
     */
    public function offsetGet($offset)
    {
        $this->_reindex();

        if (strcmp($offset, $this->getMimeId()) === 0) {
            $this->parent = null;
            return $this;
        }

        foreach ($this->_parts as $val) {
            if (strcmp($offset, $val->getMimeId()) === 0) {
                $val->parent = $this;
                return $val;
            }

            if ($found = $val[$offset]) {
                return $found;
            }
        }

        return null;
    }

    /**
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->_parts[] = $value;
            $this->_status |= self::STATUS_REINDEX;
        } elseif ($part = $this[$offset]) {
            if ($part->parent === $this) {
                if (($k = array_search($part, $this->_parts, true)) !== false) {
                    $value->setMimeId($part->getMimeId());
                    $this->_parts[$k] = $value;
                }
            } else {
                $this->parent[$offset] = $value;
            }
        }
    }

    /**
     */
    public function offsetUnset($offset)
    {
        if ($part = $this[$offset]) {
            if ($part->parent === $this) {
                if (($k = array_search($part, $this->_parts, true)) !== false) {
                    unset($this->_parts[$k]);
                    $this->_parts = array_values($this->_parts);
                }
            } else {
                unset($part->parent[$offset]);
            }
            $this->_status |= self::STATUS_REINDEX;
        }
    }

    /* Countable methods. */

    /**
     * Returns the number of child message parts (doesn't include
     * grandchildren or more remote ancestors).
     *
     * @return integer  Number of message parts.
     */
    public function count()
    {
        return count($this->_parts);
    }

    /* RecursiveIterator methods. */

    /**
     * @since 2.8.0
     */
    public function current()
    {
        return (($key = $this->key()) === null)
            ? null
            : $this->getPartByIndex($key);
    }

    /**
     * @since 2.8.0
     */
    public function key()
    {
        return (isset($this->_temp['iterate']) && isset($this->_parts[$this->_temp['iterate']]))
            ? $this->_temp['iterate']
            : null;
    }

    /**
     * @since 2.8.0
     */
    public function next()
    {
        ++$this->_temp['iterate'];
    }

    /**
     * @since 2.8.0
     */
    public function rewind()
    {
        $this->_reindex();
        reset($this->_parts);
        $this->_temp['iterate'] = key($this->_parts);
    }

    /**
     * @since 2.8.0
     */
    public function valid()
    {
        return ($this->key() !== null);
    }

    /**
     * @since 2.8.0
     */
    public function hasChildren()
    {
        return (($curr = $this->current()) && count($curr));
    }

    /**
     * @since 2.8.0
     */
    public function getChildren()
    {
        return $this->current();
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
            $this->_bytes,
            $this->_eol,
            $this->_hdrCharset,
            $this->_headers,
            $this->_metadata,
            $this->_mimeid,
            $this->_parts,
            $this->_status,
            $this->_transferEncoding
        );

        if (!empty($this->_contents)) {
            $data[] = $this->_readStream($this->_contents);
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
            switch ($data[0]) {
            case 1:
                $convert = new Horde_Mime_Part_Upgrade_V1($data);
                $data = $convert->data;
                break;

            default:
                $data = null;
                break;
            }

            if (is_null($data)) {
                throw new Exception('Cache version change');
            }
        }

        $key = 0;
        $this->_bytes = $data[++$key];
        $this->_eol = $data[++$key];
        $this->_hdrCharset = $data[++$key];
        $this->_headers = $data[++$key];
        $this->_metadata = $data[++$key];
        $this->_mimeid = $data[++$key];
        $this->_parts = $data[++$key];
        $this->_status = $data[++$key];
        $this->_transferEncoding = $data[++$key];

        if (isset($data[++$key])) {
            $this->setContents($data[$key]);
        }
    }

    /* Deprecated elements. */

    /**
     * @deprecated
     */
    const UNKNOWN = 'x-unknown';

    /**
     * @deprecated
     */
    public static $encodingTypes = array(
        '7bit', '8bit', 'base64', 'binary', 'quoted-printable',
        // Non-RFC types, but old mailers may still use
        'uuencode', 'x-uuencode', 'x-uue'
    );

    /**
     * @deprecated
     */
    public static $mimeTypes = array(
        'text', 'multipart', 'message', 'application', 'audio', 'image',
        'video', 'model'
    );

    /**
     * @deprecated  Use setContentTypeParameter with a null $data value.
     */
    public function clearContentTypeParameter($label)
    {
        $this->setContentTypeParam($label, null);
    }

    /**
     * @deprecated  Use iterator instead.
     */
    public function contentTypeMap($sort = true)
    {
        $map = array();

        foreach ($this->partIterator() as $val) {
            $map[$val->getMimeId()] = $val->getType();
        }

        return $map;
    }

    /**
     * @deprecated  Use array access instead.
     */
    public function addPart($mime_part)
    {
        $this[] = $mime_part;
    }

    /**
     * @deprecated  Use array access instead.
     */
    public function getPart($id)
    {
        return $this[$id];
    }

    /**
     * @deprecated  Use array access instead.
     */
    public function alterPart($id, $mime_part)
    {
        $this[$id] = $mime_part;
    }

    /**
     * @deprecated  Use array access instead.
     */
    public function removePart($id)
    {
        unset($this[$id]);
    }

}
