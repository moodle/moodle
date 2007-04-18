<?php

require_once 'HTMLPurifier/AttrDef.php';

/**
 * Validates an integer representation of pixels according to the HTML spec.
 */
class HTMLPurifier_AttrDef_HTML_Pixels extends HTMLPurifier_AttrDef
{
    
    function validate($string, $config, &$context) {
        
        $string = trim($string);
        if ($string === '0') return $string;
        if ($string === '')  return false;
        $length = strlen($string);
        if (substr($string, $length - 2) == 'px') {
            $string = substr($string, 0, $length - 2);
        }
        if (!is_numeric($string)) return false;
        $int = (int) $string;
        
        if ($int < 0) return '0';
        
        // upper-bound value, extremely high values can
        // crash operating systems, see <http://ha.ckers.org/imagecrash.html>
        // WARNING, above link WILL crash you if you're using Windows
        
        if ($int > 1200) return '1200';
        
        return (string) $int;
        
    }
    
}

?>