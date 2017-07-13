<?php
/**
 * This class provides an object-oriented representation of a MIME part
 * (defined by RFC 2045).
 *
 * Copyright 1999-2014 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author   Chuck Hagenbuch <chuck@horde.org>
 * @author   Michael Slusarz <slusarz@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Mime
 */
class Horde_Mime_Part implements ArrayAccess, Countable, Serializable
{
    /* Serialized version. */
    const VERSION = 1;

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

    /* Unknown types. */
    const UNKNOWN = 'x-unknown';

    /* MIME nesting limit. */
    const NESTING_LIMIT = 100;

    /**
     * The default charset to use when parsing text parts with no charset
     * information.
     *
     * @var string
     */
    static public $defaultCharset = 'us-ascii';

    /**
     * Valid encoding types.
     *
     * @var array
     */
    static public $encodingTypes = array(
        '7bit', '8bit', 'base64', 'binary', 'quoted-printable',
        // Non-RFC types, but old mailers may still use
        'uuencode', 'x-uuencode', 'x-uue'
    );

    /**
     * The memory limit for use with the PHP temp stream.
     *
     * @var integer
     */
    static public $memoryLimit = 2097152;

    /**
     * Valid MIME types.
     *
     * @var array
     */
    static public $mimeTypes = array(
        'text', 'multipart', 'message', 'application', 'audio', 'image',
        'video', 'model'
    );

    /**
     * The type (ex.: text) of this part.
     * Per RFC 2045, the default is 'application'.
     *
     * @var string
     */
    protected $_type = 'application';

    /**
     * The subtype (ex.: plain) of this part.
     * Per RFC 2045, the default is 'octet-stream'.
     *
     * @var string
     */
    protected $_subtype = 'octet-stream';

    /**
     * The body of the part. Always stored in binary format.
     *
     * @var resource
     */
    protected $_contents;

    /**
     * The desired transfer encoding of this part.
     *
     * @var string
     */
    protected $_transferEncoding = self::DEFAULT_ENCODING;

    /**
     * The language(s) of this part.
     *
     * @var array
     */
    protected $_language = array();

    /**
     * The description of this part.
     *
     * @var string
     */
    protected $_description = '';

    /**
     * The disposition of this part (inline or attachment).
     *
     * @var string
     */
    protected $_disposition = '';

    /**
     * The disposition parameters of this part.
     *
     * @var array
     */
    protected $_dispParams = array();

    /**
     * The content type parameters of this part.
     *
     * @var Horde_Support_CaseInsensitiveArray
     */
    protected $_contentTypeParams;

    /**
     * The subparts of this part.
     *
     * @var array
     */
    protected $_parts = array();

    /**
     * The MIME ID of this part.
     *
     * @var string
     */
    protected $_mimeid = null;

    /**
     * The sequence to use as EOL for this part.
     * The default is currently to output the EOL sequence internally as
     * just "\n" instead of the canonical "\r\n" required in RFC 822 & 2045.
     * To be RFC complaint, the full <CR><LF> EOL combination should be used
     * when sending a message.
     * It is not crucial here since the PHP/PEAR mailing functions will handle
     * the EOL details.
     *
     * @var string
     */
    protected $_eol = self::EOL;

    /**
     * Internal temp array.
     *
     * @var array
     */
    protected $_temp = array();

    /**
     * Metadata.
     *
     * @var array
     */
    protected $_metadata = array();

    /**
     * Unique Horde_Mime_Part boundary string.
     *
     * @var string
     */
    protected $_boundary = null;

    /**
     * Default value for this Part's size.
     *
     * @var integer
     */
    protected $_bytes;

    /**
     * The content-ID for this part.
     *
     * @var string
     */
    protected $_contentid = null;

    /**
     * The duration of this part's media data (RFC 3803).
     *
     * @var integer
     */
    protected $_duration;

    /**
     * Do we need to reindex the current part?
     *
     * @var boolean
     */
    protected $_reindex = false;

    /**
     * Is this the base MIME part?
     *
     * @var boolean
     */
    protected $_basepart = false;

    /**
     * The charset to output the headers in.
     *
     * @var string
     */
    protected $_hdrCharset = null;

