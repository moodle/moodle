<?php

namespace PhpXmlRpc\Helper;

use PhpXmlRpc\PhpXmlRpc;

/**
 * @todo implement an interface
 */
class Charset
{
    // tables used for transcoding different charsets into us-ascii xml
    protected $xml_iso88591_Entities = array("in" => array(), "out" => array());

    //protected $xml_cp1252_Entities = array('in' => array(), out' => array());

    protected $charset_supersets = array(
        'US-ASCII' => array('ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4',
            'ISO-8859-5', 'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8',
            'ISO-8859-9', 'ISO-8859-10', 'ISO-8859-11', 'ISO-8859-12',
            'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15', 'UTF-8',
            'EUC-JP', 'EUC-', 'EUC-KR', 'EUC-CN',),
    );

    /** @var Charset $instance */
    protected static $instance = null;

    /**
     * This class is singleton for performance reasons.
     * @todo should we just make $xml_iso88591_Entities a static variable instead ?
     *
     * @return Charset
     */
    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * Force usage as singleton
     */
    protected function __construct()
    {
    }

    /**
     * @param string $tableName
     * @throws \Exception for unsupported $tableName
     * @todo add support for cp1252 as well as latin-2 .. latin-10
     *       Optimization creep: instead of building all those tables on load, keep them ready-made php files
     *       which are not even included until needed
     * @todo should we add to the latin-1 table the characters from cp_1252 range, i.e. 128 to 159 ?
     *       Those will NOT be present in true ISO-8859-1, but will save the unwary windows user from sending junk
     *       (though no luck when receiving them...)
     *       Note also that, apparently, while 'ISO/IEC 8859-1' has no characters defined for bytes 128 to 159,
     *       IANA ISO-8859-1 does have well-defined 'C1' control codes for those - wikipedia's page on latin-1 says:
     *       "ISO-8859-1 is the IANA preferred name for this standard when supplemented with the C0 and C1 control codes from ISO/IEC 6429."
     *       Check what mbstring/iconv do by default with those?
     */
    protected function buildConversionTable($tableName)
    {
        switch($tableName) {
            case 'xml_iso88591_Entities':
                if (count($this->xml_iso88591_Entities['in'])) {
                    return;
                }
                for ($i = 0; $i < 32; $i++) {
                    $this->xml_iso88591_Entities["in"][] = chr($i);
                    $this->xml_iso88591_Entities["out"][] = "&#{$i};";
                }

                /// @todo to be 'print safe', should we encode as well character 127 (DEL) ?

                for ($i = 160; $i < 256; $i++) {
                    $this->xml_iso88591_Entities["in"][] = chr($i);
                    $this->xml_iso88591_Entities["out"][] = "&#{$i};";
                }
                break;

            /*case 'xml_cp1252_Entities':
                if (count($this->xml_cp1252_Entities['in'])) {
                    return;
                }
                for ($i = 128; $i < 160; $i++)
                {
                    $this->xml_cp1252_Entities['in'][] = chr($i);
                }
                $this->xml_cp1252_Entities['out'] = array(
                    '&#x20AC;', '?',        '&#x201A;', '&#x0192;',
                    '&#x201E;', '&#x2026;', '&#x2020;', '&#x2021;',
                    '&#x02C6;', '&#x2030;', '&#x0160;', '&#x2039;',
                    '&#x0152;', '?',        '&#x017D;', '?',
                    '?',        '&#x2018;', '&#x2019;', '&#x201C;',
                    '&#x201D;', '&#x2022;', '&#x2013;', '&#x2014;',
                    '&#x02DC;', '&#x2122;', '&#x0161;', '&#x203A;',
                    '&#x0153;', '?',        '&#x017E;', '&#x0178;'
                );
                $this->buildConversionTable('xml_iso88591_Entities');
                break;*/

            default:
                throw new \Exception('Unsupported table: ' . $tableName);
        }
    }

