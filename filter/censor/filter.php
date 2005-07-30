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

    if (empty($words)) {
        $words = array();
        $badwords = explode(',', get_string('badwords', 'censor'));
        foreach ($badwords as $badword) {
            $badword = trim($badword);
            $words[] = new filterobject($badword, '<span class="censoredtext" title="'.$badword.'">', '</span>', 
                                        false, false, str_pad('',strlen($badword),'*'));
        }
    }

    return filter_phrases($text, $words);  // Look for all these words in the text
}


?>
