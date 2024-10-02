<?php
/**
 * Custom XML parser for signed and/or encrypted XML Docs
 *
 * @author  Donal McMullan  donal@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mnet
 */

/**
 * Custom XML parser class for signed and/or encrypted XML Docs
 */
class mnet_encxml_parser {

    /** @var resource|false|XMLParser — a resource handle for the new XML parser. */
    private $parser;

    /** @var int unique ID for each tag. */
    private $tag_number;

    /** @var string digest string. */
    private $digest;

    /** @var string remote_timestamp string. */
    public $remote_timestamp;

    /** @var string remote_wwwroot string. */
    public $remote_wwwroot;

    /** @var string signature string. */
    public $signature;

    /** @var string data_object string. */
    public $data_object;

    /** @var string URI value inside the RETRIEVALMETHOD xml tag. */
    private $key_URI;

    /** @var bool true if $chiper has a value, otherwise false. */
    public $payload_encrypted;

    /** @var array the chiper string. */
    public $cipher = [];

    /** @var array error information with code and string keys. */
    public $error = [];

    /** @var string The remote error string, specified in the discard_data(). */
    public $remoteerror;

    /** @var stdClass error started status. */
    private $errorstarted;

    /**
     * Constructor creates and initialises parser resource and calls initialise
     *
     * @return bool True
     */
    public function __construct() {
        return $this->initialise();
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function mnet_encxml_parser() {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct();
    }

    /**
     * Set default element handlers and initialise properties to empty.
     *
     * @return bool True
     */
    function initialise() {
        $this->parser = xml_parser_create();

        xml_set_element_handler($this->parser, [$this, "start_element"], [$this, "end_element"]);
        xml_set_character_data_handler($this->parser, [$this, "discard_data"]);

        $this->tag_number        = 0; // Just a unique ID for each tag
        $this->digest            = '';
        $this->remote_timestamp  = '';
        $this->remote_wwwroot    = '';
        $this->signature         = '';
        $this->data_object       = '';
        $this->key_URI           = '';
        $this->payload_encrypted = false;
        $this->cipher            = array();
        $this->error             = array();
        $this->remoteerror       = null;
        $this->errorstarted      = false;
        return true;
    }

    /**
     * Parse a block of XML text
     *
     * The XML Text will be an XML-RPC request which is wrapped in an XML doc
     * with a signature from the sender. This envelope may be encrypted and
     * delivered within another XML envelope with a symmetric key. The parser
     * should first decrypt this XML, and then place the XML-RPC request into
     * the data_object property, and the signature into the signature property.
     *
     * See the W3C's {@link http://www.w3.org/TR/xmlenc-core/ XML Encryption Syntax and Processing}
     * and {@link http://www.w3.org/TR/2001/PR-xmldsig-core-20010820/ XML-Signature Syntax and Processing}
     * guidelines for more detail on the XML.
     *
     * -----XML-Envelope---------------------------------
     * |                                                |
     * |    Symmetric-key--------------------------     |
     * |    |_____________________________________|     |
     * |                                                |
     * |    Encrypted data-------------------------     |
     * |    |                                     |     |
     * |    |  -XML-Envelope------------------    |     |
     * |    |  |                             |    |     |
     * |    |  |  --Signature-------------   |    |     |
     * |    |  |  |______________________|   |    |     |
     * |    |  |                             |    |     |
     * |    |  |  --Signed-Payload--------   |    |     |
     * |    |  |  |                      |   |    |     |
     * |    |  |  |   XML-RPC Request    |   |    |     |
     * |    |  |  |______________________|   |    |     |
     * |    |  |                             |    |     |
     * |    |  |_____________________________|    |     |
     * |    |_____________________________________|     |
     * |                                                |
     * |________________________________________________|
     *
     * @param   string  $data   The XML that you want to parse
     * @return  bool            True on success - false on failure
     */
    function parse($data) {
        $p = xml_parse($this->parser, $data);

        if ($p == 0) {
            // Parse failed
            $errcode = xml_get_error_code($this->parser);
            $errstring = xml_error_string($errcode);
            $lineno = xml_get_current_line_number($this->parser);
            if ($lineno !== false) {
                $error = array('lineno' => $lineno);
                $lineno--; // Line numbering starts at 1.
                while ($lineno > 0) {
                    $data = strstr($data, "\n");
                    $lineno--;
                }
                $data .= "\n"; // In case there's only one line (no newline)
                $line = substr($data, 0, strpos($data, "\n"));
                $error['code']   = $errcode;
                $error['string'] = $errstring;
                $error['line']   = $line;
                $this->error[] = $error;
            } else {
                $this->error[] = array('code' => $errcode, 'string' => $errstring);
            }
        }

        if (!empty($this->remoteerror)) {
            return false;
        }

        if (count($this->cipher) > 0) {
            $this->cipher = array_values($this->cipher);
            $this->payload_encrypted = true;
        }

        return (bool)$p;
    }

    /**
     * Destroy the parser and free up any related resource.
     */
    function free_resource() {
        $free = xml_parser_free($this->parser);
    }

    /**
     * Set the character-data handler to the right function for each element
     *
     * For each tag (element) name, this function switches the character-data
     * handler to the function that handles that element. Note that character
     * data is referred to the handler in blocks of 1024 bytes.
     *
     * @param   mixed   $parser The XML parser
     * @param   string  $name   The name of the tag, e.g. method_call
     * @param   array   $attrs  The tag's attributes (if any exist).
     * @return  bool            True
     */
    function start_element($parser, $name, $attrs) {
        $this->tag_number++;
        $handler = 'discard_data';
        switch(strtoupper($name)) {
            case 'DIGESTVALUE':
                $handler = 'parse_digest';
                break;
            case 'SIGNATUREVALUE':
                $handler = 'parse_signature';
                break;
            case 'OBJECT':
                $handler = 'parse_object';
                break;
            case 'RETRIEVALMETHOD':
                $this->key_URI = $attrs['URI'];
                break;
            case 'TIMESTAMP':
                $handler = 'parse_timestamp';
                break;
            case 'WWWROOT':
                $handler = 'parse_wwwroot';
                break;
            case 'CIPHERVALUE':
                $this->cipher[$this->tag_number] = '';
                $handler = 'parse_cipher';
                break;
            case 'FAULT':
                $handler = 'parse_fault';
            default:
                break;
        }
        xml_set_character_data_handler($this->parser, [$this, $handler]);
        return true;
    }

    /**
     * Add the next chunk of character data to the remote_timestamp string
     *
     * @param   mixed   $parser The XML parser
     * @param   string  $data   The content of the current tag (1024 byte chunk)
     * @return  bool            True
     */
    function parse_timestamp($parser, $data) {
        $this->remote_timestamp .= $data;
        return true;
    }

    /**
     * Add the next chunk of character data to the cipher string for that tag
     *
     * The XML parser calls the character-data handler with 1024-character
     * chunks of data. This means that the handler may be called several times
     * for a single tag, so we use the concatenate operator (.) to build the
     * tag content into a string.
     * We should not encounter more than one of each tag type, except for the
     * cipher tag. We will often see two of those. We prevent the content of
     * these two tags being concatenated together by counting each tag, and
     * using its 'number' as the key to an array of ciphers.
     *
     * @param   mixed   $parser The XML parser
     * @param   string  $data   The content of the current tag (1024 byte chunk)
     * @return  bool            True
     */
    function parse_cipher($parser, $data) {
        $this->cipher[$this->tag_number] .= $data;
        return true;
    }

    /**
     * Add the next chunk of character data to the remote_wwwroot string
     *
     * @param   mixed   $parser The XML parser
     * @param   string  $data   The content of the current tag (1024 byte chunk)
     * @return  bool            True
     */
    function parse_wwwroot($parser, $data) {
        $this->remote_wwwroot .= $data;
        return true;
    }

    /**
     * Add the next chunk of character data to the digest string
     *
     * @param   mixed   $parser The XML parser
     * @param   string  $data   The content of the current tag (1024 byte chunk)
     * @return  bool            True
     */
    function parse_digest($parser, $data) {
        $this->digest .= $data;
        return true;
    }

    /**
     * Add the next chunk of character data to the signature string
     *
     * @param   mixed   $parser The XML parser
     * @param   string  $data   The content of the current tag (1024 byte chunk)
     * @return  bool            True
     */
    function parse_signature($parser, $data) {
        $this->signature .= $data;
        return true;
    }

    /**
     * Add the next chunk of character data to the data_object string
     *
     * @param   mixed   $parser The XML parser
     * @param   string  $data   The content of the current tag (1024 byte chunk)
     * @return  bool            True
     */
    function parse_object($parser, $data) {
        $this->data_object .= $data;
        return true;
    }

    /**
     * Discard the next chunk of character data
     *
     * This is used for tags that we're not interested in.
     *
     * @param   mixed   $parser The XML parser
     * @param   string  $data   The content of the current tag (1024 byte chunk)
     * @return  bool            True
     */
    function discard_data($parser, $data) {
        if (!$this->errorstarted) {
            // Not interested
            return true;
        }
        $data = trim($data);
        if (isset($this->errorstarted->faultstringstarted) && !empty($data)) {
            $this->remoteerror .= ', message: ' . $data;
        } else if (isset($this->errorstarted->faultcodestarted)) {
            $this->remoteerror = 'code: ' . $data;
            unset($this->errorstarted->faultcodestarted);
        } else if ($data == 'faultCode') {
            $this->errorstarted->faultcodestarted = true;
        } else if ($data == 'faultString') {
            $this->errorstarted->faultstringstarted = true;
        }
        return true;

    }

    function parse_fault($parser, $data) {
        $this->errorstarted = new StdClass;
        return true;
    }

    /**
     * Switch the character-data handler to ignore the next chunk of data
     *
     * @param   mixed   $parser The XML parser
     * @param   string  $name   The name of the tag, e.g. method_call
     * @return  bool            True
     */
    function end_element($parser, $name) {
        $ok = xml_set_character_data_handler($this->parser, [$this, "discard_data"]);
        return true;
    }
}
