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
    var $work_phrase;
    var $work_hreftagbegin;
    var $work_hreftagend;
    var $work_casesensitive;
    var $work_fullmatch;
    var $work_calculated;

    /// a constructor just because I like constructing
    function filterobject($phrase, $hreftagbegin='<span class="highlight">', 
                                   $hreftagend='</span>', 
                                   $casesensitive=false, 
                                   $fullmatch=false) {

        $this->phrase           = $phrase;
        $this->hreftagbegin     = $hreftagbegin;
        $this->hreftagend       = $hreftagend;
        $this->casesensitive    = $casesensitive;
        $this->fullmatch        = $fullmatch;
        $this->work_calculated  = false;

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
function filter_phrases ($text, &$link_array, $ignoretagsopen=NULL, $ignoretagsclose=NULL) {

    global $CFG;

    static $usedphrases;

    $ignoretags = array();  //To store all the enclosig tags to be completely ignored
    $tags = array();        //To store all the simple tags to be ignored

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


////Remove everything enclosed by the ignore tags from $text    
    filter_save_ignore_tags($text,$filterignoretagsopen,$filterignoretagsclose,$ignoretags);

/// Remove tags from $text
    filter_save_tags($text,$tags);

/// Time to cycle through each phrase to be linked
    $size = sizeof($link_array);
    for ($n=0; $n < $size; $n++) {
        $linkobject =& $link_array[$n];

    /// Set some defaults if certain properties are missing
    /// Properties may be missing if the filterobject class has not been used to construct the object
        if (empty($linkobject->phrase)) {
            continue;
        }

    /// Avoid integers < 1000 to be linked. See bug 1446.
        $intcurrent = intval($linkobject->phrase);
        if (!empty($intcurrent) && strval($intcurrent) == $linkobject->phrase && $intcurrent < 1000) {
            continue;
        }

    /// All this work has to be done ONLY it it hasn't been done before
	if (!$linkobject->work_calculated) {
            if (!isset($linkobject->hreftagbegin) or !isset($linkobject->hreftagend)) {
                $linkobject->work_hreftagbegin = '<span class="highlight"';
                $linkobject->work_hreftagend   = '</span>';
            } else {
                $linkobject->work_hreftagbegin = $linkobject->hreftagbegin;
                $linkobject->work_hreftagend   = $linkobject->hreftagend;
            }
            if (empty($linkobject->casesensitive)) {
                $linkobject->work_casesensitive = false;
            } else {
                $linkobject->work_casesensitive = true;
            }
            if (empty($linkobject->fullmatch)) {
                $linkobject->work_fullmatch = false;
            } else {
                $linkobject->work_fullmatch = true;
            }

        /// Strip tags out of the phrase
            $linkobject->work_phrase = strip_tags($linkobject->phrase);

        /// Quote any regular expression characters and the delimiter
            $linkobject->work_phrase = preg_quote($linkobject->work_phrase, '/');

        /// Work calculated
            $linkobject->work_calculated = true;
    
        }

    /// If $CFG->filtermatchoneperpage, avoid previously (request) linked phrases
        if (!empty($CFG->filtermatchoneperpage)) {
            if (!empty($usedphrases) && in_array($linkobject->work_phrase,$usedphrases)) {
                continue;
            }
        }

    /// Regular expression modifiers
        $modifiers = ($linkobject->work_casesensitive) ? 's' : 'is';

    /// Do we need to do a fullmatch?
    /// If yes then go through and remove any non full matching entries
        if ($linkobject->work_fullmatch) {
            $notfullmatches = array();
            $regexp = '/'.$filterinvalidprefixes.'('.$linkobject->work_phrase.')|('.$linkobject->work_phrase.')'.$filterinvalidsuffixes.'/'.$modifiers;

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
        if (!empty($CFG->filtermatchonepertext) || !empty($CFG->filtermatchoneperpage)) {
            $resulttext = preg_replace('/('.$linkobject->work_phrase.')/'.$modifiers, 
                                      $linkobject->work_hreftagbegin.'$1'.$linkobject->work_hreftagend, $text, 1);
        } else {
            $resulttext = preg_replace('/('.$linkobject->work_phrase.')/'.$modifiers, 
                                      $linkobject->work_hreftagbegin.'$1'.$linkobject->work_hreftagend, $text);
        }


    /// If the text has changed we have to look for links again
        if ($resulttext != $text) {
        /// Set $text to $resulttext
            $text = $resulttext;
        /// Remove everything enclosed by the ignore tags from $text    
            filter_save_ignore_tags($text,$filterignoretagsopen,$filterignoretagsclose,$ignoretags);
        /// Remove tags from $text
            filter_save_tags($text,$tags);
        /// If $CFG->filtermatchoneperpage, save linked phrases to request
            if (!empty($CFG->filtermatchoneperpage)) {
                $usedphrases[] = $linkobject->work_phrase;
            }
        }


    /// Replace the not full matches before cycling to next link object
        if (!empty($notfullmatches)) {
            $text = str_replace(array_keys($notfullmatches),$notfullmatches,$text);
            unset($notfullmatches);
        }
    }

/// Rebuild the text with all the excluded areas

    if (!empty($tags)) {
        $text = str_replace(array_keys($tags), $tags, $text);
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

/**
 * Extract open/lose tags and their contents to avoid being processed by filters.
 * Useful to extract pieces of code like <a>...</a> tags. It returns the text
 * converted with some <#x.x#> codes replacing the extracted text. Such extracted
 * texts are returned in the ignoretags array (as values), with codes as keys.
 *
 * param  text                  the text that we are filtering (in/out)
 * param  filterignoretagsopen  an array of open tags to start searching
 * param  filterignoretagsclose an array of close tags to end searching 
 * param  ignoretags            an array of saved strings useful to rebuild the original text (in/out)
 **/
function filter_save_ignore_tags(&$text,$filterignoretagsopen,$filterignoretagsclose,&$ignoretags) {

/// Remove everything enclosed by the ignore tags from $text
    foreach ($filterignoretagsopen as $ikey=>$opentag) {
        $closetag = $filterignoretagsclose[$ikey];
    /// form regular expression
        $opentag  = str_replace('/','\/',$opentag); // delimit forward slashes
        $closetag = str_replace('/','\/',$closetag); // delimit forward slashes
        $pregexp = '/'.$opentag.'(.+?)'.$closetag.'/is';
        
        preg_match_all($pregexp, $text, $list_of_ignores);
        foreach (array_unique($list_of_ignores[0]) as $key=>$value) {
            $prefix = (string)(count($ignoretags) + 1);
            $ignoretags['<#'.$prefix.'.'.$key.'#>'] = $value;
        }
        if (!empty($ignoretags)) {
            $text = str_replace($ignoretags,array_keys($ignoretags),$text);
        }
    }
}

/**
 * Extract tags (any text enclosed by < and > to avoid being processed by filters.
 * It returns the text converted with some <%x.x%> codes replacing the extracted text. Such extracted
 * texts are returned in the tags array (as values), with codes as keys.
 *      
 * param  text   the text that we are filtering (in/out)
 * param  tags   an array of saved strings useful to rebuild the original text (in/out)
 **/
function filter_save_tags(&$text,&$tags) {

    preg_match_all('/<([^#%*].*?)>/is',$text,$list_of_newtags);
    foreach (array_unique($list_of_newtags[0]) as $ntkey=>$value) {
        $prefix = (string)(count($tags) + 1);
        $tags['<%'.$prefix.'.'.$ntkey.'%>'] = $value;
    }
    if (!empty($tags)) {
        $text = str_replace($tags,array_keys($tags),$text);
    }
}

?>