    /**
     * Convert a string to the correct XML representation in a target charset.
     * This involves:
     * - character transformation for all characters which have a different representation in source and dest charsets
     * - using 'charset entity' representation for all characters which are outside of the target charset
     *
     * To help correct communication of non-ascii chars inside strings, regardless of the charset used when sending
     * requests, parsing them, sending responses and parsing responses, an option is to convert all non-ascii chars
     * present in the message into their equivalent 'charset entity'. Charset entities enumerated this way are
     * independent of the charset encoding used to transmit them, and all XML parsers are bound to understand them.
     *
     * Note that when not sending a charset encoding mime type along with http headers, we are bound by RFC 3023 to emit
     * strict us-ascii for 'text/xml' payloads (but we should review RFC 7303, which seems to have changed the rules...)
     *
     * @todo do a bit of basic benchmarking (strtr vs. str_replace)
     * @todo make usage of iconv() or mb_string() where available
     * @todo support aliases for charset names, eg ASCII, LATIN1, ISO-88591 (see f.e. polyfill-iconv for a list),
     *       but then take those into account as well in other methods, ie.isValidCharset)
     * @todo when converting to ASCII, allow to choose whether to escape the range 0-31,127 (non-print chars) or not
     * @todo allow picking different strategies to deal w. invalid chars? eg. source in latin-1 and chars 128-159
     * @todo add support for escaping using CDATA sections? (add cdata start and end tokens, replace only ']]>' with ']]]]><![CDATA[>')
     *
     * @param string $data
     * @param string $srcEncoding
     * @param string $destEncoding
     *
     * @return string
     */
    public function encodeEntities($data, $srcEncoding = '', $destEncoding = '')
    {
        if ($srcEncoding == '') {
            // lame, but we know no better...
            $srcEncoding = PhpXmlRpc::$xmlrpc_internalencoding;
        }

        if ($destEncoding == '') {
            $destEncoding = 'US-ASCII';
        }

        $conversion = strtoupper($srcEncoding . '_' . $destEncoding);

        // list ordered with (expected) most common scenarios first
        switch ($conversion) {
            case 'UTF-8_UTF-8':
            case 'ISO-8859-1_ISO-8859-1':
            case 'US-ASCII_UTF-8':
            case 'US-ASCII_US-ASCII':
            case 'US-ASCII_ISO-8859-1':
            //case 'CP1252_CP1252':
                $escapedData = str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), $data);
                break;

            case 'UTF-8_US-ASCII':
            case 'UTF-8_ISO-8859-1':
                // NB: this will choke on invalid UTF-8, going most likely beyond EOF
                $escapedData = '';
                // be kind to users creating string xmlrpc values out of different php types
                $data = (string)$data;
                $ns = strlen($data);
                for ($nn = 0; $nn < $ns; $nn++) {
                    $ch = $data[$nn];
                    $ii = ord($ch);
                    // 7 bits in 1 byte: 0bbbbbbb (127)
                    if ($ii < 32) {
                        if ($conversion == 'UTF-8_US-ASCII') {
                            $escapedData .= sprintf('&#%d;', $ii);
                        } else {
                            $escapedData .= $ch;
                        }
                    }
                    else if ($ii < 128) {
                        /// @todo shall we replace this with a (supposedly) faster str_replace?
                        /// @todo to be 'print safe', should we encode as well character 127 (DEL) ?
                        switch ($ii) {
                            case 34:
                                $escapedData .= '&quot;';
                                break;
                            case 38:
                                $escapedData .= '&amp;';
                                break;
                            case 39:
                                $escapedData .= '&apos;';
                                break;
                            case 60:
                                $escapedData .= '&lt;';
                                break;
                            case 62:
                                $escapedData .= '&gt;';
                                break;
                            default:
                                $escapedData .= $ch;
                        } // switch
                    } // 11 bits in 2 bytes: 110bbbbb 10bbbbbb (2047)
                    elseif ($ii >> 5 == 6) {
                        $b1 = ($ii & 31);
                        $b2 = (ord($data[$nn + 1]) & 63);
                        $ii = ($b1 * 64) + $b2;
                        $escapedData .= sprintf('&#%d;', $ii);
                        $nn += 1;
                    } // 16 bits in 3 bytes: 1110bbbb 10bbbbbb 10bbbbbb
                    elseif ($ii >> 4 == 14) {
                        $b1 = ($ii & 15);
                        $b2 = (ord($data[$nn + 1]) & 63);
                        $b3 = (ord($data[$nn + 2]) & 63);
                        $ii = ((($b1 * 64) + $b2) * 64) + $b3;
                        $escapedData .= sprintf('&#%d;', $ii);
                        $nn += 2;
                    } // 21 bits in 4 bytes: 11110bbb 10bbbbbb 10bbbbbb 10bbbbbb
                    elseif ($ii >> 3 == 30) {
                        $b1 = ($ii & 7);
                        $b2 = (ord($data[$nn + 1]) & 63);
                        $b3 = (ord($data[$nn + 2]) & 63);
                        $b4 = (ord($data[$nn + 3]) & 63);
                        $ii = ((((($b1 * 64) + $b2) * 64) + $b3) * 64) + $b4;
                        $escapedData .= sprintf('&#%d;', $ii);
                        $nn += 3;
                    }
                }

