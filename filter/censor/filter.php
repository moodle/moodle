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

/// These lines are important - the variable must match the name 
/// of the actual function below

    $textfilter_function = 'censor_filter';

    if (function_exists($textfilter_function)) {
        return;
    }


/// This is the filtering function itself.  It accepts the 
/// courseid and the text to be filtered (in HTML form).

function censor_filter($courseid, $text) {

    static $search  = array('fuck', 'cunt', 'shit', 'wank', 'cock');
    static $replace = array('f***', 'c***', 's***', 'w***', 'c***');

    return str_ireplace($search, $replace, $text);
}


?>
