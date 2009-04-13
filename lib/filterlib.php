<?php // $Id$
      // Contains special functions that are particularly useful to filters

/**
 * The states a filter can be in, stored in the filter_active table.
 */
define('TEXTFILTER_ON', 1);
define('TEXTFILTER_OFF', -1);
define('TEXTFILTER_DISABLED', -9999);

abstract class filter_base {
    public static $filters = array();
    protected $courseid;
    protected $format;
    protected $options;

    public function __construct($courseid, $format, $options) {
        $this->courseid = $courseid;
        $this->format   = $format;
        $this->options  = $options;
    }

    public static function addfilter($classname, $obj) {
        if (empty(self::$filters[$classname])) {
            self::$filters[$classname] = $obj;
            return true;
        } else {
            return false;
        }
    }

    public static function do_filter($text, $courseid = null) {
        global $CFG;

        foreach (self::$filters as $n=>$obj) {
            $text = $obj->filter($text); 
        }

        // back compatable with old filter plugins
        if (isset($CFG->textfilters)) {
            $textfilters = explode(',', $CFG->textfilters);
            foreach ($textfilters as $v) {
                $text_filter = basename($v).'_filter';
                if (empty(self::$filters[$text_filter]) && is_readable($CFG->dirroot .'/'. $v .'/filter.php')) {
                    include_once($CFG->dirroot .'/'. $v .'/filter.php');
                    if (function_exists($text_filter)) {
                        $text = $text_filter($courseid, $text);
                    }
                }
            }
        }
        return $text;
    }

    public function hash() {
        return __CLASS__;
    }

    // filter plugin must overwrite this function to filter
    abstract function filter($text);
}

/// Define one exclusive separator that we'll use in the temp saved tags
/// keys. It must be something rare enough to avoid having matches with
/// filterobjects. MDL-18165
define('EXCL_SEPARATOR', '-%-');

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
    var $replacementphrase;
    var $work_phrase;
    var $work_hreftagbegin;
    var $work_hreftagend;
    var $work_casesensitive;
    var $work_fullmatch;
    var $work_replacementphrase;
    var $work_calculated;

    /// a constructor just because I like constructing
    function filterobject($phrase, $hreftagbegin='<span class="highlight">', 
                                   $hreftagend='</span>', 
                                   $casesensitive=false, 
                                   $fullmatch=false,
                                   $replacementphrase=NULL) {

        $this->phrase           = $phrase;
        $this->hreftagbegin     = $hreftagbegin;
        $this->hreftagend       = $hreftagend;
        $this->casesensitive    = $casesensitive;
        $this->fullmatch        = $fullmatch;
        $this->replacementphrase= $replacementphrase;
        $this->work_calculated  = false;

    }
}

/**
 * Look up the name of this filter in the most appropriate location.
 * If $filterlocation = 'mod' then does get_string('filtername', $filter);
 * else if $filterlocation = 'filter' then does get_string('filtername', 'filter_' . $filter);
 * with a fallback to get_string('filtername', $filter) for backwards compatibility.
 * These are the only two options supported at the moment.
 * @param string $filterlocation 'filter' or 'mod'.
 * @param string $filter the folder name where the filter lives.
 * @return string the human-readable name for this filter.
 */
function filter_get_name($filterlocation, $filter) {
    switch ($filterlocation) {
        case 'filter':
            $strfiltername = get_string('filtername', 'filter_' . $filter);
            if (substr($strfiltername, 0, 2) != '[[') {
                // found a valid string.
                return $strfiltername;
            }
            // Fall through to try the legacy location.

        case 'mod':
            $strfiltername = get_string('filtername', $filter);
            if (substr($strfiltername, 0, 2) == '[[') {
                $strfiltername .= ' (' . $filterlocation . '/' . $filter . ')';
            }
            return $strfiltername;

        default:
            throw new coding_exception('Unknown filter location ' . $filterlocation);
    }
}

/**
 * Get the names of all the filters installed in this Moodle.
 * @return array path => filter name from the appropriate lang file. e.g.
 * array('mod/glossary' => 'Glossary Auto-linking', 'filter/tex' => 'TeX Notation');
 * sorted in alphabetical order of name.
 */
