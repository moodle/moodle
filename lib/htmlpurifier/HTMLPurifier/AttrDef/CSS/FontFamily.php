<?php

require_once 'HTMLPurifier/AttrDef.php';

// whitelisting allowed fonts would be nice

/**
 * Validates a font family list according to CSS spec
 */
class HTMLPurifier_AttrDef_CSS_FontFamily extends HTMLPurifier_AttrDef
{
    
    /**
     * Generic font family keywords.
     * @protected
     */
    var $generic_names = array(
        'serif' => true,
        'sans-serif' => true,
        'monospace' => true,
        'fantasy' => true,
        'cursive' => true
    );
    
    function validate($string, $config, &$context) {
        $string = $this->parseCDATA($string);
        // assume that no font names contain commas in them
        $fonts = explode(',', $string);
        $final = '';
        foreach($fonts as $font) {
            $font = trim($font);
            if ($font === '') continue;
            // match a generic name
            if (isset($this->generic_names[$font])) {
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
            }
            // process font
            if (ctype_alnum($font)) {
                // very simple font, allow it in unharmed
                $final .= $font . ', ';
                continue;
            }
            $nospace = str_replace(array(' ', '.', '!'), '', $font);
            if (ctype_alnum($nospace)) {
                // font with spaces in it
                $final .= "'$font', ";
                continue;
            }
        }
        $final = rtrim($final, ', ');
        if ($final === '') return false;
        return $final;
    }
    
}

?>