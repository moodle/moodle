<?PHP // $Id$

///////////////////////////////////////////////////////////////////////////
// weblib.php - functions for web output
//
// Library of all general-purpose Moodle PHP functions and constants
// that produce HTML output
//
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-2003  Martin Dougiamas  http://dougiamas.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/// Constants

/// Define text formatting types ... eventually we can add Wiki, BBcode etc
define("FORMAT_MOODLE", "0");   // Does all sorts of transformations and filtering
define("FORMAT_HTML",   "1");   // Plain HTML (with some tags stripped)
define("FORMAT_PLAIN",  "2");   // Plain text (even tags are printed in full)
define("FORMAT_WIKI",   "3");   // Wiki-formatted text

$ALLOWED_TAGS =
"<p><br><b><i><u><font><table><tbody><span><div><tr><td><ol><ul><dl><li><dt><dd><h1><h2><h3><h4><h5><h6><hr><img><a><strong><emphasis><em><sup><sub><address><cite><blockquote><pre><strike><embed><object><param><acronym><nolink><style><lang><tex><algebra><math><mi><mn><mo><mtext><mspace><ms><mrow><mfrac><msqrt><mroot><mstyle><merror><mpadded><mphantom><mfenced><msub><msup><msubsup><munder><mover><munderover><mmultiscripts><mtable><mtr><mtd><maligngroup><malignmark><maction><cn><ci><apply><reln><fn><interval><inverse><sep><condition><declare><lambda><compose><ident><quotient><exp><factorial><divide><max><min><minus><plus><power><rem><times><root><gcd><and><or><xor><not><implies><forall><exists><abs><conjugate><eq><neq><gt><lt><geq><leq><ln><log><int><diff><partialdiff><lowlimit><uplimit><bvar><degree><set><list><union><intersect><in><notin><subset><prsubset><notsubset><notprsubset><setdiff><sum><product><limit><tendsto><mean><sdev><variance><median><mode><moment><vector><matrix><matrixrow><determinant><transpose><selector><annotation><semantics><annotation-xml>";


/// Functions

function s($var) {
/// returns $var with HTML characters (like "<", ">", etc.) properly quoted,

    if (empty($var)) {
        return "";
    }
    return htmlSpecialChars(stripslashes_safe($var));
}

function p($var) {
/// prints $var with HTML characters (like "<", ">", etc.) properly quoted,

    if (empty($var)) {
        echo "";
    }
    echo htmlSpecialChars(stripslashes_safe($var));
}

function nvl(&$var, $default="") {
/// if $var is undefined, return $default, otherwise return $var

    return isset($var) ? $var : $default;
}

function strip_querystring($url) {
/// takes a URL and returns it without the querystring portion

    if ($commapos = strpos($url, '?')) {
        return substr($url, 0, $commapos);
    } else {
        return $url;
    }
}

function get_referer() {
/// returns the URL of the HTTP_REFERER, less the querystring portion

    return strip_querystring(nvl($_SERVER["HTTP_REFERER"]));
}


function me() {
/// returns the name of the current script, WITH the querystring portion.
/// this function is necessary because PHP_SELF and REQUEST_URI and SCRIPT_NAME
/// return different things depending on a lot of things like your OS, Web
/// server, and the way PHP is compiled (ie. as a CGI, module, ISAPI, etc.)

    if (!empty($_SERVER["REQUEST_URI"])) {
        return $_SERVER["REQUEST_URI"];

    } else if (!empty($_SERVER["PHP_SELF"])) {
        if (!empty($_SERVER["QUERY_STRING"])) {
            return $_SERVER["PHP_SELF"]."?".$_SERVER["QUERY_STRING"];
        }
        return $_SERVER["PHP_SELF"];

    } else if (!empty($_SERVER["SCRIPT_NAME"])) {
        if (!empty($_SERVER["QUERY_STRING"])) {
            return $_SERVER["SCRIPT_NAME"]."?".$_SERVER["QUERY_STRING"];
        }
        return $_SERVER["SCRIPT_NAME"];

    } else {
        notify("Warning: Could not find any of these web server variables: \$REQUEST_URI, \$PHP_SELF or \$SCRIPT_NAME");
        return false;
    }
}