    /**
     * The list of member variables to serialize.
     *
     * @var array
     */
    protected $_serializedVars = array(
        '_type',
        '_subtype',
        '_transferEncoding',
        '_language',
        '_description',
        '_disposition',
        '_dispParams',
        '_contentTypeParams',
        '_parts',
        '_mimeid',
        '_eol',
        '_metadata',
        '_boundary',
        '_bytes',
        '_contentid',
        '_duration',
        '_reindex',
        '_basepart',
        '_hdrCharset'
    );

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->_init();
    }

    /**
     * Initialization tasks.
     */
    protected function _init()
    {
        $this->_contentTypeParams = new Horde_Support_CaseInsensitiveArray();
    }

    /**
     * Function to run on clone.
     */
    public function __clone()
    {
        reset($this->_parts);
        while (list($k, $v) = each($this->_parts)) {
            $this->_parts[$k] = clone $v;
        }

        $this->_contentTypeParams = clone $this->_contentTypeParams;
    }

    /**
     * Set the content-disposition of this part.
     *
     * @param string $disposition  The content-disposition to set ('inline',
     *                             'attachment', or an empty value).
     */
    public function setDisposition($disposition = null)
    {
        if (empty($disposition)) {
            $this->_disposition = '';
        } else {
            $disposition = Horde_String::lower($disposition);
            if (in_array($disposition, array('inline', 'attachment'))) {
                $this->_disposition = $disposition;
            }
        }
    }

    /**
     * Get the content-disposition of this part.
     *
     * @return string  The part's content-disposition.  An empty string means
     *                 no desired disposition has been set for this part.
     */
    public function getDisposition()
    {
        return $this->_disposition;
    }

    /**
     * Add a disposition parameter to this part.
     *
     * @param string $label  The disposition parameter label.
     * @param string $data   The disposition parameter data.
     */
    public function setDispositionParameter($label, $data)
    {
        $this->_dispParams[$label] = $data;

        switch ($label) {
        case 'size':
            // RFC 2183 [2.7] - size parameter
            $this->_bytes = intval($data);
            break;
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
        return (isset($this->_dispParams[$label]))
            ? $this->_dispParams[$label]
            : null;
    }

    /**
     * Get all parameters from the Content-Disposition header.
     *
     * @return array  An array of all the parameters
     *                Returns the empty array if no parameters set.
     */
    public function getAllDispositionParameters()
    {
        return $this->_dispParams;
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
        $this->clearContents();
        if (empty($options['encoding'])) {
            $options['encoding'] = $this->_transferEncoding;
        }

        $fp = (empty($options['usestream']) || !is_resource($contents))
            ? $this->_writeStream($contents)
            : $contents;

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
            $stream = new Horde_Stream_Existing(array(
                'stream' => $fp
            ));

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
        if (($this->_transferEncoding == self::UNKNOWN) ||
            (strpos($type, '/') === false)) {
            return;
        }

        list($this->_type, $this->_subtype) = explode('/', Horde_String::lower($type));

        if (in_array($this->_type, self::$mimeTypes)) {
            /* Set the boundary string for 'multipart/*' parts. */
            if ($this->_type == 'multipart') {
                if (!$this->getContentTypeParameter('boundary')) {
                    $this->setContentTypeParameter('boundary', $this->_generateBoundary());
                }
            } else {
                $this->clearContentTypeParameter('boundary');
            }
        } else {
            $this->_type = self::UNKNOWN;
            $this->clearContentTypeParameter('boundary');
        }
    }

     /**
      * Get the full MIME Content-Type of this part.
      *
      * @param boolean $charset  Append character set information to the end
      *                          of the content type if this is a text/* part?
      *`
      * @return string  The mimetype of this part (ex.: text/plain;
      *                 charset=us-ascii) or false.
      */
    public function getType($charset = false)
    {
        if (empty($this->_type) || empty($this->_subtype)) {
            return false;
        }

        $ptype = $this->getPrimaryType();
        $type = $ptype . '/' . $this->getSubType();
        if ($charset &&
            ($ptype == 'text') &&
            ($charset = $this->getCharset())) {
            $type .= '; charset=' . $charset;
        }

        return $type;
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
        return $this->_type;
    }

    /**
     * Get the subtype of this part.
     *
     * @return string  The MIME subtype of this part.
     */
    public function getSubType()
    {
        return $this->_subtype;
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
     * @return string  The character set of this part. Returns null if there
     *                 is no character set.
     */
    public function getCharset()
    {
        $charset = $this->getContentTypeParameter('charset');
        if (is_null($charset) && $this->getPrimaryType() != 'text') {
            return null;
        }

        $charset = Horde_String::lower($charset);

        if ($this->getPrimaryType() == 'text') {
            $d_charset = Horde_String::lower(self::$defaultCharset);
            if ($d_charset != 'us-ascii' &&
                (!$charset || $charset == 'us-ascii')) {
                return $d_charset;
            }
        }

        return $charset;
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
     * @return string  The character set.
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
        $this->_language = is_array($lang)
            ? $lang
            : array($lang);
    }

    /**
     * Get the language(s) of this part.
     *
     * @param array  The list of languages.
     */
    public function getLanguage()
    {
        return $this->_language;
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
            unset($this->_duration);
        } else {
            $this->_duration = intval($duration);
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
        return isset($this->_duration)
            ? $this->_duration
            : null;
    }

    /**
     * Set the description of this part.
     *
     * @param string $description  The description of this part.
     */
    public function setDescription($description)
    {
        $this->_description = $description;
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
        $desc = $this->_description;

        if ($default && empty($desc)) {
            $desc = $this->getName();
        }

        return $desc;
    }

    /**
     * Set the transfer encoding to use for this part. Only needed in the
     * following circumstances:
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

        $encoding = Horde_String::lower($encoding);

        if (in_array($encoding, self::$encodingTypes)) {
            if (empty($options['send'])) {
                $this->_transferEncoding = $encoding;
            } else {
                $this->_temp['sendEncoding'] = $encoding;
            }
        } elseif (empty($options['send'])) {
            /* RFC 2045: Any entity with unrecognized encoding must be treated
             * as if it has a Content-Type of "application/octet-stream"
             * regardless of what the Content-Type field actually says. */
            $this->setType('application/octet-stream');
            $this->_transferEncoding = self::UNKNOWN;
        }
    }

    /**
     * Add a MIME subpart.
     *
     * @param Horde_Mime_Part $mime_part  Add a subpart to the current object.
     */
    public function addPart($mime_part)
    {
        $this->_parts[] = $mime_part;
        $this->_reindex = true;
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
     * Retrieve a specific MIME part.
     *
     * @param string $id  The MIME ID to get.
     *
     * @return Horde_Mime_Part  The part requested or null if the part doesn't
     *                          exist.
     */
    public function getPart($id)
    {
        return $this->_partAction($id, 'get');
    }

    /**
     * Remove a subpart.
     *
     * @param string $id  The MIME ID to delete.
     *
     * @param boolean  Success status.
     */
    public function removePart($id)
    {
        return $this->_partAction($id, 'remove');
    }

    /**
     * Alter a current MIME subpart.
     *
     * @param string $id                  The MIME ID to alter.
     * @param Horde_Mime_Part $mime_part  The MIME part to store.
     *
     * @param boolean  Success status.
     */
    public function alterPart($id, $mime_part)
    {
        return $this->_partAction($id, 'alter', $mime_part);
    }

    /**
     * Function used to find a specific MIME part by ID and perform an action
     * on it.
     *
     * @param string $id                  The MIME ID.
     * @param string $action              The action to perform ('get',
     *                                    'remove', or 'alter').
     * @param Horde_Mime_Part $mime_part  The object to use for 'alter'.
     *
     * @return mixed  See calling functions.
     */
    protected function _partAction($id, $action, $mime_part = null)
    {
        $this_id = $this->getMimeId();

        /* Need strcmp() because, e.g., '2.0' == '2'. */
        if (($action === 'get') && (strcmp($id, $this_id) === 0)) {
            return $this;
        }

        if ($this->_reindex) {
            $this->buildMimeIds(is_null($this_id) ? '1' : $this_id);
        }

        foreach ($this->_parts as $key => $val) {
            $partid = $val->getMimeId();

            if (($match = (strcmp($id, $partid) === 0)) ||
                (strpos($id, $partid . '.') === 0) ||
                (strrchr($partid, '.') === '.0')) {
                switch ($action) {
                case 'alter':
                    if ($match) {
                        $mime_part->setMimeId($partid);
                        $this->_parts[$key] = $mime_part;
                        return true;
                    }
                    return $val->alterPart($id, $mime_part);

                case 'get':
                    return $match
                        ? $val
                        : $val->getPart($id);

                case 'remove':
                    if ($match) {
                        unset($this->_parts[$key]);
                        $this->_reindex = true;
                        return true;
                    }
                    return $val->removePart($id);
                }
            }
        }

        return ($action === 'get') ? null : false;
    }

    /**
     * Add a content type parameter to this part.
     *
     * @param string $label  The disposition parameter label.
     * @param string $data   The disposition parameter data.
     */
    public function setContentTypeParameter($label, $data)
    {
        $this->_contentTypeParams[$label] = $data;
    }

    /**
     * Clears a content type parameter from this part.
     *
     * @param string $label  The disposition parameter label.
     * @param string $data   The disposition parameter data.
     */
    public function clearContentTypeParameter($label)
    {
        unset($this->_contentTypeParams[$label]);
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
        return isset($this->_contentTypeParams[$label])
            ? $this->_contentTypeParams[$label]
            : null;
    }

    /**
     * Get all parameters from the Content-Type header.
     *
     * @return array  An array of all the parameters
     *                Returns the empty array if no parameters set.
     */
    public function getAllContentTypeParameters()
    {
        return $this->_contentTypeParams->getArrayCopy();
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
     *             DEFAULT: See self::_getTransferEncoding()
     *   - headers: (Horde_Mime_Headers) The object to add the MIME headers
     *              to.
     *              DEFAULT: Add headers to a new object
     *
     * @return Horde_Mime_Headers  A Horde_Mime_Headers object.
     */
    public function addMimeHeaders($options = array())
    {
        $headers = empty($options['headers'])
            ? new Horde_Mime_Headers()
            : $options['headers'];

        /* Get the Content-Type itself. */
        $ptype = $this->getPrimaryType();
        $c_params = $this->getAllContentTypeParameters();
        if ($ptype != 'text') {
            unset($c_params['charset']);
        }
        $headers->replaceHeader('Content-Type', $this->getType(), array('params' => $c_params));

        /* Add the language(s), if set. (RFC 3282 [2]) */
        if ($langs = $this->getLanguage()) {
            $headers->replaceHeader('Content-Language', implode(',', $langs));
        }

        /* Get the description, if any. */
        if (($descrip = $this->getDescription())) {
            $headers->replaceHeader('Content-Description', $descrip);
        }

        /* Set the duration, if it exists. (RFC 3803) */
        if (($duration = $this->getDuration()) !== null) {
            $headers->replaceHeader('Content-Duration', $duration);
        }

        /* Per RFC 2046 [4], this MUST appear in the base message headers. */
        if ($this->_basepart) {
            $headers->replaceHeader('MIME-Version', '1.0');
        }

        /* message/* parts require no additional header information. */
        if ($ptype == 'message') {
            return $headers;
        }

        /* Don't show Content-Disposition unless a disposition has explicitly
         * been set or there are parameters.
         * If there is a name, but no disposition, default to 'attachment'.
         * RFC 2183 [2] indicates that default is no requested disposition -
         * the receiving MUA is responsible for display choice. */
        $disposition = $this->getDisposition();
        $disp_params = $this->getAllDispositionParameters();
        $name = $this->getName();
        if ($disposition || !empty($name) || !empty($disp_params)) {
            if (!$disposition) {
                $disposition = 'attachment';
            }
            if ($name) {
                $disp_params['filename'] = $name;
            }
            $headers->replaceHeader('Content-Disposition', $disposition, array('params' => $disp_params));
        } else {
            $headers->removeHeader('Content-Disposition');
        }

        /* Add transfer encoding information. RFC 2045 [6.1] indicates that
         * default is 7bit. No need to send the header in this case. */
        $encoding = $this->_getTransferEncoding(empty($options['encode']) ? null : $options['encode']);
        if ($encoding == '7bit') {
            $headers->removeHeader('Content-Transfer-Encoding');
        } else {
            $headers->replaceHeader('Content-Transfer-Encoding', $encoding);
        }

        /* Add content ID information. */
        if (!is_null($this->_contentid)) {
            $headers->replaceHeader('Content-ID', '<' . $this->_contentid . '>');
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
            if (!($part = $this->getPart($id))) {
                return $part;
            }
            unset($options['id']);
            $contents = $part->toString($options);

            $prev_id = Horde_Mime::mimeIdArithmetic($id, 'up', array('norfc822' => true));
            $prev_part = ($prev_id == $this->getMimeId())
                ? $this
                : $this->getPart($prev_id);
            if (!$prev_part) {
                return $contents;
            }

            $boundary = trim($this->getContentTypeParameter('boundary'), '"');
            $parts = array(
                $eol . '--' . $boundary . $eol,
                $contents
            );

            if (!$this->getPart(Horde_Mime::mimeIdArithmetic($id, 'next'))) {
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

                    reset($this->_parts);
                    while (list(,$part) = each($this->_parts)) {
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
                $hdr_ob = $this->addMimeHeaders(array('encode' => $options['encode'], 'headers' => ($headers === true) ? null : $headers));
                $hdr_ob->setEOL($eol);
                if (!empty($this->_temp['toString'])) {
                    $hdr_ob->replaceHeader('Content-Transfer-Encoding', $this->_temp['toString']);
                }
                array_unshift($parts, $hdr_ob->toString(array('charset' => $this->getHeaderCharset(), 'defserver' => $options['defserver'])));
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
            $nobinary = false;

            switch ($this->getPrimaryType()) {
            case 'message':
            case 'multipart':
                /* RFC 2046 [5.2.1] - message/rfc822 messages only allow 7bit,
                 * 8bit, and binary encodings. If the current encoding is
                 * either base64 or q-p, switch it to 8bit instead.
                 * RFC 2046 [5.2.2, 5.2.3, 5.2.4] - All other message/*
                 * messages only allow 7bit encodings.
                 *
                 * TODO: What if message contains 8bit characters and we are
                 * in strict 7bit mode? Not sure there is anything we can do
                 * in that situation, especially for message/rfc822 parts.
                 *
                 * These encoding will be figured out later (via toString()).
                 * They are limited to 7bit, 8bit, and binary. Default to
                 * '7bit' per RFCs. */
                $encoding = '7bit';
                $nobinary = true;
                break;

            case 'text':
                $eol = $this->getEOL();

                if ($this->_scanStream($this->_contents, '8bit')) {
                    $encoding = ($encode & self::ENCODE_8BIT || $encode & self::ENCODE_BINARY)
                        ? '8bit'
                        : 'quoted-printable';
                } elseif ($this->_scanStream($this->_contents, 'preg', "/(?:" . $eol . "|^)[^" . $eol . "]{999,}(?:" . $eol . "|$)/")) {
                    /* If the text is longer than 998 characters between
                     * linebreaks, use quoted-printable encoding to ensure the
                     * text will not be chopped (i.e. by sendmail if being
                     * sent as mail text). */
                    $encoding = 'quoted-printable';
                } else {
                    $encoding = '7bit';
                }
                break;

            default:
                /* If transfer encoding has changed from the default, use that
                 * value. */
                if ($this->_transferEncoding != self::DEFAULT_ENCODING) {
                    $encoding = $this->_transferEncoding;
                } else {
                    $encoding = ($encode & self::ENCODE_8BIT || $encode & self::ENCODE_BINARY)
                        ? '8bit'
                        : 'base64';
                }
                break;
            }

            /* Need to do one last check for binary data if encoding is 7bit
             * or 8bit.  If the message contains a NULL character at all, the
             * message MUST be in binary format. RFC 2046 [2.7, 2.8, 2.9]. Q-P
             * and base64 can handle binary data fine so no need to switch
             * those encodings. */
            if (!$nobinary &&
                in_array($encoding, array('8bit', '7bit')) &&
                $this->_scanStream($this->_contents, 'binary')) {
                $encoding = ($encode & self::ENCODE_BINARY)
                    ? 'binary'
                    : 'base64';
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
            reset($this->_parts);
            while (list(,$part) = each($this->_parts)) {
                $bytes += $part->getBytes($approx);
            }
            return $bytes;
        }

        if ($this->_contents) {
            fseek($this->_contents, 0, SEEK_END);
            $bytes = ftell($this->_contents);
        } else {
            $bytes = $this->_bytes;
        }

        /* Base64 transfer encoding is approx. 33% larger than original
         * data size (RFC 2045 [6.8]). */
        if ($approx && ($this->_transferEncoding == 'base64')) {
            $bytes *= 0.75;
        }

        return intval($bytes);
    }

    /**
     * Explicitly set the size (in bytes) of this part. This value will only
     * be returned (via getBytes()) if there are no contents currently set.
     * This function is useful for setting the size of the part when the
     * contents of the part are not fully loaded (i.e. creating a
     * Horde_Mime_Part object from IMAP header information without loading the
     * data of the part).
     *
     * @param integer $bytes  The size of this part in bytes.
     */
    public function setBytes($bytes)
    {
        $this->setDispositionParameter('size', $bytes);
    }

    /**
     * Output the size of this MIME part in KB.
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
     * @param string $cid  Use this CID (if not already set).  Else, generate
     *                     a random CID.
     *
     * @return string  The Content-ID for this part.
     */
    public function setContentId($cid = null)
    {
        if (is_null($this->_contentid)) {
            $this->_contentid = is_null($cid)
                ? (strval(new Horde_Support_Randomid()) . '@' . $_SERVER['SERVER_NAME'])
                : trim($cid, '<>');
        }

        return $this->_contentid;
    }

    /**
     * Returns the Content-ID for this part.
     *
     * @return string  The Content-ID for this part.
     */
    public function getContentId()
    {
        return $this->_contentid;
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
                    $this->setMimeId('1');
                    $id = '1.';
                } else {
                    $this->setMimeId($id . '0');
                }
                $i = 1;
                foreach (array_keys($this->_parts) as $val) {
                    $this->_parts[$val]->buildMimeIds($id . ($i++));
                }
            }
        } else {
            $this->setMimeId($id);
            $id = $id
                ? ((substr($id, -2) === '.0') ? substr($id, 0, -1) : ($id . '.'))
                : '';

            if ($this->getType() == 'message/rfc822') {
                if (count($this->_parts)) {
                    reset($this->_parts);
                    $this->_parts[key($this->_parts)]->buildMimeIds($id, true);
                }
            } elseif (!empty($this->_parts)) {
                $i = 1;
                foreach (array_keys($this->_parts) as $val) {
                    $this->_parts[$val]->buildMimeIds($id . ($i++));
                }
            }
        }

        $this->_reindex = false;
    }

    /**
     * Generate the unique boundary string (if not already done).
     *
     * @return string  The boundary string.
     */
    protected function _generateBoundary()
    {
        if (is_null($this->_boundary)) {
            $this->_boundary = '=_' . strval(new Horde_Support_Randomid());
        }
        return $this->_boundary;
    }

    /**
     * Returns a mapping of all MIME IDs to their content-types.
     *
     * @param boolean $sort  Sort by MIME ID?
     *
     * @return array  Keys: MIME ID; values: content type.
     */
    public function contentTypeMap($sort = true)
    {
        $map = array($this->getMimeId() => $this->getType());
        foreach ($this->_parts as $val) {
            $map += $val->contentTypeMap(false);
        }

        if ($sort) {
            uksort($map, 'strnatcmp');
        }

        return $map;
    }

    /**
     * Is this the base MIME part?
     *
     * @param boolean $base  True if this is the base MIME part.
     */
    public function isBasePart($base)
    {
        $this->_basepart = $base;
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
     *   - encode: (integer) The encoding to use. A mask of self::ENCODE_*
     *             values.
     *             DEFAULT: Auto-determined based on transport driver.
     *
     * @throws Horde_Mime_Exception
     * @throws InvalidArgumentException
     */
    public function send($email, $headers, Horde_Mail_Transport $mailer,
                         array $opts = array())
    {
        $old_basepart = $this->_basepart;
        $this->_basepart = true;

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
        if (!$headers->getValue('MIME-Version')) {
            $headers = $this->addMimeHeaders(array('encode' => $encode, 'headers' => $headers));
        }

        if (!empty($this->_temp['toString'])) {
            $headers->replaceHeader('Content-Transfer-Encoding', $this->_temp['toString']);
            switch ($this->_temp['toString']) {
            case '8bit':
                if ($mailer instanceof Horde_Mail_Transport_Smtp) {
                    $mailer->addServiceExtensionParameter('BODY', '8BITMIME');
                } elseif ($mailer instanceof Horde_Mail_Transport_Smtphorde) {
                    $mailer->send8bit = true;
                }
                break;
            }
        }

        $this->_basepart = $old_basepart;
        $rfc822 = new Horde_Mail_Rfc822();
        try {
            $mailer->send($rfc822->parseAddressList($email)->writeAddress(array(
                'encode' => $this->getHeaderCharset(),
                'idn' => true
            )), $headers->toArray(array(
                'canonical' => $canonical,
                'charset' => $this->getHeaderCharset()
            )), $msg);
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
        $initial_id = $this->getMimeId();
        $this->buildMimeIds();

        foreach ($this->contentTypeMap() as $mime_id => $mime_type) {
            if ((strpos($mime_type, 'text/') === 0) &&
                (!$initial_id || (intval($mime_id) == 1)) &&
                (is_null($subtype) || (substr($mime_type, 5) == $subtype)) &&
                ($part = $this->getPart($mime_id)) &&
                ($part->getDisposition() != 'attachment')) {
                return $mime_id;
            }
        }

        return null;
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

        if (!empty($options['filter'])) {
            $append_filter = array();
            foreach ($options['filter'] as $key => $val) {
                $append_filter[] = stream_filter_append($fp, $key, STREAM_FILTER_WRITE, $val);
            }
        }

        if (!empty($options['error'])) {
            set_error_handler(array($this, '_writeStreamErrorHandler'));
            $error = null;
        }

        try {
            reset($data);
            while (list(,$d) = each($data)) {
                if (is_resource($d)) {
                    rewind($d);
                    while (!feof($d)) {
                        fwrite($fp, fread($d, 8192));
                    }
                } else {
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

        if (!empty($options['filter'])) {
            foreach ($append_filter as $val) {
                stream_filter_remove($val);
            }
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
     * Error handler for _writeStream().
     *
     * @param integer $errno  Error code.
     * @param string $errstr  Error text.
     *
     * @throws ErrorException
     */
    protected function _writeStreamErrorHandler($errno, $errstr)
    {
        throw new ErrorException($errstr, $errno);
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
     * Scans a stream for the requested data.
     *
     * @param resource $fp  A stream resource.
     * @param string $type  Either '8bit', 'binary', or 'preg'.
     * @param mixed $data   Any additional data needed to do the scan.
     *
     * @param boolean  The result of the scan.
     */
    protected function _scanStream($fp, $type, $data = null)
    {
        rewind($fp);
        while (is_resource($fp) && !feof($fp)) {
            $line = fread($fp, 8192);
            switch ($type) {
            case '8bit':
                if (Horde_Mime::is8bit($line)) {
                    return true;
                }
                break;

            case 'binary':
                if (strpos($line, "\0") !== false) {
                    return true;
                }
                break;

            case 'preg':
                if (preg_match($data, $line)) {
                    return true;
                }
                break;
            }
        }

        return false;
    }

    /**
     * Attempts to build a Horde_Mime_Part object from message text.
     * This function can be called statically via:
     *    $mime_part = Horde_Mime_Part::parseMessage();
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
    static public function parseMessage($text, array $opts = array())
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
    static protected function _getStructure($header, $body,
                                            array $opts = array())
    {
        $opts = array_merge(array(
            'ctype' => 'application/octet-stream',
            'forcemime' => false,
            'level' => 0,
            'no_body' => false
        ), $opts);

        /* Parse headers text into a Horde_Mime_Headers object. */
        $hdrs = Horde_Mime_Headers::parseHeaders($header);

        $ob = new Horde_Mime_Part();

        /* This is not a MIME message. */
        if (!$opts['forcemime'] && !$hdrs->getValue('mime-version')) {
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
        if ($tmp = $hdrs->getValue('content-type', Horde_Mime_Headers::VALUE_BASE)) {
            $ob->setType($tmp);

            $ctype_params = $hdrs->getValue('content-type', Horde_Mime_Headers::VALUE_PARAMS);
            foreach ($ctype_params as $key => $val) {
                $ob->setContentTypeParameter($key, $val);
            }
        } else {
            $ob->setType($opts['ctype']);
        }

        /* Content transfer encoding. */
        if ($tmp = $hdrs->getValue('content-transfer-encoding')) {
            $ob->setTransferEncoding($tmp);
        }

        /* Content-Description. */
        if ($tmp = $hdrs->getValue('content-description')) {
            $ob->setDescription($tmp);
        }

        /* Content-Disposition. */
        if ($tmp = $hdrs->getValue('content-disposition', Horde_Mime_Headers::VALUE_BASE)) {
            $ob->setDisposition($tmp);
            foreach ($hdrs->getValue('content-disposition', Horde_Mime_Headers::VALUE_PARAMS) as $key => $val) {
                $ob->setDispositionParameter($key, $val);
            }
        }

        /* Content-Duration */
        if ($tmp = $hdrs->getValue('content-duration')) {
            $ob->setDuration($tmp);
        }

        /* Content-ID. */
        if ($tmp = $hdrs->getValue('content-id')) {
            $ob->setContentId($tmp);
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
                $ob->addPart(self::parseMessage($body, array('forcemime' => true)));
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
                    $ob->addPart(self::_getStructure(substr($subpart, 0, $hdr_pos), substr($subpart, $hdr_pos + 2), array(
                        'ctype' => ($ob->getSubType() == 'digest') ? 'message/rfc822' : 'text/plain',
                        'forcemime' => true,
                        'level' => $opts['level'],
                        'no_body' => $opts['no_body']
                    )));
                }
            }
            break;
        }

        return $ob;
    }

    /**
     * Attempts to obtain the raw text of a MIME part.
     * This function can be called statically via:
     *    $data = Horde_Mime_Part::getRawPartText();
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
    static public function getRawPartText($text, $type, $id)
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
        if ($hdr_ob->getValue('Content-Type', Horde_Mime_Headers::VALUE_BASE) == 'message/rfc822') {
            return self::getRawPartText(substr($rawtext, $curr_pos + 1), $type, $id);
        }

        $base_pos = strpos($id, '.');
        $orig_id = $id;

        if ($base_pos !== false) {
            $base_pos = substr($id, 0, $base_pos);
            $id = substr($id, $base_pos);
        } else {
            $base_pos = $id;
            $id = 0;
        }

        $params = $hdr_ob->getValue('Content-Type', Horde_Mime_Headers::VALUE_PARAMS);
        if (!isset($params['boundary'])) {
            if ($orig_id == '1') {
                return substr($rawtext, $curr_pos + 1);
            }

            throw new Horde_Mime_Exception('Could not find MIME part.');
        }

        $b_find = self::_findBoundary($rawtext, $curr_pos, $params['boundary'], $base_pos);

        if (!isset($b_find[$base_pos])) {
            throw new Horde_Mime_Exception('Could not find MIME part.');
        }

        return self::getRawPartText(substr($rawtext, $b_find[$base_pos]['start'], $b_find[$base_pos]['length'] - 1), $type, $id);
    }

    /**
     * Find the location of the end of the header text.
     *
     * @param string $text  The text to search.
     * @param string $eol   The EOL string.
     *
     * @return integer  Header position.
     */
    static protected function _findHeader($text, $eol)
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
    static protected function _findBoundary($text, $pos, $boundary,
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

    /* ArrayAccess methods. */

    public function offsetExists($offset)
    {
        return ($this->getPart($offset) !== null);
    }

    public function offsetGet($offset)
    {
        return $this->getPart($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->alterPart($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->removePart($offset);
    }

    /* Countable methods. */

    /**
     * Returns the number of message parts.
     *
     * @return integer  Number of message parts.
     */
    public function count()
    {
        return count($this->_parts);
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
            self::VERSION
        );

        foreach ($this->_serializedVars as $val) {
            switch ($val) {
            case '_contentTypeParams':
                $data[] = $this->$val->getArrayCopy();
                break;

            default:
                $data[] = $this->$val;
                break;
            }
        }

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
            (array_shift($data) != self::VERSION)) {
            throw new Exception('Cache version change');
        }

        $this->_init();

        foreach ($this->_serializedVars as $key => $val) {
            switch ($val) {
            case '_contentTypeParams':
                $this->$val = new Horde_Support_CaseInsensitiveArray($data[$key]);
                break;

            default:
                $this->$val = $data[$key];
                break;
            }
        }

        // $key now contains the last index of _serializedVars.
        if (isset($data[++$key])) {
            $this->setContents($data[$key]);
        }
    }

}
