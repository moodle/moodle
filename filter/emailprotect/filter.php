<?PHP // $Id$
      // This function looks for email addresses in Moodle text and 
      // hides them using the Moodle obfuscate_text function. 
      // Original code by Mike Churchward

function emailprotect_filter($courseid, $text) {

    
    if (!empty($CFG->formatstring)) {
        return $text;
    }
                                            
/// Do a quick check using stripos to avoid unnecessary work
    if (strpos($text, '@') === false) {
        return $text;
    }

/// There might be an email in here somewhere so continue ...
    $matches = array();

/// regular expression to define a standard email string.
    $emailregex = '((?:[\w\.\-])+\@(?:(?:[a-zA-Z\d\-])+\.)+(?:[a-zA-Z\d]{2,4}))';

/// pattern to find a mailto link with the linked text.
    $pattern = '|(<a\s+href\s*=\s*[\'"]?mailto:)'.$emailregex.'([\'"]?\s*>)'.'(.*)'.'(</a>)|iU';
    $text = preg_replace_callback($pattern, 'alter_mailto', $text);

/// pattern to find any other email address in the text.
    $pattern = '/(^|\s+|>)'.$emailregex.'($|\s+|\.\s+|\.$|<)/i';
    $text = preg_replace_callback($pattern, 'alter_email', $text);

    return $text;
}


function alter_email($matches) {
    return $matches[1].obfuscate_text($matches[2]).$matches[3];
}


function alter_mailto($matches) {
    return obfuscate_mailto($matches[2], $matches[4]);
}

?>