function qualified_me() {
/// like me() but returns a full URL

    if (!empty($_SERVER["HTTP_HOST"])) {
        $hostname = $_SERVER["HTTP_HOST"];
    } else if (!empty($_ENV["HTTP_HOST"])) {
        $hostname = $_ENV["HTTP_HOST"];
    } else if (!empty($_SERVER["SERVER_NAME"])) {
        $hostname = $_SERVER["SERVER_NAME"];
    } else if (!empty($_ENV["SERVER_NAME"])) {
        $hostname = $_ENV["SERVER_NAME"];
    } else {
        notify("Warning: could not find the name of this server!");
        return false;
    }

    $protocol = (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on") ? "https://" : "http://";
    $url_prefix = $protocol.$hostname;
    return $url_prefix . me();
}


function match_referer($goodreferer = "") {
/// returns true if the referer is the same as the goodreferer.  If
/// goodreferer is not specified, use qualified_me as the goodreferer
    global $CFG;

    if (empty($CFG->secureforms)) {    // Don't bother checking referer
        return true;
    }

    if ($goodreferer == "nomatch") {   // Don't bother checking referer
        return true;
    }

    if (empty($goodreferer)) {
        $goodreferer = qualified_me();
    }
    return $goodreferer == get_referer();
}

function data_submitted($url="") {
/// Used on most forms in Moodle to check for data
/// Returns the data as an object, if it's found.
/// This object can be used in foreach loops without
/// casting because it's cast to (array) automatically
///
/// Checks that submitted POST data exists, and also
/// checks the referer against the given url (it uses
/// the current page if none was specified.

    global $CFG;

    if (empty($_POST)) {
        return false;

    } else {
        if (match_referer($url)) {
            return (object)$_POST;
        } else {
            if ($CFG->debug > 10) {
                notice("The form did not come from this page! (referer = ".get_referer().")");
            }
            return false;
        }
    }
}

function stripslashes_safe($string) {
/// stripslashes() removes ALL backslashes even from strings
/// so  C:\temp becomes C:temp  ... this isn't good.
/// The following should work as a fairly safe replacement
/// to be called on quoted AND unquoted strings (to be sure)

    $string = str_replace("\\'", "'", $string);
    $string = str_replace('\\"', '"', $string);
    //$string = str_replace('\\\\', '\\', $string);  // why?
    return $string;
}


function break_up_long_words($string, $maxsize=20, $cutchar=' ') {
/// Given some normal text, this function will break up any
/// long words to a given size, by inserting the given character

    if (in_array(current_language(), array('ja', 'zh_cn', 'zh_tw', 'zh_tw_utf8'))) {  // Multibyte languages
        return $string;
    }

    $output = '';
    $length = strlen($string);
    $wordlength = 0;

    for ($i=0; $i<$length; $i++) {
        $char = $string[$i];
        if ($char == ' ' or $char == "\t" or $char == "\n" or $char == "\r") {
            $wordlength = 0;
        } else {
            $wordlength++;
            if ($wordlength > $maxsize) {
                $output .= $cutchar;
                $wordlength = 0;
            }
        }
        $output .= $char;
    }
    return $output;
}


if (!function_exists('str_ireplace')) {    /// Only exists in PHP 5
    function str_ireplace($find, $replace, $string) {
    /// This does a search and replace, ignoring case
    /// This function is only used for versions of PHP older than version 5
    /// which do not have a native version of this function.
    /// Taken from the PHP manual, by bradhuizenga @ softhome.net

        if (!is_array($find)) {
            $find = array($find);
        }

        if(!is_array($replace)) {
            if (!is_array($find)) {
                $replace = array($replace);
            } else {
                // this will duplicate the string into an array the size of $find
                $c = count($find);
                $rString = $replace;
                unset($replace);
                for ($i = 0; $i < $c; $i++) {
                    $replace[$i] = $rString;
                }
            }
        }

        foreach ($find as $fKey => $fItem) {
            $between = explode(strtolower($fItem),strtolower($string));
            $pos = 0;
            foreach($between as $bKey => $bItem) {
                $between[$bKey] = substr($string,$pos,strlen($bItem));
                $pos += strlen($bItem) + strlen($fItem);
            }
            $string = implode($replace[$fKey],$between);
        }
        return ($string);
    }
}

if (!function_exists('stripos')) {    /// Only exists in PHP 5
    function stripos($haystack, $needle, $offset=0) {
    /// This function is only used for versions of PHP older than version 5
    /// which do not have a native version of this function.
    /// Taken from the PHP manual, by dmarsh @ spscc.ctc.edu
        return strpos(strtoupper($haystack), strtoupper($needle), $offset);
    }
}

function read_template($filename, &$var) {
/// return a (big) string containing the contents of a template file with all
/// the variables interpolated.  all the variables must be in the $var[] array or
/// object (whatever you decide to use).
///
/// WARNING: do not use this on big files!!

    $temp = str_replace("\\", "\\\\", implode(file($filename), ""));
    $temp = str_replace('"', '\"', $temp);
    eval("\$template = \"$temp\";");
    return $template;
}

function checked(&$var, $set_value = 1, $unset_value = 0) {
/// if variable is set, set it to the set_value otherwise set it to the
/// unset_value.  used to handle checkboxes when you are expecting them from
/// a form

    if (empty($var)) {
        $var = $unset_value;
    } else {
        $var = $set_value;
    }
}

function frmchecked(&$var, $true_value = "checked", $false_value = "") {
/// prints the word "checked" if a variable is true, otherwise prints nothing,
/// used for printing the word "checked" in a checkbox form input

    if ($var) {
        echo $true_value;
    } else {
        echo $false_value;
    }
}


function link_to_popup_window ($url, $name="popup", $linkname="click here",
                               $height=400, $width=500, $title="Popup window", $options="none") {
/// This will create a HTML link that will work on both
/// Javascript and non-javascript browsers.
/// Relies on the Javascript function openpopup in javascript.php
/// $url must be relative to home page  eg /mod/survey/stuff.php

    global $CFG;

    if ($options == "none") {
        $options = "menubar=0,location=0,scrollbars,resizable,width=$width,height=$height";
    }
    $fullscreen = 0;

    echo "<a target=\"$name\" title=\"$title\" href=\"$CFG->wwwroot$url\" ".
         "onClick=\"return openpopup('$url', '$name', '$options', $fullscreen);\">$linkname</a>\n";
}


function button_to_popup_window ($url, $name="popup", $linkname="click here",
                                 $height=400, $width=500, $title="Popup window", $options="none") {
/// This will create a HTML link that will work on both
/// Javascript and non-javascript browsers.
/// Relies on the Javascript function openpopup in javascript.php
/// $url must be relative to home page  eg /mod/survey/stuff.php

    global $CFG;

    if ($options == "none") {
        $options = "menubar=0,location=0,scrollbars,resizable,width=$width,height=$height";
    }
    $fullscreen = 0;

    echo "<input type=\"button\" name=\"popupwindow\" title=\"$title\" value=\"$linkname ...\" ".
         "onClick=\"return openpopup('$url', '$name', '$options', $fullscreen);\">\n";
}


function close_window_button() {
/// Prints a simple button to close a window

    echo "<center>\n";
    echo "<script>\n";
    echo "<!--\n";
    echo "document.write('<form>');\n";
    echo "document.write('<input type=\"button\" onClick=\"self.close();\" value=\"".get_string("closewindow")."\" />');\n";
    echo "document.write('</form>');\n";
    echo "-->\n";
    echo "</script>\n";
    echo "<noscript>\n";
    echo "<a href=\"".$_SERVER['HTTP_REFERER']."\"><---</a>\n";
    echo "</noscript>\n";
    echo "</center>\n";
}


function choose_from_menu ($options, $name, $selected="", $nothing="choose", $script="", $nothingvalue="0", $return=false) {
/// Given an array of value, creates a popup menu to be part of a form
/// $options["value"]["label"]

    if ($nothing == "choose") {
        $nothing = get_string("choose")."...";
    }

    if ($script) {
        $javascript = "onChange=\"$script\"";
    } else {
        $javascript = "";
    }

    $output = "<select name=\"$name\" $javascript>\n";
    if ($nothing) {
        $output .= "   <option value=\"$nothingvalue\"\n";
        if ($nothingvalue === $selected) {
            $output .= " selected=\"true\"";
        }
        $output .= ">$nothing</option>\n";
    }
    if (!empty($options)) {
        foreach ($options as $value => $label) {
            $output .= "   <option value=\"$value\"";
            if ($value == $selected) {
                $output .= " selected=\"true\"";
            }
            if ($label === "") {
                $output .= ">$value</option>\n";
            } else {
                $output .= ">$label</option>\n";
            }
        }
    }
    $output .= "</select>\n";

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

function popup_form ($common, $options, $formname, $selected="", $nothing="choose", $help="", $helptext="", $return=false, $targetwindow="self") {
///  Implements a complete little popup form
///  $common   = the URL up to the point of the variable that changes
///  $options  = A list of value-label pairs for the popup list
///  $formname = name must be unique on the page
///  $selected = the option that is already selected
///  $nothing  = The label for the "no choice" option
///  $help     = The name of a help page if help is required
///  $helptext  = The name of the label for the help button
///  $return   = Boolean indicating whether the function should return the text
///    as a string or echo it directly to the page being rendered

// TODO:
//
//  * Make sure it's W3C conformant (<form name=""> has to go for example)
//  * Code it in a way that doesn't require JS to be on. Example code:
//        $selector .= '<form method="get" action="" style="display: inline;"><span>';
//        $selector .= '<input type="hidden" name="var" value="value" />';
//        if(!empty($morevars)) {
//            $getarray = explode('&amp;', $morevars);
//            foreach($getarray as $thisvar) {
//                $selector .= '<input type="hidden" name="'.strtok($thisvar, '=').'" value="'.strtok('=').'" />';
//            }
//        }
//        $selector .= '<select name="" onchange="form.submit();">';
//        foreach($options as $id => $text) {
//            $selector .= "\n<option value='$id'";
//            if($option->id == $selected) {
//                $selector .= ' selected';
//            }
//            $selector .= '>'.$text."</option>\n";
//        }
//        $selector .= '</select>';
//        $selector .= '<noscript id="unique_id" style="display: inline;"> <input type="submit" value="'.get_string('somestring').'" /></noscript>';
//        $selector .= '<script type="text/javascript">'."\n<!--\n".'document.getElementById("unique_id").style.display = "none";'."\n<!--\n".'</script>';
//        $selector .= '</span></form>';
//

    global $CFG;
    
    if (empty($options)) {
        return '';
    }

    if ($nothing == "choose") {
        $nothing = get_string("choose")."...";
    }

    $startoutput = "<form method=\"get\" target=\"{$CFG->framename}\" name=\"$formname\">";
    $output = "<select name=\"popup\" onchange=\"$targetwindow.location=document.$formname.popup.options[document.$formname.popup.selectedIndex].value\">\n";

    if ($nothing != "") {
        $output .= "   <option value=\"javascript:void(0)\">$nothing</option>\n";
    }

    foreach ($options as $value => $label) {
        if (substr($label,0,2) == "--") {
            $output .= "   <optgroup label=\"$label\"></optgroup>";   // Plain labels
            continue;
        } else {
            $output .= "   <option value=\"$common$value\"";
            if ($value == $selected) {
                $output .= " selected=\"true\"";
            }
        }
        if ($label) {
            $output .= ">$label</option>\n";
        } else {
            $output .= ">$value</option>\n";
        }
    }
    $output .= "</select>";
    $output .= "</form>\n";

    if ($return) {
        return $startoutput.$output;
    } else {
        echo $startoutput;
        if ($help) {
            helpbutton($help, $helptext);
        }
        echo $output;
    }
}



function formerr($error) {
/// Prints some red text
    if (!empty($error)) {
        echo "<font color=\"#ff0000\">$error</font>";
    }
}


function validate_email ($address) {
/// Validates an email to make sure it makes sense.
    return (ereg('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+'.
                  '@'.
                  '[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.'.
                  '[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$',
                  $address));
}

function detect_munged_arguments($string) {
    if (substr_count($string, '..') > 1) {   // We allow one '..' in a URL
        return true;
    }
    if (ereg('[\|\`]', $string)) {  // check for other bad characters
        return true;
    }
    if (empty($string) or $string == '/') {
        return true;
    }

    return false;
}

function get_slash_arguments($file="file.php") {
/// Searches the current environment variables for some slash arguments

    if (!$string = me()) {
        return false;
    }

    $pathinfo = explode($file, $string);

    if (!empty($pathinfo[1])) {
        return $pathinfo[1];
    } else {
        return false;
    }
}

function parse_slash_arguments($string, $i=0) {
/// Extracts arguments from "/foo/bar/something"
/// eg http://mysite.com/script.php/foo/bar/something

    if (detect_munged_arguments($string)) {
        return false;
    }
    $args = explode("/", $string);

    if ($i) {     // return just the required argument
        return $args[$i];

    } else {      // return the whole array
        array_shift($args);  // get rid of the empty first one
        return $args;
    }
}

function format_text_menu() {
/// Just returns an array of formats suitable for a popup menu
    return array (FORMAT_MOODLE => get_string("formattext"),
                  FORMAT_HTML   => get_string("formathtml"),
                  FORMAT_PLAIN  => get_string("formatplain"),
                  FORMAT_WIKI   => get_string("formatwiki"));
}

function format_text($text, $format=FORMAT_MOODLE, $options=NULL, $courseid=NULL ) {
/// Given text in a variety of format codings, this function returns
/// the text as safe HTML.
///
/// $text is raw text (originally from a user)
/// $format is one of the format constants, defined above

    global $CFG, $course;

    if (!empty($CFG->cachetext)) {
        $time = time() - $CFG->cachetext;
        $md5key = md5($text);
        if ($cacheitem = get_record_select('cache_text', "md5key = '$md5key' AND timemodified > '$time'")) {
            return $cacheitem->formattedtext;
        }
    }

    if (empty($courseid)) {
        if (!empty($course->id)) {         // An ugly hack for better compatibility
            $courseid = $course->id;
        }
    }

    $CFG->currenttextiscacheable = true;   // Default status - can be changed by any filter

    switch ($format) {
        case FORMAT_HTML:
            replace_smilies($text);
            $text = filter_text($text, $courseid);
            break;

        case FORMAT_PLAIN:
            $text = htmlentities($text);
            $text = rebuildnolinktag($text);
            $text = str_replace("  ", "&nbsp; ", $text);
            replace_smilies($text);
            $text = nl2br($text);
            break;

        case FORMAT_WIKI:
            $text = wiki_to_html($text);
            $text = rebuildnolinktag($text);
            $text = filter_text($text, $courseid);
            break;

        default:  // FORMAT_MOODLE or anything else
            if (!isset($options->smiley)) {
                $options->smiley=true;
            }
            if (!isset($options->para)) {
                $options->para=true;
            }
            if (!isset($options->newlines)) {
                $options->newlines=true;
            }
            $text = text_to_html($text, $options->smiley, $options->para, $options->newlines);
            $text = filter_text($text, $courseid);
            break;
    }

    if (!empty($CFG->cachetext) and $CFG->currenttextiscacheable) {
        $newrecord->md5key = $md5key;
        $newrecord->formattedtext = addslashes($text);
        $newrecord->timemodified = time();
        insert_record('cache_text', $newrecord);
    }

    return $text;
}

function format_text_email($text, $format) {
/// Given text in a variety of format codings, this function returns
/// the text as plain text suitable for plain email.
///
/// $text is raw text (originally from a user)
/// $format is one of the format constants, defined above

    switch ($format) {

        case FORMAT_PLAIN:
            return $text;
            break;

        case FORMAT_WIKI:
            $text = wiki_to_html($text);
        /// This expression turns links into something nice in a text format. (Russell Jungwirth)
        /// From: http://php.net/manual/en/function.eregi-replace.php and simplified
            $text = eregi_replace('(<a [^<]*href=["|\']?([^ "\']*)["|\']?[^>]*>([^<]*)</a>)','\\3 [\\2]', $text);
            return strtr(strip_tags($text), array_flip(get_html_translation_table(HTML_ENTITIES)));
            break;

        case FORMAT_HTML:
            return html_to_text($text);
            break;

        default:  // FORMAT_MOODLE or anything else
            $text = eregi_replace('(<a [^<]*href=["|\']?([^ "\']*)["|\']?[^>]*>([^<]*)</a>)','\\3 [\\2]', $text);
            return strtr(strip_tags($text), array_flip(get_html_translation_table(HTML_ENTITIES)));
            break;
    }
}


function filter_text($text, $courseid=NULL) {
/// Given some text in HTML format, this function will pass it
/// through any filters that have been defined in $CFG->textfilterx
/// The variable defines a filepath to a file containing the
/// filter function.  The file must contain a variable called
/// $textfilter_function which contains the name of the function
/// with $courseid and $text parameters

    global $CFG;

    if (!empty($CFG->textfilters)) {
        $textfilters = explode(',', $CFG->textfilters);
        foreach ($textfilters as $textfilter) {
            if (is_readable("$CFG->dirroot/$textfilter/filter.php")) {
                include("$CFG->dirroot/$textfilter/filter.php");
                $text = $textfilter_function($courseid, $text);
            }
        }
    }

    return $text;
}


function clean_text($text, $format=FORMAT_MOODLE) {
/// Given raw text (eg typed in by a user), this function cleans it up
/// and removes any nasty tags that could mess up Moodle pages.

    global $ALLOWED_TAGS;

    switch ($format) {
        case FORMAT_PLAIN:
            return $text;

        default:

        /// Remove tags that are not allowed
            $text = strip_tags($text, $ALLOWED_TAGS);

        /// Munge javascript: label
            $text = str_ireplace("javascript:", "Xjavascript:", $text);
            $text = str_ireplace("vbscript:", "Xvbscript:", $text);

        /// Remove script events
            $text = eregi_replace("([^a-z])language([[:space:]]*)=", "\\1Xlanguage=", $text);
            $text = eregi_replace("([^a-z])on([a-z]+)([[:space:]]*)=", "\\1Xon\\2=", $text);

        /// Remove Javascript entities
            $text = eregi_replace("&\{([^};]*)\};", "\\1", $text);

            return $text;
    }
}

function replace_smilies(&$text) {
/// Replaces all known smileys in the text with image equivalents
    global $CFG;

/// this builds the mapping array only once
    static $runonce = false;
    static $e = array();
    static $img = array();
    static $emoticons = array(
        ':-)'  => 'smiley',
        ':)'   => 'smiley',
        ':-D'  => 'biggrin',
        ';-)'  => 'wink',
        ':-/'  => 'mixed',
        'V-.'  => 'thoughtful',
        ':-P'  => 'tongueout',
        'B-)'  => 'cool',
        '^-)'  => 'approve',
        '8-)'  => 'wideeyes',
        ':o)'  => 'clown',
        ':-('  => 'sad',
        ':('   => 'sad',
        '8-.'  => 'shy',
        ':-I'  => 'blush',
        ':-X'  => 'kiss',
        '8-o'  => 'surprise',
        'P-|'  => 'blackeye',
        '8-['  => 'angry',
        'xx-P' => 'dead',
        '|-.'  => 'sleepy',
        '}-]'  => 'evil',
        );

    if ($runonce == false) {  /// After the first time this is not run again
        foreach ($emoticons as $emoticon => $image){
            $alttext = get_string($image, 'pix');

            $e[] = $emoticon;
            $img[] = "<img alt=\"$alttext\" width=\"15\" height=\"15\" src=\"$CFG->pixpath/s/$image.gif\" />";
        }
        $runonce = true;
    }

    // Exclude from transformations all the code inside <script> tags
    // Needed to solve Bug 1185. Thanks to jouse 2001 detecting it. :-)
    // Based on code from glossary fiter by Williams Castillo.
    //       - Eloy

    // Detect all the <script> zones to take out
    $excludes = array();
    preg_match_all('/<script language(.+?)<\/script>/is',$text,$list_of_excludes);

    // Take out all the <script> zones from text
    foreach (array_unique($list_of_excludes[0]) as $key=>$value) {
        $excludes['<+'.$key.'+>'] = $value;
    }
    if ($excludes) {
        $text = str_replace($excludes,array_keys($excludes),$text);
    }

/// this is the meat of the code - this is run every time
    $text = str_replace($e, $img, $text);

    // Recover all the <script> zones to text
    if ($excludes) {
        $text = str_replace(array_keys($excludes),$excludes,$text);
    }
}

function text_to_html($text, $smiley=true, $para=true, $newlines=true) {
/// Given plain text, makes it into HTML as nicely as possible.
/// May contain HTML tags already

    global $CFG;

/// Remove any whitespace that may be between HTML tags
    $text = eregi_replace(">([[:space:]]+)<", "><", $text);

/// Remove any returns that precede or follow HTML tags
    $text = eregi_replace("([\n\r])<", " <", $text);
    $text = eregi_replace(">([\n\r])", "> ", $text);

    convert_urls_into_links($text);

/// Make returns into HTML newlines.
    if ($newlines) {
        $text = nl2br($text);
    }

/// Turn smileys into images.
    if ($smiley) {
        replace_smilies($text);
    }

/// Wrap the whole thing in a paragraph tag if required
    if ($para) {
        return "<p>".$text."</p>";
    } else {
        return $text;
    }
}

function wiki_to_html($text) {
/// Given Wiki formatted text, make it into XHTML using external function
    global $CFG;

    require_once("$CFG->libdir/wiki.php");

    $wiki = new Wiki;
    return $wiki->format($text);
}

function html_to_text($html) {
/// Given HTML text, make it into plain text using external function
    global $CFG;

    require_once("$CFG->libdir/html2text.php");

    return html2text($html);
}


function convert_urls_into_links(&$text) {
/// Given some text, it converts any URLs it finds into HTML links.

/// Make lone URLs into links.   eg http://moodle.com/
    $text = eregi_replace("([[:space:]]|^|\(|\[)([[:alnum:]]+)://([^[:space:]]*)([[:alnum:]#?/&=])",
                          "\\1<a href=\"\\2://\\3\\4\" target=\"newpage\">\\2://\\3\\4</a>", $text);

/// eg www.moodle.com
    $text = eregi_replace("([[:space:]]|^|\(|\[)www\.([^[:space:]]*)([[:alnum:]#?/&=])",
                          "\\1<a href=\"http://www.\\2\\3\" target=\"newpage\">www.\\2\\3</a>", $text);
}

function highlight($needle, $haystack, $case=0,
                    $left_string="<span class=\"highlight\">", $right_string="</span>") {
/// This function will highlight search words in a given string
/// It cares about HTML and will not ruin links.  It's best to use
/// this function after performing any conversions to HTML.
/// Function found here: http://forums.devshed.com/t67822/scdaa2d1c3d4bacb4671d075ad41f0854.html

    if (empty($needle)) {
        return $haystack;
    }

    $list_of_words = eregi_replace("[^-a-zA-Z0-9&']", " ", $needle);
    $list_array = explode(" ", $list_of_words);
    for ($i=0; $i<sizeof($list_array); $i++) {
        if (strlen($list_array[$i]) == 1) {
            $list_array[$i] = "";
        }
    }
    $list_of_words = implode(" ", $list_array);
    $list_of_words_cp = $list_of_words;
    $final = array();
    preg_match_all('/<(.+?)>/is',$haystack,$list_of_words);

    foreach (array_unique($list_of_words[0]) as $key=>$value) {
        $final['<|'.$key.'|>'] = $value;
    }

    $haystack = str_replace($final,array_keys($final),$haystack);
    $list_of_words_cp = eregi_replace(" +", "|", $list_of_words_cp);

    if ($list_of_words_cp{0}=="|") {
        $list_of_words_cp{0} = "";
    }
    if ($list_of_words_cp{strlen($list_of_words_cp)-1}=="|") {
        $list_of_words_cp{strlen($list_of_words_cp)-1}="";
    }
    $list_of_words_cp = "(".trim($list_of_words_cp).")";

    if (!$case){
        $haystack = eregi_replace("$list_of_words_cp", "$left_string"."\\1"."$right_string", $haystack);
    } else {
        $haystack = ereg_replace("$list_of_words_cp", "$left_string"."\\1"."$right_string", $haystack);
    }
    $haystack = str_replace(array_keys($final),$final,$haystack);

    return stripslashes($haystack);
}

function highlightfast($needle, $haystack) {
/// This function will highlight instances of $needle in $haystack
/// It's faster that the above function and doesn't care about
/// HTML or anything.

    $parts = explode(strtolower($needle), strtolower($haystack));

    $pos = 0;

    foreach ($parts as $key => $part) {
        $parts[$key] = substr($haystack, $pos, strlen($part));
        $pos += strlen($part);

        $parts[$key] .= "<span class=\"highlight\">".substr($haystack, $pos, strlen($needle))."</span>";
        $pos += strlen($needle);
    }

    return (join('', $parts));
}


/// STANDARD WEB PAGE PARTS ///////////////////////////////////////////////////

function print_header ($title="", $heading="", $navigation="", $focus="", $meta="",
                       $cache=true, $button="&nbsp;", $menu="", $usexml=false, $bodytags="") {
// $title - appears top of window
// $heading - appears top of page
// $navigation - premade navigation string
// $focus - indicates form element eg  inputform.password
// $meta - meta tags in the header
// $cache - should this page be cacheable?
// $button - HTML code for a button (usually for module editing)
// $menu - HTML code for a popup menu
// $usexml - use XML for this page
// $bodytags - this text will be included verbatim in the <body> tag (useful for onload() etc)

    global $USER, $CFG, $THEME, $SESSION;

    global $course;                // This is a bit of an ugly hack to be gotten rid of later
    if (!empty($course->lang)) {
        $CFG->courselang = $course->lang;
    }

    if (file_exists("$CFG->dirroot/theme/$CFG->theme/styles.php")) {
        $styles = $CFG->stylesheet;
    } else {
        $styles = "$CFG->wwwroot/theme/standard/styles.php";
    }

    if ($navigation == "home") {
        $home = true;
        $navigation = "";
    } else {
        $home = false;
    }

    if ($button == "") {
        $button = "&nbsp;";
    }

    if (!$menu and $navigation) {
        if (isset($USER->id)) {
            $menu = "<font size=\"2\"><a target=\"$CFG->framename\" href=\"$CFG->wwwroot/login/logout.php\">".get_string("logout")."</a></font>";
        } else {
            $menu = "<font size=\"2\"><a target=\"$CFG->framename\" href=\"$CFG->wwwroot/login/index.php\">".get_string("login")."</a></font>";
        }
    }

    // Add a stylesheet for the HTML editor
    $meta = "<style type=\"text/css\">@import url($CFG->wwwroot/lib/editor/htmlarea.css);</style>\n$meta\n";

    if (!empty($CFG->unicode)) {
        $encoding = "utf-8";
    } else if (!empty($CFG->courselang)) {
        $encoding = get_string("thischarset");
        moodle_setlocale();
    } else {
        if (!empty($SESSION->encoding)) {
            $encoding = $SESSION->encoding;
        } else {
            $SESSION->encoding = $encoding = get_string("thischarset");
        }
    }
    $meta = "<meta http-equiv=\"content-type\" content=\"text/html; charset=$encoding\" />\n$meta\n";

    if ( get_string("thisdirection") == "rtl" ) {
        $direction = " dir=\"rtl\"";
    } else {
        $direction = " dir=\"ltr\"";
    }

    if (!$cache) {   // Do everything we can to prevent clients and proxies caching
        @header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        @header("Pragma: no-cache");
        $meta .= "\n<meta http-equiv=\"pragma\" content=\"no-cache\" />";
        $meta .= "\n<meta http-equiv=\"expires\" content=\"0\" />";
    }

    if ($usexml) {       // Added by Gustav Delius / Mad Alex for MathML output
        $currentlanguage = current_language();

        @header("Content-type: text/xml");
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\" ?>\n";
        if (!empty($CFG->xml_stylesheets)) {
            $stylesheets = explode(";", $CFG->xml_stylesheets);
            foreach ($stylesheets as $stylesheet) {
                echo "<?xml-stylesheet type=\"text/xsl\" href=\"$CFG->wwwroot/$stylesheet\" ?>\n";
            }
        }
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1";
        if (!empty($CFG->xml_doctype_extra)) {
            echo " plus $CFG->xml_doctype_extra";
        }
        echo "//" . strtoupper($currentlanguage) . "\" \"$CFG->xml_dtd\">\n";
        $direction = " xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"$currentlanguage\" $direction";
    }

    $title = str_replace('"', '&quot;', $title);
    $title = strip_tags($title);

    include ("$CFG->dirroot/theme/$CFG->theme/header.html");
}

function print_header_simple($title="", $heading="", $navigation="", $focus="", $meta="",
                       $cache=true, $button="&nbsp;", $menu="", $usexml=false, $bodytags="") {
/// This version of print_header is simpler because the course name does not have to be
/// provided explicitly in the strings. It can be used on the site page as in courses
/// Eventually all print_header could be replaced by print_header_simple

    global $course;                // The same hack is used in print_header

    $shortname ='';
    if ($course->category) {
        $shortname = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
    }

    print_header("$course->shortname: $title", "$course->fullname $heading", "$shortname $navigation", $focus, $meta,
                       $cache, $button, $menu, $usexml, $bodytags);
}

function print_footer ($course=NULL) {
// Can provide a course object to make the footer contain a link to
// to the course home page, otherwise the link will go to the site home
    global $USER, $CFG, $THEME;


/// Course links
    if ($course) {
        if ($course == "home") {   // special case for site home page - please do not remove
            $homelink  = "<p align=\"center\"><a title=\"moodle $CFG->release ($CFG->version)\" href=\"http://moodle.org/\" target=\"_blank\">";
            $homelink .= "<br /><img width=\"100\" height=\"30\" src=\"pix/moodlelogo.gif\" border=\"0\" /></a></p>";
            $course = get_site();
            $homepage = true;
        } else {
            $homelink = "<a target=\"{$CFG->framename}\" href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a>";
        }
    } else {
        $homelink = "<a target=\"{$CFG->framename}\" href=\"$CFG->wwwroot/\">".get_string("home")."</a>";
        $course = get_site();
    }

/// User links
    $loggedinas = user_login_string($course, $USER);

    include ("$CFG->dirroot/theme/$CFG->theme/footer.html");
}

function style_sheet_setup($lastmodified=0, $lifetime=300, $themename="") {
/// This function is called by stylesheets to set up the header
/// approriately as well as the current path

    global $CFG;

    header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastmodified) . " GMT");
    header("Expires: " . gmdate("D, d M Y H:i:s", time() + $lifetime) . " GMT");
    header("Cache-control: max_age = $lifetime");
    header("Pragma: ");
    header("Content-type: text/css");  // Correct MIME type

    if (!empty($themename)) {
        $CFG->theme = $themename;
    }

    return "$CFG->wwwroot/theme/$CFG->theme";

}


