<?php // $id$
//////////////////////////////////////////////////////////////
//  Censorship filtering
// 
//  This very simple example of a Text Filter will parse
//  printed text, replacing words with other words
//
//  To activate this filter, add a line like this to your
//  list of filters in your Filter configuration:
//
//  filter/censor/filter.php
//
//////////////////////////////////////////////////////////////

/// This is the filtering function itself.  It accepts the 
/// courseid and the text to be filtered (in HTML form).

function censor_filter($courseid, $text) {

    static $search  = array('fuck', 'cunt', 'shit', 'wank', 'cock');
    static $replace = array('f***', 'c***', 's***', 'w***', 'c***');

    return str_ireplace($search, $replace, $text);
}


?>
