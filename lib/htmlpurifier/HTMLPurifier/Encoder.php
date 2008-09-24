<?php

HTMLPurifier_ConfigSchema::define(
    'Core', 'Encoding', 'utf-8', 'istring', 
    'If for some reason you are unable to convert all webpages to UTF-8, '. 
    'you can use this directive as a stop-gap compatibility change to '. 
    'let HTML Purifier deal with non UTF-8 input.  This technique has '. 
    'notable deficiencies: absolutely no characters outside of the selected '. 
    'character encoding will be preserved, not even the ones that have '. 
    'been ampersand escaped (this is due to a UTF-8 specific <em>feature</em> '.
    'that automatically resolves all entities), making it pretty useless '.
    'for anything except the most I18N-blind applications, although '.
    '%Core.EscapeNonASCIICharacters offers fixes this trouble with '.
    'another tradeoff. This directive '.
    'only accepts ISO-8859-1 if iconv is not enabled.'
);

HTMLPurifier_ConfigSchema::define(
    'Core', 'EscapeNonASCIICharacters', false, 'bool',
    'This directive overcomes a deficiency in %Core.Encoding by blindly '.
    'converting all non-ASCII characters into decimal numeric entities before '.
    'converting it to its native encoding. This means that even '.
    'characters that can be expressed in the non-UTF-8 encoding will '.
    'be entity-ized, which can be a real downer for encodings like Big5. '.
    'It also assumes that the ASCII repetoire is available, although '.
    'this is the case for almost all encodings. Anyway, use UTF-8! This '.
    'directive has been available since 1.4.0.'
);

if ( !function_exists('iconv') ) {
    // only encodings with native PHP support
    HTMLPurifier_ConfigSchema::defineAllowedValues(
        'Core', 'Encoding', array(
            'utf-8',
            'iso-8859-1'
        )
    );
    HTMLPurifier_ConfigSchema::defineValueAliases(
        'Core', 'Encoding', array(
            'iso8859-1' => 'iso-8859-1'
        )
    );
}

HTMLPurifier_ConfigSchema::define(
    'Test', 'ForceNoIconv', false, 'bool', 
    'When set to true, HTMLPurifier_Encoder will act as if iconv does not '.
    'exist and use only pure PHP implementations.'
);

/**
 * A UTF-8 specific character encoder that handles cleaning and transforming.
 * @note All functions in this class should be static.
 */
class HTMLPurifier_Encoder
{
    
    /**
     * Constructor throws fatal error if you attempt to instantiate class
     */
    function HTMLPurifier_Encoder() {
        trigger_error('Cannot instantiate encoder, call methods statically', E_USER_ERROR);
    }
    
    /**
     * Error-handler that mutes errors, alternative to shut-up operator.
     */
    function muteErrorHandler() {}
    