function user_login_string($course, $user=NULL) {
    global $USER, $CFG;

    if (empty($user)) {
        $user = $USER;
    }

    if (isset($user->realuser)) {
        if ($realuser = get_record("user", "id", $user->realuser)) {
            $fullname = fullname($realuser, true);
            $realuserinfo = " [<a target=\"{$CFG->framename}\"
            href=\"$CFG->wwwroot/course/loginas.php?id=$course->id&amp;return=$realuser->id\">$fullname</a>] ";
        }
    } else {
        $realuserinfo = "";
    }

    if (isset($user->id) and $user->id) {
        $fullname = fullname($user, true);
        $username = "<a target=\"{$CFG->framename}\" href=\"$CFG->wwwroot/user/view.php?id=$user->id&amp;course=$course->id\">$fullname</a>";
        $loggedinas = $realuserinfo.get_string("loggedinas", "moodle", "$username").
                      " (<a target=\"{$CFG->framename}\" href=\"$CFG->wwwroot/login/logout.php\">".get_string("logout")."</a>)";
    } else {
        $loggedinas = get_string("loggedinnot", "moodle").
                      " (<a target=\"{$CFG->framename}\" href=\"$CFG->wwwroot/login/index.php\">".get_string("login")."</a>)";
    }
    return $loggedinas;
}