function filter_get_all_installed() {
    global $CFG;
    $filternames = array();
    $filterlocations = array('mod', 'filter');
    foreach ($filterlocations as $filterlocation) {
        $filters = get_list_of_plugins($filterlocation);
        foreach ($filters as $filter) {
            $path = $filterlocation . '/' . $filter;
            if (is_readable($CFG->dirroot . '/' . $path . '/filter.php')) {
                $strfiltername = filter_get_name($filterlocation, $filter);
                $filternames[$path] = $strfiltername;
            }
        }
    }
    asort($filternames, SORT_LOCALE_STRING);
    return $filternames;
}

/**
 * Set the global activated state for a text filter.
 * @param string$filter The filter name, for example 'filter/tex' or 'mod/glossary'.
 * @param integer $state One of the values TEXTFILTER_ON, TEXTFILTER_OFF or TEXTFILTER_DISABLED.
 * @param integer $sortorder (optional) a position in the sortorder to place this filter.
 *      If not given defaults to:
 *      No change in order if we are updating an exsiting record, and not changing to or from TEXTFILTER_DISABLED.
 *      Just after the last currently active filter for a change to TEXTFILTER_ON or TEXTFILTER_OFF
 *      Just after the very last filter for a change to TEXTFILTER_DISABLED
 */
