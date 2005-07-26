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

    static $words;
    global $CFG;

    if (!isset($CFG->filter_censor_badwords)) {
        set_config( 'filter_censor_badwords','' );
    }

    if (empty($words)) {
        $words = array();
        $badwords = explode(',', $CFG->filter_censor_badwords );
        foreach ($badwords as $badword) {
            $words[] = new filterobject(trim($badword), '<span class="censoredtext">', '</span>', false, false,str_pad('',strlen(trim($badword)),'*'));
        }
    }

    return filter_phrases($text, $words);  // Look for all these words in the text
}


?>