function print_navigation ($navigation) {
   global $CFG;

   if ($navigation) {
       if (! $site = get_site()) {
           $site->shortname = get_string("home");;
       }
       $navigation = str_replace('->', '&raquo;', $navigation);
       echo "<a target=\"{$CFG->framename}\" href=\"$CFG->wwwroot/\">$site->shortname</a> &raquo; $navigation";
   }
}

function print_headline($text, $size=2) {
    echo "<b><font size=\"$size\">$text</font></b><br />\n";
}

function print_heading($text, $align="center", $size=3) {
    echo "<p align=\"$align\"><font size=\"$size\"><b>".stripslashes_safe($text)."</b></font></p>";
}

function print_heading_with_help($text, $helppage, $module="moodle", $icon="") {
// Centered heading with attached help button (same title text)
// and optional icon attached
    echo "<p align=\"center\"><font size=\"3\">$icon<b>".stripslashes_safe($text);
    helpbutton($helppage, $text, $module);
    echo "</b></font></p>";
}

function print_continue($link) {

    if (!$link) {
        $link = $_SERVER["HTTP_REFERER"];
    }

    print_heading("<a href=\"$link\">".get_string("continue")."</a>");
}


function print_simple_box($message, $align="", $width="", $color="#FFFFFF", $padding=5, $class="generalbox") {
    print_simple_box_start($align, $width, $color, $padding, $class);
    echo stripslashes_safe($message);
    print_simple_box_end();
}