function filter_set_global_state($filter, $state, $sortorder = false) {
    global $DB;

    // Check requested state is valid.
    if (!in_array($state, array(TEXTFILTER_ON, TEXTFILTER_OFF, TEXTFILTER_DISABLED))) {
        throw new coding_exception("Illegal option '$state' passed to filter_set_global_state. ' .
                'Must be one of TEXTFILTER_ON, TEXTFILTER_OFF or TEXTFILTER_DISABLED.");
    }

    // Check sortorder is valid.
    if ($sortorder !== false) {
        if ($sortorder < 1 || $sortorder > $DB->get_field('filter_active', 'MAX(sortorder)', array()) + 1) {
            throw new coding_exception("Invalid sort order passed to filter_set_global_state.");
        }
    }

    // See if there is an existing record.
    $syscontext = get_context_instance(CONTEXT_SYSTEM);
    $rec = $DB->get_record('filter_active', array('filter' => $filter, 'contextid' => $syscontext->id));
    if (empty($rec)) {
        $insert = true;
        $rec = new stdClass;
        $rec->filter = $filter;
        $rec->contextid = $syscontext->id;
    } else {
        $insert = false;
        if ($sortorder === false && !($rec->active == TEXTFILTER_DISABLED xor $state == TEXTFILTER_DISABLED)) {
            $sortorder = $rec->sortorder;
        }
    }

    // Automatic sort order.
    if ($sortorder === false) {
        if ($state == TEXTFILTER_DISABLED) {
            $prevmaxsortorder = $DB->get_field('filter_active', 'MAX(sortorder)', array());
        } else {
            $prevmaxsortorder = $DB->get_field_select('filter_active', 'MAX(sortorder)', 'active <> ?', array(TEXTFILTER_DISABLED));
        }
        if (empty($prevmaxsortorder)) {
            $sortorder = 1;
        } else {
            $sortorder = $prevmaxsortorder + 1;
            if (!$insert && $state == TEXTFILTER_DISABLED) {
                $sortorder = $prevmaxsortorder;
            }
        }
    }

    // Move any existing records out of the way of the sortorder.
    if ($insert) {
        $DB->execute('UPDATE {filter_active} SET sortorder = sortorder + 1 WHERE sortorder >= ?', array($sortorder));
    } else if ($sortorder != $rec->sortorder) {
        $sparesortorder = $DB->get_field('filter_active', 'MIN(sortorder)', array()) - 1;
        $DB->set_field('filter_active', 'sortorder', $sparesortorder, array('filter' => $filter, 'contextid' => $syscontext->id));
        if ($sortorder < $rec->sortorder) {
            $DB->execute('UPDATE {filter_active} SET sortorder = sortorder + 1 WHERE sortorder >= ? AND sortorder < ?',
                    array($sortorder, $rec->sortorder));
        } else if ($sortorder > $rec->sortorder) {
            $DB->execute('UPDATE {filter_active} SET sortorder = sortorder - 1 WHERE sortorder <= ? AND sortorder > ?',
                    array($sortorder, $rec->sortorder));
        }
    }

    // Insert/update the new record.
    $rec->active = $state;
    $rec->sortorder = $sortorder;
    if ($insert) {
        $DB->insert_record('filter_active', $rec);
    } else {
        $DB->update_record('filter_active', $rec);
    }
}

/**
 * Set a particular local config variable for a filter in a context.
 * @param string $filter The filter name, for example 'filter/tex' or 'mod/glossary'.
 * @param integer $contextid The ID of the context to get the local config for.
 * @param string $name the setting name.
 * @param string $value the corresponding value.
 */
function filter_set_local_config($filter, $contextid, $name, $value) {
    global $DB;
    $rec = $DB->get_record('filter_config', array('filter' => $filter, 'contextid' => $contextid, 'name' => $name));
    $insert = false;
    if (empty($rec)) {
        $insert = true;
        $rec = new stdClass;
        $rec->filter = $filter;
        $rec->contextid = $contextid;
        $rec->name = $name;
    }

    $rec->value = $value;

    if ($insert) {
        $DB->insert_record('filter_config', $rec);
    } else {
        $DB->update_record('filter_config', $rec);
    }
}

/**
 * Get local config variables for a filter in a context. Normally (when your
 * filter is running) you don't need to call this, becuase the config is fetched
 * for you automatically. You only need this, for example, when you are getting
 * the config so you can show the user an editing from.
 * @param string $filter The filter name, for example 'filter/tex' or 'mod/glossary'.
 * @param integer $contextid The ID of the context to get the local config for.
 * @return array of name => value pairs.
 */
function filter_get_local_config($filter, $contextid) {
    global $DB;
    return $DB->get_records_menu('filter_config', array('filter' => $filter, 'contextid' => $contextid), '', 'name,value');
}

/**
 * Process phrases intelligently found within a HTML text (such as adding links)
 *
 * param  text             the text that we are filtering
 * param  link_array       an array of filterobjects
 * param  ignoretagsopen   an array of opening tags that we should ignore while filtering
 * param  ignoretagsclose  an array of corresponding closing tags
 **/
function filter_phrases($text, &$link_array, $ignoretagsopen=NULL, $ignoretagsclose=NULL) {

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
/// Every "word" character, but the underscore, is a invalid suffix or prefix.
/// (nice to use this because it includes national characters (accents...) as word characters.
    $filterinvalidprefixes = '([^\W_])';
    $filterinvalidsuffixes = '([^\W_])';

/// Add the user defined ignore tags to the default list
/// Unless specified otherwise, we will not replace within <a></a> tags
    if ( $ignoretagsopen === NULL ) {
        //$ignoretagsopen  = array('<a(.+?)>');
        $ignoretagsopen  = array('<a\s[^>]+?>');
        $ignoretagsclose = array('</a>');
    }
    
    if ( is_array($ignoretagsopen) ) {
        foreach ($ignoretagsopen as $open) $filterignoretagsopen[] = $open;
        foreach ($ignoretagsclose as $close) $filterignoretagsclose[] = $close;
    }

    //// Double up some magic chars to avoid "accidental matches"
    $text = preg_replace('/([#*%])/','\1\1',$text);


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

        /// Double up chars to protect true duplicates
        /// be cleared up before returning to the user.
            $linkobject->work_hreftagbegin = preg_replace('/([#*%])/','\1\1',$linkobject->work_hreftagbegin);

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

        /// Double up chars that might cause a false match -- the duplicates will
        /// be cleared up before returning to the user.
            $linkobject->work_phrase = preg_replace('/([#*%])/','\1\1',$linkobject->work_phrase);

        /// Set the replacement phrase properly
            if ($linkobject->replacementphrase) {    //We have specified a replacement phrase
            /// Strip tags
                $linkobject->work_replacementphrase = strip_tags($linkobject->replacementphrase);
            } else {                                 //The replacement is the original phrase as matched below
                $linkobject->work_replacementphrase = '$1';
            }

        /// Quote any regular expression characters and the delimiter in the work phrase to be searched
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
        $modifiers = ($linkobject->work_casesensitive) ? 's' : 'isu'; // works in unicode mode!

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
                                      $linkobject->work_hreftagbegin.
                                      $linkobject->work_replacementphrase.
                                      $linkobject->work_hreftagend, $text, 1);
        } else {
            $resulttext = preg_replace('/('.$linkobject->work_phrase.')/'.$modifiers, 
                                      $linkobject->work_hreftagbegin.
                                      $linkobject->work_replacementphrase.
                                      $linkobject->work_hreftagend, $text);
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
        $ignoretags = array_reverse($ignoretags); /// Reversed so "progressive" str_replace() will solve some nesting problems.
        $text = str_replace(array_keys($ignoretags),$ignoretags,$text);
    }

    //// Remove the protective doubleups 
    $text =  preg_replace('/([#*%])(\1)/','\1',$text);