    /**
    /**
     * Cleans a UTF-8 string for well-formedness and SGML validity
     * 
     * It will parse according to UTF-8 and return a valid UTF8 string, with
     * non-SGML codepoints excluded.
     * 
     * @static
     * @note Just for reference, the non-SGML code points are 0 to 31 and
     *       127 to 159, inclusive.  However, we allow code points 9, 10
     *       and 13, which are the tab, line feed and carriage return
     *       respectively. 128 and above the code points map to multibyte
     *       UTF-8 representations.
     * 
     * @note Fallback code adapted from utf8ToUnicode by Henri Sivonen and
     *       hsivonen@iki.fi at <http://iki.fi/hsivonen/php-utf8/> under the
     *       LGPL license.  Notes on what changed are inside, but in general,
     *       the original code transformed UTF-8 text into an array of integer
     *       Unicode codepoints. Understandably, transforming that back to
     *       a string would be somewhat expensive, so the function was modded to
     *       directly operate on the string.  However, this discourages code
     *       reuse, and the logic enumerated here would be useful for any
     *       function that needs to be able to understand UTF-8 characters.
     *       As of right now, only smart lossless character encoding converters
     *       would need that, and I'm probably not going to implement them.
     *       Once again, PHP 6 should solve all our problems.
     */
    function cleanUTF8($str, $force_php = false) {
        
        // UTF-8 validity is checked since PHP 4.3.5
        // This is an optimization: if the string is already valid UTF-8, no
        // need to do PHP stuff. 99% of the time, this will be the case.
        // The regexp matches the XML char production, as well as well as excluding
        // non-SGML codepoints U+007F to U+009F
        if (preg_match('/^[\x{9}\x{A}\x{D}\x{20}-\x{7E}\x{A0}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]*$/Du', $str)) {
            return $str;
        }
        
        $mState = 0; // cached expected number of octets after the current octet
                     // until the beginning of the next UTF8 character sequence
        $mUcs4  = 0; // cached Unicode character
        $mBytes = 1; // cached expected number of octets in the current sequence
        
        // original code involved an $out that was an array of Unicode
        // codepoints.  Instead of having to convert back into UTF-8, we've
        // decided to directly append valid UTF-8 characters onto a string
        // $out once they're done.  $char accumulates raw bytes, while $mUcs4
        // turns into the Unicode code point, so there's some redundancy.
        
        $out = '';
        $char = '';
        
        $len = strlen($str);
        for($i = 0; $i < $len; $i++) {
            $in = ord($str{$i});
            $char .= $str[$i]; // append byte to char
            if (0 == $mState) {
                // When mState is zero we expect either a US-ASCII character 
                // or a multi-octet sequence.
                if (0 == (0x80 & ($in))) {
                    // US-ASCII, pass straight through.
                    if (($in <= 31 || $in == 127) && 
                        !($in == 9 || $in == 13 || $in == 10) // save \r\t\n
                    ) {
                        // control characters, remove
                    } else {
                        $out .= $char;
                    }
                    // reset
                    $char = '';
                    $mBytes = 1;
                } elseif (0xC0 == (0xE0 & ($in))) {
                    // First octet of 2 octet sequence
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 0x1F) << 6;
                    $mState = 1;
                    $mBytes = 2;
                } elseif (0xE0 == (0xF0 & ($in))) {
                    // First octet of 3 octet sequence
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 0x0F) << 12;
                    $mState = 2;
                    $mBytes = 3;
                } elseif (0xF0 == (0xF8 & ($in))) {
                    // First octet of 4 octet sequence
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 0x07) << 18;
                    $mState = 3;
                    $mBytes = 4;
                } elseif (0xF8 == (0xFC & ($in))) {
                    // First octet of 5 octet sequence.
                    // 
                    // This is illegal because the encoded codepoint must be 
                    // either:
                    // (a) not the shortest form or
                    // (b) outside the Unicode range of 0-0x10FFFF.
                    // Rather than trying to resynchronize, we will carry on 
                    // until the end of the sequence and let the later error
                    // handling code catch it.
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 0x03) << 24;
                    $mState = 4;
                    $mBytes = 5;
                } elseif (0xFC == (0xFE & ($in))) {
                    // First octet of 6 octet sequence, see comments for 5
                    // octet sequence.
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 1) << 30;
                    $mState = 5;
                    $mBytes = 6;
                } else {
                    // Current octet is neither in the US-ASCII range nor a 
                    // legal first octet of a multi-octet sequence.
                    $mState = 0;
                    $mUcs4  = 0;
                    $mBytes = 1;
                    $char = '';
                }
            } else {
                // When mState is non-zero, we expect a continuation of the
                // multi-octet sequence
                if (0x80 == (0xC0 & ($in))) {
                    // Legal continuation.
                    $shift = ($mState - 1) * 6;
                    $tmp = $in;
                    $tmp = ($tmp & 0x0000003F) << $shift;
                    $mUcs4 |= $tmp;
                    
                    if (0 == --$mState) {
                        // End of the multi-octet sequence. mUcs4 now contains
                        // the final Unicode codepoint to be output
                        
                        // Check for illegal sequences and codepoints.
                        
                        // From Unicode 3.1, non-shortest form is illegal
                        if (((2 == $mBytes) && ($mUcs4 < 0x0080)) ||
                            ((3 == $mBytes) && ($mUcs4 < 0x0800)) ||
                            ((4 == $mBytes) && ($mUcs4 < 0x10000)) ||
                            (4 < $mBytes) ||
                            // From Unicode 3.2, surrogate characters = illegal
                            (($mUcs4 & 0xFFFFF800) == 0xD800) ||
                            // Codepoints outside the Unicode range are illegal
                            ($mUcs4 > 0x10FFFF)
                        ) {
                            
                        } elseif (0xFEFF != $mUcs4 && // omit BOM
                            // check for valid Char unicode codepoints
                            (
                                0x9 == $mUcs4 ||
                                0xA == $mUcs4 ||
                                0xD == $mUcs4 ||
                                (0x20 <= $mUcs4 && 0x7E >= $mUcs4) ||
                                // 7F-9F is not strictly prohibited by XML,
                                // but it is non-SGML, and thus we don't allow it
                                (0xA0 <= $mUcs4 && 0xD7FF >= $mUcs4) ||
                                (0x10000 <= $mUcs4 && 0x10FFFF >= $mUcs4)
                            )
                        ) {
                            $out .= $char;
                        }
                        // initialize UTF8 cache (reset)
                        $mState = 0;
                        $mUcs4  = 0;
                        $mBytes = 1;
                        $char = '';
                    }
                } else {
                    // ((0xC0 & (*in) != 0x80) && (mState != 0))
                    // Incomplete multi-octet sequence.
                    // used to result in complete fail, but we'll reset
                    $mState = 0;
                    $mUcs4  = 0;
                    $mBytes = 1;
                    $char ='';
                }
            }
        }
        return $out;
    }
    
    /**
     * Translates a Unicode codepoint into its corresponding UTF-8 character.
     * @static
     * @note Based on Feyd's function at
     *       <http://forums.devnetwork.net/viewtopic.php?p=191404#191404>,
     *       which is in public domain.
     * @note While we're going to do code point parsing anyway, a good
     *       optimization would be to refuse to translate code points that
     *       are non-SGML characters.  However, this could lead to duplication.
     * @note This is very similar to the unichr function in
     *       maintenance/generate-entity-file.php (although this is superior,
     *       due to its sanity checks).
     */
    
    // +----------+----------+----------+----------+
    // | 33222222 | 22221111 | 111111   |          |
    // | 10987654 | 32109876 | 54321098 | 76543210 | bit
    // +----------+----------+----------+----------+
    // |          |          |          | 0xxxxxxx | 1 byte 0x00000000..0x0000007F
    // |          |          | 110yyyyy | 10xxxxxx | 2 byte 0x00000080..0x000007FF
    // |          | 1110zzzz | 10yyyyyy | 10xxxxxx | 3 byte 0x00000800..0x0000FFFF
    // | 11110www | 10wwzzzz | 10yyyyyy | 10xxxxxx | 4 byte 0x00010000..0x0010FFFF
    // +----------+----------+----------+----------+
    // | 00000000 | 00011111 | 11111111 | 11111111 | Theoretical upper limit of legal scalars: 2097151 (0x001FFFFF)
    // | 00000000 | 00010000 | 11111111 | 11111111 | Defined upper limit of legal scalar codes
    // +----------+----------+----------+----------+ 
    
    function unichr($code) {
        if($code > 1114111 or $code < 0 or
          ($code >= 55296 and $code <= 57343) ) {
            // bits are set outside the "valid" range as defined
            // by UNICODE 4.1.0 
            return '';
        }
        
        $x = $y = $z = $w = 0; 
        if ($code < 128) {
            // regular ASCII character
            $x = $code;
        } else {
            // set up bits for UTF-8
            $x = ($code & 63) | 128;
            if ($code < 2048) {
                $y = (($code & 2047) >> 6) | 192;
            } else {
                $y = (($code & 4032) >> 6) | 128;
                if($code < 65536) {
                    $z = (($code >> 12) & 15) | 224;
                } else {
                    $z = (($code >> 12) & 63) | 128;
                    $w = (($code >> 18) & 7)  | 240;
                }
            } 
        }
        // set up the actual character
        $ret = '';
        if($w) $ret .= chr($w);
        if($z) $ret .= chr($z);
        if($y) $ret .= chr($y);
        $ret .= chr($x); 
        
        return $ret;
    }
    
    /**
     * Converts a string to UTF-8 based on configuration.
     * @static
     */
    function convertToUTF8($str, $config, &$context) {
        $encoding = $config->get('Core', 'Encoding');
        if ($encoding === 'utf-8') return $str;
        static $iconv = null;
        if ($iconv === null) $iconv = function_exists('iconv');
        set_error_handler(array('HTMLPurifier_Encoder', 'muteErrorHandler'));
        if ($iconv && !$config->get('Test', 'ForceNoIconv')) {
            $str = iconv($encoding, 'utf-8//IGNORE', $str);
            // If the string is bjorked by Shift_JIS or a similar encoding
            // that doesn't support all of ASCII, convert the naughty
            // characters to their true byte-wise ASCII/UTF-8 equivalents.
            $str = strtr($str, HTMLPurifier_Encoder::testEncodingSupportsASCII($encoding));
            restore_error_handler();
            return $str;
        } elseif ($encoding === 'iso-8859-1') {
            $str = utf8_encode($str);
            restore_error_handler();
            return $str;
        }
        trigger_error('Encoding not supported', E_USER_ERROR);
    }
    
    /**
     * Converts a string from UTF-8 based on configuration.
     * @static
     * @note Currently, this is a lossy conversion, with unexpressable
     *       characters being omitted.
     */
    function convertFromUTF8($str, $config, &$context) {
        $encoding = $config->get('Core', 'Encoding');
        if ($encoding === 'utf-8') return $str;
        static $iconv = null;
        if ($iconv === null) $iconv = function_exists('iconv');
        if ($escape = $config->get('Core', 'EscapeNonASCIICharacters')) {
            $str = HTMLPurifier_Encoder::convertToASCIIDumbLossless($str);
        }
        set_error_handler(array('HTMLPurifier_Encoder', 'muteErrorHandler'));
        if ($iconv && !$config->get('Test', 'ForceNoIconv')) {
            // Undo our previous fix in convertToUTF8, otherwise iconv will barf
            $ascii_fix = HTMLPurifier_Encoder::testEncodingSupportsASCII($encoding);
            if (!$escape && !empty($ascii_fix)) {
                $clear_fix = array();
                foreach ($ascii_fix as $utf8 => $native) $clear_fix[$utf8] = '';
                $str = strtr($str, $clear_fix);
            }
            $str = strtr($str, array_flip($ascii_fix));
            // Normal stuff
            $str = iconv('utf-8', $encoding . '//IGNORE', $str);
            restore_error_handler();
            return $str;
        } elseif ($encoding === 'iso-8859-1') {
            $str = utf8_decode($str);
            restore_error_handler();
            return $str;
        }
        trigger_error('Encoding not supported', E_USER_ERROR);
    }
    
    /**
     * Lossless (character-wise) conversion of HTML to ASCII
     * @static
     * @param $str UTF-8 string to be converted to ASCII
     * @returns ASCII encoded string with non-ASCII character entity-ized
     * @warning Adapted from MediaWiki, claiming fair use: this is a common
     *       algorithm. If you disagree with this license fudgery,
     *       implement it yourself.
     * @note Uses decimal numeric entities since they are best supported.
     * @note This is a DUMB function: it has no concept of keeping
     *       character entities that the projected character encoding
     *       can allow. We could possibly implement a smart version
     *       but that would require it to also know which Unicode
     *       codepoints the charset supported (not an easy task).
     * @note Sort of with cleanUTF8() but it assumes that $str is
     *       well-formed UTF-8
     */
    function convertToASCIIDumbLossless($str) {
        $bytesleft = 0;
        $result = '';
        $working = 0;
        $len = strlen($str);
        for( $i = 0; $i < $len; $i++ ) {
            $bytevalue = ord( $str[$i] );
            if( $bytevalue <= 0x7F ) { //0xxx xxxx
                $result .= chr( $bytevalue );
                $bytesleft = 0;
            } elseif( $bytevalue <= 0xBF ) { //10xx xxxx
                $working = $working << 6;
                $working += ($bytevalue & 0x3F);
                $bytesleft--;
                if( $bytesleft <= 0 ) {
                    $result .= "&#" . $working . ";";
                }
            } elseif( $bytevalue <= 0xDF ) { //110x xxxx
                $working = $bytevalue & 0x1F;
                $bytesleft = 1;
            } elseif( $bytevalue <= 0xEF ) { //1110 xxxx
                $working = $bytevalue & 0x0F;
                $bytesleft = 2;
            } else { //1111 0xxx
                $working = $bytevalue & 0x07;
                $bytesleft = 3;
            }
        }
        return $result;
    }
    
    /**
     * This expensive function tests whether or not a given character
     * encoding supports ASCII. 7/8-bit encodings like Shift_JIS will
     * fail this test, and require special processing. Variable width
     * encodings shouldn't ever fail.
     * 
     * @param string $encoding Encoding name to test, as per iconv format
     * @param bool $bypass Whether or not to bypass the precompiled arrays.
     * @return Array of UTF-8 characters to their corresponding ASCII,
     *      which can be used to "undo" any overzealous iconv action.
     */
    function testEncodingSupportsASCII($encoding, $bypass = false) {
        static $encodings = array();
        if (!$bypass) {
            if (isset($encodings[$encoding])) return $encodings[$encoding];
            $lenc = strtolower($encoding);
            switch ($lenc) {
                case 'shift_jis':
                    return array("\xC2\xA5" => '\\', "\xE2\x80\xBE" => '~');
                case 'johab':
                    return array("\xE2\x82\xA9" => '\\');
            }
            if (strpos($lenc, 'iso-8859-') === 0) return array();
        }
        $ret = array();
        set_error_handler(array('HTMLPurifier_Encoder', 'muteErrorHandler'));
        if (iconv('UTF-8', $encoding, 'a') === false) return false;
        for ($i = 0x20; $i <= 0x7E; $i++) { // all printable ASCII chars
            $c = chr($i);
            if (iconv('UTF-8', "$encoding//IGNORE", $c) === '') {
                // Reverse engineer: what's the UTF-8 equiv of this byte
                // sequence? This assumes that there's no variable width
                // encoding that doesn't support ASCII.
                $ret[iconv($encoding, 'UTF-8//IGNORE', $c)] = $c;
            }
        }
        restore_error_handler();
        $encodings[$encoding] = $ret;
        return $ret;
    }
    
    
}

