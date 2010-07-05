<?php

/**
 * Validates a font family list according to CSS spec
 * @todo whitelisting allowed fonts would be nice
 */
class HTMLPurifier_AttrDef_CSS_FontFamily extends HTMLPurifier_AttrDef
{

    public function validate($string, $config, $context) {
        static $generic_names = array(
            'serif' => true,
            'sans-serif' => true,
            'monospace' => true,
            'fantasy' => true,
            'cursive' => true
        );

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
            }

            $font = $this->expandCSSEscape($font);

            // $font is a pure representation of the font name

            if (ctype_alnum($font) && $font !== '') {
                // very simple font, allow it in unharmed
                $final .= $font . ', ';
                continue;
            }

            // bugger out on whitespace.  form feed (0C) really
            // shouldn't show up regardless
            $font = str_replace(array("\n", "\t", "\r", "\x0C"), ' ', $font);

            // These ugly transforms don't pose a security
            // risk (as \\ and \" might).  We could try to be clever and
            // use single-quote wrapping when there is a double quote
            // present, but I have choosen not to implement that.
            // (warning: this code relies on the selection of quotation
            // mark below)
            $font = str_replace('\\', '\\5C ', $font);
            $font = str_replace('"',  '\\22 ', $font);

            // complicated font, requires quoting
            $final .= "\"$font\", "; // note that this will later get turned into &quot;
        }
        $final = rtrim($final, ', ');
        if ($final === '') return false;
        return $final;
    }

}

// vim: et sw=4 sts=4
