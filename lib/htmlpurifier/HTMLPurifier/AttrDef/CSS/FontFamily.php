<?php

require_once 'HTMLPurifier/AttrDef.php';

// whitelisting allowed fonts would be nice

/**
 * Validates a font family list according to CSS spec
 */
class HTMLPurifier_AttrDef_CSS_FontFamily extends HTMLPurifier_AttrDef
{
    
    function validate($string, $config, &$context) {
        static $generic_names = array(
            'serif' => true,
            'sans-serif' => true,
            'monospace' => true,
            'fantasy' => true,
            'cursive' => true
        );
        
        $string = $this->parseCDATA($string);
        // assume that no font names contain commas in them
        $fonts = explode(',', $string);
        $final = '';
        foreach($fonts as $font) {
            $font = trim($font);
            if ($font === '') continue;
            // match a generic name
            if (isset($generic_names[$font])) {
                $final .= $font . ', ';
                continue;
            }
            // match a quoted name
            if ($font[0] === '"' || $font[0] === "'") {
                $length = strlen($font);
                if ($length <= 2) continue;
                $quote = $font[0];
                if ($font[$length - 1] !== $quote) continue;
                $font = substr($font, 1, $length - 2);
                // double-backslash processing is buggy
                $font = str_replace("\\$quote", $quote, $font); // de-escape quote
                $font = str_replace("\\\n", "\n", $font);       // de-escape newlines
            }
            // $font is a pure representation of the font name
            
            if (ctype_alnum($font)) {
                // very simple font, allow it in unharmed
                $final .= $font . ', ';
                continue;
            }
            
            // complicated font, requires quoting
            
            // armor single quotes and new lines
            $font = str_replace("'", "\\'", $font);
            $font = str_replace("\n", "\\\n", $font);
            $final .= "'$font', ";
        }
        $final = rtrim($final, ', ');
        if ($final === '') return false;
        return $final;
    }
    
}