                // when converting to latin-1, do not be so eager with using entities for characters 160-255
                if ($conversion == 'UTF-8_ISO-8859-1') {
                    $this->buildConversionTable('xml_iso88591_Entities');
                    $escapedData = str_replace(array_slice($this->xml_iso88591_Entities['out'], 32), array_slice($this->xml_iso88591_Entities['in'], 32), $escapedData);
                }
                break;

            case 'ISO-8859-1_UTF-8':
                $escapedData = str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), $data);
                $escapedData = utf8_encode($escapedData);
                break;

            case 'ISO-8859-1_US-ASCII':
                $this->buildConversionTable('xml_iso88591_Entities');
                $escapedData = str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), $data);
                $escapedData = str_replace($this->xml_iso88591_Entities['in'], $this->xml_iso88591_Entities['out'], $escapedData);
                break;

            /*
            case 'CP1252_US-ASCII':
                $this->buildConversionTable('xml_cp1252_Entities');
                $escapedData = str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), $data);
                $escapedData = str_replace($this->xml_iso88591_Entities']['in'], $this->xml_iso88591_Entities['out'], $escapedData);
                $escapedData = str_replace($this->xml_cp1252_Entities['in'], $this->xml_cp1252_Entities['out'], $escapedData);
                break;
            case 'CP1252_UTF-8':
                $this->buildConversionTable('xml_cp1252_Entities');
                $escapedData = str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), $data);
                /// @todo we could use real UTF8 chars here instead of xml entities... (note that utf_8 encode all alone will NOT convert them)
                $escapedData = str_replace($this->xml_cp1252_Entities['in'], $this->xml_cp1252_Entities['out'], $escapedData);
                $escapedData = utf8_encode($escapedData);
                break;
            case 'CP1252_ISO-8859-1':
                $this->buildConversionTable('xml_cp1252_Entities');
                $escapedData = str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), $data);
                // we might as well replace all funky chars with a '?' here, but we are kind and leave it to the receiving application layer to decide what to do with these weird entities...
                $escapedData = str_replace($this->xml_cp1252_Entities['in'], $this->xml_cp1252_Entities['out'], $escapedData);
                break;
            */

            default:
                $escapedData = '';
                Logger::instance()->errorLog('XML-RPC: ' . __METHOD__ . ": Converting from $srcEncoding to $destEncoding: not supported...");
        }

        return $escapedData;
    }

    /**
     * Checks if a given charset encoding is present in a list of encodings or if it is a valid subset of any encoding
     * in the list.
     *
     * @param string $encoding charset to be tested
     * @param string|array $validList comma separated list of valid charsets (or array of charsets)
     *
     * @return bool
     */
    public function isValidCharset($encoding, $validList)
    {
        if (is_string($validList)) {
            $validList = explode(',', $validList);
        }
        if (@in_array(strtoupper($encoding), $validList)) {
            return true;
        } else {
            if (array_key_exists($encoding, $this->charset_supersets)) {
                foreach ($validList as $allowed) {
                    if (in_array($allowed, $this->charset_supersets[$encoding])) {
                        return true;
                    }
                }
            }

            return false;
        }
    }

    /**
     * Used only for backwards compatibility
     * @deprecated
     *
     * @param string $charset
     *
     * @return array
     *
     * @throws \Exception for unknown/unsupported charsets
     */
    public function getEntities($charset)
    {
        //trigger_error('Method ' . __METHOD__ . ' is deprecated', E_USER_DEPRECATED);

        switch ($charset)
        {
            case 'iso88591':
                return $this->xml_iso88591_Entities;
            default:
                throw new \Exception('Unsupported charset: ' . $charset);
        }
    }
}
