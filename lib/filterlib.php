<?php // $Id$
      // Contains special functions that are particularly useful to filters


/**
 * This is just a little object to define a phrase and some instructions 
 * for how to process it.  Filters can create an array of these to pass 
 * to the filter_phrases function below.
 **/
class filterobject {
    var $phrase;
    var $hreftagbegin;
    var $hreftagend;
    var $casesensitive;
    var $fullmatch;

    /// a constructor just because I like constructing
    function filterobject($phrase, $hreftagbegin='<span class="highlight">', 
                                   $hreftagend='</span>', 
                                   $casesensitive=false, 
                                   $fullmatch=false) {

        $this->phrase        = $phrase;
        $this->hreftagbegin  = $hreftagbegin;
        $this->hreftagend    = $hreftagend;
        $this->casesensitive = $casesensitive;
        $this->fullmatch     = $fullmatch;
    }
}


/**
 * Process phrases intelligently found within a HTML text (such as adding links)
 *
 * param  text             the text that we are filtering
 * param  link_array       an array of filterobjects
 * param  ignoretagsopen   an array of opening tags that we should ignore while filtering
 * param  ignoretagsclose  an array of corresponding closing tags
 **/
function filter_phrases ($text, $link_array, $ignoretagsopen=NULL, $ignoretagsclose=NULL) {

/// A list of open/close tags that we should not replace within
/// No reason why you can't put full preg expressions in here too
/// eg '<script(.+?)>' to match any type of script tag
    $filterignoretagsopen  = array('<head>' , '<nolink>' , '<span class="nolink">');
    $filterignoretagsclose = array('</head>', '</nolink>', '</span>');

/// Invalid prefixes and suffixes for the fullmatch searches
    $filterinvalidprefixes = '([a-zA-Z0-9])';
    $filterinvalidsuffixes  = '([a-zA-Z0-9])';


/// Add the user defined ignore tags to the default list
/// Unless specified otherwise, we will not replace within <a></a> tags
    if ( $ignoretagsopen === NULL ) {
        //$ignoretagsopen  = array('<a(.+?)>');
        $ignoretagsopen  = array('<a[^>]+?>');
        $ignoretagsclose = array('</a>');
    }
    
    if ( is_array($ignoretagsopen) ) {
        foreach ($ignoretagsopen as $open) $filterignoretagsopen[] = $open;
        foreach ($ignoretagsclose as $close) $filterignoretagsclose[] = $close;
    }


/// Remove everything enclosed by the ignore tags from $text
    $ignoretags = array();
    foreach ($filterignoretagsopen as $ikey=>$opentag) {
        $closetag = $filterignoretagsclose[$ikey];
    /// form regular expression
        $opentag  = str_replace('/','\/',$opentag); // delimit forward slashes
        $closetag = str_replace('/','\/',$closetag); // delimit forward slashes
        $pregexp = '/'.$opentag.'(.+?)'.$closetag.'/is';
        
        preg_match_all($pregexp, $text, $list_of_ignores);
        foreach (array_unique($list_of_ignores[0]) as $key=>$value) {
            $ignoretags['<#'.$ikey.'.'.$key.'#>'] = $value;
        }
        if (!empty($ignoretags)) {
            $text = str_replace($ignoretags,array_keys($ignoretags),$text);
        }
    }


/// Remove tags from $text
    $tags = array();
    preg_match_all('/<[^\#](.*?)>/is',$text,$list_of_tags);
    foreach (array_unique($list_of_tags[0]) as $key=>$value) {
        $tags['<|'.$key.'|>'] = $value;
    }
    if (!empty($tags)) {
        $text = str_replace($tags,array_keys($tags),$text);
    }


/// Time to cycle through each phrase to be linked
    foreach ($link_array as $linkobject) {

    /// Set some defaults if certain properties are missing
    /// Properties may be missing if the filterobject class has not been used to construct the object
        if (!isset($linkobject->phrase)) {
            continue;
        }
        if (!isset($linkobject->hreftagbegin) or !isset($linkobject->hreftagend)) {
            $linkobject->hreftagbegin = '<span class="highlight"';
            $linkobject->hreftagend   = '</span>';
        }
        if (!isset($linkobject->casesensitive)) {
            $linkobject->casesensitive = false;
        }
        if (!isset($linkobject->fullmatch)) {
            $linkobject->fullmatch = false;
        }


    /// Avoid integers < 1000 to be linked. See bug 1446.
        $intcurrent = intval($linkobject->phrase);
        if (!empty($intcurrent) && strval($intcurrent) == $linkobject->phrase && $intcurrent < 1000) {
            continue;
        }


    /// Strip tags out of the phrase
        $linkobject->phrase = strip_tags($linkobject->phrase);

    /// Quote any regular expression characters and the delimiter
        $linkobject->phrase = preg_quote($linkobject->phrase, '/');
    
    /// Regular expression modifiers
        $modifiers = ($linkobject->casesensitive) ? 's' : 'is';
    
    /// Do we need to do a fullmatch?
    /// If yes then go through and remove any non full matching entries
        if ($linkobject->fullmatch) {
            $notfullmatches = array();
            $regexp = '/'.$filterinvalidprefixes.'('.$linkobject->phrase.')|('.$linkobject->phrase.')'.$filterinvalidsuffixes.'/'.$modifiers;

            preg_match_all($regexp,$text,$list_of_notfullmatches);

            if ($list_of_notfullmatches) {
                foreach (array_unique($list_of_notfullmatches[0]) as $key=>$value) {
                    $notfullmatches['<*'.$key.'*>'] = $value;
                }
                if (!empty($notfullmatches)) {
                    $text = str_replace($notfullmatches,array_keys($notfullmatches),$text);
                }
            }
        }

    /// Finally we do our highlighting
        if (!empty($CFG->filtermatchonepertext)) {
            $text = preg_replace('/('.$linkobject->phrase.')/'.$modifiers, 
                                      $linkobject->hreftagbegin.'$1'.$linkobject->hreftagend, $text, 1);
        } else {
            $text = preg_replace('/('.$linkobject->phrase.')/'.$modifiers, 
                                      $linkobject->hreftagbegin.'$1'.$linkobject->hreftagend, $text);
        }


    /// Replace the not full matches before cycling to next link object
        if (!empty($notfullmatches)) {
            $text = str_replace(array_keys($notfullmatches),$notfullmatches,$text);
            unset($notfullmatches);
        }


    /// We need to remove any tags we've just added
        if (!isset($newtagsarray)) {
            $newtagsarray = array();
        }
        $newtagsprefix = (string)(count($newtagsarray) + 1);
        $newtags = array();
        preg_match_all('/<(.+?)>/is',$text,$list_of_newtags);
        foreach (array_unique($list_of_newtags[0]) as $ntkey=>$value) {
            $newtags['<%'.$newtagsprefix.'.'.$ntkey.'%>'] = $value;
        }
        if (!empty($newtags)) {
            $text = str_replace($newtags,array_keys($newtags),$text);
            $newtagsarray[] = $newtags;
        }
        unset($newtags);
    
    }


/// Rebuild the text with all the excluded areas

    if (!empty($newtagsarray)) {
        $newtagsarray = array_reverse($newtagsarray, true);
        foreach ($newtagsarray as $newtags) {
            $text = str_replace(array_keys($newtags), $newtags, $text);
        }
    }

    if (!empty($tags)) {
        $text = str_replace(array_keys($tags),$tags,$text);
    }
    if (!empty($ignoretags)) {
        $text = str_replace(array_keys($ignoretags),$ignoretags,$text);
    }

    return $text;

}



function filter_remove_duplicates($linkarray) {

    $concepts  = array(); // keep a record of concepts as we cycle through
    $lconcepts = array(); // a lower case version for case insensitive

    $cleanlinks = array();
    
    foreach ($linkarray as $key=>$filterobject) {
        if ($filterobject->casesensitive) {
            $exists = in_array($filterobject->phrase, $concepts);
        } else {
            $exists = in_array(strtolower($filterobject->phrase), $lconcepts);
        }
        
        if (!$exists) {
            $cleanlinks[] = $filterobject;
            $concepts[] = $filterobject->phrase;
            $lconcepts[] = strtolower($filterobject->phrase);
        }
    }

    return $cleanlinks;
}

?>