function print_simple_box_start($align="", $width="", $color="#FFFFFF", $padding=5, $class="generalbox") {
    global $THEME;

    if ($align) {
        $align = "align=\"$align\"";
    }
    if ($width) {
        $width = "width=\"$width\"";
    }
    echo "<table $align $width class=\"$class\" border=\"0\" cellpadding=\"$padding\" cellspacing=\"0\"><tr><td bgcolor=\"$color\" class=\"$class"."content\">";
}

function print_simple_box_end() {
    echo "</td></tr></table>";
}

function print_single_button($link, $options, $label="OK", $method="get") {
    echo "<form action=\"$link\" method=\"$method\">";
    if ($options) {
        foreach ($options as $name => $value) {
            echo "<input type=\"hidden\" name=\"$name\" value=\"$value\" />";
        }
    }
    echo "<input type=\"submit\" value=\"$label\" /></form>";
}

function print_spacer($height=1, $width=1, $br=true) {
    global $CFG;
    echo "<img height=\"$height\" width=\"$width\" src=\"$CFG->wwwroot/pix/spacer.gif\" alt=\"\" />";
    if ($br) {
        echo "<br />\n";
    }
}

function print_file_picture($path, $courseid=0, $height="", $width="", $link="") {
// Given the path to a picture file in a course, or a URL,
// this function includes the picture in the page.
    global $CFG;

    if ($height) {
        $height = "height=\"$height\"";
    }
    if ($width) {
        $width = "width=\"$width\"";
    }
    if ($link) {
        echo "<a href=\"$link\">";
    }
    if (substr(strtolower($path), 0, 7) == "http://") {
        echo "<img border=\"0\" $height $width src=\"$path\" />";

    } else if ($courseid) {
        echo "<img border=\"0\" $height $width src=\"";
        if ($CFG->slasharguments) {        // Use this method if possible for better caching
            echo "$CFG->wwwroot/file.php/$courseid/$path";
        } else {
            echo "$CFG->wwwroot/file.php?file=/$courseid/$path";
        }
        echo "\" />";
    } else {
        echo "Error: must pass URL or course";
    }
    if ($link) {
        echo "</a>";
    }
}

function print_user_picture($userid, $courseid, $picture, $large=false, $returnstring=false, $link=true) {
    global $CFG;

    if ($link) {
        $output = "<a href=\"$CFG->wwwroot/user/view.php?id=$userid&amp;course=$courseid\">";
    } else {
        $output = "";
    }
    if ($large) {
        $file = "f1";
        $size = 100;
    } else {
        $file = "f2";
        $size = 35;
    }
    if ($picture) {  // Print custom user picture
        if ($CFG->slasharguments) {        // Use this method if possible for better caching
            $output .= "<img align=\"absmiddle\" src=\"$CFG->wwwroot/user/pix.php/$userid/$file.jpg\"".
                       " border=\"0\" width=\"$size\" height=\"$size\" alt=\"\" />";
        } else {
            $output .= "<img align=\"absmiddle\" src=\"$CFG->wwwroot/user/pix.php?file=/$userid/$file.jpg\"".
                       " border=\"0\" width=\"$size\" height=\"$size\" alt=\"\" />";
        }
    } else {         // Print default user pictures (use theme version if available)
        $output .= "<img align=\"absmiddle\" src=\"$CFG->pixpath/u/$file.png\"".
                   " border=\"0\" width=\"$size\" height=\"$size\" alt=\"\" />";
    }
    if ($link) {
        $output .= "</a>";
    }

    if ($returnstring) {
        return $output;
    } else {
        echo $output;
    }
}

function print_user($user, $course) {
/// Prints a summary of a user in a nice little box

    global $CFG,$USER;

    static $string;
    static $datestring;
    static $countries;
    static $isteacher;

    if (empty($string)) {     // Cache all the strings for the rest of the page

        $string->email       = get_string("email");
        $string->location    = get_string("location");
        $string->lastaccess  = get_string("lastaccess");
        $string->activity    = get_string("activity");
        $string->unenrol     = get_string("unenrol");
        $string->loginas     = get_string("loginas");
        $string->fullprofile = get_string("fullprofile");
        $string->role        = get_string("role");
        $string->name        = get_string("name");
        $string->never       = get_string("never");

        $datestring->day     = get_string("day");
        $datestring->days    = get_string("days");
        $datestring->hour    = get_string("hour");
        $datestring->hours   = get_string("hours");
        $datestring->min     = get_string("min");
        $datestring->mins    = get_string("mins");
        $datestring->sec     = get_string("sec");
        $datestring->secs    = get_string("secs");

        $countries = get_list_of_countries();

        $isteacher = isteacher($course->id);
    }

    echo '<table width="80%" align="center" border="0" cellpadding="10" cellspacing="0" class="userinfobox">';
    echo '<tr>';
    echo '<td width="100" bgcolor="#ffffff" valign="top" class="userinfoboxside">';
    print_user_picture($user->id, $course->id, $user->picture, true);
    echo '</td>';
    echo '<td width="100%" bgcolor="#ffffff" valign="top" class="userinfoboxsummary">';
    echo '<font size="-1">';
    echo '<font size="3"><b>'.fullname($user, $isteacher).'</b></font>';
    echo '<p>';
    if (!empty($user->role) and ($user->role <> $course->teacher)) {
        echo "$string->role: $user->role<br />";
    }
    if ($user->maildisplay == 1 or ($user->maildisplay == 2 and $course->category and !isguest()) or $isteacher) {
        echo "$string->email: <a href=\"mailto:$user->email\">$user->email</a><br />";
    }
    if ($user->city or $user->country) {
        echo "$string->location: $user->city, ".$countries["$user->country"]."<br />";
    }
    if ($user->lastaccess) {
        echo "$string->lastaccess: ".userdate($user->lastaccess);
        echo "&nbsp (".format_time(time() - $user->lastaccess, $datestring).")";
    } else {
        echo "$string->lastaccess: $string->never";
    }
    echo '</td><td valign="bottom" bgcolor="#ffffff" nowrap="nowrap" class="userinfoboxlinkcontent">';

    echo '<font size="1">';
    if ($isteacher) {
        $timemidnight = usergetmidnight(time());
        echo "<a href=\"$CFG->wwwroot/course/user.php?id=$course->id&user=$user->id\">$string->activity</a><br>";
        if (!iscreator($user->id)) {  // Includes admins
            if (isstudent($course->id, $user->id)) {  // Includes admins
                echo "<a href=\"$CFG->wwwroot/course/unenrol.php?id=$course->id&user=$user->id\">$string->unenrol</a><br />";
            }
            if ($USER->id != $user->id) {
                echo "<a href=\"$CFG->wwwroot/course/loginas.php?id=$course->id&user=$user->id\">$string->loginas</a><br />";
            }
        }
    }
    echo "<a href=\"$CFG->wwwroot/user/view.php?id=$user->id&course=$course->id\">$string->fullprofile...</a>";
    echo '</font>';

    echo '</td></tr></table>';
}


function print_group_picture($group, $courseid, $large=false, $returnstring=false, $link=true) {
    global $CFG;

    static $isteacheredit;

    if (!isset($isteacheredit)) {
        $isteacheredit = isteacheredit($courseid);
    }

    if ($group->hidepicture and !$isteacheredit) {
        return '';
    }

    if ($link or $isteacheredit) {
        $output = "<a href=\"$CFG->wwwroot/course/group.php?id=$courseid&amp;group=$group->id\">";
    } else {
        $output = '';
    }
    if ($large) {
        $file = "f1";
        $size = 100;
    } else {
        $file = "f2";
        $size = 35;
    }
    if ($group->picture) {  // Print custom group picture
        if ($CFG->slasharguments) {        // Use this method if possible for better caching
            $output .= "<img align=\"absmiddle\" src=\"$CFG->wwwroot/user/pixgroup.php/$group->id/$file.jpg\"".
                       " border=\"0\" width=\"$size\" height=\"$size\" alt=\"\" title=\"$group->name\"/>";
        } else {
            $output .= "<img align=\"absmiddle\" src=\"$CFG->wwwroot/user/pixgroup.php?file=/$group->id/$file.jpg\"".
                       " border=\"0\" width=\"$size\" height=\"$size\" alt=\"\" title=\"$group->name\"/>";
        }
    }
    if ($link or $isteacheredit) {
        $output .= "</a>";
    }

    if ($returnstring) {
        return $output;
    } else {
        echo $output;
    }
}


function print_png($url, $sizex, $sizey, $returnstring, $parameters='alt=""') {
    global $CFG;
    static $recentIE;

    if (!isset($recentIE)) {
        $recentIE = check_browser_version('MSIE', '5.0');
    }

    if ($recentIE) {  // work around the HORRIBLE bug IE has with alpha transparencies
        $output .= "<img src=\"$CFG->pixpath/spacer.gif\" width=\"$sizex\" height=\"$sizey\"".
                   " border=\"0\" style=\"width: {$sizex}px; height: {$sizey}px; ".
                   " filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='$url', sizingMethod='scale') ".
                   " $parameters />";
    } else {
        $output .= "<img src=\"$url\" border=\"0\" width=\"$sizex\" height=\"$sizey\" ".
                   " $parameters />";
    }

    if ($returnstring) {
        return $output;
    } else {
        echo $output;
    }
}


