<?php // $id$
//////////////////////////////////////////////////////////////
//  Censorship filtering
// 
//  This very simple example of a Text Filter will parse
//  printed text, blacking out words perceived to be bad
//  
//  The list of words is in the lang/xx/moodle.php
//
//////////////////////////////////////////////////////////////

/// This is the filtering function itself.  It accepts the 
/// courseid and the text to be filtered (in HTML form).

function censor_filter($courseid, $text) {

    static $search, $replace;

    if (empty($search)) {
        $search = explode(',', get_string('censorbadwords'));
        rsort($search);
        foreach ($search as $key => $word) {
            $replace[$key] = '<span class="censoredtext">'.$word.'</span>';
        }
    }

    return str_ireplace($search, $replace, $text);
}


?>