/// Add missing javascript for popus
    $text = filter_add_javascript($text);


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
            $exists = in_array(moodle_strtolower($filterobject->phrase), $lconcepts);
        }
        
        if (!$exists) {
            $cleanlinks[] = $filterobject;
            $concepts[] = $filterobject->phrase;
            $lconcepts[] = moodle_strtolower($filterobject->phrase);
        }
    }

    return $cleanlinks;
}

/**
 * Extract open/lose tags and their contents to avoid being processed by filters.
 * Useful to extract pieces of code like <a>...</a> tags. It returns the text
 * converted with some <#xEXCL_SEPARATORx#> codes replacing the extracted text. Such extracted
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
        $pregexp = '/'.$opentag.'(.*?)'.$closetag.'/is';
        
        preg_match_all($pregexp, $text, $list_of_ignores);
        foreach (array_unique($list_of_ignores[0]) as $key=>$value) {
            $prefix = (string)(count($ignoretags) + 1);
            $ignoretags['<#'.$prefix.EXCL_SEPARATOR.$key.'#>'] = $value;
        }
        if (!empty($ignoretags)) {
            $text = str_replace($ignoretags,array_keys($ignoretags),$text);
        }
    }
}

/**
 * Extract tags (any text enclosed by < and > to avoid being processed by filters.
 * It returns the text converted with some <%xEXCL_SEPARATORx%> codes replacing the extracted text. Such extracted
 * texts are returned in the tags array (as values), with codes as keys.
 *      
 * param  text   the text that we are filtering (in/out)
 * param  tags   an array of saved strings useful to rebuild the original text (in/out)
 **/
function filter_save_tags(&$text,&$tags) {

    preg_match_all('/<([^#%*].*?)>/is',$text,$list_of_newtags);
    foreach (array_unique($list_of_newtags[0]) as $ntkey=>$value) {
        $prefix = (string)(count($tags) + 1);
        $tags['<%'.$prefix.EXCL_SEPARATOR.$ntkey.'%>'] = $value;
    }
    if (!empty($tags)) {
        $text = str_replace($tags,array_keys($tags),$text);
    }
}

/**
 * Add missing openpopup javascript to HTML files.
 */
function filter_add_javascript($text) {
    global $CFG;

    if (stripos($text, '</html>') === FALSE) {
        return $text; // this is not a html file
    }
    if (strpos($text, 'onclick="return openpopup') === FALSE) {
        return $text; // no popup - no need to add javascript
    }
    $js =" 
    <script type=\"text/javascript\">
    <!--
        function openpopup(url,name,options,fullscreen) {
          fullurl = \"".$CFG->httpswwwroot."\" + url;
          windowobj = window.open(fullurl,name,options);
          if (fullscreen) {
            windowobj.moveTo(0,0);
            windowobj.resizeTo(screen.availWidth,screen.availHeight);
          }
          windowobj.focus();
          return false;
        }
    // -->
    </script>";
    if (stripos($text, '</head>') !== FALSE) {
        //try to add it into the head element
        $text = str_ireplace('</head>', $js.'</head>', $text);
        return $text;
    }

    //last chance - try adding head element
    return preg_replace("/<html.*?>/is", "\\0<head>".$js.'</head>', $text);
}
?>