function print_table($table) {
// Prints a nicely formatted table.
// $table is an object with several properties.
//     $table->head      is an array of heading names.
//     $table->align     is an array of column alignments
//     $table->size      is an array of column sizes
//     $table->wrap      is an array of "nowrap"s or nothing
//     $table->data[]    is an array of arrays containing the data.
//     $table->width     is an percentage of the page
//     $table->cellpadding    padding on each cell
//     $table->cellspacing    spacing between cells

    global $THEME;

    if (isset($table->align)) {
        foreach ($table->align as $key => $aa) {
            if ($aa) {
                $align[$key] = " align=\"$aa\"";
            } else {
                $align[$key] = "";
            }
        }
    }
    if (isset($table->size)) {
        foreach ($table->size as $key => $ss) {
            if ($ss) {
                $size[$key] = " width=\"$ss\"";
            } else {
                $size[$key] = "";
            }
        }
    }
    if (isset($table->wrap)) {
        foreach ($table->wrap as $key => $ww) {
            if ($ww) {
                $wrap[$key] = " nowrap=\"nowrap\" ";
            } else {
                $wrap[$key] = "";
            }
        }
    }

    if (empty($table->width)) {
        $table->width = "80%";
    }

    if (empty($table->cellpadding)) {
        $table->cellpadding = "5";
    }

    if (empty($table->cellspacing)) {
        $table->cellspacing = "1";
    }

    print_simple_box_start("center", "$table->width", "#ffffff", 0);
    echo "<table width=\"100%\" border=\"0\" valign=\"top\" align=\"center\" ";
    echo " cellpadding=\"$table->cellpadding\" cellspacing=\"$table->cellspacing\" class=\"generaltable\">\n";

    $countcols = 0;

    if (!empty($table->head)) {
        $countcols = count($table->head);;
        echo "<tr>";
        foreach ($table->head as $key => $heading) {

            if (!isset($size[$key])) {
                $size[$key] = "";
            }
            if (!isset($align[$key])) {
                $align[$key] = "";
            }
            echo "<th valign=\"top\" ".$align[$key].$size[$key]." nowrap=\"nowrap\" class=\"generaltableheader\">$heading</th>";
        }
        echo "</tr>\n";
    }

    if (!empty($table->data)) {
        foreach ($table->data as $row) {
            echo "<tr valign=\"top\">";
            if ($row == "hr" and $countcols) {
                echo "<td colspan=\"$countcols\"><div class=\"tabledivider\"></div></td>";
            } else {  /// it's a normal row of data
                foreach ($row as $key => $item) {
                    if (!isset($size[$key])) {
                        $size[$key] = "";
                    }
                    if (!isset($align[$key])) {
                        $align[$key] = "";
                    }
                    if (!isset($wrap[$key])) {
                        $wrap[$key] = "";
                    }
                    echo "<td ".$align[$key].$size[$key].$wrap[$key]." class=\"generaltablecell\">$item</td>";
                }
            }
            echo "</tr>\n";
        }
    }
    echo "</table>\n";
    print_simple_box_end();

    return true;
}

function make_table($table) {
// Creates  a nicely formatted table and returns it
// $table is an object with several properties.
//     $table->head      is an array of heading names.
//     $table->align     is an array of column alignments
//     $table->size      is an array of column sizes
//     $table->wrap      is an array of "nowrap"s or nothing
//     $table->data[]    is an array of arrays containing the data.
//     $table->width     is an percentage of the page
//     $table->class     is a class
//     $table->fontsize  is the size of all the text
//     $table->tablealign     align the whole table
//     $table->cellpadding    padding on each cell
//     $table->cellspacing    spacing between cells

    if (isset($table->align)) {
        foreach ($table->align as $key => $aa) {
            if ($aa) {
                $align[$key] = " align=\"$aa\"";
            } else {
                $align[$key] = "";
            }
        }
    }
    if (isset($table->size)) {
        foreach ($table->size as $key => $ss) {
            if ($ss) {
                $size[$key] = " width=\"$ss\"";
            } else {
                $size[$key] = "";
            }
        }
    }
    if (isset($table->wrap)) {
        foreach ($table->wrap as $key => $ww) {
            if ($ww) {
                $wrap[$key] = " nowrap=\"nowrap\" ";
            } else {
                $wrap[$key] = "";
            }
        }
    }

    if (empty($table->width)) {
        $table->width = "80%";
    }

    if (empty($table->tablealign)) {
        $table->tablealign = "center";
    }

    if (empty($table->cellpadding)) {
        $table->cellpadding = "5";
    }

    if (empty($table->cellspacing)) {
        $table->cellspacing = "1";
    }

    if (empty($table->class)) {
        $table->class = "generaltable";
    }

    if (empty($table->fontsize)) {
        $fontsize = "";
    } else {
        $fontsize = "<font size=\"$table->fontsize\">";
    }

    $output =  "<table width=\"$table->width\" valign=\"top\" align=\"$table->tablealign\" ";
    $output .= " cellpadding=\"$table->cellpadding\" cellspacing=\"$table->cellspacing\" class=\"$table->class\">\n";

    if (!empty($table->head)) {
        $output .= "<tr>";
        foreach ($table->head as $key => $heading) {
            if (!isset($size[$key])) {
                $size[$key] = "";
            }
            if (!isset($align[$key])) {
                $align[$key] = "";
            }
            $output .= "<th valign=\"top\" ".$align[$key].$size[$key]." nowrap=\"nowrap\" class=\"{$table->class}header\">$fontsize$heading</th>";
        }
        $output .= "</tr>\n";
    }

    foreach ($table->data as $row) {
        $output .= "<tr valign=\"top\">";
        foreach ($row as $key => $item) {
            if (!isset($size[$key])) {
                $size[$key] = "";
            }
            if (!isset($align[$key])) {
                $align[$key] = "";
            }
            if (!isset($wrap[$key])) {
                $wrap[$key] = "";
            }
            $output .= "<td ".$align[$key].$size[$key].$wrap[$key]." class=\"{$table->class}cell\">$fontsize$item</td>";
        }
        $output .= "</tr>\n";
    }
    $output .= "</table>\n";

    return $output;
}

function print_textarea($usehtmleditor, $rows, $cols, $width, $height, $name, $value="", $courseid=0) {
/// Prints a basic textarea field
/// $width and height are legacy fields and no longer used

    global $CFG, $course;

    if (empty($courseid)) {
        if (!empty($course->id)) {  // search for it in global context
            $courseid = $course->id;
        }
    }

    if ($usehtmleditor) {
        if (!empty($courseid) and isteacher($courseid)) {
            echo "<script type=\"text/javascript\" src=\"$CFG->wwwroot/lib/editor/htmlarea.php?id=$courseid\"></script>\n";
        } else {
            echo "<script type=\"text/javascript\" src=\"$CFG->wwwroot/lib/editor/htmlarea.php\"></script>\n";
        }
        echo "<script type=\"text/javascript\" src=\"$CFG->wwwroot/lib/editor/dialog.js\"></script>\n";
        echo "<script type=\"text/javascript\" src=\"$CFG->wwwroot/lib/editor/lang/en.php\"></script>\n";
        echo "<script type=\"text/javascript\" src=\"$CFG->wwwroot/lib/editor/popupwin.js\"></script>\n";

        if ($rows < 10) {
            $rows = 10;
        }
        if ($cols < 65) {
            $cols = 65;
        }
    }

    echo "<textarea id=\"$name\" name=\"$name\" rows=\"$rows\" cols=\"$cols\" wrap=\"virtual\">";
    p($value);
    echo "</textarea>\n";
}

function print_richedit_javascript($form, $name, $source="no") {
/// Legacy function, provided for backward compatability
    use_html_editor($name);
}

function use_html_editor($name="") {
/// Sets up the HTML editor on textareas in the current page.
/// If a field name is provided, then it will only be
/// applied to that field - otherwise it will be used
/// on every textarea in the page.
///
/// In most cases no arguments need to be supplied

    echo "<script language=\"javascript\" type=\"text/javascript\" defer=\"1\">\n";
    if (empty($name)) {
        echo "HTMLArea.replaceAll();\n";
    } else {
        echo "HTMLArea.replace('$name');\n";
    }
    echo "</script>\n";
}


function update_course_icon($courseid) {
// Used to be an icon, but it's now a simple form button
    global $CFG, $USER;

    if (isteacheredit($courseid)) {
        if (!empty($USER->editing)) {
            $string = get_string("turneditingoff");
            $edit = "off";
        } else {
            $string = get_string("turneditingon");
            $edit = "on";
        }
        return "<form target=\"$CFG->framename\" method=\"get\" action=\"$CFG->wwwroot/course/view.php\">".
               "<input type=\"hidden\" name=\"id\" value=\"$courseid\" />".
               "<input type=\"hidden\" name=\"edit\" value=\"$edit\" />".
               "<input type=\"submit\" value=\"$string\" /></form>";
    }
}

function update_module_button($moduleid, $courseid, $string) {
// Prints the editing button on a module "view" page
    global $CFG;

    if (isteacheredit($courseid)) {
        $string = get_string("updatethis", "", $string);
        return "<form target=\"$CFG->framename\" method=\"get\" action=\"$CFG->wwwroot/course/mod.php\">".
               "<input type=\"hidden\" name=\"update\" value=\"$moduleid\" />".
               "<input type=\"hidden\" name=\"return\" value=\"true\" />".
               "<input type=\"submit\" value=\"$string\" /></form>";
    } else {
        return "";
    }
}

