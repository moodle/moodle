<?php

require_once 'HTMLPurifier/AttrDef.php';

HTMLPurifier_ConfigSchema::define(
    'Core', 'ColorKeywords', array(
        'maroon'    => '#800000',
        'red'       => '#FF0000',
        'orange'    => '#FFA500',
        'yellow'    => '#FFFF00',
        'olive'     => '#808000',
        'purple'    => '#800080',
        'fuchsia'   => '#FF00FF',
        'white'     => '#FFFFFF',
        'lime'      => '#00FF00',
        'green'     => '#008000',
        'navy'      => '#000080',
        'blue'      => '#0000FF',
        'aqua'      => '#00FFFF',
        'teal'      => '#008080',
        'black'     => '#000000',
        'silver'    => '#C0C0C0',
        'gray'      => '#808080'
    ), 'hash', '
Lookup array of color names to six digit hexadecimal number corresponding
to color, with preceding hash mark. Used when parsing colors.
This directive has been available since 2.0.0.
');

/**
 * Validates Color as defined by CSS.
 */
class HTMLPurifier_AttrDef_CSS_Color extends HTMLPurifier_AttrDef
{
    
    function validate($color, $config, &$context) {
        
        static $colors = null;
        if ($colors === null) $colors = $config->get('Core', 'ColorKeywords');
        
        $color = trim($color);
        if ($color === '') return false;
        
        $lower = strtolower($color);
        if (isset($colors[$lower])) return $colors[$lower];
        
        if (strpos($color, 'rgb(') !== false) {
            // rgb literal handling
            $length = strlen($color);
            if (strpos($color, ')') !== $length - 1) return false;
            $triad = substr($color, 4, $length - 4 - 1);
            $parts = explode(',', $triad);
            if (count($parts) !== 3) return false;
            $type = false; // to ensure that they're all the same type
            $new_parts = array();
            foreach ($parts as $part) {
                $part = trim($part);
                if ($part === '') return false;
                $length = strlen($part);
                if ($part[$length - 1] === '%') {
                    // handle percents
                    if (!$type) {
                        $type = 'percentage';
                    } elseif ($type !== 'percentage') {
                        return false;
                    }
                    $num = (float) substr($part, 0, $length - 1);
                    if ($num < 0) $num = 0;
                    if ($num > 100) $num = 100;
                    $new_parts[] = "$num%";
                } else {
                    // handle integers
                    if (!$type) {
                        $type = 'integer';
                    } elseif ($type !== 'integer') {
                        return false;
                    }
                    $num = (int) $part;
                    if ($num < 0) $num = 0;
                    if ($num > 255) $num = 255;
                    $new_parts[] = (string) $num;
                }
            }
            $new_triad = implode(',', $new_parts);
            $color = "rgb($new_triad)";
        } else {
            // hexadecimal handling
            if ($color[0] === '#') {
                $hex = substr($color, 1);
            } else {
                $hex = $color;
                $color = '#' . $color;
            }
            $length = strlen($hex);
            if ($length !== 3 && $length !== 6) return false;
            if (!ctype_xdigit($hex)) return false;
        }
        
        return $color;
        
    }
    
}

