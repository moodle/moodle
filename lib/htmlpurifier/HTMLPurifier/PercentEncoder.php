<?php

/**
 * Class that handles operations involving percent-encoding in URIs.
 */
class HTMLPurifier_PercentEncoder
{
    
    /**
     * Fix up percent-encoding by decoding unreserved characters and normalizing
     * @param $string String to normalize
     */
    function normalize($string) {
        if ($string == '') return '';
        $parts = explode('%', $string);
        $ret = array_shift($parts);
        foreach ($parts as $part) {
            $length = strlen($part);
            if ($length < 2) {
                $ret .= '%25' . $part;
                continue;
            }
            $encoding = substr($part, 0, 2);
            $text     = substr($part, 2);
            if (!ctype_xdigit($encoding)) {
                $ret .= '%25' . $part;
                continue;
            }
            $int = hexdec($encoding);
            if (
                ($int >= 48 && $int <= 57) || // digits
                ($int >= 65 && $int <= 90) || // uppercase letters
                ($int >= 97 && $int <= 122) || // lowercase letters
                $int == 126 || $int == 45 || $int == 46 || $int == 95 // ~-._
            ) {
                $ret .= chr($int) . $text;
                continue;
            }
            $encoding = strtoupper($encoding);
            $ret .= '%' . $encoding . $text;
        }
        return $ret;
    }
    
}