function update_category_button($categoryid) {
// Prints the editing button on a category page
    global $CFG, $USER;

    if (iscreator()) {
        if (!empty($USER->categoryediting)) {
            $string = get_string("turneditingoff");
            $edit = "off";
        } else {
            $string = get_string("turneditingon");
            $edit = "on";
        }
        return "<form target=\"$CFG->framename\" method=\"get\" action=\"$CFG->wwwroot/course/category.php\">".
               "<input type=\"hidden\" name=\"id\" value=\"$categoryid\" />".
               "<input type=\"hidden\" name=\"edit\" value=\"$edit\" />".
               "<input type=\"submit\" value=\"$string\" /></form>";
    }
}

function update_categories_button() {
// Prints the editing button on categories listing
    global $CFG, $USER;

    if (isadmin()) {
        if (!empty($USER->categoriesediting)) {
            $string = get_string("turneditingoff");
            $edit = "off";
        } else {
            $string = get_string("turneditingon");
            $edit = "on";
        }
        return "<form target=\"$CFG->framename\" method=\"get\" action=\"$CFG->wwwroot/course/index.php\">".
               "<input type=\"hidden\" name=\"edit\" value=\"$edit\" />".
               "<input type=\"submit\" value=\"$string\" /></form>";
    }
}

function update_group_button($courseid, $groupid) {
// Prints the editing button on group page
    global $CFG, $USER;

    if (isteacheredit($courseid)) {
        $string = get_string('editgroupprofile');
        return "<form target=\"$CFG->framename\" method=\"get\" action=\"$CFG->wwwroot/course/group.php\">".
               "<input type=\"hidden\" name=\"id\" value=\"$courseid\" />".
               "<input type=\"hidden\" name=\"group\" value=\"$groupid\" />".
               "<input type=\"hidden\" name=\"edit\" value=\"on\" />".
               "<input type=\"submit\" value=\"$string\" /></form>";
    }
}

function update_groups_button($courseid) {
// Prints the editing button on groups page
    global $CFG, $USER;

    if (isteacheredit($courseid)) {
        if (!empty($USER->groupsediting)) {
            $string = get_string("turneditingoff");
            $edit = "off";
        } else {
            $string = get_string("turneditingon");
            $edit = "on";
        }
        return "<form target=\"$CFG->framename\" method=\"get\" action=\"$CFG->wwwroot/course/groups.php\">".
               "<input type=\"hidden\" name=\"id\" value=\"$courseid\" />".
               "<input type=\"hidden\" name=\"edit\" value=\"$edit\" />".
               "<input type=\"submit\" value=\"$string\" /></form>";
    }
}

function print_group_menu($groups, $groupmode, $currentgroup, $urlroot) {
/// Prints an appropriate group selection menu

/// Add an "All groups" to the start of the menu
    $groupsmenu[0] = get_string("allparticipants");
    foreach ($groups as $key => $groupname) {
        $groupsmenu[$key] = $groupname;
    }

    echo '<table><tr><td align="right">';
    if ($groupmode == VISIBLEGROUPS) {
        print_string('groupsvisible');
    } else {
        print_string('groupsseparate');
    }
    echo ':';
    echo '</td><td nowrap="nowrap" align="left">';
    popup_form($urlroot.'&group=', $groupsmenu, 'selectgroup', $currentgroup, "", "", "", false, "self");
    echo '</tr></table>';

}


function navmenu($course, $cm=NULL, $targetwindow="self") {
// Given a course and a (current) coursemodule
// This function returns a small popup menu with all the
// course activity modules in it, as a navigation menu
// The data is taken from the serialised array stored in
// the course record

    global $CFG;

    if ($cm) {
        $cm = $cm->id;
    }

    if ($course->format == 'weeks') {
        $strsection = get_string("week");
    } else {
        $strsection = get_string("topic");
    }

    if (!$modinfo = unserialize($course->modinfo)) {
        return "";
    }
    $isteacher = isteacher($course->id);
    $section = -1;
    $selected = "";
    $url = "";
    $previousmod = NULL;
    $backmod = NULL;
    $nextmod = NULL;
    $selectmod = NULL;
    $logslink = NULL;
    $flag = false;
    $menu = array();

    $sectionrecs = get_records("course_sections","course","$course->id","section","section,visible");

    foreach ($modinfo as $mod) {
        if ($mod->mod == "label") {
            continue;
        }

        if ($mod->section > 0 and $section <> $mod->section) {
            //Only add if visible or collapsed or teacher or course format = weeks
            if ($sectionrecs[$mod->section]->visible or !$course->hiddensections or $isteacher) {
                $menu[] = "-------------- $strsection $mod->section --------------";
            }
        }

        $section = $mod->section;

        //Only add visible or teacher mods to jumpmenu
        if ($mod->visible or $isteacher) {
            $url = "$mod->mod/view.php?id=$mod->cm";
            if ($flag) { // the current mod is the "next" mod
                $nextmod = $mod;
                $flag = false;
            }
            if ($cm == $mod->cm) {
                $selected = $url;
                $selectmod = $mod;
                $backmod = $previousmod;
                $flag = true; // set flag so we know to use next mod for "next"
            }
            $mod->name = strip_tags(urldecode($mod->name));
            if (strlen($mod->name) > 55) {
                $mod->name = substr($mod->name, 0, 50)."...";
            }
            if (!$mod->visible) {
                $mod->name = "(".$mod->name.")";
            }
            $menu[$url] = $mod->name;
            $previousmod = $mod;
        }
    }
    if ($selectmod and $isteacher) {
        $logslink = "<td><a target=\"$CFG->framename\" href=".
                    "\"$CFG->wwwroot/course/log.php?chooselog=1&user=0&date=0&id=$course->id&modid=$selectmod->cm\">".
                    "<img border=\"0\" height=\"16\" width=\"16\" src=\"$CFG->pixpath/i/log.gif\"></a></td>";

    }
    if ($backmod) {
        $backmod = "<form action=\"$CFG->wwwroot/mod/$backmod->mod/view.php\" target=\"$CFG->framename\">".
                   "<input type=\"hidden\" name=\"id\" value=\"$backmod->cm\">".
                   "<input type=\"submit\" value=\"&lt;\"></form>";
    }
    if ($nextmod) {
        $nextmod = "<form action=\"$CFG->wwwroot/mod/$nextmod->mod/view.php\" target=\"$CFG->framename\">".
                   "<input type=\"hidden\" name=\"id\" value=\"$nextmod->cm\">".
                   "<input type=\"submit\" value=\"&gt;\"></form>";
    }
    return "<table><tr>$logslink<td>$backmod</td><td>" .
            popup_form("$CFG->wwwroot/mod/", $menu, "navmenu", $selected, get_string("jumpto"),
                       "", "", true, $targetwindow).
            "</td><td>$nextmod</td></tr></table>";
}


function print_date_selector($day, $month, $year, $currenttime=0) {
// Currenttime is a default timestamp in GMT
// Prints form items with the names $day, $month and $year

    if (!$currenttime) {
        $currenttime = time();
    }
    $currentdate = usergetdate($currenttime);

    for ($i=1; $i<=31; $i++) {
        $days[$i] = "$i";
    }
    for ($i=1; $i<=12; $i++) {
        $months[$i] = userdate(gmmktime(12,0,0,$i,1,2000), "%B");
    }
    for ($i=2000; $i<=2010; $i++) {
        $years[$i] = $i;
    }
    choose_from_menu($days,   $day,   $currentdate['mday'], "");
    choose_from_menu($months, $month, $currentdate['mon'],  "");
    choose_from_menu($years,  $year,  $currentdate['year'], "");
}

function print_time_selector($hour, $minute, $currenttime=0, $step=5) {
// Currenttime is a default timestamp in GMT
// Prints form items with the names $hour and $minute

    if (!$currenttime) {
        $currenttime = time();
    }
    $currentdate = usergetdate($currenttime);
    if ($step != 1) {
        $currentdate['minutes'] = ceil($currentdate['minutes']/$step)*$step;
    }
    for ($i=0; $i<=23; $i++) {
        $hours[$i] = sprintf("%02d",$i);
    }
    for ($i=0; $i<=59; $i+=$step) {
        $minutes[$i] = sprintf("%02d",$i);
    }
    choose_from_menu($hours,   $hour,   $currentdate['hours'],   "");
    choose_from_menu($minutes, $minute, $currentdate['minutes'], "");
}

function print_timer_selector($timelimit = 0, $unit = "") {
/// Prints time limit value selector

    global $CFG;

    if ($unit) {
        $unit = ' '.$unit;
    }

    // Max timelimit is sessiontimeout - 10 minutes.
    $maxvalue = ($CFG->sessiontimeout / 60) - 10;

    for ($i=1; $i<=$maxvalue; $i++) {
        $minutes[$i] = $i.$unit;
    }
    choose_from_menu($minutes, "timelimit", $timelimit, get_string("none"));
}

function print_grade_menu($courseid, $name, $current, $includenograde=true) {
/// Prints a grade menu (as part of an existing form) with help
/// Showing all possible numerical grades and scales

    global $CFG;

    $strscale = get_string("scale");
    $strscales = get_string("scales");

    $scales = get_scales_menu($courseid);
    foreach ($scales as $i => $scalename) {
        $grades[-$i] = "$strscale: $scalename";
    }
    if ($includenograde) {
        $grades[0] = get_string("nograde");
    }
    for ($i=100; $i>=1; $i--) {
        $grades[$i] = $i;
    }
    choose_from_menu($grades, "$name", "$current", "");

    $helpicon = "$CFG->pixpath/help.gif";
    $linkobject = "<img align=\"absmiddle\" border=\"0\" height=\"17\" width=\"22\" alt=\"$strscales\" src=\"$helpicon\" />";
    link_to_popup_window ("/course/scales.php?id=$courseid&amp;list=true", "ratingscales",
                          $linkobject, 400, 500, $strscales);
}

