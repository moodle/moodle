<?php

/***************************************************************
 * Library to convert HTML into an approximate text equivalent *
 ***************************************************************

  Version: 1.0.3  (with modifications)
  Copyright 2003 Mark Wilton-Jones
  License: HowToCreate script license with written permission
  URL: http://www.howtocreate.co.uk/php/

  For full details about the script and to get the latest version,
  please see the HowToCreate web site above.

  This version contains modifications for Moodle.  In each case the
  lines are marked with "Moodle", so you can  see what has changed.

  ********************************************************************/

function html2text( $badStr ) {

    $is_open_tb = false;
    $is_open_dq = false;
    $is_open_sq = false;

    //remove comments

    while (substr_count($badStr, '<!--') && 
           substr_count($badStr, '-->') && 
           strpos($badStr, '-->', strpos($badStr, '<!--' ) ) > strpos( $badStr, '<!--' ) ) {
           $badStr = substr( $badStr, 0, strpos( $badStr, '<!--' ) ) . 
                     substr( $badStr, strpos( $badStr, '-->', 
                     strpos( $badStr, '<!--' ) ) + 3 );
    }

    //now make sure all HTML tags are correctly written (> not in between quotes)

    $len = strlen($badStr); // Moodle
    $chr = $badStr{0}; // Moodle
    $goodStr = ''; // Moodle

    if ($len > 0) { // Moodle
        for ($x=0; $x < $len; $x++ ) { // Moodle
            $chr = $badStr{$x}; //take each letter in turn and check if that character is permitted there
            switch ( $chr ) {
                case '<':
                    if ( !$is_open_tb && strtolower( substr( $badStr, $x + 1, 5 ) ) == 'style' ) {
                        $x = strpos( strtolower( $badStr ), '</style>', $x ) + 7; // Moodle
                        $chr = '';
                    } else if ( !$is_open_tb && strtolower( substr( $badStr, $x + 1, 6 ) ) == 'script' ) {
                        $x = strpos( strtolower( $badStr ), '</script>', $x ) + 8;  // Moodle
                        $chr = '';
                    } else if (!$is_open_tb) { 
                        $is_open_tb = true; 
                    } else { 
                        $chr = '&lt;'; 
                    }
                    break;

                case '>':
                    if ( !$is_open_tb || $is_open_dq || $is_open_sq ) { 
                        $chr = '&gt;'; 
                    } else { 
                        $is_open_tb = false; 
                    }
                    break;

                case '"':
                    if ( $is_open_tb && !$is_open_dq && !$is_open_sq ) { 
                        $is_open_dq = true; 
                    } else if ( $is_open_tb && $is_open_dq && !$is_open_sq ) { 
                        $is_open_dq = false; 
                    } else { 
                        $chr = '&quot;'; 
                    }
                    break;

                case "'":
                    if ( $is_open_tb && !$is_open_dq && !$is_open_sq ) { 
                        $is_open_sq = true; 
                    } else if ( $is_open_tb && !$is_open_dq && $is_open_sq ) { 
                        $is_open_sq = false; 
                    }
                    break;
            }
            $goodStr .= $chr;
        }
    } // Moodle

    //now that the page is valid (I hope) for strip_tags, strip all unwanted tags

    $goodStr = strip_tags( $goodStr, '<title><hr><h1><h2><h3><h4><h5><h6><div><p><pre><sup><ul><ol><br><dl><dt><table><caption><tr><li><dd><th><td><a><area><img><form><input><textarea><button><select><option>' );

    //strip extra whitespace except between <pre> and <textarea> tags

    $badStr = preg_split( "/<\/?pre[^>]*>/i", $goodStr );

    for ( $x = 0; isset($badStr[$x]) && is_string( $badStr[$x] ); $x++ ) { // Moodle: added isset() test
        if ( $x % 2 ) { $badStr[$x] = '<pre>'.$badStr[$x].'</pre>'; } else {
            $goodStr = preg_split( "/<\/?textarea[^>]*>/i", $badStr[$x] );
            for ( $z = 0; isset($goodStr[$z]) && is_string( $goodStr[$z] ); $z++ ) { // Moodle: added isset() test
                if ( $z % 2 ) { $goodStr[$z] = '<textarea>'.$goodStr[$z].'</textarea>'; } else {
                    $goodStr[$z] = str_replace('  ', ' ', $goodStr[$z] );
                }
            }
            $badStr[$x] = implode('',$goodStr);
        }
    }

    $goodStr = implode('',$badStr);

    //remove all options from select inputs

    $goodStr = preg_replace( "/<option[^>]*>[^<]*/i", '', $goodStr );

    //replace all tags with their text equivalents

    $goodStr = preg_replace( "/<(\/title|hr)[^>]*>/i", "\n          --------------------\n", $goodStr );

    $goodStr = preg_replace( "/<(h|div|p)[^>]*>/i", "\n\n", $goodStr );

    $goodStr = preg_replace( "/<sup[^>]*>/i", '^', $goodStr );

    $goodStr = preg_replace( "/<(ul|ol|br|dl|dt|table|caption|\/textarea|tr[^>]*>\s*<(td|th))[^>]*>/i", "\n", $goodStr );

    $goodStr = preg_replace( "/<li[^>]*>/i", "\n� ", $goodStr );

    $goodStr = preg_replace( "/<dd[^>]*>/i", "\n\t", $goodStr );

    $goodStr = preg_replace( "/<(th|td)[^>]*>/i", "\t", $goodStr );

 // $goodStr = preg_replace( "/<a[^>]* href=(\"((?!\"|#|javascript:)[^\"#]*)(\"|#)|'((?!'|#|javascript:)[^'#]*)('|#)|((?!'|\"|>|#|javascript:)[^#\"'> ]*))[^>]*>/i", "[LINK: $2$4$6] ", $goodStr );   // Moodle
    $goodStr = preg_replace( "/<a[^>]* href=(\"((?!\"|#|javascript:)[^\"#]*)(\"|#)|'((?!'|#|javascript:)[^'#]*)('|#)|((?!'|\"|>|#|javascript:)[^#\"'> ]*))[^>]*>([^<]*)<\/a>/i", "$7 [$2$4$6]", $goodStr );

    // $goodStr = preg_replace( "/<img[^>]* alt=(\"([^\"]+)\"|'([^']+)'|([^\"'> ]+))[^>]*>/i", "[IMAGE: $2$3$4] ", $goodStr );   // Moodle
    $goodStr = preg_replace( "/<img[^>]* alt=(\"([^\"]+)\"|'([^']+)'|([^\"'> ]+))[^>]*>/i", "[$2$3$4] ", $goodStr );

    $goodStr = preg_replace( "/<form[^>]* action=(\"([^\"]+)\"|'([^']+)'|([^\"'> ]+))[^>]*>/i", "\n[FORM: $2$3$4] ", $goodStr );

    $goodStr = preg_replace( "/<(input|textarea|button|select)[^>]*>/i", "[INPUT] ", $goodStr );

    //strip all remaining tags (mostly closing tags)

    $goodStr = strip_tags( $goodStr );

    //convert HTML entities

    $goodStr = strtr( $goodStr, array_flip( get_html_translation_table( HTML_ENTITIES ) ) );

    preg_replace( "/&#(\d+);/me", "chr('$1')", $goodStr );

    //wordwrap

    // $goodStr = wordwrap( $goodStr );   // Moodle
    $goodStr = wordwrap( $goodStr, 78 );

    //make sure there are no more than 3 linebreaks in a row and trim whitespace
    $goodStr = preg_replace("/\r\n?|\f/", "\n", $goodStr);
    $goodStr = preg_replace("/\n(\s*\n){2}/", "\n\n\n", $goodStr);
    $goodStr = preg_replace("/[ \t]+(\n|$)/", "$1", $goodStr);
    $goodStr = preg_replace("/^\n*|\n*$/", '', $goodStr);

    return $goodStr;

}

?>