function print_scale_menu($courseid, $name, $current) {
/// Prints a scale menu (as part of an existing form) including help button
/// Just like print_grade_menu but without the numerical grades

    global $CFG;

    $strscales = get_string("scales");
    choose_from_menu(get_scales_menu($courseid), "$name", $current, "");
    $helpicon = "$CFG->pixpath/help.gif";
    $linkobject = "<img align=\"absmiddle\" border=\"0\" height=\"17\" width=\"22\" alt=\"$strscales\" src=\"$helpicon\" />";
    link_to_popup_window ("/course/scales.php?id=$courseid&amp;list=true", "ratingscales",
                          $linkobject, 400, 500, $strscales);
}


function print_scale_menu_helpbutton($courseid, $scale) {
/// Prints a help button about a scale
/// scale is an object

    global $CFG;

    $strscales = get_string("scales");
    $helpicon = "$CFG->pixpath/help.gif";
    $linkobject = "<img align=\"absmiddle\" border=\"0\" height=\"17\" width=\"22\" alt=\"$scale->name\" src=\"$helpicon\" />";
    link_to_popup_window ("/course/scales.php?id=$courseid&amp;list=true&amp;scale=$scale->id", "ratingscale",
                          $linkobject, 400, 500, $scale->name);
}


function error ($message, $link="") {
    global $CFG, $SESSION;

    print_header(get_string("error"));
    echo "<br />";
    print_simple_box($message, "center", "", "#FFBBBB");

    if (!$link) {
        if ( !empty($SESSION->fromurl) ) {
            $link = "$SESSION->fromurl";
            unset($SESSION->fromurl);
        } else {
            $link = "$CFG->wwwroot/";
        }
    }
    print_continue($link);
    print_footer();
    die;
}

function helpbutton ($page, $title="", $module="moodle", $image=true, $linktext=false, $text="") {
    // $page = the keyword that defines a help page
    // $title = the title of links, rollover tips, alt tags etc
    // $module = which module is the page defined in
    // $image = use a help image for the link?  (true/false/"both")
    // $text = if defined then this text is used in the page, and
    //         the $page variable is ignored.
    global $CFG, $THEME;

    if ($module == "") {
        $module = "moodle";
    }

    if ($image) {
        $icon = "$CFG->pixpath/help.gif";
        if ($linktext) {
            $linkobject = "<span style=\"cursor:help;\">$title<img align=\"absmiddle\" border=\"0\" ".
                          " height=\"17\" width=\"22\" alt=\"\" src=\"$icon\" /></span>";
        } else {
            $linkobject = "<img align=\"absmiddle\" border=\"0\" height=\"17\" width=\"22\" ".
                          " alt=\"$title\" style=\"cursor:help;\" src=\"$icon\" />";
        }
    } else {
        $linkobject = "<span style=\"cursor:help;\">$title</span>";
    }
    if ($text) {
        $url = "/help.php?module=$module&amp;text=".htmlentities(urlencode($text));
    } else {
        $url = "/help.php?module=$module&amp;file=$page.html";
    }
    link_to_popup_window ($url, "popup", $linkobject, 400, 500, $title);
}

function emoticonhelpbutton($form, $field) {
/// Prints a special help button that is a link to the "live" emoticon popup
    global $CFG, $SESSION;

    $SESSION->inserttextform = $form;
    $SESSION->inserttextfield = $field;
    helpbutton("emoticons", get_string("helpemoticons"), "moodle", false, true);
    echo "&nbsp;";
    link_to_popup_window ("/help.php?module=moodle&amp;file=emoticons.html", "popup",
                          "<img src=\"$CFG->pixpath/s/smiley.gif\" border=\"0\" align=\"absmiddle\" width=\"15\" height=\"15\" />",
                           400, 500, get_string("helpemoticons"));
    echo "<br />";
}

function notice ($message, $link="") {
    global $CFG, $THEME;

    if (!$link) {
        if (!empty($_SERVER["HTTP_REFERER"])) {
            $link = $_SERVER["HTTP_REFERER"];
        } else {
            $link = "$CFG->wwwroot/";
        }
    }

    echo "<br />";
    print_simple_box($message, "center", "50%", "$THEME->cellheading", "20", "noticebox");
    print_heading("<a href=\"$link\">".get_string("continue")."</a>");
    print_footer(get_site());
    die;
}

function notice_yesno ($message, $linkyes, $linkno) {
    global $THEME;

    print_simple_box_start("center", "60%", "$THEME->cellheading");
    echo "<p align=\"center\"><font size=\"3\">$message</font></p>";
    echo "<p align=\"center\"><font size=\"3\"><b>";
    echo "<a href=\"$linkyes\">".get_string("yes")."</a>";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    echo "<a href=\"$linkno\">".get_string("no")."</a>";
    echo "</b></font></p>";
    print_simple_box_end();
}

function redirect($url, $message="", $delay="0") {
// Redirects the user to another page, after printing a notice

    if (empty($message)) {
        echo "<meta http-equiv=\"refresh\" content=\"$delay; url=$url\" />";
        echo "<script>location.replace('$url');</script>";   // To cope with Mozilla bug
    } else {
        if (empty($delay)) {
            $delay = 3;  // There's no point having a message with no delay
        }
        print_header("", "", "", "", "<meta http-equiv=\"refresh\" content=\"$delay; url=$url\" />");
        echo "<center>";
        echo "<p>$message</p>";
        echo "<p>( <a href=\"$url\">".get_string("continue")."</a> )</p>";
        echo "</center>";
        flush();
        sleep($delay);
        echo "<script>location.replace('$url');</script>";   // To cope with Mozilla bug
    }
    die;
}

function notify ($message, $color="red", $align="center") {
    echo "<p align=\"$align\"><b><font color=\"$color\">$message</font></b></p>\n";
}

function obfuscate_email($email) {
/// Given an email address, this function will return an obfuscated version of it
    $i = 0;
    $length = strlen($email);
    $obfuscated = "";
    while ($i < $length) {
        if (rand(0,2)) {
            $obfuscated.='%'.dechex(ord($email{$i}));
        } else {
            $obfuscated.=$email{$i};
        }
        $i++;
    }
    return $obfuscated;
}

function obfuscate_text($plaintext) {
/// This function takes some text and replaces about half of the characters
/// with HTML entity equivalents.   Return string is obviously longer.
    $i=0;
    $length = strlen($plaintext);
    $obfuscated="";
    $prev_obfuscated = false;
    while ($i < $length) {
        $c = ord($plaintext{$i});
        $numerical = ($c >= ord('0')) && ($c <= ord('9'));
        if ($prev_obfuscated and $numerical ) {
            $obfuscated.='&#'.ord($plaintext{$i});
        } else if (rand(0,2)) {
            $obfuscated.='&#'.ord($plaintext{$i});
            $prev_obfuscated = true;
        } else {
            $obfuscated.=$plaintext{$i};
            $prev_obfuscated = false;
        }
      $i++;
    }
    return $obfuscated;
}

function obfuscate_mailto($email, $label="", $dimmed=false) {
/// This function uses the above two functions to generate a fully
/// obfuscated email link, ready to use.

    if (empty($label)) {
        $label = $email;
    }
    if ($dimmed) {
        $title = get_string('emaildisable');
        $dimmed = ' class="dimmed"';
    } else {
        $title = '';
        $dimmed = '';
    }
    return sprintf("<a href=\"%s:%s\" $dimmed title=\"$title\">%s</a>",
                    obfuscate_text('mailto'), obfuscate_email($email),
                    obfuscate_text($label));
}

function print_paging_bar($totalcount, $page, $perpage, $baseurl) {
/// Prints a single paging bar to provide access to other pages  (usually in a search)

    $maxdisplay = 18;

    if ($totalcount > $perpage) {
        echo "<center>";
        echo "<p>".get_string("page").":";
        if ($page > 0) {
            $pagenum=$page-1;
            echo "&nbsp;(<a  href=\"{$baseurl}page=$pagenum\">".get_string("previous")."</a>)&nbsp;";
        }
        $lastpage = ceil($totalcount / $perpage);
        if ($page > 15) {
            $startpage = $page - 10;
            echo "&nbsp<a href=\"{$baseurl}page=0\">1</a>&nbsp;...";
        } else {
            $startpage = 0;
        }
        $currpage = $startpage;
        $displaycount = 0;
        while ($displaycount < $maxdisplay and $currpage < $lastpage) {
            $displaypage = $currpage+1;
            if ($page == $currpage) {
                echo "&nbsp;&nbsp;$displaypage";
            } else {
                echo "&nbsp;&nbsp;<a href=\"{$baseurl}page=$currpage\">$displaypage</a>";
            }
            $displaycount++;
            $currpage++;
        }
        if ($currpage < $lastpage) {
            $lastpageactual = $lastpage - 1;
            echo "&nbsp;...<a href=\"{$baseurl}page=$lastpageactual\">$lastpage</a>&nbsp;";
        }
        $pagenum = $page + 1;
        if ($pagenum != $displaypage) {
            echo "&nbsp;&nbsp;(<a href=\"{$baseurl}page=$pagenum\">".get_string("next")."</a>)";
        }
        echo "</p>";
        echo "</center>";
    }
}

//This function is used to rebuild the <nolink> tag because some formats (PLAIN and WIKI)
//will transform it to html entities
function rebuildnolinktag($text) {

    $text = preg_replace('/&lt;(\/*nolink)&gt;/i','<$1>',$text);

    return $text;
}

// vim:autoindent:expandtab:shiftwidth=4:tabstop=4:tw=140:
?>
